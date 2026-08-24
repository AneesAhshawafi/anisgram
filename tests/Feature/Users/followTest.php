<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|-------------------------------------------------------------------------
| Follow / Unfollow Route Tests
|-------------------------------------------------------------------------
*/

it('redirects guests attempting to follow a user', function () {
    $targetUser = User::factory()->create();

    $response = $this->get(route('follow_user', $targetUser));

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('follows', 0);
});

it('redirects guests attempting to unfollow a user', function () {
    $targetUser = User::factory()->create();

    $response = $this->get(route('unfollow_user', $targetUser));

    $response->assertRedirect(route('login'));
});

it('allows an authenticated user to follow a public account via route', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['private_account' => false]);

    $response = $this->actingAs($user)->get(route('follow_user', $targetUser));

    $response->assertRedirect();
    $this->assertDatabaseHas('follows', [
        'user_id' => $user->id,
        'following_user_id' => $targetUser->id,
        'confirmed' => 1,
    ]);
});

it('allows an authenticated user to follow a private account via route as unconfirmed request', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['private_account' => true]);

    $response = $this->actingAs($user)->get(route('follow_user', $targetUser));

    $response->assertRedirect();
    $this->assertDatabaseHas('follows', [
        'user_id' => $user->id,
        'following_user_id' => $targetUser->id,
        'confirmed' => 0,
    ]);
});

it('allows an authenticated user to unfollow a user via route', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();
    $user->follow($targetUser);

    $response = $this->actingAs($user)->get(route('unfollow_user', $targetUser));

    $response->assertRedirect();
    $this->assertDatabaseMissing('follows', [
        'user_id' => $user->id,
        'following_user_id' => $targetUser->id,
    ]);
});

/*
|-------------------------------------------------------------------------
| Livewire: posts.follow Component Rendering Tests
|-------------------------------------------------------------------------
*/

it('renders livewire follow component with Follow state when not following', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('posts.follow', ['user_id' => $targetUser->id])
        ->assertOk()
        ->assertSee('Follow')
        ->assertDontSee('Unfollow')
        ->assertDontSee('Requested')
        ->assertSee('text-white');
});

it('renders livewirk follow component with Unfollow state when following confirmed user', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();
    $user->follow($targetUser);

    $this->actingAs($user);

    Livewire::test('posts.follow', ['user_id' => $targetUser->id])
        ->assertOk()
        ->assertSee('Unfollow')
        ->assertDontSee('Follow')
        ->assertDontSee('Requested')
        ->assertSee('text-white');
});

it('renders livewire follow component with Requested state and muted text when follow request is pending', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['private_account' => true]);
    $user->follow($targetUser);

    $this->actingAs($user);

    Livewire::test('posts.follow', ['user_id' => $targetUser->id])
        ->assertOk()
        ->assertSee('Requested')
        ->assertDontSee('Unfollow')
        ->assertSee('text-gray-400');
});

it('applies custom button classes dynamically when classes property is provided', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['private_account' => false]);

    $this->actingAs($user);

    $component = Livewire::test('posts.follow', [
        'user_id' => $targetUser->id,
        'classes' => 'profile-button',
    ])
        ->assertOk()
        ->assertSee('follow-btn-profile');

    $component->call('toggle')
        ->assertSee('unfollow-btn-profile');
});

/*
|-------------------------------------------------------------------------
| Livewire: posts.follow Toggle Action & Instant UI Update Tests
|-------------------------------------------------------------------------
*/

it('immediately updates UI from Follow to Unfollow on first click for public accounts', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['private_account' => false]);

    $this->actingAs($user);

    Livewire::test('posts.follow', ['user_id' => $targetUser->id])
        ->assertSee('Follow')
        ->call('toggle')
        ->assertSee('Unfollow')
        ->assertDontSee('Follow');

    expect($user->isFollowing($targetUser))->toBeTrue();
});

it('immediately updates UI from Unfollow to Follow when unfollowing', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['private_account' => false]);
    $user->follow($targetUser);

    $this->actingAs($user);

    Livewire::test('posts.follow', ['user_id' => $targetUser->id])
        ->assertSee('Unfollow')
        ->call('toggle')
        ->assertSee('Follow')
        ->assertDontSee('Unfollow');
    expect($user->isFollowing($targetUser))->toBeFalse();
});

it('immediately updates UI from Follow to Requested on first click for private accounts', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['private_account' => true]);

    $this->actingAs($user);

    Livewire::test('posts.follow', ['user_id' => $targetUser->id])
        ->assertSee('Follow')
        ->call('toggle')
        ->assertSee('Requested')
        ->assertSee('text-gray-400')
        ->assertDontSee('Unfollow');

    expect($user->isPending($targetUser))->toBeTrue();
});

it('cancels pending follow request and immediately updates UI to Follow', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create(['private_account' => true]);
    $user->follow($targetUser);

    $this->actingAs($user);

    Livewire::test('posts.follow', ['user_id' => $targetUser->id])
        ->assertSee('Requested')
        ->call('toggle')
        ->assertSee('Follow')
        ->assertDontSee('Requested');

    expect($user->isPending($targetUser))->toBeFalse();
});

it('handles non-existent target user gracefully without throwing errors', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('posts.follow', ['user_id' => 99999])
        ->assertOk()
        ->assertSee('Follow')
        ->call('toggle')
        ->assertOk();
});
