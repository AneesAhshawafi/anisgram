<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public int $user_id;
    public $classes = '';

    #[Computed]
    public function targetUser()
    {
        return User::find($this->user_id);
    }

    #[Computed]
    public function isPending(): bool
    {
        return $this->targetUser ? Auth::user()->isPending($this->targetUser) : false;
    }

    #[Computed]
    public function isFollowing(): bool
    {
        return $this->targetUser ? Auth::user()->isFollowing($this->targetUser) : false;
    }

    #[Computed]
    public function followState(): string
    {
        if ($this->isPending) {
            return 'Requested';
        }

        if ($this->isFollowing) {
            return 'Unfollow';
        }

        return 'Follow';
    }

    #[Computed]
    public function buttonClasses(): string
    {
        if (empty($this->classes)) {
            return '';
        }

        return $this->isPending || $this->isFollowing ? 'unfollow-btn-profile' : 'follow-btn-profile';
    }

    public function toggle()
    {
        if (!$this->targetUser) {
            return;
        }

        if ($this->isPending) {
            Auth::user()->unfollow($this->targetUser);
        } else {
            Auth::user()->toggle_follow($this->targetUser);
        }

        // Invalidate computed property cache so the view re-evaluates against the updated DB:
        unset($this->isPending, $this->isFollowing, $this->followState, $this->buttonClasses);
    }
};
?>

<div>
    <a wire:click="toggle"
        class="pl-5 {{ $this->isPending ? 'text-gray-400' : 'text-white' }} hover:text-gray-600 cursor-pointer {{ $this->buttonClasses }}">
        {{ __($this->followState) }}
    </a>
</div>
