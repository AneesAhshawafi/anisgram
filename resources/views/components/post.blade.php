<div class="card">
    <div class="card-header">
        <img src="{{ $post->user->image }}" class="w-9 h-9 mr-3 rounded-full" />
        <a href="/{{ $post->user->username }}" class="font-bold">{{ $post->user->username }}</a>
    </div>

    <div class="card-body">
        <div class="max-h-[35rem] overflow-hidden">

            <img src="{{ asset('storage/' . $post->image) }}" class="h-auto w-full object-cover"
                alt="{{ $post->description }}">
        </div>
        <div class="pl-3 mt-3">

            <a href="/{{ $post->user->username }}" class="font-bold mr-1">{{ $post->user->username }}</a>
            {{ $post->description }}
        </div>
        @if ($post->comments->count() > 0)
            <div class="pl-3  text-gray-300">
                <a href="/p/{{ $post->slug }}">{{ __("View All {$post->comments->count()}  comments") }}</a>
            </div>
        @else
            <div class="pl-3 pb-3 text-gray-300">
                <p>No comments on this post yet</p>
            </div>
        @endif

        <div class=" pl-3 pb-3 text-sm uppercase text-gray-300">
            {{ $post->created_at->longAbsoluteDiffForHumans() }} ago
        </div>
        <div class="card-footer">
            <div class="border-t border-gray-400 p-2">
                <form action="/p/{{ $post->slug }}/comment" method="POST">
                    @csrf
                    <div class="flex flex-row">
                        <textarea name="body" id="" rows="1" class="comment-feild"
                            placeholder="{{ __('Add a comment ...') }}"></textarea>
                        <button class="post-comment-btn"
                            title="{{ __('Post new comment') }}">{{ __('Post') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
