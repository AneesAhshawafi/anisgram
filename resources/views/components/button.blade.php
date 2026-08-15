<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' =>
            'rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500',
    ]) }}>{{ $slot }}</button>
