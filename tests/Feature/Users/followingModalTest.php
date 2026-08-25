<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Livewire: users.following-modal Component Tests
|--------------------------------------------------------------------------
*/

it('renders following modal component successfully', function () {
    $user = User::factory()->create();

    Livewire::test('users.following-modal', ['user_id' => $user->id])
        ->assertOk()
        ->assertSee('Following');
});

it('displays empty state when user is not following anyone', function () {
    $user = User::factory()->create();

    Livewire::test('users.following-modal', ['user_id' => $user->id])
        ->assertOk()
        ->assertSee('You are not following anyone.');
});

it('displays list of confirmed followed users in the modal', function () {
    $user = User::factory()->create();
    $followed1 = User::factory()->create(['username' => 'followed_one', 'name' => 'First Followed']);
    $followed2 = User::factory()->create(['username' => 'followed_two', 'name' => 'Second Followed']);

    $user->following()->attach($followed1->id, ['confirmed' => true]);
    $user->following()->attach($followed2->id, ['confirmed' => true]);

    Livewire::test('users.following-modal', ['user_id' => $user->id])
        ->assertOk()
        ->assertSee('followed_one')
        ->assertSee('First Followed')
        ->assertSee('followed_two')
        ->assertSee('Second Followed')
        ->assertDontSee('You are not following anyone.');
});

it('only shows confirmed followings and hides pending follow requests', function () {
    $user = User::factory()->create();
    $confirmedUser = User::factory()->create(['username' => 'confirmed_user']);
    $pendingUser = User::factory()->create(['username' => 'pending_user']);

    $user->following()->attach($confirmedUser->id, ['confirmed' => true]);
    $user->following()->attach($pendingUser->id, ['confirmed' => false]);

    Livewire::test('users.following-modal', ['user_id' => $user->id])
        ->assertOk()
        ->assertSee('confirmed_user')
        ->assertDontSee('pending_user');
});

it('allows unfollowing a user directly from the modal and dispatches unfollow event', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['username' => 'unfollowed_user']);

    $user->following()->attach($targetUser->id, ['confirmed' => true]);

    expect($user->following()->wherePivot('confirmed', true)->count())->toBe(1);

    Livewire::test('users.following-modal', ['user_id' => $user->id])
        ->assertSee('unfollowed_user')
        ->call('unfollow', $targetUser->id)
        ->assertDispatched('unfollow');

    $user->refresh();
    expect($user->following()->wherePivot('confirmed', true)->count())->toBe(0);
});

/*
|--------------------------------------------------------------------------
| Livewire: users.following Counter Component Tests
|--------------------------------------------------------------------------
*/

it('renders users.following component with exact confirmed count', function () {
    $user = User::factory()->create();
    $followed1 = User::factory()->create();
    $followed2 = User::factory()->create();

    $user->following()->attach($followed1->id, ['confirmed' => true]);
    $user->following()->attach($followed2->id, ['confirmed' => true]);

    Livewire::test('users.following', ['user_id' => $user->id])
        ->assertOk()
        ->assertSee('2');
});

it('refreshes count when unfollow event is fired', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create();

    $user->following()->attach($followed->id, ['confirmed' => true]);

    $component = Livewire::test('users.following', ['user_id' => $user->id])
        ->assertSee('1');

    $user->unfollow($followed);

    $component->dispatch('unfollow')
        ->assertSee('0');
});
