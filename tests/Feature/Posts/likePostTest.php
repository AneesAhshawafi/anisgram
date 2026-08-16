use Illuminate\Support\Facades\Route;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Guest Authorization Tests
|--------------------------------------------------------------------------
*/

it('redirects unauthenticated users attempting to like a post to the login page', function () {
    $post = Post::factory()->create();

    $response = $this->get(route('like_post', $post));

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('likes', 0);
});

/*
|--------------------------------------------------------------------------
| Toggle Like Actions & Database State Tests
|--------------------------------------------------------------------------
*/

it('allows an authenticated user to like a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)->get(route('like_post', $post));

    $response->assertRedirect();
    $this->assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

it('allows an authenticated user to un-like a post they have already liked', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    // User already liked the post
    $user->likes()->attach($post->id);
    $this->assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    // Perform toggle action (un-like)
    $response = $this->actingAs($user)->get(route('like_post', $post));

    $response->assertRedirect();
    $this->assertDatabaseMissing('likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

it('toggles like state sequentially when triggered multiple times', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    // 1st trigger -> Like
    $this->actingAs($user)->get(route('like_post', $post));
    $this->assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    // 2nd trigger -> Unlike
    $this->actingAs($user)->get(route('like_post', $post));
    $this->assertDatabaseMissing('likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

it('ensures likes from multiple users are tracked independently', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $post = Post::factory()->create();

    // User A & User B both like the post
    $this->actingAs($userA)->get(route('like_post', $post));
    $this->actingAs($userB)->get(route('like_post', $post));

    $this->assertDatabaseCount('likes', 2);
    $this->assertDatabaseHas('likes', ['user_id' => $userA->id, 'post_id' => $post->id]);
    $this->assertDatabaseHas('likes', ['user_id' => $userB->id, 'post_id' => $post->id]);

    // User A un-likes post
    $this->actingAs($userA)->get(route('like_post', $post));

    $this->assertDatabaseCount('likes', 1);
    $this->assertDatabaseMissing('likes', ['user_id' => $userA->id, 'post_id' => $post->id]);
    $this->assertDatabaseHas('likes', ['user_id' => $userB->id, 'post_id' => $post->id]);
});

/*
|--------------------------------------------------------------------------
| View & Blade Component Rendering Tests
|--------------------------------------------------------------------------
*/

it('renders post component without red fill icon when post is not liked by auth user', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $component = $this->actingAs($user)
        ->blade('<x-post :post="$post" />', ['post' => $post]);

    $component->assertDontSee('fill text-red-500');
});

it('renders post component with red fill icon when post is liked by auth user', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $user->likes()->attach($post->id);

    $component = $this->actingAs($user)
        ->blade('<x-post :post="$post" />', ['post' => $post]);

    $component->assertSee('fill text-red-500');
});

/*
|--------------------------------------------------------------------------
| Edge Cases & Route Binding Tests
|--------------------------------------------------------------------------
*/

it('returns 404 when attempting to like a non-existent post slug', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/p/non-existent-post-slug/like');

    $response->assertNotFound();
    $this->assertDatabaseCount('likes', 0);
});
