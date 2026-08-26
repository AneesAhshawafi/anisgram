<?php

namespace App\Livewire\Users;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use LivewireUI\Modal\ModalComponent;

class PendingFollowersCount extends ModalComponent
{
    #[Computed]
    public function targetUser()
    {
        return Auth::user();
    }

    #[On('update-pending-followers-count')]
    public function refreshCount() {}

    #[Computed]
    public function count()
    {
        return $this->targetUser->pending_followers()->count();
    }

    public function render()
    {
        return view('livewire.users.pending-followers-count');
    }
}
