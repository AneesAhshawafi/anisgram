<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Explore extends Component
{
    public int $amount = 12;

    public function loadMore(): void
    {
        $this->amount += 12;
    }

    public function render()
    {
        $query = Post::whereRelation('user', 'private_account', false)
            ->when(Auth::check(), fn ($q) => $q->whereNot('user_id', Auth::id()))
            ->latest();

        $total = $query->count();
        $posts = $query->take($this->amount)->get();

        return view('livewire.posts.explore', [
            'posts' => $posts,
            'hasMore' => $this->amount < $total,
        ]);
    }
}
