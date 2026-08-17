<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| User Profile View Tests (GET /{user:username})
|--------------------------------------------------------------------------
*/

it('redirects guests away from user profile page', function () {
    $user = User::factory()->create();

    $response = $this->get(route('user_profile', $user));

    $response->assertRedirect(route('login'));
});

it('renders user profile page for authenticated users', function () {
    $user = User::factory()->create(['username' => 'johndoe']);

    $response = $this->actingAs($user)->get(route('user_profile', $user));

    $response->assertOk();
    $response->assertViewIs('user.profile');
    $response->assertSee('johndoe');
});

it('shows edit profile button when viewing own profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('user_profile', $user));

    $response->assertOk();
    $response->assertSee('/'.$user->username.'/edit');
});

it('hides edit profile button when viewing another user profile', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $response = $this->actingAs($user)->get(route('user_profile', $otherUser));

    $response->assertOk();
    $response->assertDontSee(route('edit_user_profile', $otherUser));
});

it('displays posts on public user profile', function () {
    $user = User::factory()->create(['private_account' => false]);
    $viewer = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($viewer)->get(route('user_profile', $user));

    $response->assertOk();
    $response->assertSee($post->slug);
});

it('hides posts and shows private message when viewing another user private profile', function () {
    $privateUser = User::factory()->create(['private_account' => true]);
    $viewer = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $privateUser->id]);

    $response = $this->actingAs($viewer)->get(route('user_profile', $privateUser));

    $response->assertOk();
    $response->assertSee('This account is private. Follow them to see thier posts.');
    $response->assertDontSee($post->slug);
});

it('shows posts to owner even if profile is private', function () {
    $privateUser = User::factory()->create(['private_account' => true]);
    $post = Post::factory()->create(['user_id' => $privateUser->id]);

    $response = $this->actingAs($privateUser)->get(route('user_profile', $privateUser));

    $response->assertOk();
    $response->assertSee($post->slug);
});

it('returns 404 when user profile username does not exist', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/nonexistent-username-xyz');

    $response->assertNotFound();
});

/*
|--------------------------------------------------------------------------
| Edit User Profile View Tests (GET /{user:username}/edit)
|--------------------------------------------------------------------------
*/

it('redirects guests away from edit profile page', function () {
    $user = User::factory()->create();

    $response = $this->get(route('edit_user_profile', $user));

    $response->assertRedirect(route('login'));
});

it('renders edit profile page for authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('edit_user_profile', $user));

    $response->assertOk();
    $response->assertViewIs('user.edit');
    $response->assertViewHas('user', fn ($viewUser) => $viewUser->id === $user->id);
});

/*
|--------------------------------------------------------------------------
| Update User Profile Action Tests (PATCH /{user:username}/update)
|--------------------------------------------------------------------------
*/

it('redirects guests attempting to update user profile', function () {
    $user = User::factory()->create();

    $response = $this->patch(route('update_user_profile', $user), [
        'username' => 'newusername',
        'name' => 'New Name',
        'email' => 'newemail@example.com',
    ]);

    $response->assertRedirect(route('login'));
});

it('allows user to update basic profile information', function () {
    $user = User::factory()->create([
        'username' => 'oldusername',
        'name' => 'Old Name',
        'bio' => 'Old Bio',
        'email' => 'old@example.com',
        'private_account' => false,
    ]);

    $response = $this->actingAs($user)->patch(route('update_user_profile', $user), [
        'username' => 'newusername',
        'name' => 'New Name',
        'bio' => 'Updated bio text',
        'email' => 'new@example.com',
        'private_account' => 'on',
    ]);

    $user->refresh();

    $response->assertRedirect(route('user_profile', $user));
    $response->assertSessionHas('success', 'Your profile has been updated successfully!');

    expect($user->username)->toBe('newusername');
    expect($user->name)->toBe('New Name');
    expect($user->bio)->toBe('Updated bio text');
    expect($user->email)->toBe('new@example.com');
    expect($user->private_account)->toBeTruthy();
});

it('allows user to update profile image', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $newAvatar = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->actingAs($user)->patch(route('update_user_profile', $user), [
        'username' => $user->username,
        'name' => $user->name,
        'email' => $user->email,
        'image' => $newAvatar,
    ]);

    $user->refresh();

    $response->assertRedirect(route('user_profile', $user));
    expect($user->image)->toStartWith('/storage/users/');

    $storagePath = str_replace('/storage/', '', $user->image);
    Storage::disk('public')->assertExists($storagePath);
});

it('allows user to update password when confirmation matches', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password-123'),
    ]);

    $response = $this->actingAs($user)->patch(route('update_user_profile', $user), [
        'username' => $user->username,
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertRedirect(route('user_profile', $user));

    $user->refresh();
    expect(Hash::check('new-password-123', $user->password))->toBeTrue();
});

it('keeps existing password if password field is left empty', function () {
    $originalHash = Hash::make('original-password');
    $user = User::factory()->create([
        'password' => $originalHash,
    ]);

    $response = $this->actingAs($user)->patch(route('update_user_profile', $user), [
        'username' => $user->username,
        'name' => $user->name,
        'email' => $user->email,
        'password' => '',
        'password_confirmation' => '',
    ]);

    $response->assertRedirect(route('user_profile', $user));

    $user->refresh();
    expect($user->password)->toBe($originalHash);
});

/*
|--------------------------------------------------------------------------
| Validation & Edge Cases
|--------------------------------------------------------------------------
*/

it('fails validation when username is empty', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('update_user_profile', $user), [
        'username' => '',
        'name' => 'Valid Name',
        'email' => 'valid@example.com',
    ]);

    $response->assertSessionHasErrors(['username']);
});

it('fails validation when username is taken by another user', function () {
    User::factory()->create(['username' => 'taken_username']);
    $user = User::factory()->create(['username' => 'my_username']);

    $response = $this->actingAs($user)->patch(route('update_user_profile', $user), [
        'username' => 'taken_username',
        'name' => $user->name,
        'email' => $user->email,
    ]);

    $response->assertSessionHasErrors(['username']);
});

it('allows user to submit their current username', function () {
    $user = User::factory()->create(['username' => 'same_username']);

    $response = $this->actingAs($user)->patch(route('update_user_profile', $user), [
        'username' => 'same_username',
        'name' => 'Updated Name',
        'email' => $user->email,
    ]);

    $response->assertSessionHasNoErrors();
    $user->refresh();
    expect($user->name)->toBe('Updated Name');
});

it('fails validation when email is invalid', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('update_user_profile', $user), [
        'username' => $user->username,
        'name' => $user->name,
        'email' => 'invalid-email-string',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('fails validation when password confirmation does not match', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('update_user_profile', $user), [
        'username' => $user->username,
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'new-password-123',
        'password_confirmation' => 'mismatched-password',
    ]);

    $response->assertSessionHasErrors(['password']);
});

it('fails validation when password is too short', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('update_user_profile', $user), [
        'username' => $user->username,
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors(['password']);
});

it('fails validation when image is not a valid image file', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $pdfFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $response = $this->actingAs($user)->patch(route('update_user_profile', $user), [
        'username' => $user->username,
        'name' => $user->name,
        'email' => $user->email,
        'image' => $pdfFile,
    ]);

    $response->assertSessionHasErrors(['image']);
});
