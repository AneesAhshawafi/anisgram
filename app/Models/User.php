<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
// use App\Models\User;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

// #[Fillable(['name', 'bio', 'private_account', 'image', 'email', 'password'])]
// #[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'bio',
        'private_account',
        'image',
        'email',
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the comments for the blog post.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function suggested_users()
    {
        $follwing = Auth::user()->following()->wherePivot('confirmed', true)->get();

        return User::all()->diff($follwing)->except(Auth::id())->shuffle()->take(5);
    }

    /**
     * The posts that belong to (are liked) by the user.
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'likes');
    }

    /**
     * The users that belong to (are followed) by this user.
     */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id', 'following_user_id')->withTimestamps()->withPivot('confirmed'); // this wil return the users that their ids are matching the follwing_user_id in the follows table (as we say they are the users this user is following)
    }

    /**
     * The users that belong to ( following)  this user.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_user_id', 'user_id')->withTimestamps()->withPivot('confirmed'); // this will return the users the thier ids matching with the user_id column in the follows table (who are considered as the followers of this user)
    }

    public function pending_followers()
    {
        return $this->followers()->wherePivot('confirmed', false);
    }

    public function delete_following_request(User $pending_follower)
    {
        return $this->followers()->detach($pending_follower);
    }

    public function confirm_following_request(User $pending_follower)
    {
        return $this->followers()->updateExistingPivot($pending_follower, ['confirmed' => true]);
    }

    public function toggle_follow(User $user)
    {
        $this->following()->toggle($user);
        if (! $user->private_account) {

            $this->following()->updateExistingPivot($user, ['confirmed' => true]);
        }
    }

    public function follow(User $user)
    {
        if ($user->private_account) {

            return $this->following()->attach($user);
        }

        return $this->following()->attach($user, ['confirmed' => true]);
    }

    public function unfollow(User $user)
    {
        return $this->following()->detach($user, ['confirmed' => false]);
    }

    public function isFollowing(User $user)
    {
        return $this->following()->where('following_user_id', $user->id)->where('confirmed', true)->exists();
    }

    public function isFollower(User $user)
    {
        return $this->followers()->where('user_id', $user->id)->where('confirmed', true)->exists();
    }

    public function isPending(User $user)
    {
        return $this->following()->where('following_user_id', $user->id)->where('confirmed', false)->exists();
    }
}
