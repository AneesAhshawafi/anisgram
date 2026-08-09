<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

// ─── UserFactory ─────────────────────────────────────────────────────────────

it('user factory creates a valid user record', function () {
    $user = User::factory()->create();

    expect($user->id)->not->toBeNull();
    expect($user->name)->not->toBeEmpty();
    expect($user->username)->not->toBeEmpty();
    expect($user->email)->not->toBeEmpty();
    expect($user->password)->not->toBeEmpty();

    $this->assertDatabaseHas('users', ['email' => $user->email]);
});

it('user factory generates a unique email for each user', function () {
    [$first, $second] = User::factory()->count(2)->create();

    expect($first->email)->not->toBe($second->email);
});

it('user factory sets email_verified_at by default', function () {
    $user = User::factory()->create();

    expect($user->email_verified_at)->not->toBeNull();
});

it('user factory unverified state nullifies email_verified_at', function () {
    $user = User::factory()->unverified()->create();

    expect($user->email_verified_at)->toBeNull();
    $this->assertDatabaseHas('users', [
        'email'             => $user->email,
        'email_verified_at' => null,
    ]);
});

// ─── PostFactory ──────────────────────────────────────────────────────────────

it('post factory creates a valid post record', function () {
    $post = Post::factory()->create();

    expect($post->id)->not->toBeNull();
    expect($post->description)->not->toBeEmpty();
    expect($post->slug)->not->toBeEmpty();
    expect($post->image)->not->toBeEmpty();
    expect($post->user_id)->not->toBeNull();

    $this->assertDatabaseHas('posts', ['id' => $post->id]);
});

it('post factory automatically creates an associated user', function () {
    $post = Post::factory()->create();

    $this->assertDatabaseHas('users', ['id' => $post->user_id]);
});

it('post factory accepts an explicit user_id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    expect($post->user_id)->toBe($user->id);
});

// ─── CommentFactory ───────────────────────────────────────────────────────────

it('comment factory creates a valid comment record', function () {
    $comment = Comment::factory()->create();

    expect($comment->id)->not->toBeNull();
    expect($comment->body)->not->toBeEmpty();
    expect($comment->user_id)->not->toBeNull();
    expect($comment->post_id)->not->toBeNull();

    $this->assertDatabaseHas('comments', ['id' => $comment->id]);
});

it('comment factory automatically creates an associated user and post', function () {
    $comment = Comment::factory()->create();

    $this->assertDatabaseHas('users', ['id' => $comment->user_id]);
    $this->assertDatabaseHas('posts', ['id' => $comment->post_id]);
});

it('comment factory accepts explicit user_id and post_id', function () {
    $user    = User::factory()->create();
    $post    = Post::factory()->create(['user_id' => $user->id]);
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    expect($comment->user_id)->toBe($user->id);
    expect($comment->post_id)->toBe($post->id);
});
