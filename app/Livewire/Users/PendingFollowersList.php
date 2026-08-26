<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PendingFollowersList extends Component
{
    #[Computed]
    public function targetUser()
    {
        return Auth::user();
    }

    #[Computed]
    public function getUserByUserId($user_id)
    {
        return User::find($user_id);
    }

    #[Computed]
    public function pending_followers()
    {
        return $this->targetUser ? $this->targetUser->pending_followers()->get() : collect();
    }

    public function confirm($pending_follower_id)
    {
        $pending_follower = $this->getUserByUserId($pending_follower_id);
        $this->targetUser->confirm_following_request($pending_follower);
        $this->dispatch('update-pending-followers-count');
    }

    public function delete($pending_follower_id)
    {
        $pending_follower = $this->getUserByUserId($pending_follower_id);
        $this->targetUser->delete_following_request($pending_follower);
        $this->dispatch('update-pending-followers-count');
    }

    public function render()
    {
        return view('livewire.users.pending-followers-list');
    }
}
