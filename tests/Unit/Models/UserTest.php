<?php

use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Tests that the User model defines all required mass-assignable attributes ($fillable array).
 */
it('has the correct fillable attributes', function () {
    $fillable = (new User)->getFillable();

    expect($fillable)
        ->toContain('name')
        ->toContain('username')
        ->toContain('image')
        ->toContain('email')
        ->toContain('password');
});

/**
 * Tests that the User model's 'password' attribute is automatically hashed upon saving.
 */
it('casts the password to a hashed value', function () {
    $user = User::factory()->create(['password' => 'plain-text-password']);

    // Ensure raw password is not stored unencrypted
    expect($user->password)->not->toBe('plain-text-password');

    // Verify password hash matches the given raw password
    expect(password_verify('plain-text-password', $user->password))->toBeTrue();
});

/**
 * Tests that 'email_verified_at' attribute is properly cast to a Carbon DateTime object.
 */
it('casts email_verified_at to a datetime instance', function () {
    $user = User::factory()->create();

    expect($user->email_verified_at)->toBeInstanceOf(Carbon::class);
});

/**
 * Tests that the User model defines a HasMany relationship method named 'posts'.
 */
it('has a posts hasMany relationship', function () {
    $relation = (new User)->posts();

    expect($relation)->toBeInstanceOf(HasMany::class);
});

/**
 * Tests that a User instance can retrieve multiple associated Post models through the posts relationship.
 */
it('can have many posts', function () {
    $user = User::factory()->create();
    Post::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->posts)->toHaveCount(3);
});

/**
 * Tests that a newly created User without posts has an empty posts collection.
 */
it('posts count is zero for a new user with no posts', function () {
    $user = User::factory()->create();

    expect($user->posts)->toHaveCount(0);
});

/**
 * Tests that using the unverified() factory state sets 'email_verified_at' to null.
 */
it('unverified factory state sets email_verified_at to null', function () {
    $user = User::factory()->unverified()->create();

    expect($user->email_verified_at)->toBeNull();
});

/**
 * Tests that creating a default User factory sets 'email_verified_at' to a valid date.
 */
it('verified factory state sets email_verified_at to a non-null value', function () {
    $user = User::factory()->create();

    expect($user->email_verified_at)->not->toBeNull();
});
