<?php

use App\Events\PostLiked;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|---------------------------------------------------------------
| Guest Authorization Tests
|---------------------------------------------------------------
*/

it('redirects unauthenticated users attempting to like a post to the login page', function () {
    $post = Post::factory()->create();

    $response = $this->get(route('like_post', $post));

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('likes', 0);
});

/*
|---------------------------------------------------------------
| Toggle Like Actions & Database State Tests (Controller Routes)
|---------------------------------------------------------------
*/

it('allows an authenticated user to like a post via route', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)->get(route('like_post', $post));

    $response->assertRedirect();
    $this->assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

it('allows an authenticated user to un-like a post they have already liked via route', function () {
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

it('toggles like state sequentially when triggered multiple times via route', function () {
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
|---------------------------------------------------------------
| Livewire: posts.like Component Tests
|---------------------------------------------------------------
*/

it('renders livewire like component without red fill icon when unliked', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user);

    Livewire::test('posts.like', ['post' => $post])
        ->assertOk()
        ->assertDontSee('fill text-red-500');
});

it('renders livewire like component with red fill icon when liked', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $user->likes()->attach($post->id);

    $this->actingAs($user);

    Livewire::test('posts.like', ['post' => $post])
        ->assertOk()
        ->assertSee('fill text-red-500');
});

it('toggles like, dispatches likeToggled, and broadcasts PostLiked event via livewire like component', function () {
    Event::fake([PostLiked::class]);

    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user);

    // Call toggle_like to like the post
    Livewire::test('posts.like', ['post' => $post])
        ->call('toggle_like')
        ->assertDispatched('likeToggled');

    $this->assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    Event::assertDispatched(PostLiked::class, function (PostLiked $event) use ($post) {
        return $event->postId === $post->id;
    });

    // Call toggle_like again to unlike the post
    Livewire::test('posts.like', ['post' => $post])
        ->call('toggle_like')
        ->assertDispatched('likeToggled');

    $this->assertDatabaseMissing('likes', ['user_id' => $user->id, 'post_id' => $post->id]);
});

/*
|---------------------------------------------------------------
| Livewire: posts.likedby Component Tests
||---------------------------------------------------------------
*/

it('renders livewire likedby component with empty state when post has zero likes', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user);

    Livewire::test('posts.likedby', ['post' => $post])
        ->assertOk()
        ->assertDontSee('Liked By')
        ->assertDontSee('others');
});

it('renders livewire likedby component with username when post has 1 like', function () {
    $user = User::factory()->create(['username' => 'alice']);
    $post = Post::factory()->create();
    $user->likes()->attach($post->id);

    $this->actingAs($user);

    Livewire::test('posts.likedby', ['post' => $post])
        ->assertOk()
        ->assertSee('Liked By')
        ->assertSee('alice')
        ->assertDontSee('others');
});

it('renders livewire likedby component with others text when post has multiple likes', function () {
    $userA = User::factory()->create(['username' => 'alice']);
    $userB = User::factory()->create(['username' => 'bob']);
    $post = Post::factory()->create();

    $userA->likes()->attach($post->id);
    $userB->likes()->attach($post->id);

    $this->actingAs($userA);

    Livewire::test('posts.likedby', ['post' => $post])
        ->assertOk()
        ->assertSee('Liked By')
        ->assertSee('others');
});

it('refreshes likes count on livewire likedby component when refreshLikes is triggered', function () {
    $user = User::factory()->create(['username' => 'charlie']);
    $post = Post::factory()->create();

    $this->actingAs($user);

    $test = Livewire::test('posts.likedby', ['post' => $post])
        ->assertDontSee('charlie');

    // Attach like in database
    $user->likes()->attach($post->id);

    // Trigger refreshLikes (via event listener)
    $test->call('refreshLikes')
        ->assertSee('charlie');
});

/*
|---------------------------------------------------------------
| Edge Cases & Route Binding Tests
|---------------------------------------------------------------
*/

it('returns 404 when attempting to like a non-existent post slug', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/p/non-existent-post-slug/like');

    $response->assertNotFound();
    $this->assertDatabaseCount('likes', 0);
});
