<div>
    <div class="grid grid-cols-3 gap-1 md:gap-5 mt-8">
        @foreach ($posts as $post)
            <div wire:key="explore-post-{{ $post->id }}">
                <a href="/p/{{ $post->slug }}">
                    <img src="{{ asset('storage/' . $post->image) }}"
                        class="w-full aspect-square object-cover hover:opacity-90 transition rounded-sm">
                </a>
            </div>
        @endforeach
    </div>

    @if ($posts->isEmpty())
        <div class="w-full text-center mt-20 text-gray-600 dark:text-gray-400 font-medium">
            <p>{{ __('No posts to explore yet.') }}</p>
        </div>
    @endif

    @if ($hasMore)
        <div wire:intersect="loadMore" class="py-10 flex justify-center items-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900 dark:border-white"></div>
        </div>
    @endif
</div>
