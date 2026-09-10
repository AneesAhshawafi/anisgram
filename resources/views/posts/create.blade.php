<x-app-layout>
    <div class="card p-10">
        {{-- title --}}
        <h1 class="text-3xl mb-10 text-gray-900 dark:text-white">{{ __('Create a new post') }}</h1>
        {{-- errors --}}
        <div class="flex flex-col justify-center items-center w-full ">
            @if ($errors->any())
                <div class="w-full bg-red-700 text-white p-5 mb-5 rounded-xl">
                    <ul class="list-disc rtl:pr-4 ltr:pl-4">
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
            <x-create-edit-form />
            <x-primary-button class="mt-4 ">
                {{ __('Create Post') }}
            </x-primary-button>

        </form>

    </div>
</x-app-layout>
