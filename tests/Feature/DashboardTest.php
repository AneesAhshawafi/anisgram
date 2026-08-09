<?php

use App\Models\User;

test('guests are redirected to login when accessing the dashboard', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

test('authenticated and verified users can access the dashboard', function () {
    $user = User::factory()->create(); // email_verified_at is set by default

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
});

test('the welcome page is publicly accessible', function () {
    $response = $this->get('/');

    $response->assertOk();
});
