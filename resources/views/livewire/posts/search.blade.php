<div class="relative flex items-center">
    <span
        class="material-symbols-outlined absolute rtl:right-3 ltr:left-3 text-gray-500 dark:text-gray-400 pointer-events-none text-xl">
        search
    </span>

    <input type="text" name="search" wire:model.live="searchInput"
        class="text-gray-900 dark:text-white w-56 md:w-64 lg:w-96 border border-gray-300 dark:border-none bg-gray-100 dark:bg-gray-600 rounded-xl h-10 rtl:pr-10 rtl:pl-9 ltr:pl-10 ltr:pr-9 focus:ring-2 focus:ring-gray-500 text-sm placeholder-gray-500 dark:placeholder-gray-400"
        placeholder="{{ __('Search...') }}" autocomplete="off" />

    @if (!empty($searchInput))
        <button type="button"
            class="absolute rtl:left-3 ltr:right-3 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white flex items-center justify-center"
            wire:click="clear">
            <span class="material-symbols-outlined text-lg">
                close
            </span>
        </button>
    @endif

    @if (!empty($results) && !empty($searchInput))
        <ul
            class="absolute w-56 md:w-64 lg:w-96 bg-white dark:bg-gray-800 p-2 border border-neutral-200 dark:border-neutral-700 z-50 rounded-lg shadow-xl top-12 rtl:right-0 ltr:left-0 text-gray-800 dark:text-gray-200">
            @forelse ($results as $result)
                <li class="flex flex-row w-full p-3 items-center text-sm hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer rounded-md"
                    wire:key="user-{{ $result->id }}" wire:click="goto('{{ $result->username }}')">
                    <div>
                        <img src="{{ $result->image }}"
                            class="w-10 h-10 rtl:ml-2 ltr:mr-2 rounded-full border border-neutral-200 dark:border-neutral-600 object-cover" />
                    </div>
                    <div class="flex flex-col grow">
                        <div class="font-bold text-gray-900 dark:text-white">
                            <a href="/{{ $result->username }}">{{ $result->username }}</a>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $result->name }}
                        </div>
                    </div>
                </li>
            @empty
                <li class="w-full p-3 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('No users found.') }}
                </li>
            @endforelse
        </ul>
    @endif
</div>
