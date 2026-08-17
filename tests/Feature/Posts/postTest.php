use App\Models\Comment;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
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
        'description' => 'Testing image extension '.$filename,
        'image' => $file,
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('posts', [
        'user_id' => $user->id,
        'description' => 'Testing image extension '.$filename,
    ]);
})->with(['photo.jpeg', 'photo.jpg', 'photo.png', 'photo.gif']);

/*
|--------------------------------------------------------------------------
| Edit View Tests
|--------------------------------------------------------------------------
*/
it('redirects guests away from the post edit form', function () {
    $post = Post::factory()->create();
    $response = $this->get(route('edit_post', $post));
    $response->assertRedirect(route('login'));
});
it('renders the edit form for the post owner', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $owner->id]);
    $response = $this->actingAs($owner)->get(route('edit_post', $post));
    $response->assertOk();
    $response->assertViewIs('posts.edit');
    $response->assertViewHas('post', fn ($viewPost) => $viewPost->id === $post->id);
});
it('prevents non-owners from accessing the edit form', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $owner->id]);
    $response = $this->actingAs($otherUser)->get(route('edit_post', $post));
    $response->assertRedirect();
    $response->assertSessionHasErrors(['error' => 'You do not have the permission to edit this post']);
});
/*
|--------------------------------------------------------------------------
| Update Action Tests
|--------------------------------------------------------------------------
*/
it('redirects guests attempting to update a post', function () {
    $post = Post::factory()->create();
    $response = $this->patch(route('update_post', $post), [
        'description' => 'Updated content',
    ]);
    $response->assertRedirect(route('login'));
});
it('allows post owner to update post description', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $owner->id,
        'description' => 'Original description',
    ]);
    $response = $this->actingAs($owner)->patch(route('update_post', $post), [
        'description' => 'Updated description',
    ]);
    $response->assertRedirect('/p/'.$post->slug);
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'description' => 'Updated description',
    ]);
});
it('allows post owner to update post with a new image', function () {
    Storage::fake('public');
    $owner = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $owner->id]);
    $newImage = UploadedFile::fake()->image('new_photo.jpg');
    $response = $this->actingAs($owner)->patch(route('update_post', $post), [
        'description' => 'Updated description with image',
        'image' => $newImage,
    ]);
    $response->assertRedirect('/p/'.$post->slug);
    $post->refresh();
    expect($post->description)->toBe('Updated description with image');
    Storage::disk('public')->assertExists($post->image);
});
it('fails validation when updating post with an empty description', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $owner->id,
        'description' => 'Original description',
    ]);
    $response = $this->actingAs($owner)->patch(route('update_post', $post), [
        'description' => '',
    ]);
    $response->assertSessionHasErrors(['description']);
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'description' => 'Original description',
    ]);
});
it('fails validation when updating with an invalid image type', function () {
    Storage::fake('public');
    $owner = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $owner->id]);
    $invalidFile = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');
    $response = $this->actingAs($owner)->patch(route('update_post', $post), [
        'description' => 'Valid description',
        'image' => $invalidFile,
    ]);
    $response->assertSessionHasErrors(['image']);
});
it('prevents non-owners from updating another user post', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $owner->id,
        'description' => 'Original description',
    ]);
    $response = $this->actingAs($otherUser)->patch(route('update_post', $post), [
        'description' => 'Hacked description',
    ]);
    $response->assertRedirect();
    $response->assertSessionHasErrors(['error' => 'You do not have the permission to edit this post']);
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'description' => 'Original description',
    ]);
});
/*
|--------------------------------------------------------------------------
| Delete / Destroy Action Tests
|--------------------------------------------------------------------------
*/
it('redirects guests attempting to delete a post', function () {
    $post = Post::factory()->create();
    $response = $this->delete(route('delete_post', $post));
    $response->assertRedirect(route('login'));
});
it('allows post owner to delete their post', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $owner->id]);
    $response = $this->actingAs($owner)->delete(route('delete_post', $post));
    $response->assertRedirect(url('home'));
    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
});
it('prevents non-owners from deleting another user post', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $owner->id]);
    $response = $this->actingAs($otherUser)->delete(route('delete_post', $post));
    $response->assertRedirect();
    $response->assertSessionHasErrors(['error' => 'You do not have the permission to delete this post']);
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
    ]);
});
/*
|--------------------------------------------------------------------------
| Home Page (Index) Tests
|--------------------------------------------------------------------------
*/

it('redirects guests away from the home page', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect(route('login'));
});

it('renders the home index view for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk();
    $response->assertViewIs('posts.index');
});

it('passes posts and suggested users to the home index view', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Post::factory()->count(3)->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk();
    $response->assertViewHas('posts');
    $response->assertViewHas('suggested_users');
});

it('displays existing posts on the home page', function () {
    $user = User::factory()->create();
    $postOwner = User::factory()->create();
    $user->follow($postOwner);

    $post = Post::factory()->create([
        'user_id' => $postOwner->id,
        'description' => 'Unique home page feed post description',
    ]);

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk();
    $response->assertSee('Unique home page feed post description');
});

/*
|--------------------------------------------------------------------------
| Explore Page Tests
|--------------------------------------------------------------------------
*/

it('redirects guests away from the explore page', function () {
    $response = $this->get(route('explore'));

    $response->assertRedirect(route('login'));
});

it('renders the explore view for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('explore'));

    $response->assertOk();
    $response->assertViewIs('posts.explore');
});

it('excludes the authenticated user own posts from the explore feed', function () {
    $user = User::factory()->create();
    $myPost = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->get(route('explore'));

    $response->assertOk();
    $response->assertDontSee('/p/'.$myPost->slug);
});

it('excludes posts from users with private accounts from the explore feed', function () {
    $user = User::factory()->create();
    $privateUser = User::factory()->create(['private_account' => 1]);
    $privatePost = Post::factory()->create([
        'user_id' => $privateUser->id,
    ]);

    $response = $this->actingAs($user)->get(route('explore'));

    $response->assertOk();
    $response->assertDontSee('/p/'.$privatePost->slug);
});

it('includes posts from public accounts of other users in the explore feed', function () {
    $user = User::factory()->create();
    $publicUser = User::factory()->create(['private_account' => 0]);
    $publicPost = Post::factory()->create([
        'user_id' => $publicUser->id,
    ]);

    $response = $this->actingAs($user)->get(route('explore'));

    $response->assertOk();
    $response->assertSee('/p/'.$publicPost->slug);
});

it('paginates posts on the explore page with 12 items per page', function () {
    $user = User::factory()->create();
    $publicUser = User::factory()->create(['private_account' => 0]);

    Post::factory()->count(15)->create([
        'user_id' => $publicUser->id,
    ]);

    $response = $this->actingAs($user)->get(route('explore'));

    $response->assertOk();
    $response->assertViewHas('posts', function ($posts) {
        return $posts->count() === 12;
    });
});
