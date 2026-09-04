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
            new Middleware('throttle:10,1', only: [
                'store',
                'update',
                'toggleStatus',
                'bulkDelete',
                'bulkStatus',
                'restoreAll',
            ]),
        ];
    }

    /**
     * Display posts with search, filters, sorting,
     * pagination and statistics.
     */
    public function index(Request $request)
    {
        $query = Post::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Category Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | Date Range Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('from_date')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->to_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $allowedSorts = [
            'id',
            'title',
            'category',
            'status',
            'created_at',
        ];

        $sort = $request->get('sort', 'created_at');

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        $direction = $request->get('direction', 'desc');

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query->orderBy($sort, $direction);

        /*
        |--------------------------------------------------------------------------
        | Per Page
        |--------------------------------------------------------------------------
        */

        $allowedPerPage = [5, 10, 25, 50];

        $perPage = (int) $request->get('per_page', 5);

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 5;
        }

        $posts = $query
            ->paginate($perPage)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $categories = Post::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->orderBy('category')
            ->pluck('category');

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalPosts = Post::count();

        $publishedPosts = Post::where(
            'status',
            'published'
        )->count();

        $draftPosts = Post::where(
            'status',
            'draft'
        )->count();

        $trashPosts = Post::onlyTrashed()->count();

        return view('posts.index', compact(
            'posts',
            'categories',
            'totalPosts',
            'publishedPosts',
            'draftPosts',
            'trashPosts',
            'sort',
            'direction',
            'perPage'
        ));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $categories = Post::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->orderBy('category')
            ->pluck('category');

        return view('posts.create', compact('categories'));
    }

    /**
     * Store new post.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                'unique:posts,title',
            ],

            'content' => 'nullable|string',

            'category' => 'nullable|string|max:100',

            'status' => 'required|in:published,draft',

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], [
            'title.unique' =>
            'A post with this title already exists.',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request
                ->file('image')
                ->store('posts', 'public');
        }

        Post::create($data);

        return redirect()
            ->route('posts.index')
            ->with(
                'success',
                'Post Created Successfully!'
            );
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
            ->where('category', '!=', '')
            ->orderBy('category')
            ->pluck('category');

        return view(
            'posts.edit',
            compact('post', 'categories')
        );
    }

    /**
     * Update post.
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $data = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                'unique:posts,title,' . $post->id,
            ],

            'content' => 'nullable|string',

            'category' => 'nullable|string|max:100',

            'status' => 'required|in:published,draft',

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], [
            'title.unique' =>
            'Another post with this title already exists.',
        ]);

        if ($request->hasFile('image')) {

            if ($post->image) {
                Storage::disk('public')->delete(
                    $post->image
                );
            }

            $data['image'] = $request
                ->file('image')
                ->store('posts', 'public');
        }

        $post->update($data);

        return redirect()
            ->route('posts.index')
            ->with(
                'success',
                'Post Updated Successfully!'
            );
    }

    /**
     * Delete post.
     */
    public function destroy($id)
    {
        Post::findOrFail($id)->delete();

        return redirect()
            ->route('posts.index')
            ->with(
                'success',
                'Post Deleted Successfully!'
            );
    }

    /**
     * Trash list.
     */
    public function trash()
    {
        $posts = Post::onlyTrashed()
            ->latest('deleted_at')
            ->paginate(5);

        $trashCount = Post::onlyTrashed()->count();

        return view(
            'posts.trash',
            compact('posts', 'trashCount')
        );
    }

    /**
     * Restore one post.
     */
    public function restore($id)
    {
        Post::onlyTrashed()
            ->findOrFail($id)
            ->restore();

        return redirect()
            ->route('posts.trash')
            ->with(
                'success',
                'Post Restored Successfully!'
            );
    }

    /**
     * Restore all trash.
     */
    public function restoreAll()
    {
        $count = Post::onlyTrashed()->count();

        if ($count === 0) {
            return redirect()
                ->route('posts.trash')
                ->with(
                    'error',
                    'Trash is already empty.'
                );
        }

        Post::onlyTrashed()->restore();

        return redirect()
            ->route('posts.trash')
            ->with(
                'success',
                "{$count} post(s) restored successfully!"
            );
    }

    /**
     * Permanently delete post.
     */
    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()
            ->findOrFail($id);

        if ($post->image) {
            Storage::disk('public')->delete(
                $post->image
            );
        }

        $post->forceDelete();

        return redirect()
            ->route('posts.trash')
            ->with(
                'success',
                'Post Permanently Deleted!'
            );
    }

    /**
     * AJAX status toggle.
     */
    public function toggleStatus($id)
    {
        $post = Post::findOrFail($id);

        $post->status =
            $post->status === 'published'
            ? 'draft'
            : 'published';

        $post->save();

        return response()->json([
            'success' => true,
            'message' =>
            'Post status updated successfully!',
            'status' => $post->status,
            'post_id' => $post->id,
        ]);
    }

    /**
     * Bulk delete posts.
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'post_ids' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'post_ids.*' => [
                    'integer',
                    'exists:posts,id',
                ],
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' =>
                'Please select at least one valid post.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $postIds = $request->post_ids;

        $posts = Post::whereIn(
            'id',
            $postIds
        )->get();

        $deletedCount = $posts->count();

        foreach ($posts as $post) {
            $post->delete();
        }

        return response()->json([
            'success' => true,
            'message' =>
            "{$deletedCount} post(s) moved to trash successfully!",
            'deleted_count' => $deletedCount,
        ]);
    }

    /**
     * Bulk status update.
     */
    public function bulkStatus(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'post_ids' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'post_ids.*' => [
                    'integer',
                    'exists:posts,id',
                ],

                'status' => [
                    'required',
                    'in:published,draft',
                ],
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' =>
                'Invalid bulk status request.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $count = Post::whereIn(
            'id',
            $request->post_ids
        )->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' =>
            "{$count} post(s) updated to " .
                ucfirst($request->status) .
                ".",
            'updated_count' => $count,
            'status' => $request->status,
        ]);
    }

    /**
     * Export filtered posts as CSV.
     */
    public function exportCsv(Request $request)
    {
        $query = Post::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere(
                        'title',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'content',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($request->filled('category')) {
            $query->where(
                'category',
                $request->category
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('from_date')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->to_date
            );
        }

        $posts = $query
            ->latest()
            ->get();

        $filename =
            'posts_filtered_' .
            now()->format('Ymd_His') .
            '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' =>
            "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($posts) {

            $handle = fopen(
                'php://output',
                'w'
            );

            fputcsv($handle, [
                'ID',
                'Title',
                'Content',
                'Category',
                'Status',
                'Created At',
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

        return view(
            'posts.pdf',
            compact('posts')
        );
    }
}
