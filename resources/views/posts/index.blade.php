<x-app-layout>

    <div class="flex flex-row max-w-full gap-8 mx-auto">
        {{-- left side --}}
        <livewire:posts.posts-list />
        {{-- Right side --}}
        <div class="hidden w-[30rem] lg:flex lg:flex-col  pt-4">
            <div class="flex flex-row text-sm">
                <div class="mr-5">
                    <a href="/{{ auth()->user()->username }}">
                        <img src="{{ auth()->user()->image }}" alt="{{ auth()->user()->username }}"
                            class="border border-gray-300 rounded-full aspect-square h-12 w-12">
                    </a>
                </div>
                <div class="flex flex-col">
                    <a href="{{ auth()->user()->username }}"
                        class="font-bold text-white">{{ auth()->user()->username }}</a>

                    <div class="text-gray-400 text-sm">{{ auth()->user()->name }}</div>
                </div>
            </div>
            <livewire:users.suggested_users />

        </div>
        {{-- <div>

        <livewire:counter />
        @livewire('counter')
    </div> --}}
</x-app-layout>
