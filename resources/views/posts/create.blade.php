<x-app-layout>
    <div class="card p-10">
        {{-- title --}}
        <h1 class="text-3xl mb-10 text-white">{{ __('Create a new post') }}</h1>
        {{-- errors --}}
        <div class="flex flex-col justify-center items-center w-full ">
            @if ($errors->any())
                <div class="w-full bg-red-700 p-5 mb-5 rounded-xl">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- form --}}
        <form action="/p/create" method="post" class="w-full" enctype="multipart/form-data">
            @csrf
            <input type="file" class="w-full border border-gray-200 bg-gray-500 block focus:outline-none rounded-xl"
                name="image" id="file_input">
            <p class="mt-2 text-sm text-gray-500 dark:test-gray-300" id="file_input_help">PNG, JPG, or GIF</p>
            <textarea name="description" rows="5" id="" cols="30" rows="10"
                class="mt-2 w-full text-white border border-gray-200 bg-gray-900 rounded-xl"
                placeholder="{{ __('Write a decription...') }}"></textarea>
            <x-primary-button class="mt-4 ">
                {{ __('Create Post') }}
            </x-primary-button>

        </form>

    </div>
</x-app-layout>
