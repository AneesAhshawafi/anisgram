<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Guest Authorization Tests
|--------------------------------------------------------------------------
*/

it('redirects guests away from the post creation form', function () {
    $response = $this->get(route('create_post'));

    $response->assertRedirect(route('login'));
});

it('redirects guests attempting to store a post', function () {
    $response = $this->post(route('store_post'), [
        'description' => 'Test description',
    ]);

    $response->assertRedirect(route('login'));
});

/*
|--------------------------------------------------------------------------
| Create View Tests
|--------------------------------------------------------------------------
*/

it('renders the post creation form for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('create_post'));

    $response->assertOk();
    $response->assertViewIs('posts.create');
});

/*
|--------------------------------------------------------------------------
| Store Action Tests
|--------------------------------------------------------------------------
*/

it('allows an authenticated user to store a post with a valid image', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');

    $response = $this->actingAs($user)->post(route('store_post'), [
        'description' => 'This is my brand new post',
        'image' => $file,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Post created successfully!');

    $this->assertDatabaseHas('posts', [
        'user_id' => $user->id,
        'description' => 'This is my brand new post',
    ]);

    $post = Post::where('user_id', $user->id)->first();
    expect($post)->not->toBeNull();
    Storage::disk('public')->assertExists($post->image);
});

it('fails validation when storing a post without a description', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');

    $response = $this->actingAs($user)->post(route('store_post'), [
        'image' => $file,
    ]);

    $response->assertSessionHasErrors(['description']);
    $this->assertDatabaseCount('posts', 0);
});

it('fails validation when storing a post without an image', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('store_post'), [
        'description' => 'Post without image',
    ]);

    $response->assertSessionHasErrors(['image']);
    $this->assertDatabaseCount('posts', 0);
});

it('fails validation when uploading an invalid file type', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $response = $this->actingAs($user)->post(route('store_post'), [
        'description' => 'Post with PDF',
        'image' => $file,
    ]);

    $response->assertSessionHasErrors(['image']);
    $this->assertDatabaseCount('posts', 0);
});
