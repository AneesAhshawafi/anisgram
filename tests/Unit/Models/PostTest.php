<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Tests that the Post model defines a BelongsTo relationship method named 'user'.
 */
it('belongs to a user', function () {
    $relation = (new Post)->user();

    expect($relation)->toBeInstanceOf(BelongsTo::class);
});

/**
 * Tests that the Post model defines a HasMany relationship method named 'comments'.
 */
it('has many comments', function () {
    $relation = (new Post)->comments();

    expect($relation)->toBeInstanceOf(HasMany::class);
});

/**
 * Tests that a Post instance can retrieve multiple related Comment models.
 */
it('can have many comments', function () {
    $post = Post::factory()->create();
    Comment::factory()->count(3)->create(['post_id' => $post->id]);

    expect($post->comments)->toHaveCount(3);
});

/**
 * Tests that a newly created Post without comments has an empty comments collection.
 */
it('returns zero comments for a fresh post', function () {
    $post = Post::factory()->create();

    expect($post->comments)->toHaveCount(0);
});

/**
 * Tests that a Post created via factory persists all expected columns and default values (e.g. likes = 0) in the database.
 */
it('is stored with the correct columns in the database', function () {
    $post = Post::factory()->create();

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'slug' => $post->slug,
        'description' => $post->description,
        'image' => $post->image,
        'likes' => 0, // default value from migration
        'user_id' => $post->user_id,
    ]);
});

/**
 * Tests database foreign key constraint: deleting a User automatically deletes all associated Post records (cascade delete).
 */
it('is cascade-deleted when its owner user is deleted', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $user->delete();

    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

/**
 * Tests database foreign key constraint: deleting a Post automatically deletes all associated Comment records (cascade delete).
 */
it('cascade-deletes all its comments when deleted', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->create(['post_id' => $post->id]);

    $post->delete();

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});
