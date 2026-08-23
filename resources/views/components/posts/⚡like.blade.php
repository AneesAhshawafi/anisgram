    <?php
    
    use Livewire\Component;
    use Livewire\Attributes\Computed;
    use Livewire\Attributes\On;
    use App\Events\PostLiked;
    
    new class extends Component {
        public $post;
        // public function _construct(Post $post)
        // {
        //     $this->post = $post;
        // }
        public function toggle_like()
        {
            Auth::user()->likes()->toggle($this->post);
    
            // Broadcasts to other connected users over WebSockets
            broadcast(new PostLiked($this->post->id));
    
            // Updates the current user's interface immediately
            $this->dispatch('likeToggled');
        }
    };
    ?>

    <div class="text-white">
        <a wire:click="toggle_like" class="">
            <span
                class="material-symbols-outlined {{ $post->liked(Auth::user()) ? 'fill text-red-500' : '' }} hover:text-gray-400 cursor-pointer">
                favorite
            </span>
        </a>
    </div>
