<?php

namespace App\Livewire\Posts;

use App\Models\User;
use Livewire\Component;

class Search extends Component
{
    public $searchInput = '';

    public $results = [];

    public function clear()
    {
        $this->reset('results');
        $this->reset('searchInput');
    }

    public function goto($username)
    {
        return redirect()->route('user_profile', ['user' => $username]);
    }

    public function render()
    {

        if (! empty($this->searchInput)) {
            $this->results = User::where('username', 'Like', '%'.$this->searchInput.'%')->get(['id', 'name', 'username', 'image']);
        } else {
            $this->results = [];
        }

        return view('livewire.posts.search', [
            'results' => $this->results,
        ]);
    }
}
