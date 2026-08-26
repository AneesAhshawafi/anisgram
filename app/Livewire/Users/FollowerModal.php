<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\Computed;
use LivewireUI\Modal\ModalComponent;

class FollowerModal extends ModalComponent
{
    public $user_id;

    #[Computed]
    public function targetUser()
    {
        return User::find($this->user_id);
    }

    #[Computed]
    public function getUserByUserId($user_id)
    {
        return User::find($user_id);
    }

    #[Computed]
    public function followers()
    {
        return $this->targetUser->followers()->wherePivot('confirmed', true)->get();
    }

    public function render()
    {
        return view('livewire.users.follower-modal');
    }
}
