<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('throttle:10,1', only: ['store', 'update']),
        ];
    }

    /**
     * Display posts with search and filters.
     */
    public function index(Request $request)
    {
        $query = Post::latest();

        if ($request->filled('search')) {
            $s = $request->search;

            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('content', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->paginate(5)->withQueryString();

        $categories = Post::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return view('posts.index', compact('posts', 'categories'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $categories = Post::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return view('posts.create', compact('categories'));
    }

    /**
     * Store new post.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'status'   => 'required|in:published,draft',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('posts', 'public');
        }

        Post::create($data);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post Created Successfully!');
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);

        $categories = Post::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return view('posts.edit', compact('post', 'categories'));
    }

    /**
     * Update post.
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'status'   => 'required|in:published,draft',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {

            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            $data['image'] = $request->file('image')
                ->store('posts', 'public');
        }

        $post->update($data);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post Updated Successfully!');
    }

    /**
     * Delete post.
     */
    public function destroy($id)
    {
        Post::findOrFail($id)->delete();

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post Deleted Successfully!');
    }

    /**
     * Trash list.
     */
    public function trash()
    {
        $posts = Post::onlyTrashed()
            ->latest()
            ->paginate(5);

        return view('posts.trash', compact('posts'));
    }

    /**
     * Restore soft deleted post.
     */
    public function restore($id)
    {
        Post::onlyTrashed()
            ->findOrFail($id)
            ->restore();

        return redirect()
            ->route('posts.trash')
            ->with('success', 'Post Restored Successfully!');
    }

    /**
     * Permanently delete post.
     */
    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->forceDelete();

        return redirect()
            ->route('posts.trash')
            ->with('success', 'Post Permanently Deleted!');
    }

    /**
     * AJAX status toggle.
     */
    public function toggleStatus($id)
    {
        $post = Post::findOrFail($id);

        $post->status = $post->status === 'published'
            ? 'draft'
            : 'published';

        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Post status updated successfully!',
            'status'  => $post->status,
            'post_id' => $post->id,
        ]);
    }

    /**
     * Bulk delete posts.
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_ids'   => 'required|array|min:1',
            'post_ids.*' => 'integer|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one valid post.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $postIds = $request->post_ids;

        $posts = Post::whereIn('id', $postIds)->get();

        $deletedCount = $posts->count();

        foreach ($posts as $post) {
            $post->delete();
        }

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} post(s) moved to trash successfully!",
            'deleted_count' => $deletedCount,
        ]);
    }

    /**
     * Export CSV.
     */
    public function exportCsv()
    {
        $posts = Post::all();

        $filename = 'posts_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($posts) {

            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Title',
                'Content',
                'Category',
                'Status',
                'Created At'
            ]);

            foreach ($posts as $post) {

                fputcsv($handle, [
                    $post->id,
                    $post->title,
                    $post->content,
                    $post->category,
                    $post->status,
                    $post->created_at,
                ]);
            }

            fclose($handle);
        };

        return response()->stream(
            $callback,
            200,
            $headers
        );
    }

    /**
     * Export simple printable PDF.
     */
    public function exportPdf()
    {
        $posts = Post::all();

        return view('posts.pdf', compact('posts'));
    }
}