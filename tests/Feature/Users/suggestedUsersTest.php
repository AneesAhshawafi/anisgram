<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Livewire: users.suggested-users Component Tests
|--------------------------------------------------------------------------
*/

it('renders users.suggested-users component successfully for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('users.suggested-users')
        ->assertOk()
        ->assertViewIs('livewire.users.suggested-users')
        ->assertSee(__('Suggestions For You'));
});

it('excludes already followed users from suggestions', function () {
    $user = User::factory()->create();
    $followedUser = User::factory()->create(['username' => 'already_followed']);
    $suggestedUser = User::factory()->create(['username' => 'not_followed_yet']);

    $user->following()->attach($followedUser->id, ['confirmed' => true]);

    $this->actingAs($user);

    Livewire::test('users.suggested-users')
        ->assertOk()
        ->assertDontSee('already_followed')
        ->assertSee('not_followed_yet');
});

it('invalidates computed property cache and refreshes suggestions on toggle_follow event', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['username' => 'newly_followed_target']);

    $this->actingAs($user);

    $component = Livewire::test('users.suggested-users')
        ->assertSee('newly_followed_target');

    // Follow targetUser
    $user->following()->attach($targetUser->id, ['confirmed' => true]);

    // Dispatch toggle_follow event to refresh suggested users
    $component->dispatch('toggle_follow')
        ->assertDontSee('newly_followed_target');
});

it('handles null targetUser safely without throwing errors for unauthenticated state', function () {
    Livewire::test('users.suggested-users')
        ->assertOk();
});
