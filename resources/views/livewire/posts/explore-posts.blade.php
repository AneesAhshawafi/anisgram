<div>
    <div class="grid grid-cols-3 gap-1 md:gap-5 mt-8">
        @foreach ($this->posts as $post)
            <div wire:key="post-{{ $post->id }}">
                <a href="/p/{{ $post->slug }}">
                    <img src="{{ asset('storage/' . $post->image) }}" class="w-full aspect-square object-cover">
                </a>
            </div>
        @endforeach
    </div>

    {{-- Infinite scroll trigger --}}
    @if ($this->hasMore)
        <div wire:intersect="loadMore" class="w-full flex justify-center py-8">
            <div wire:loading class="text-sm text-gray-500 dark:text-gray-400">
                <svg class="animate-spin h-6 w-6 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </div>
        </div>
    @endif
</div>
