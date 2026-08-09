<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     */
    public function index(): View
    {
        $posts = Post::with(['user', 'comments'])->latest()->get();

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'description' => ['required', 'string'],
            'image' => ['required', 'string'],
        ]);

        $post = $request->user()->posts()->create([
            'description' => $validated['description'],
            'image' => $validated['image'],
            'slug' => Str::random(10),
        ]);

        return response()->json($post->load('user'), 201);
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): View
    {
        $post->load(['user', 'comments.user']);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post): View
    {
        abort_if(request()->user()->id !== $post->user_id, 403);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        abort_if($request->user()->id !== $post->user_id, 403, 'You do not own this post.');

        $validated = $request->validate([
            'description' => ['sometimes', 'required', 'string'],
            'image' => ['sometimes', 'required', 'string'],
        ]);

        $post->update($validated);

        return response()->json($post->fresh(), 200);
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Request $request, Post $post)
    {
        abort_if($request->user()->id !== $post->user_id, 403, 'You do not own this post.');

        $post->delete();

        return response()->noContent();
    }
}
