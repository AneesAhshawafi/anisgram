<?php

namespace App\Livewire\Posts;

use LivewireUI\Modal\ModalComponent;

class FiltersModal extends ModalComponent
{
    public $image;

    public $filters = ['Original', 'Clarendon', 'Gingham', 'Moon', 'Perpetua'];

    public function mount($image)
    {
        $this->image = $image;
    }

    public function render()
    {
        return view('livewire.posts.filters-modal');
    }
}
