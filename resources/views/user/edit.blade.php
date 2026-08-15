<x-app-layout>
    <form action="/{{ $user->username }}/update" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="space-y-12">
            <div class="border-b border-white/10 pb-12">
                <h2 class="text-base/7 font-semibold text-white">{{ __('Profile') }}</h2>
                <p class="mt-1 text-sm/6 text-gray-400">
                    {{ __('This information will be displayed publicly so be careful what you share.') }}
                </p>

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                        <label for="username" class="block text-sm/6 font-medium text-white">{{ __('Username') }}</label>
                        <div class="mt-2">
                            <div class="flex items-center rounded    ">
                                {{-- <div class="shrink-0 select-none text-base text-gray-400 sm:text-sm/6">
                                </div> --}}
                                <input id="username" type="text" name="username" placeholder="janesmith"
                                    class="block min-w-0 grow rounded-md bg-transparent py-1.5 pl-1 pr-3 text-base text-white placeholder:text-gray-500 focus:outline focus:outline-0 sm:text-sm/6"
                                    value="{{ $user->username }}" />
                            </div>
                        </div>
                    </div>

                    <div class="col-span-full">
                        <label for="about" class="block text-sm/6 font-medium text-white">{{ __('Bio') }}</label>
                        <div class="mt-2">
                            <textarea id="bio" name="bio" rows="3"
                                class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6">{{ $user->bio }}</textarea>
                        </div>
                        <p class="mt-3 text-sm/6 text-gray-400">{{ __('Write a few sentences about yourself.') }}</p>
                    </div>

                    <div class="col-span-full">
                        <label for="photo" class="block text-sm/6 font-medium text-white">{{ __('Photo') }}</label>
                        <div class="mt-2 flex items-center gap-x-3">
                            <div class="px-4">
                                {{-- col-span-1 :means this div take one column from the grid columns --}}
                                {{-- order-1 : means this div will be the first column from the grid columns --}}
                                <img src="/{{ $user->image }}" alt="{{ $user->username }}'s profile picture"
                                    class="w-20 md:w-40 aspect-square object-cover rounded-full border border-neutral-300">

                            </div>
                            <input type="file"
                                class="w-full border border-gray-200 bg-gray-500 block focus:outline-none rounded-xl"
                                name="image" id="file_input" value="{{ $user->image }}">
                        </div>
                        <div class="flex text-white items-center gap-x-3 mt-3  ">
                            <label for="private_account">{{ __('Private Account') }}</label>
                            <input type="checkbox" id="private_account" name="private_account"
                                class="focus:ring-neutral-500 h-4 w-4 border-gray-300 rounded"
                                {{ $user->private_account ? 'checked' : '' }}>
                        </div>
                    </div>
                    {{-- cover-photo --}}
                    {{-- <div class="col-span-full">
                        <label for="cover-photo" class="block text-sm/6 font-medium text-white">Cover photo</label>
                        <div
                            class="mt-2 flex justify-center rounded-lg border border-dashed border-white/25 px-6 py-10">
                            <div class="text-center">
                                <svg viewBox="0 0 24 24" fill="currentColor" data-slot="icon" aria-hidden="true"
                                    class="mx-auto size-12 text-gray-600">
                                    <path
                                        d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z"
                                        clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                                <div class="mt-4 flex text-sm/6 text-gray-400">
                                    <label for="file-upload"
                                        class="relative cursor-pointer rounded-md bg-transparent font-semibold text-indigo-400 focus-within:outline focus-within:outline-2 focus-within:outline-offset-2 focus-within:outline-indigo-500 hover:text-indigo-300">
                                        <span>Upload a file</span>
                                        <input id="file-upload" type="file" name="file-upload" class="sr-only" />
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs/5 text-gray-400">PNG, JPG, GIF up to 10MB</p>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>

            <div class="border-b border-white/10 pb-12">
                <h2 class="text-base/7 font-semibold text-white">{{ __('Personal Information') }}</h2>
                <p class="mt-1 text-sm/6 text-gray-400">Use a permanent address where you can receive mail.</p>

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="name"
                            class="block text-sm/6 font-medium text-white">{{ __('Name') }}</label>
                        <div class="mt-2">
                            <input id="name" type="text" name="name" value="{{ $user->name }}"
                                autocomplete="given-name"
                                class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                        </div>
                    </div>
                    <div class="sm:col-span-4">
                        <label for="email" class="block text-sm/6 font-medium text-white">Email address</label>
                        <div class="mt-2">
                            <input id="email" type="email" name="email" value="{{ $user->email }}"
                                autocomplete="email"
                                class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                        </div>
                    </div>
                    <div class="sm:col-span-2 sm:col-start-1">
                        <label for="password"
                            class="block text-sm/6 font-medium text-white">{{ __('Password') }}</label>
                        {{-- <div class="mt-2">
                            <input id="password" type="password" name="password" autocomplete="address-level2"
                                class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                        </div> --}}
                        <div class="mt-2 relative">
                            <input id="password" type="password" name="password" autocomplete="new-password"
                                class="block w-full rounded-md bg-white/5 px-3 py-1.5 pr-10 text-base text-white outline outline-1 -outline-offset-1 outline-white/10 focus:outline focus:outline-2 focus:outline-indigo-500 sm:text-sm/6" />

                            <button type="button"
                                onclick="const input = document.getElementById('password'); const isPass = input.type === 'password'; input.type = isPass ? 'text' : 'password'; this.querySelector('span').innerText = isPass ? 'visibility_off' : 'visibility';"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white">
                                <span class="material-symbols-outlined text-xl">visibility</span>
                            </button>
                        </div>

                    </div>

                    <div class="sm:col-span-2">
                        <label for="password_confirmation"
                            class="block text-sm/6 font-medium text-white">{{ __('Confirm Password') }}</label>
                        {{-- <div class="mt-2">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                autocomplete="address-level1"
                                class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                        </div> --}}
                        <div class="mt-2 relative">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                autocomplete="new-password"
                                class="block w-full rounded-md bg-white/5 px-3 py-1.5 pr-10 text-base text-white outline outline-1 -outline-offset-1 outline-white/10 focus:outline focus:outline-2 focus:outline-indigo-500 sm:text-sm/6" />

                            <button type="button"
                                onclick="const input = document.getElementById('password_confirmation'); const isPass = input.type === 'password'; input.type = isPass ? 'text' : 'password'; this.querySelector('span').innerText = isPass ? 'visibility_off' : 'visibility';"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white">
                                <span class="material-symbols-outlined text-xl">visibility</span>
                            </button>
                        </div>

                    </div>


                </div>
            </div>

        </div>

        <div class="py-6 flex items-center justify-end gap-x-6">
            <x-button>{{ __('Save') }}
            </x-button>
            {{-- <button type="button" class="text-sm/6 font-semibold text-white">Cancel</button> --}}
            {{-- <button type="submit"
                class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Save</button> --}}
        </div>
    </form>
</x-app-layout>
