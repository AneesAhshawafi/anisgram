<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Livewire\Attributes\Computed;
use LivewireUI\Modal\ModalComponent;

class EditPostModal extends ModalComponent
{
    public $post_id;

    public $description;

    public function mount()
    {
        $this->setDescription();
    }

    #[Computed]
    public function targetPost()
    {
        return Post::find($this->post_id);
    }

    public function setDescription()
    {
        $post = $this->targetPost;
        $this->description = $post->description;
    }

    public static function modalmaxWidth(): string
    {
        return '5xl';
    }

    public function update()
    {
        $this->validate([
            'description' => 'required',
        ]);
        $this->targetPost->update([
            'description' => $this->description,
        ]);
        $this->forceClose()->closeModal();
        $this->dispatch('post_updated');
    }

    public function render()
    {
        return view('livewire.posts.edit-post-modal');
    }
}
