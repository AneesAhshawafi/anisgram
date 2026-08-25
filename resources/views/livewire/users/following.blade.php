<div>
    <li class="flex flex-col md:flex-row text-center items-center">
        <div class="md:mr-1 font-bold md:font-normal p-2">
            {{ $this->count }}

        </div>
        <button
            wire:click="$dispatch('openModal', { component: 'users.following-modal' , arguments: { user_id: {{ $this->targetUser->id }} }})"
            class="text-neutral-500 ">{{ __('following') }}</button>
    </li>
</div>
