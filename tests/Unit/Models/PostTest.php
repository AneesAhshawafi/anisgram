<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('belongs to a user', function () {
    $relation = (new Post())->user();

    expect($relation)->toBeInstanceOf(BelongsTo::class);
});

it('has many comments', function () {
    $relation = (new Post())->comments();

    expect($relation)->toBeInstanceOf(HasMany::class);
});

it('can have many comments', function () {
    $post = Post::factory()->create();
    Comment::factory()->count(3)->create(['post_id' => $post->id]);

    expect($post->comments)->toHaveCount(3);
});

it('returns zero comments for a fresh post', function () {
    $post = Post::factory()->create();

    expect($post->comments)->toHaveCount(0);
});

it('is stored with the correct columns in the database', function () {
    $post = Post::factory()->create();

    $this->assertDatabaseHas('posts', [
        'id'          => $post->id,
        'slug'        => $post->slug,
        'description' => $post->description,
        'image'       => $post->image,
        'likes'       => 0, // default value from migration
        'user_id'     => $post->user_id,
    ]);
});

it('is cascade-deleted when its owner user is deleted', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $user->delete();

    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

it('cascade-deletes all its comments when deleted', function () {
    $post    = Post::factory()->create();
    $comment = Comment::factory()->create(['post_id' => $post->id]);

    $post->delete();

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});
