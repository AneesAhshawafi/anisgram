<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Theme Switching & Profile Synchronization Tests
|--------------------------------------------------------------------------
*/

it('allows guest to set dark theme in session via /theme/dark', function () {
    $response = $this->get(route('theme.update', ['theme' => 'dark']));

    $response->assertNoContent();
    $this->assertEquals('dark', session('theme'));
});

it('allows guest to set light theme in session via /theme/light', function () {
    $response = $this->get(route('theme.update', ['theme' => 'light']));

    $response->assertNoContent();
    $this->assertEquals('light', session('theme'));
});

it('ignores invalid theme values for guests', function () {
    session(['theme' => 'light']);

    $response = $this->get(route('theme.update', ['theme' => 'neon']));

    $response->assertNoContent();
    $this->assertEquals('light', session('theme'));
});

it('synchronizes theme with user database record for authenticated users', function () {
    $user = User::factory()->create(['theme' => 'light']);

    $response = $this->actingAs($user)->get(route('theme.update', ['theme' => 'dark']));

    $response->assertNoContent();
    $this->assertEquals('dark', $user->fresh()->theme);
    $this->assertEquals('dark', session('theme'));
});

it('allows authenticated user to set theme to system', function () {
    $user = User::factory()->create(['theme' => 'dark']);

    $response = $this->actingAs($user)->get(route('theme.update', ['theme' => 'system']));

    $response->assertNoContent();
    $this->assertEquals('system', $user->fresh()->theme);
});

it('allows user to update theme via profile edit form', function () {
    $user = User::factory()->create(['theme' => 'light', 'lang' => 'ar']);

    $response = $this->actingAs($user)->patch(route('update_user_profile', $user), [
        'username' => $user->username,
        'name' => $user->name,
        'email' => $user->email,
        'lang' => 'ar',
        'theme' => 'dark',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertEquals('dark', $user->fresh()->theme);
});

it('fails validation when an invalid theme is submitted in profile update', function () {
    $user = User::factory()->create(['theme' => 'light']);

    $response = $this->actingAs($user)->patch(route('update_user_profile', $user), [
        'username' => $user->username,
        'name' => $user->name,
        'email' => $user->email,
        'lang' => 'ar',
        'theme' => 'invalid_theme',
    ]);

    $response->assertSessionHasErrors(['theme']);
});
