<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Post;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Post $post)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $post->comments()->create($data);
        return back();
    }
}
