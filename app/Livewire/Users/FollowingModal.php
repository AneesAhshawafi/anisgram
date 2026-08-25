<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\Computed;
use LivewireUI\Modal\ModalComponent;

class FollowingModal extends ModalComponent
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
    public function following()
    {
        return $this->targetUser->following()->wherePivot('confirmed', true)->get();
    }

    public function unfollow($user_id)
    {
        $user = $this->getUserByUserId($user_id);
        $this->targetUser->unfollow($user);
        $this->dispatch('unfollow');
    }

    public function render()
    {
        return view('livewire.users.following-modal');
    }
}
