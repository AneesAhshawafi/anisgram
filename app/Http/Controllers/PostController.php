<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     */
    public function index(): View
    {
        // $posts = Post::with(['user', 'comments'])->latest()->get();
        // $posts = Post::all();
        $ids = auth()->user()->following()->wherePivot('confirmed', true)->get()->pluck('id');
        $posts = Post::whereIn('user_id', $ids)->latest()->get();

        $suggested_users = auth()->user()->suggested_users();

        return view('posts.index', compact(['posts', 'suggested_users']));
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
    public function store(PostRequest $request)
    {
        // Get validated data from PostRequest
        $data = $request->validated();
        // Handle file upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }
        $data['slug'] = Str::random(10);
        // Create post via relationship (user_id is set automatically)
        auth()->user()->posts()->create($data);

        // Return redirect with a success flash message
        // return redirect()->route('posts.show', $post)
        //     ->with('success', 'Post created successfully!');
        return redirect('/p/'.$data['slug'])->with('success', 'Post created successfully!');

        // return redirect()->back()
        //     ->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): View
    {

        // $post = Post::find($id);
        // // dd($post);
        // die($post->image);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Request $request, Post $post)
    {
        // if ($post->user->id === Auth::id()) {
        //     return view('posts.edit', compact('post'));
        // }
        // authorization using policy
        Gate::authorize('update', $post);

        return view('posts.edit', compact('post'));

        return redirect()->back()->withErrors(['error' => 'You do not have the permission to edit this post']);
    }

    /**
     * Update the specified post in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        // if ($post->user->id === auth()->id()) {

        //     $data = $request->validated();
        //     if ($request->has('image')) {
        //         $data['image'] = $request->file('image')->store('posts', 'public');
        //     }
        //     $post->update($data);

        //     return redirect('/p/'.$post->slug);
        // }

        // return redirect()->back()->withErrors(['error' => 'You do not have the permission to edit this post']);
        // authorization using policy
        Gate::authorize('update', $post);
        $data = $request->validated();
        if ($request->has('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }
        $post->update($data);

        return redirect('/p/'.$post->slug);
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        // if ($post->user->id === auth()->id()) {
        //     Storage::delete('public'.$post->slug);
        //     $post->delete($post->id);

        //     return redirect(url('home'));
        // }

        // return redirect()->back()->withErrors(['error' => 'You do not have the permission to delete this post']);
        Gate::authorize('delete', $post);
        Storage::delete('public'.$post->slug);
        $post->delete($post->id);

        return redirect(url('/'));
    }

    public function explore()
    {
        // whereRelation('user','private_account','=',0) get posts for the users the thier accounts art not private
        $posts = Post::whereRelation('user', 'private_account', '=', 0)->whereNot('user_id', auth()->id())->simplePaginate(12);

        return view('posts.explore', compact('posts'));
    }
}
