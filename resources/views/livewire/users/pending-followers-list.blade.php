<div
    class="max-h-96 w-80 flex flex-col bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg">

    <ul class="overflow-y-auto p-2">
        @forelse ($this->pending_followers as $pending_follower)
            <li class="mt-1">
                <div class="flex flex-row items-center text-sm">
                    <div class="rtl:ml-5 ltr:mr-5">

                        <a href="/{{ $pending_follower->username }}">
                            <img src="{{ $pending_follower->image }}" alt="{{ $pending_follower->username }}"
                                class="border border-gray-300 dark:border-gray-700 rounded-full h-8 w-8 aspect-square object-cover">
                        </a>
                    </div>
                    <div class="flex flex-col grow">
                        <a href="/{{ $pending_follower->username }}"
                            class="font-bold text-gray-900 dark:text-white">{{ $pending_follower->username }}
                        </a>
                        <div class="text-gray-500 dark:text-gray-400 text-sm">{{ $pending_follower->name }}</div>
                    </div>
                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                        <button wire:click="confirm({{ $pending_follower->id }})"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-2.5 py-1.5 rounded-md text-xs transition">
                            {{ __('Confirm') }}
                        </button>
                        <button wire:click="delete({{ $pending_follower->id }})"
                            class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold px-3 py-1.5 rounded-md text-xs transition">
                            {{ __('Delete') }}
                        </button>
                    </div>
                </div>
            </li>
        @empty
            <li class="w-full p-3 text-center text-gray-600 dark:text-gray-400">
                {{ __('You do not have any following requests.') }}
            </li>
        @endforelse
    </ul>
</div>
