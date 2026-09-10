<div class="w-[30rem] mx-auto lg:w-[40rem]">
    @forelse ($this->posts as $post)
        <livewire:posts.post :post="$post" :key="$post->id" />
    @empty <div class="max-w-2xl gap-8 mx-auto text-gray-900 dark:text-white mt-10 text-center font-medium">
            {{ __('Start following your friends and enjoy.') }}
        </div>
    @endforelse
</div>
