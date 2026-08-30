<?php

use App\Livewire\Posts\EditPostModal;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Livewire: posts.edit-post-modal Component Tests
|--------------------------------------------------------------------------
*/

it('mounts edit-post-modal with post_id and initializes description', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'description' => 'Original modal description',
        'image' => 'posts/test_modal.jpg',
    ]);

    $this->actingAs($user);

    Livewire::test('posts.edit-post-modal', ['post_id' => $post->id])
        ->assertOk()
        ->assertSet('post_id', $post->id)
        ->assertSet('description', 'Original modal description')
        ->assertSee(asset('storage/posts/test_modal.jpg'));
});

it('returns 5xl for modalMaxWidth static method', function () {
    expect(EditPostModal::modalmaxWidth())->toBe('5xl');
});

it('retrieves target post via computed property', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $component = Livewire::test('posts.edit-post-modal', ['post_id' => $post->id]);

    expect($component->get('targetPost')->id)->toBe($post->id);
});

it('allows user to update post description, closes modal and dispatches post_updated event', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'description' => 'Before update description',
    ]);

    $this->actingAs($user);

    Livewire::test('posts.edit-post-modal', ['post_id' => $post->id])
        ->set('description', 'Successfully updated post description!')
        ->call('update')
        ->assertHasNoErrors()
        ->assertDispatched('post_updated')
        ->assertDispatched('closeModal');

    $post->refresh();
    expect($post->description)->toBe('Successfully updated post description!');
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'description' => 'Successfully updated post description!',
    ]);
});

it('fails validation when updating post description with empty value', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'description' => 'Original description untouched',
    ]);

    $this->actingAs($user);

    Livewire::test('posts.edit-post-modal', ['post_id' => $post->id])
        ->set('description', '')
        ->call('update')
        ->assertHasErrors(['description' => 'required']);

    $post->refresh();
    expect($post->description)->toBe('Original description untouched');
});
