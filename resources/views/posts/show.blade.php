<x-app-layout>
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
    <div class="text-sm h-screen md:flex md:flex-row">
        {{-- left side --}}
        <div class="h-full md:w-7/12 bg-black flex items-center">
            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->description }}"
                class="max-h-screen object-cover mx-auto">
        </div>
        {{-- right side --}}
        <div class="flex flex-col w-full bg-gray-800 md:w-5/12">
            {{-- top --}}
            <div class="border-b-2">
                <div class="flex items-center p-5">
                    <img src="{{ $post->user->image }}" alt="{{ $post->user->username }}"
                        class="mr-5 h-10 w-10 rounded-full">
                    <div class="grow">

                        <a href="/{{ $post->user->username }}"
                            class="font-bold text-white">{{ $post->user->username }}</a>
                    </div>
                    @if ($post->user->id == auth()->id())
                        <div class=" text-yellow-300" title="{{ __('edit your post') }}">
                            <a href="/p/{{ $post->slug }}/edit">

                                <span class="material-symbols-outlined">
                                    edit_square
                                </span>
                            </a>
                        </div>
                        <form action="/p/{{ $post->slug }}/delete" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Are you sure you want to delete this post?')"><span
                                    class="material-symbols-outlined text-red-500 pl-2">
                                    delete
                                </span></button>
                        </form>
                    @endif
                </div>
            </div>{{-- top --}}
            {{-- middle --}}
            <div class="grow overflow-y-auto">
                <div class="flex items-start p-5">

                    {{-- <img src="{{ $post->user->image }}" class="mr-5 h-10 w-10 rounded-full"> --}}
                    <div class="text-white text-sm">
                        {{-- <a href="/{{ $post->user->username }}"
                            class="font-bold text-white">{{ $post->user->username }}</a> --}}
                        {{ $post->description }}
                    </div>
                </div>
                {{-- comments --}}
                <h2 class="text-white pl-3">Comments</h2>
                <div class=" border-t-2">

                    @foreach ($post->comments as $comment)
                        <div class="flex items-start px-5 py-2">
                            <img src="{{ $comment->user->image }}" class="mr-5 h-10 w-10 rounded-full">
                            <div class="flex flex-col">
                                <div class="text-white">
                                    <a href="/{{ $comment->user->username }}"
                                        class="font-bold text-white">{{ $comment->user->username }}</a>
                                    {{ $comment->body }}
                                </div>
                                <div class="mt-1 text-sm font-bold text-gray-200">
                                    {{ $comment->created_at->longAbsoluteDiffForHumans() }} ago
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="border-t-2 p-5">
                    <form action="/p/{{ $post->slug }}/comment" method="POST">
                        @csrf
                        <div class="flex flex-row">
                            <textarea name="body" id="comment_body" rows="1" placeholder="{{ __('Add a comment ...') }}"
                                class="comment-feild"></textarea>
                            <button type="submit" class="post-comment-btn"
                                title="{{ __('Post your comment') }}">{{ __('Post') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
