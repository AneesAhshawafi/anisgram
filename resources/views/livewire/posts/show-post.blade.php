<div>
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
    <div class="text-sm h-screen md:flex md:flex-row">
        {{-- left side --}}
        <div class="h-full md:w-7/12 bg-black flex items-center">
            <img src="{{ asset('storage/' . $this->targetPost->image) }}" alt="{{ $this->targetPost->description }}"
                class="max-h-screen object-cover mx-auto">
        </div>
        {{-- right side --}}
        <div class="flex flex-col w-full bg-white dark:bg-gray-800 md:w-5/12">
            {{-- top --}}
            <div class="border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center p-5">
                    <img src="{{ $this->targetPost->user->image }}" alt="{{ $this->targetPost->user->username }}"
                        class="rtl:ml-5 ltr:mr-5 h-10 w-10 rounded-full">
                    <div class="grow">

                        <a href="/{{ $this->targetPost->user->username }}"
                            class="font-bold text-gray-900 dark:text-white ">{{ $this->targetPost->user->username }}</a>
                    </div>
                    @can('update', $this->targetPost)
                        <div class=" text-yellow-300" title="{{ __('edit your post') }}">
                            <button
                                onclick="Livewire.dispatch('openModal', { component: 'posts.edit-post-modal', arguments: { post_id: {{ $this->targetPost->id }} } })">
                                <span class="material-symbols-outlined text-yellow-400">
                                    edit_square
                                </span>
                            </button>
                        </div>
                        <form action="/p/{{ $this->targetPost->slug }}/delete" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('{{ __('Are you sure you want to delete this post?') }}')"><span
                                    class="material-symbols-outlined text-red-500 rtl:pr-2 ltr:pl-2">
                                    delete
                                </span></button>
                        </form>
                    @endcan
                    @cannot('update', $this->targetPost)
                        <livewire:posts.follow-button :user_id="$this->targetPost->user->id" />
                    @endcannot()
                </div>
            </div>{{-- top --}}
            {{-- middle --}}
            <div class="grow overflow-y-auto">
                <div class="flex items-start p-5">

                    {{-- <img src="{{ $this->targetPost->user->image }}" class="mr-5 h-10 w-10 rounded-full"> --}}
                    <div class="text-gray-900 dark:text-white text-sm">
                        {{-- <a href="/{{ $this->targetPost->user->username }}"
                            class="font-bold text-white">{{ $this->targetPost->user->username }}</a> --}}
                        {{ $this->targetPost->description }}
                    </div>
                </div>
                {{-- comments --}}
                <div class=" border-t border-gray-200 dark:border-gray-700">
                    {{-- <h2 class="text-white pl-3">Comments</h2> --}}

                    @foreach ($this->targetPost->comments as $comment)
                        <div class="flex items-start px-5 py-2">
                            <img src="{{ $comment->user->image }}" class="rtl:ml-5 ltr:mr-5 h-10 w-10 rounded-full">
                            <div class="flex flex-col">
                                <div class="text-gray-900 dark:text-white">
                                    <a href="/{{ $comment->user->username }}"
                                        class="font-bold text-gray-900 dark:text-white">{{ $comment->user->username }}</a>
                                    {{ $comment->body }}
                                </div>
                                <div class="mt-1 text-sm font-bold text-gray-500 dark:text-gray-400">
                                    {{ $comment->created_at->longAbsoluteDiffForHumans() }} {{ __('ago') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-3 flex flex-row space-x-2 border-t border-gray-200 dark:border-gray-700">
                    {{-- comments components  --}}
                    {{-- <livewire:posts.commnets /> --}}
                    {{-- like component --}}
                    <livewire:posts.like :post="$this->targetPost" />
                    <a onClick="document.getElementById('comment_body').focus()" class="grow">
                        <span
                            class="material-symbols-outlined text-gray-800 dark:text-white hover:text-gray-500 dark:hover:text-gray-300 cursor-pointer rtl:ml-3 ltr:mr-3">
                            comment
                        </span>
                    </a>
                </div>
                <livewire:posts.likedby :post="$this->targetPost" />
                <div class="border-t border-gray-200 dark:border-gray-700 p-5">
                    <form action="/p/{{ $this->targetPost->slug }}/comment" method="POST">
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

</div>
