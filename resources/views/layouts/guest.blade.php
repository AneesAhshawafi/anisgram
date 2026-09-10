<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-25..200" />

    <!-- Scripts -->
    <script>
        (function() {
            const userTheme = "{{ auth()->user()?->theme ?? session('theme') }}";
            let theme = userTheme;
            if (!theme || theme === 'system') {
                theme = localStorage.theme;
            }
            if (!theme || theme === 'system') {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <a href="/">
                <x-application-logo class="w-80 h-20 fill-current text-gray-500" />
            </a>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
            <div
                class="w-full sm:max-w-md mt-4 px-6 py-2 flex items-center justify-around text-sm text-gray-600 dark:text-gray-400">
                <a href="/lang-ar"
                    class="hover:underline hover:text-gray-900 dark:hover:text-white {{ app()->getLocale() == 'ar' ? 'font-bold text-indigo-500 dark:text-indigo-400' : '' }}">العربية</a>
                <a href="/lang-en"
                    class="hover:underline hover:text-gray-900 dark:hover:text-white {{ app()->getLocale() == 'en' ? 'font-bold text-indigo-500 dark:text-indigo-400' : '' }}">English</a>
                <div x-data="{
                    dark: document.documentElement.classList.contains('dark'),
                    toggle() {
                        this.dark = !this.dark;
                        if (this.dark) {
                            document.documentElement.classList.add('dark');
                            localStorage.theme = 'dark';
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.theme = 'light';
                        }
                        fetch('/theme/' + (this.dark ? 'dark' : 'light'));
                    }
                }">
                    <button @click="toggle()" type="button"
                        class="p-1 rounded text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        <span x-show="dark" class="material-symbols-outlined block text-base">light_mode</span>
                        <span x-show="!dark" class="material-symbols-outlined block text-base"
                            style="display: none;">dark_mode</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
