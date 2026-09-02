<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('throttle:10,1', only: ['store', 'update']),
        ];
    }

    public function index(Request $request)
    {
        $query = Post::latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%$s%")
                  ->orWhere('content', 'like', "%$s%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->paginate(5)->withQueryString();
        $categories = Post::select('category')->distinct()->whereNotNull('category')->pluck('category');

        return view('posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = Post::select('category')->distinct()->whereNotNull('category')->pluck('category');
        return view('posts.create', compact('categories'));
    }

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
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        Post::create($data);

        return redirect()->route('posts.index')->with('success', 'Post Created Successfully!');
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $categories = Post::select('category')->distinct()->whereNotNull('category')->pluck('category');
        return view('posts.edit', compact('post', 'categories'));
    }

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
                \Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($data);

        return redirect()->route('posts.index')->with('success', 'Post Updated Successfully!');
    }

    public function destroy($id)
    {
        Post::findOrFail($id)->delete();
        return redirect()->route('posts.index')->with('success', 'Post Deleted Successfully!');
    }

    // Trash list
    public function trash()
    {
        $posts = Post::onlyTrashed()->latest()->paginate(5);
        return view('posts.trash', compact('posts'));
    }

    // Restore soft-deleted post
    public function restore($id)
    {
        Post::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('posts.trash')->with('success', 'Post Restored Successfully!');
    }

    // Permanent delete
    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        if ($post->image) {
            \Storage::disk('public')->delete($post->image);
        }
        $post->forceDelete();
        return redirect()->route('posts.trash')->with('success', 'Post Permanently Deleted!');
    }

    // Toggle published/draft
    public function toggleStatus($id)
    {
        $post = Post::findOrFail($id);
        $post->status = $post->status === 'published' ? 'draft' : 'published';
        $post->save();
        return redirect()->back()->with('success', 'Status Updated!');
    }

    // Export CSV
    public function exportCsv()
    {
        $posts = Post::all();
        $filename = 'posts_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($posts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Title', 'Content', 'Category', 'Status', 'Created At']);
            foreach ($posts as $post) {
                fputcsv($handle, [$post->id, $post->title, $post->content, $post->category, $post->status, $post->created_at]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Export simple HTML-based PDF (no extra package needed)
    public function exportPdf()
    {
        $posts = Post::all();
        return view('posts.pdf', compact('posts'));
    }
}
