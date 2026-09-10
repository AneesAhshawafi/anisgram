<?php

use App\Livewire\Posts\ExplorePosts;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Livewire: posts.explore-posts Component Tests
|--------------------------------------------------------------------------
*/

it('renders posts.explore-posts component successfully for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(ExplorePosts::class)
        ->assertOk()
        ->assertViewIs('livewire.posts.explore-posts');
});

it('displays posts from public accounts of other users in the explore feed', function () {
    $user = User::factory()->create();
    $publicUser = User::factory()->create(['private_account' => 0]);
    $post = Post::factory()->create([
        'user_id' => $publicUser->id,
    ]);

    $this->actingAs($user);

    Livewire::test(ExplorePosts::class)
        ->assertSee('/p/'.$post->slug);
});

it('excludes the authenticated user own posts from the explore feed', function () {
    $user = User::factory()->create();
    $myPost = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    Livewire::test(ExplorePosts::class)
        ->assertDontSee('/p/'.$myPost->slug);
});

it('excludes posts from users with private accounts from the explore feed', function () {
    $user = User::factory()->create();
    $privateUser = User::factory()->create(['private_account' => 1]);
    $privatePost = Post::factory()->create([
        'user_id' => $privateUser->id,
    ]);

    $this->actingAs($user);

    Livewire::test(ExplorePosts::class)
        ->assertDontSee('/p/'.$privatePost->slug);
});

it('loads posts with default perPage limit of 12', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create(['private_account' => 0]);

    Post::factory()->count(15)->create([
        'user_id' => $otherUser->id,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(ExplorePosts::class);

    expect($component->get('perPage'))->toBe(12);
    expect($component->posts)->toHaveCount(12);
});

it('increases perPage by 12 when loadMore action is called', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create(['private_account' => 0]);

    Post::factory()->count(25)->create([
        'user_id' => $otherUser->id,
    ]);

    $this->actingAs($user);

    Livewire::test(ExplorePosts::class)
        ->assertSet('perPage', 12)
        ->call('loadMore')
        ->assertSet('perPage', 24);
});

it('computes hasMore correctly when more posts exist or all are loaded', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create(['private_account' => 0]);

    Post::factory()->count(15)->create([
        'user_id' => $otherUser->id,
    ]);

    $this->actingAs($user);

    // Initial: 12 loaded out of 15 -> hasMore should be true
    $component = Livewire::test(ExplorePosts::class);
    expect($component->hasMore)->toBeTrue();

    // After loadMore: 24 limit, all 15 loaded -> hasMore should be false
    $component->call('loadMore');
    expect($component->hasMore)->toBeFalse();
});
