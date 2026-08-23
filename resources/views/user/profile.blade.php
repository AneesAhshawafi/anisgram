<x-app-layout>
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="w-50 p-4 text-sm text-green-700 bg-green-100 rounded-lg absolute right-10 shadow shadow-neutral-200"
            role="alert">
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-4 mt-5 text-white">
        {{-- User Image --}}

        <div class="px-4 col-span-1 order-1">{{-- col-span-1 :means this div take one column from the grid columns --}}
            {{-- order-1 : means this div will be the first column from the grid columns --}}
            <img src="{{ $user->image }}" alt="{{ $user->username }}' profile picture"
                class="rounded-full w-20 md:w-40 border border-neutral-300  aspect-square object-cover ">
        </div>
        {{-- username and buttons --}}
        <div class="px-4 col-span-3 md-ml-0 flex flex-col order-3 md:col-span-3">
            {{-- user name --}}
            <div class="text-3xl mb-3">{{ $user->username }}</div>
            {{--  name --}}
            <p class="font-bold">{{ $user->name }}</p>
            {{-- User statistics  --}}
            <div class="col-span-3   text-md border-y border-y-neutral-200 order-2 md:order-3 md:border-none">
                <ul class="text-md flex flex-row justify-around md:justify-start md:space-x-4 ">
                    <li class="flex flex-col md:flex-row text-center items-center">
                        <div class="md:mr-1 font-bold md:font-normal">
                            {{ $user->posts->count() }}
                        </div>
                        <span class="text-neutral-500 ">{{ __('posts') }}</span>
                    </li>
                    <li class="flex flex-col md:flex-row text-center items-center">
                        <div class="md:mr-1 font-bold md:font-normal p-2">
                            {{ $user->followers->count() }}
                        </div>
                        <span class="text-neutral-500 ">{{ __('followers') }}</span>
                    </li>
                    <li class="flex flex-col md:flex-row text-center items-center">
                        <div class="md:mr-1 font-bold md:font-normal p-2">
                            {{ $user->following->count() }}
                        </div>
                        <span class="text-neutral-500 ">{{ __('following') }}</span>
                    </li>
                </ul>
            </div>
            {{-- User Info --}}
            <div class="text-md mt-8 col-span-1 col-start-1 order-2 md:col-start-2 md:order-4 md:mt-0">
                {{-- <p class="font-bold">{{ $user->name }}</p> --}}
                {{-- e(str): escapes HTML special characters to prevent XSS attacks --}}
                {{-- nl2br(str): converts newline characters (\n) into HTML <br> tags --}}
                {!! nl2br(e($user->bio)) !!}
            </div>
        </div>
        <div class="text-md h- col-span-3 col-start-1 order-2 md:col-start-2 md:order-4 md:my-10">
            @auth
                @if ($user->id === auth()->id())
                    <a href="/{{ $user->username }}/edit"
                        class="w-44 border text-lg py-3 px-10 font-bold py-1 rounded-md border-neutral-300 text-center">
                        {{ __('Edit profile') }}
                    </a>
                @else
                    @if (auth()->user()->isPending($user))
                        <a href="/{{ $user->username }}/unfollow"
                            class="w-44 border text-lg py-3 px-10 font-bold py-1 rounded-md border-neutral-300 text-center">{{ __('Requested') }}</a>
                    @elseif (auth()->user()->isFollowing($user))
                        <a href="/{{ $user->username }}/unfollow"
                            class="w-44 border text-lg py-3 px-10 font-bold py-1 rounded-md border-neutral-300 text-center">{{ __('Unfollow') }}</a>
                    @else
                        <a href="/{{ $user->username }}/follow"
                            class="w-full block bg-blue-500 border text-lg py-3 px-10 font-bold py-1 rounded-md border-neutral-300 text-center">{{ __('Follow') }}</a>
                    @endif
                @endif
            @endauth
            @guest
                <a href="/{{ $user->username }}/follow"
                    class="w-full block bg-blue-500 border text-lg py-3 px-10 font-bold py-1 rounded-md border-neutral-300 text-center">{{ __('Follow') }}</a>
            @endguest
        </div>



    </div>
    {{-- Bottom --}}
    <div class=" border-t-[1px] border-gray-400 pt-2 mt-10">

        @if (
            $user->posts->count() > 0 and
                ($user->private_account == false or auth()->id() == $user->id or $user->isFollower(auth()->user())))
            <div class="grid grid-cols-3 gap-1 md:gap-5  ">
                @foreach ($user->posts as $post)
                    <div>
                        <a href="/p/{{ $post->slug }}">
                            <img src="{{ asset('storage/' . $post->image) }}"
                                class="w-full aspect-square object-cover">
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="w-full text-center mt-20 text-white">
                @if ($user->private_account == true and auth()->id() != $user->id)
                    {{ __('This account is private. Follow them to see thier posts.') }}
                @else
                    {{ __('This user dose not have any posts yet.') }}
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
