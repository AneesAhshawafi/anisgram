<?php

use App\Http\Middleware\ChangeLanguage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| Language Switching Routes Tests (/lang-ar & /lang-en)
|--------------------------------------------------------------------------
*/

it('sets session language to arabic and redirects back when visiting /lang-ar', function () {
    $response = $this->from('/login')->get('/lang-ar');

    $response->assertRedirect('/login');
    expect(Session::get('lang'))->toBe('ar');
});

it('sets session language to english and redirects back when visiting /lang-en', function () {
    $response = $this->from('/login')->get('/lang-en');

    $response->assertRedirect('/login');
    expect(Session::get('lang'))->toBe('en');
});

/*
|--------------------------------------------------------------------------
| ChangeLanguage Middleware Tests
|--------------------------------------------------------------------------
*/

it('sets locale to authenticated user preferred language', function () {
    $user = User::factory()->create([
        'lang' => 'en',
    ]);

    $this->actingAs($user);

    $middleware = new ChangeLanguage;
    $request = Request::create('/', 'GET');

    $middleware->handle($request, function ($req) {
        expect(app()->getLocale())->toBe('en');

        return new Response;
    });
});

it('sets locale to session language for guest users', function () {
    Auth::logout();
    Session::put('lang', 'ar');

    $middleware = new ChangeLanguage;
    $request = Request::create('/', 'GET');

    $middleware->handle($request, function ($req) {
        expect(app()->getLocale())->toBe('ar');

        return new Response;
    });
});

it('prioritizes authenticated user language over session language', function () {
    $user = User::factory()->create([
        'lang' => 'en',
    ]);

    $this->actingAs($user);
    Session::put('lang', 'ar');

    $middleware = new ChangeLanguage;
    $request = Request::create('/', 'GET');

    $middleware->handle($request, function ($req) {
        expect(app()->getLocale())->toBe('en');

        return new Response;
    });
});

it('maintains default locale when no user or session language is set', function () {
    Auth::logout();
    Session::forget('lang');
    Config::set('app.locale', 'ar');
    app()->setLocale('ar');

    $middleware = new ChangeLanguage;
    $request = Request::create('/', 'GET');

    $middleware->handle($request, function ($req) {
        expect(app()->getLocale())->toBe('ar');

        return new Response;
    });
});
