<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ExplorePosts extends Component
{
    public int $perPage = 12;

    #[Computed]
    public function posts()
    {
        return Post::whereRelation('user', 'private_account', '=', 0)
            ->whereNot('user_id', auth()->id())
            ->latest()
            ->take($this->perPage)
            ->get();
    }

    #[Computed]
    public function hasMore(): bool
    {
        $total = Post::whereRelation('user', 'private_account', '=', 0)
            ->whereNot('user_id', auth()->id())
            ->count();

        return $this->posts->count() < $total;
    }

    public function loadMore(): void
    {
        $this->perPage += 12;
    }

    public function render()
    {
        return view('livewire.posts.explore-posts');
    }
}
