    <?php
    
    use Livewire\Component;
    use Livewire\Attributes\Computed;
    use Livewire\Attributes\On;
    use App\Events\PostLiked;
    
    new class extends Component {
        public $post;
        #[On('likeToggled')]
        #[On('echo:posts.{post.id},.PostLiked')]
        public function refreshLiked()
        {
            // $this->post->liked(Auth::user())->refresh(); // actually reload the data
        }
        public function toggle_like()
        {
            Auth::user()->likes()->toggle($this->post);
    
            // Broadcasts to other connected users over WebSockets
            broadcast(new PostLiked($this->post->id));
    
            // Updates the current user's interface immediately
            $this->dispatch('likeToggled');
        }
        #[Computed]
        public function liked()
        {
            return $this->post->liked(Auth::user());
        }
    };
    ?>

    <div class="text-white">
        <a wire:click="toggle_like">
            <span
                class="material-symbols-outlined {{ $this->liked ? 'fill text-red-500' : '' }} hover:text-gray-400 cursor-pointer">
                favorite
            </span>
        </a>
    </div>
