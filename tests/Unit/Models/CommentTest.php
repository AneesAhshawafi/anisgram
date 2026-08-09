<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('belongs to a user', function () {
    $relation = (new Comment())->user();

    expect($relation)->toBeInstanceOf(BelongsTo::class);
});

it('belongs to a post', function () {
    $relation = (new Comment())->post();

    expect($relation)->toBeInstanceOf(BelongsTo::class);
});

it('resolves its related user correctly', function () {
    $user    = User::factory()->create();
    $comment = Comment::factory()->create(['user_id' => $user->id]);

    expect($comment->user->id)->toBe($user->id);
});

it('resolves its related post correctly', function () {
    $post    = Post::factory()->create();
    $comment = Comment::factory()->create(['post_id' => $post->id]);

    expect($comment->post->id)->toBe($post->id);
});

it('is stored with the correct columns in the database', function () {
    $comment = Comment::factory()->create();

    $this->assertDatabaseHas('comments', [
        'id'      => $comment->id,
        'body'    => $comment->body,
        'user_id' => $comment->user_id,
        'post_id' => $comment->post_id,
    ]);
});

it('is cascade-deleted when its post is deleted', function () {
    $comment = Comment::factory()->create();

    $comment->post->delete();

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

it('is cascade-deleted when its user is deleted', function () {
    $user    = User::factory()->create();
    $comment = Comment::factory()->create(['user_id' => $user->id]);

    $user->delete();

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});
