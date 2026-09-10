@props(['disabled' => false])

@php
    $isPassword = $attributes->get('type') === 'password';
    $id = $attributes->get('id') ?? 'input_' . \Illuminate\Support\Str::random(8);

    if ($isPassword) {
        $customClasses = trim(preg_replace('/\b(block|w-full|mt-1)\b/', '', (string) $attributes->get('class')));
        $attributes = $attributes->except('class');
    }
@endphp

@if ($isPassword)
    <div class="mt-2 relative">
        <input @disabled($disabled)
            {{ $attributes->merge([
                'id' => $id,
                'class' => trim(
                    'block w-full rounded-md bg-white/5 px-3 py-1.5 rtl:pl-10 ltr:pr-10 text-base text-white outline outline-1 -outline-offset-1 outline-white/10 focus:outline focus:outline-2 focus:outline-indigo-500 sm:text-sm/6 ' .
                        $customClasses,
                ),
            ]) }}>

        <button type="button"
            onclick="const input = document.getElementById('{{ $id }}'); const isPass = input.type === 'password'; input.type = isPass ? 'text' : 'password'; this.querySelector('span').innerText = isPass ? 'visibility_off' : 'visibility';"
            class="absolute inset-y-0 rtl:left-0 rtl:pl-3 ltr:right-0 ltr:pr-3 flex items-center text-gray-400 hover:text-white">
            <span class="material-symbols-outlined text-xl">visibility</span>
        </button>
    </div>
@else
    <input @disabled($disabled)
        {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) }}>
@endif
