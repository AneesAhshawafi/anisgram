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
        // $path = $request->file('image')->store('users', 'public');
        // $data['image'] = '/storage/' . $path;
        // $path = $this->image->store('temp', 'public');
        // $image = '/storage/' . $path;
        // $this->dispatch('openModal', 'posts.filters-modal', ['image' => $image]);
        $path = $this->image->store('temp', 'public'); // e.g. 'temp/abc.jpg'
        $this->dispatch('openModal', 'posts.filters-modal', ['image' => $path]);
    }

    public function render()
    {
        return view('livewire.posts.create-post-modal');
    }
}
