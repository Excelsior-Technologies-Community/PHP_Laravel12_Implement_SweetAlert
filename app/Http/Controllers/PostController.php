<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Display all posts
    public function index()
    {
        // Fetch latest posts from database
        $posts = Post::latest()->get();

        // Return index page
        return view('posts.index', compact('posts'));
    }

    // Show form to create a new post
    public function create()
    {
        return view('posts.create');
    }

    // Store new post in the database
    public function store(Request $request)
    {
        // Validate request input
        $request->validate(['title' => 'required']);

        // Insert new post
        Post::create([
            'title' => $request->title
        ]);

        // Redirect with success message
        return redirect()->route('posts.index')
            ->with('success', 'Post Created Successfully!');
    }

    // Show edit form for a specific post
    public function edit($id)
    {
        // Find post by ID (404 if not found)
        $post = Post::findOrFail($id);

        return view('posts.edit', compact('post'));
    }

    // Update post in the database
    public function update(Request $request, $id)
    {
        // Validate input
        $request->validate(['title' => 'required']);

        // Update post
        $post = Post::findOrFail($id);
        $post->update([
            'title' => $request->title
        ]);

        // Redirect with success message
        return redirect()->route('posts.index')
            ->with('success', 'Post Updated Successfully!');
    }

    // Delete a post permanently
    public function destroy($id)
    {
        // Delete post
        Post::findOrFail($id)->delete();

        // Redirect with success message
        return redirect()->route('posts.index')
            ->with('success', 'Post Deleted Successfully!');
    }
}
