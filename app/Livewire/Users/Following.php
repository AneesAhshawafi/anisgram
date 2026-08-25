<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Following extends Component
{
    public $user_id;

    #[On('unfollow')]
    public function refreshCount() {}

    #[Computed]
    public function targetUser()
    {
        return User::find($this->user_id);
    }

    #[Computed]
    public function count()
    {
        return $this->targetUser->following()->wherePivot('confirmed', true)->count();
    }

    public function render()
    {
        return view('livewire.users.following');
    }
}
