<?php

namespace App\Http\Controllers;

use App\Models\Post;

class LikeController extends Controller
{
    // public function like(Post $post)
    public function __invoke(Post $post)
    {
        // auth()->user()->likes()->attach($post->id);
        // auth()->user()->likes()->detach($post->id);
        auth()->user()->likes()->toggle($post->id);

        return back();
    }
}
