<?php

namespace App\Livewire\Users;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class SuggestedUsers extends Component
{
    #[On('toggle_follow')]
    public function refreshSuggestedUsers()
    {
        unset($this->suggested_users, $this->targetUser);
    }

    #[Computed]
    public function targetUser()
    {
        return Auth::user();
    }

    #[Computed]
    public function suggested_users()
    {
        return $this->targetUser ? $this->targetUser->suggested_users() : collect();
    }

    public function render()
    {
        return view('livewire.users.suggested-users');
    }
}
