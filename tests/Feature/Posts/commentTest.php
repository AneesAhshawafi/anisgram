<?php

// use Tests\TestCase;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
/*
|--------------------------------------------------------------------------
| Guest Authorization Tests
|--------------------------------------------------------------------------
*/

it('redirects guests attempting to post a comment', function () {
    $post = Post::factory()->create();

    $response = $this->post(route('store_comment', $post), [
        'body' => 'Guest Comment Attempt',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('comments', 0);
});

/*
|--------------------------------------------------------------------------
| Store Comment Action Tests
|--------------------------------------------------------------------------
*/
it('allows an authenticated user to post a comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $response = $this->actingAs($user)->post(route('store_comment', $post), [
        'body' => 'This a test post',
        'user_id' => $user->id,
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('comments', [
        'user_id' => $user->id,
        'post_id' => $post->id,
        'body' => 'This a test post',
    ]);
});

it('fails validation when posting a comment with an empty body', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $response = $this->actingAs($user)->post(route('store_comment', $post), [
        'user_id' => $user->id,
        'body' => '',
    ]);
    $response->assertSessionHasErrors(['body']);
    $this->assertDatabaseCount('comments', 0);
});

it('returns 404 when commenting on a non-existent post slug', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post('/p/none-existing-slug/comment', [
        'body' => 'valid testing body',
    ]);
    $response->assertNotFound();
    $this->assertDatabaseCount('comments', 0);
});
