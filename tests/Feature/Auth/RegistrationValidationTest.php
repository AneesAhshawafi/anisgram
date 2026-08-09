<?php

use App\Models\User;

// Extends the happy-path test in RegistrationTest.php with validation edge cases.

test('registration screen can be rendered', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
});

test('registration fails when email is already taken', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->post('/register', [
        'name'                  => 'New User',
        'username'              => 'newuser123',
        'email'                 => 'taken@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('registration fails when username is already taken', function () {
    User::factory()->create(['username' => 'takenuser']);

    $response = $this->post('/register', [
        'name'                  => 'New User',
        'username'              => 'takenuser',
        'email'                 => 'unique@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertSessionHasErrors('username');
    $this->assertGuest();
});

test('registration fails when username is shorter than 3 characters', function () {
    $response = $this->post('/register', [
        'name'                  => 'New User',
        'username'              => 'ab',
        'email'                 => 'test@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertSessionHasErrors('username');
});

test('registration fails when username contains invalid characters', function () {
    $response = $this->post('/register', [
        'name'                  => 'New User',
        'username'              => 'invalid user!@#',
        'email'                 => 'test@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertSessionHasErrors('username');
});

test('registration fails when password confirmation does not match', function () {
    $response = $this->post('/register', [
        'name'                  => 'New User',
        'username'              => 'validuser',
        'email'                 => 'test@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'DifferentPass1!',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration fails when required fields are missing', function () {
    $response = $this->post('/register', []);

    $response->assertSessionHasErrors(['name', 'username', 'email', 'password']);
});

test('successful registration stores the user in the database', function () {
    $this->post('/register', [
        'name'                  => 'Test User',
        'username'              => 'testuser',
        'email'                 => 'test@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $this->assertDatabaseHas('users', [
        'email'    => 'test@example.com',
        'username' => 'testuser',
        'name'     => 'Test User',
    ]);
});

test('successful registration logs the user in automatically', function () {
    $response = $this->post('/register', [
        'name'                  => 'Test User',
        'username'              => 'testuser',
        'email'                 => 'test@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registration generates an avatar image url', function () {
    $this->post('/register', [
        'name'                  => 'John Doe',
        'username'              => 'johndoe',
        'email'                 => 'john@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $user = User::where('email', 'john@example.com')->first();
    expect($user->image)->toContain('ui-avatars.com');
});
