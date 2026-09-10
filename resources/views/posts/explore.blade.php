{{-- <x-app-layout>
    <div class="py-8">

        <div class="grid grid-cols-3 gap-1 md:gap-5 my-8">
            @foreach ($posts as $post)
                <div>
                    <a href="/p/{{ $post->slug }}">
                        <img src="{{ asset('storage/' . $post->image) }}" class="w-full aspect-square object-cover">
                    </a>
                </div>
            @endforeach
        </div>
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout> --}}
<x-app-layout>
    <livewire:posts.explore-posts />
</x-app-layout>
