<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('has the correct fillable attributes', function () {
    $fillable = (new User())->getFillable();

    expect($fillable)
        ->toContain('name')
        ->toContain('username')
        ->toContain('image')
        ->toContain('email')
        ->toContain('password');
});

it('casts the password to a hashed value', function () {
    $user = User::factory()->create(['password' => 'plain-text-password']);

    expect($user->password)
        ->not->toBe('plain-text-password');

    expect(password_verify('plain-text-password', $user->password))->toBeTrue();
});

it('casts email_verified_at to a datetime instance', function () {
    $user = User::factory()->create();

    expect($user->email_verified_at)
        ->toBeInstanceOf(\Carbon\Carbon::class);
});

it('has a posts hasMany relationship', function () {
    $relation = (new User())->posts();

    expect($relation)->toBeInstanceOf(HasMany::class);
});

it('can have many posts', function () {
    $user = User::factory()->create();
    Post::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->posts)->toHaveCount(3);
});

it('posts count is zero for a new user with no posts', function () {
    $user = User::factory()->create();

    expect($user->posts)->toHaveCount(0);
});

it('unverified factory state sets email_verified_at to null', function () {
    $user = User::factory()->unverified()->create();

    expect($user->email_verified_at)->toBeNull();
});

it('verified factory state sets email_verified_at to a non-null value', function () {
    $user = User::factory()->create(); // default state is verified

    expect($user->email_verified_at)->not->toBeNull();
});
