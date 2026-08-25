<div class="max-h-96 flex flex-col">
    <div class="flex w-full items-center border-b border-b-neutral-700 p-2">
        <h1 class="text-lg font-bold text-center pb-2 grow">{{ __('Following') }}</h1>
        <button wire:click="$dispatch('closeModal')">
            <span class="material-symbols-outlined text-gray-600">close</span>
        </button>
    </div>
    <ul class="overflow-y-auto p-3">
        @forelse ($this->following as $following)
            <li class="mt-3">
                <div class="flex flex-row text-sm">
                    <div class="mr-5">

                        <a href="/{{ $following->username }}">
                            <img src="{{ $following->image }}" alt="{{ $following->username }}"
                                class="border border-gray-700 rounded-full  h-12 w-12 aspect-square object-cover">
                        </a>
                    </div>
                    <div class="flex flex-col grow">
                        <a href="/{{ $following->username }}" class="font-bold text-gray-700">{{ $following->username }}
                        </a>
                        <div class="text-gray-500 text-sm">{{ $following->name }}</div>
                    </div>
                    @auth
                        <div>
                            <button wire:click="unfollow({{ $following->id }})"
                                class="border border-gray-500 px-2 py-1 rounded">{{ __('Unfollow') }}</button>
                        </div>
                    @endauth
                </div>
            </li>
        @empty
            <li class="w-full p-3 text-center">
                {{ __('You are not following anyone.') }}
            </li>
        @endforelse
    </ul>
</div>
