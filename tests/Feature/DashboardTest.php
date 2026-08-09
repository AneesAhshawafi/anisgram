<?php

use App\Models\User;

/**
 * Tests that unauthenticated guests trying to access /dashboard are redirected to /login by the 'auth' middleware.
 */
test('guests are redirected to login when accessing the dashboard', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

/**
 * Tests that authenticated users can successfully view the /dashboard route (HTTP 200 OK).
 */
test('authenticated and verified users can access the dashboard', function () {
    $user = User::factory()->create(); // email_verified_at is set by default

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
});

/**
 * Tests that the root URL ('/') is publicly accessible without requiring authentication (HTTP 200 OK).
 */
test('the welcome page is publicly accessible', function () {
    $response = $this->get('/');

    $response->assertOk();
});
