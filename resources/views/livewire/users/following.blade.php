<div>
    <li class="flex flex-col md:flex-row text-center items-center">
        <div class="rtl:md:ml-1 ltr:md:mr-1 font-bold md:font-normal p-2">
            {{ $this->count }}

        </div>
        <button
            wire:click="$dispatch('openModal', { component: 'users.following-modal' , arguments: { user_id: {{ $this->targetUser->id }} }})"
            class="text-gray-600 dark:text-gray-400 font-medium hover:text-gray-900 dark:hover:text-white">{{ __('following') }}</button>
    </li>
</div>
