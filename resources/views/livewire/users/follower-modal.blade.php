<div class="max-h-96 flex flex-col">
    <div class="flex w-full items-center border-b border-b-neutral-700 p-2">
        <h1 class="text-lg font-bold text-center pb-2 grow">{{ __('Following') }}</h1>
        <button wire:click="$dispatch('closeModal')">
            <span class="material-symbols-outlined text-gray-600">close</span>
        </button>
    </div>
    <ul class="overflow-y-auto p-3">
        @forelse ($this->followers as $follower)
            <li class="mt-3">
                <div class="flex flex-row text-sm">
                    <div class="mr-5">

                        <a href="/{{ $follower->username }}">
                            <img src="{{ $follower->image }}" alt="{{ $follower->username }}"
                                class="border border-gray-700 rounded-full  h-12 w-12 aspect-square object-cover">
                        </a>
                    </div>
                    <div class="flex flex-col grow">
                        <a href="/{{ $follower->username }}" class="font-bold text-gray-700">{{ $follower->username }}
                        </a>
                        <div class="text-gray-500 text-sm">{{ $follower->name }}</div>
                    </div>
                </div>
            </li>
        @empty
            <li class="w-full p-3 text-center">
                {{ __('You are not following anyone.') }}
            </li>
        @endforelse
    </ul>
</div>
