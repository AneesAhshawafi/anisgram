<div class="h-[45rem] w-full lg:flex lg:flex-row overflow-x-auto">
    {{-- Left Side --}}
    <div class="flex h-1/2 lg:h-full items-center justify-center overflow-hidden bg-black lg:w-8/12">
        <img class="h-full w-auto object-cover" src="{{ $filtered_image }}">
    </div>

    {{-- Right Side --}}
    <div class="lg:w-4/12 flex flex-col bg-white dark:bg-gray-800 text-gray-900 dark:text-white p-5">
        <h1 class="text-2xl font-bold text-center mb-10 text-gray-900 dark:text-white">{{ __('Filters') }}</h1>

        <div class="grid grid-cols-3 gap-4 items-start">
            @foreach ($filters as $filter)
                <div class="flex flex-col">
                    <img src="/storage/filters_thumb/{{ $filter }}.jpg" alt="{{ $filter }}"
                        class="mb-3 cursor-pointer hover:ring-1 hover:ring-gray-500 rounded"
                        wire:click="{{ strtolower($filter) }}_filter">
                    <span
                        class="text-[15px] text-center text-gray-700 dark:text-gray-300 font-medium">{{ $filter }}</span>
                </div>
            @endforeach
        </div>


        <div class="mt-3">
            <textarea name="description" id="description" cols="30" rows="10"
                placeholder="{{ __('Write description ... ') }}"
                class="border-none w-full bg-transparent text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-0"
                wire:model="description"></textarea>
            @error('description')
                <span class="text-sm text-red-500 py-5">{{ $message }}</span>
            @enderror
            <x-button class="w-full justify-center" wire:click="publish">{{ __('Publish') }}</x-button>
        </div>
    </div>
</div>
