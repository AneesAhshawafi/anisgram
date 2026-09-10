<?php

use Illuminate\Support\Facades\Blade;

/*
|--------------------------------------------------------------------------
| Blade Component: <x-text-input> Tests
|--------------------------------------------------------------------------
*/

it('renders password input wrapped in relative container with toggle button and visibility icon', function () {
    $rendered = Blade::render('<x-text-input id="password" type="password" name="password" required />');

    expect($rendered)
        ->toContain('class="mt-2 relative"')
        ->toContain('<input')
        ->toContain('id="password"')
        ->toContain('type="password"')
        ->toContain('name="password"')
        ->toContain('<button type="button"')
        ->toContain('visibility</span>')
        ->toContain('rtl:pl-10 ltr:pr-10')
        ->toContain('rtl:left-0 rtl:pl-3 ltr:right-0 ltr:pr-3');
});

it('toggles password visibility via onclick script referencing input id', function () {
    $rendered = Blade::render('<x-text-input id="password_confirmation" type="password" name="password_confirmation" />');

    expect($rendered)
        ->toContain("getElementById('password_confirmation')")
        ->toContain("isPass ? 'visibility_off' : 'visibility'");
});

it('generates a random id when no id attribute is explicitly provided for password input', function () {
    $rendered = Blade::render('<x-text-input type="password" name="secret" />');

    expect($rendered)
        ->toContain('id="input_')
        ->toContain("getElementById('input_")
        ->toContain('<button type="button"');
});

it('renders standard input without relative wrapper or toggle button when type is not password', function () {
    $rendered = Blade::render('<x-text-input id="email" type="email" name="email" required />');

    expect($rendered)
        ->not->toContain('class="mt-2 relative"')
        ->not->toContain('<button')
        ->not->toContain('visibility</span>')
        ->toContain('<input')
        ->toContain('type="email"')
        ->toContain('id="email"');
});

it('renders standard text input with default styling when type is text', function () {
    $rendered = Blade::render('<x-text-input id="username" type="text" name="username" />');

    expect($rendered)
        ->not->toContain('class="mt-2 relative"')
        ->not->toContain('<button')
        ->toContain('border-gray-300 dark:border-gray-700')
        ->toContain('id="username"');
});

it('respects disabled attribute on both password and standard inputs', function () {
    $disabledPassword = Blade::render('<x-text-input type="password" name="pwd" :disabled="true" />');
    $disabledText = Blade::render('<x-text-input type="text" name="txt" :disabled="true" />');

    expect($disabledPassword)->toContain('disabled');
    expect($disabledText)->toContain('disabled');
});
