<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Livewire: posts.posts-list Component Tests
|--------------------------------------------------------------------------
*/

it('renders posts.posts-list component successfully for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('posts.posts-list')
        ->assertOk()
        ->assertViewIs('livewire.posts.posts-list');
});

it('displays posts from followed users and excludes posts from non-followed users', function () {
    $user = User::factory()->create();
    $followedUser = User::factory()->create();
    $unfollowedUser = User::factory()->create();

    $user->following()->attach($followedUser->id, ['confirmed' => true]);

    $followedPost = Post::factory()->create([
        'user_id' => $followedUser->id,
        'description' => 'Post from followed user',
    ]);

    $unfollowedPost = Post::factory()->create([
        'user_id' => $unfollowedUser->id,
        'description' => 'Post from unfollowed user',
    ]);

    $this->actingAs($user);

    Livewire::test('posts.posts-list')
        ->assertSee('Post from followed user')
        ->assertDontSee('Post from unfollowed user');
});

it('orders posts by the most recently followed user first', function () {
    $user = User::factory()->create();
    $firstFollowed = User::factory()->create(['username' => 'first_followed']);
    $lastFollowed = User::factory()->create(['username' => 'last_followed']);

    $user->following()->attach($firstFollowed->id, ['confirmed' => true]);
    $user->following()->attach($lastFollowed->id, ['confirmed' => true]);

    DB::table('follows')
        ->where('following_user_id', $firstFollowed->id)
        ->update(['created_at' => now()->subDays(2)]);

    DB::table('follows')
        ->where('following_user_id', $lastFollowed->id)
        ->update(['created_at' => now()]);

    $firstFollowedPost = Post::factory()->create([
        'user_id' => $firstFollowed->id,
        'description' => 'Post from first followed user',
    ]);

    $lastFollowedPost = Post::factory()->create([
        'user_id' => $lastFollowed->id,
        'description' => 'Post from last followed user',
    ]);

    $this->actingAs($user);

    $component = Livewire::test('posts.posts-list');

    $posts = $component->get('posts');

    expect($posts)->not->toBeEmpty();
    expect($posts->first()->id)->toBe($lastFollowedPost->id);
});

it('invalidates computed property cache and refreshes posts on toggle_follow event', function () {
    $user = User::factory()->create();
    $newlyFollowed = User::factory()->create();

    $this->actingAs($user);

    $component = Livewire::test('posts.posts-list')
        ->assertDontSee('Newly followed user post');

    // User follows newlyFollowed user
    $user->following()->attach($newlyFollowed->id, ['confirmed' => true]);

    $newPost = Post::factory()->create([
        'user_id' => $newlyFollowed->id,
        'description' => 'Newly followed user post',
    ]);

    // Dispatch event to trigger refresh & cache invalidation
    $component->dispatch('toggle_follow')
        ->assertSee('Newly followed user post');
});

it('displays empty state message when user does not follow anyone', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('posts.posts-list')
        ->assertOk()
        ->assertSee(__('Start following your friends and enjoy.'));
});
