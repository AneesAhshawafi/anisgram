<?php

namespace App\Livewire\Posts;

use Livewire\WithFileUploads;
use LivewireUI\Modal\ModalComponent;

class CreatePostModal extends ModalComponent
{
    use WithFileUploads;

    public $image;

    public function save_temp()
    {
        $image = $this->image->store('temp', 'public');
        $this->dispatch('openModal', 'posts.filters-modal', ['image' => $image]);
    }

    public function render()
    {
        return view('livewire.posts.create-post-modal');
    }
}
