<?php

use App\Models\User;

/**
 * Tests that the registration page (GET /register) returns HTTP 200 OK.
 */
test('registration screen can be rendered', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
});

/**
 * Tests that submitting an email address already present in the database triggers a validation error.
 */
test('registration fails when email is already taken', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->post('/register', [
        'name' => 'New User',
        'username' => 'newuser123',
        'email' => 'taken@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

/**
 * Tests that submitting a username already present in the database triggers a validation error.
 */
test('registration fails when username is already taken', function () {
    User::factory()->create(['username' => 'takenuser']);

    $response = $this->post('/register', [
        'name' => 'New User',
        'username' => 'takenuser',
        'email' => 'unique@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertSessionHasErrors('username');
    $this->assertGuest();
});

/**
 * Tests that submitting a username with fewer than 3 characters triggers a min-length validation error.
 */
test('registration fails when username is shorter than 3 characters', function () {
    $response = $this->post('/register', [
        'name' => 'New User',
        'username' => 'ab',
        'email' => 'test@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertSessionHasErrors('username');
});

/**
 * Tests that submitting a username with spaces or special characters triggers an alpha_dash validation error.
 */
test('registration fails when username contains invalid characters', function () {
    $response = $this->post('/register', [
        'name' => 'New User',
        'username' => 'invalid user!@#',
        'email' => 'test@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertSessionHasErrors('username');
});

/**
 * Tests that registration fails when password and password_confirmation values do not match.
 */
test('registration fails when password confirmation does not match', function () {
    $response = $this->post('/register', [
        'name' => 'New User',
        'username' => 'validuser',
        'email' => 'test@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'DifferentPass1!',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

/**
 * Tests that submitting an empty registration payload returns validation errors for all required fields.
 */
test('registration fails when required fields are missing', function () {
    $response = $this->post('/register', []);

    $response->assertSessionHasErrors(['name', 'username', 'email', 'password']);
});

/**
 * Tests that valid registration data persists a new user record in the users database table.
 */
test('successful registration stores the user in the database', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'username' => 'testuser',
        'name' => 'Test User',
    ]);
});

/**
 * Tests that a user is automatically authenticated (logged in) immediately following successful registration.
 */
test('successful registration logs the user in automatically', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

/**
 * Tests that the RegisteredUserController automatically generates a default avatar URL (ui-avatars.com) based on user name.
 */
test('registration generates an avatar image url', function () {
    $this->post('/register', [
        'name' => 'John Doe',
        'username' => 'johndoe',
        'email' => 'john@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $user = User::where('email', 'john@example.com')->first();
    expect($user->image)->toContain('ui-avatars.com');
});
