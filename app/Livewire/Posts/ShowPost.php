<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ShowPost extends Component
{
    public $post_id;

    #[On('post_updated')]
    #[Computed]
    public function targetPost()
    {
        return Post::find($this->post_id);
    }

    public function render()
    {
        return view('livewire.posts.show-post');
    }
}
