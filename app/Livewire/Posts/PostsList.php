<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class PostsList extends Component
{
    #[Computed]
    public function targetUser()
    {
        return Auth::user();
    }

    #[On('toggle_follow')]
    public function refreshPosts()
    {
        unset($this->following_ids, $this->posts, $this->targetUser);
    }

    #[Computed]
    public function following_ids()
    {
        if (! $this->targetUser) {
            return collect();
        }

        return $this->targetUser->following()
            ->wherePivot('confirmed', true)
            ->pluck('users.id');
    }

    #[Computed]
    public function posts()
    {
        $ids = $this->following_ids ? $this->following_ids->toArray() : [];

        if (empty($ids)) {
            return collect();
        }

        return Post::whereIn('user_id', $ids)
            ->inRandomOrder()
            ->get();
    }

    public function render()
    {
        return view('livewire.posts.posts-list');
    }
}
