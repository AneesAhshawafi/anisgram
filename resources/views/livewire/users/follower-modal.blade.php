<div class="max-h-96 flex flex-col bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg">
    <div class="flex w-full items-center border-b border-b-gray-200 dark:border-b-neutral-700 p-2">
        <h1 class="text-lg font-bold text-center pb-2 grow text-gray-900 dark:text-white">{{ __('Following') }}</h1>
        <button wire:click="$dispatch('closeModal')">
            <span
                class="material-symbols-outlined text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">close</span>
        </button>
    </div>
    <ul class="overflow-y-auto p-3">
        @forelse ($this->followers as $follower)
            <li class="mt-3">
                <div class="flex flex-row text-sm">
                    <div class="rtl:ml-5 ltr:mr-5">

                        <a href="/{{ $follower->username }}">
                            <img src="{{ $follower->image }}" alt="{{ $follower->username }}"
                                class="border border-gray-300 dark:border-gray-700 rounded-full h-12 w-12 aspect-square object-cover">
                        </a>
                    </div>
                    <div class="flex flex-col grow">
                        <a href="/{{ $follower->username }}"
                            class="font-bold text-gray-900 dark:text-white">{{ $follower->username }}
                        </a>
                        <div class="text-gray-500 dark:text-gray-400 text-sm">{{ $follower->name }}</div>
                    </div>
                </div>
            </li>
        @empty
            <li class="w-full p-3 text-center text-gray-600 dark:text-gray-400">
                {{ __('You are not following anyone.') }}
            </li>
        @endforelse
    </ul>
</div>
