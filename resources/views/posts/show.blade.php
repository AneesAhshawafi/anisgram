<x-app-layout>
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
                    <a href="/{{ $post->user->username }}" class="font-bold text-white">{{ $post->user->username }}</a>
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
                <h2 class="text-white">Comments</h2>
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
                            <textarea name="body" id="comment_body" rows="1" placeholder="Add a comment ..."
                                class="text-white p-2 grow resize-none overflow-hidden border-none rounded-md  placeholder-gray-400  outline-0 focus:right-0 bg-gray-800"></textarea>
                            <button type="submit" class="ml-5 border-none  text-blue-500">Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
