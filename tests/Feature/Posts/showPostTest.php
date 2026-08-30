<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Livewire: posts.show-post Component Tests
|--------------------------------------------------------------------------
*/

it('renders show-post component successfully for an authenticated user', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test('posts.show-post', ['post_id' => $post->id])
        ->assertOk()
        ->assertViewIs('livewire.posts.show-post');
});

it('retrieves the correct post via targetPost computed property', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'description' => 'Target post description',
    ]);

    $this->actingAs($user);

    $component = Livewire::test('posts.show-post', ['post_id' => $post->id]);

    expect($component->get('targetPost')->id)->toBe($post->id);
    expect($component->get('targetPost')->description)->toBe('Target post description');
});

it('displays post author username, description, and image', function () {
    $author = User::factory()->create(['username' => 'unique_author']);
    $post = Post::factory()->create([
        'user_id' => $author->id,
        'description' => 'Featured showcase post description',
        'image' => 'posts/sample_image.jpg',
    ]);

    $this->actingAs($author);

    Livewire::test('posts.show-post', ['post_id' => $post->id])
        ->assertSee('unique_author')
        ->assertSee('Featured showcase post description')
        ->assertSee(asset('storage/posts/sample_image.jpg'));
});

it('displays edit button and delete form for the post owner', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($owner);

    Livewire::test('posts.show-post', ['post_id' => $post->id])
        ->assertSee('posts.edit-post-modal')
        ->assertSee('edit_square')
        ->assertSee('/p/'.$post->slug.'/delete')
        ->assertSee('delete');
});

it('hides edit and delete controls for non-owners and displays follow button', function () {
    $owner = User::factory()->create();
    $viewer = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($viewer);

    Livewire::test('posts.show-post', ['post_id' => $post->id])
        ->assertDontSee('posts.edit-post-modal')
        ->assertDontSee('/p/'.$post->slug.'/delete')
        ->assertSeeLivewire('posts.follow-button');
});

it('displays existing comments with comment author and body', function () {
    $user = User::factory()->create();
    $commenter = User::factory()->create(['username' => 'commenter_user']);
    $post = Post::factory()->create(['user_id' => $user->id]);

    Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $commenter->id,
        'body' => 'Amazing photo keep it up!',
    ]);

    $this->actingAs($user);

    Livewire::test('posts.show-post', ['post_id' => $post->id])
        ->assertSee('commenter_user')
        ->assertSee('Amazing photo keep it up!');
});

it('renders like and likedby child components and comment form', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test('posts.show-post', ['post_id' => $post->id])
        ->assertSeeLivewire('posts.like')
        ->assertSeeLivewire('posts.likedby')
        ->assertSee('/p/'.$post->slug.'/comment');
});

it('updates rendered description when post_updated event is dispatched', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'description' => 'Original description before edit',
    ]);

    $this->actingAs($user);

    $component = Livewire::test('posts.show-post', ['post_id' => $post->id])
        ->assertSee('Original description before edit');

    // Update post description in DB
    $post->update(['description' => 'Freshly updated description after modal edit']);

    // Dispatch post_updated event to Livewire component
    $component->dispatch('post_updated')
        ->assertSee('Freshly updated description after modal edit');
});

it('renders livewire show-post component via the web show route', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'description' => 'Route integration test post',
    ]);

    $response = $this->actingAs($user)->get(route('show_post', $post));

    $response->assertOk();
    $response->assertSeeLivewire('posts.show-post');
    $response->assertSee('Route integration test post');
});
