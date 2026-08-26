<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Livewire: users.pending-followers-list & pending-followers-count Component Tests
|--------------------------------------------------------------------------
*/

it('renders users.pending-followers-list modal component successfully', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('users.pending-followers-list', ['user_id' => $user->id])
        ->assertOk()
        ->assertSee(__('You do not have any following requests.'));
});

it('displays unconfirmed pending follower requests in the modal', function () {
    $user = User::factory()->create();
    $pendingRequester = User::factory()->create([
        'username' => 'pending_requester',
        'name' => 'Pending Requester',
    ]);

    // Attach unconfirmed follower request
    $user->followers()->attach($pendingRequester->id, ['confirmed' => false]);

    $this->actingAs($user);

    Livewire::test('users.pending-followers-list', ['user_id' => $user->id])
        ->assertOk()
        ->assertSee('pending_requester')
        ->assertSee('Pending Requester')
        ->assertSee(__('Confirm'))
        ->assertSee(__('Delete'));
});

it('confirms a pending follower request and dispatches update-pending-followers-count event', function () {
    $user = User::factory()->create();
    $pendingRequester = User::factory()->create(['username' => 'to_be_confirmed']);

    $user->followers()->attach($pendingRequester->id, ['confirmed' => false]);

    $this->actingAs($user);

    Livewire::test('users.pending-followers-list', ['user_id' => $user->id])
        ->call('confirm', $pendingRequester->id)
        ->assertDispatched('update-pending-followers-count');

    // Verify pivot table is updated to confirmed = true
    $this->assertDatabaseHas('follows', [
        'user_id' => $pendingRequester->id,
        'following_user_id' => $user->id,
        'confirmed' => 1,
    ]);
});

it('deletes a pending follower request and dispatches update-pending-followers-count event', function () {
    $user = User::factory()->create();
    $pendingRequester = User::factory()->create(['username' => 'to_be_deleted']);

    $user->followers()->attach($pendingRequester->id, ['confirmed' => false]);

    $this->actingAs($user);

    Livewire::test('users.pending-followers-list', ['user_id' => $user->id])
        ->call('delete', $pendingRequester->id)
        ->assertDispatched('update-pending-followers-count');

    // Verify row is detached from follows table
    $this->assertDatabaseMissing('follows', [
        'user_id' => $pendingRequester->id,
        'following_user_id' => $user->id,
    ]);
});

it('renders pending-followers-count component with count of unconfirmed follower requests', function () {
    $user = User::factory()->create();
    $requester1 = User::factory()->create();
    $requester2 = User::factory()->create();

    $user->followers()->attach($requester1->id, ['confirmed' => false]);
    $user->followers()->attach($requester2->id, ['confirmed' => false]);

    $this->actingAs($user);

    Livewire::test('users.pending-followers-count')
        ->assertOk()
        ->assertSee('2');
});
