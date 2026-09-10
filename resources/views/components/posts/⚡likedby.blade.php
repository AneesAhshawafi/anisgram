<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

new class extends Component {
    public $post; // note the dot before PostLiked
    // Listen to the event to trigger a component refresh
    #[On('likeToggled')]
    #[On('echo:posts.{post.id},.PostLiked')]
    public function refreshLikes()
    {
        $this->post->refresh(); // actually reload the data
    }

    #[Computed]
    public function likes()
    {
        return $this->post->likes()->count();
    }

    #[Computed]
    public function firstUsername()
    {
        return $this->post->likes()->first()->username;
    }
};
?>

<div class="px-5 mb-4 text-gray-700 dark:text-gray-300">
    @if ($this->likes > 0)
        {{ __('Liked By ') }}
        <strong class="text-gray-900 dark:text-white">
            <a href="/{{ $this->firstUsername }}">{{ $this->firstUsername }}</a>
        </strong>
    @endif

    @if ($this->likes > 1)
        {{ __('and ') }} <strong class="text-gray-900 dark:text-white">{{ __('others') }}</strong>
    @endif
</div>
