use App\Models\Comment;
<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

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

/*
|--------------------------------------------------------------------------
| Show View Tests
|--------------------------------------------------------------------------
*/

it('redirects guests attempting to view a post', function () {
    $post = Post::factory()->create();

    $response = $this->get(route('show_post', $post));

    $response->assertRedirect(route('login'));
});

it('renders the post show view for authenticated users', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('show_post', $post));

    $response->assertOk();
    $response->assertViewIs('posts.show');
    $response->assertViewHas('post', function ($viewPost) use ($post) {
        return $viewPost->id === $post->id;
    });
});

it('returns 404 when requesting a post with an invalid slug', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/p/invalid-slug-xyz');

    $response->assertNotFound();
});

it('displays existing comments on the post show page', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'body' => 'Unique comment text to test view output',
    ]);

    $response = $this->actingAs($user)->get(route('show_post', $post));

    $response->assertOk();
    $response->assertSee('Unique comment text to test view output');
});

/*
|--------------------------------------------------------------------------
| Business Logic & Edge Case Tests
|--------------------------------------------------------------------------
*/

it('generates a 10-character random slug upon post creation', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');

    $this->actingAs($user)->post(route('store_post'), [
        'description' => 'Testing slug generation',
        'image' => $file,
    ]);

    $post = Post::where('user_id', $user->id)->first();

    expect($post)->not->toBeNull();
    expect(strlen($post->slug))->toBe(10);
});

it('accepts all supported image file extensions', function (string $filename) {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image($filename);

    $response = $this->actingAs($user)->post(route('store_post'), [
        'description' => 'Testing image extension ' . $filename,
        'image' => $file,
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('posts', [
        'user_id' => $user->id,
        'description' => 'Testing image extension ' . $filename,
    ]);
})->with(['photo.jpeg', 'photo.jpg', 'photo.png', 'photo.gif']);
