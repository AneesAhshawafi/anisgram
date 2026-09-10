<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                {{-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                </div> --}}
            </div>

            <div class="hidden sm:flex sm:items-center">
                <livewire:posts.search />
            </div>
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Theme toggle button -->
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
                }" class="flex items-center rtl:ml-2 ltr:mr-2">
                    <button @click="toggle()" type="button"
                        class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150"
                        :title="dark ? '{{ __('Light') }}' : '{{ __('Dark') }}'">
                        <span x-show="dark" class="material-symbols-outlined block">light_mode</span>
                        <span x-show="!dark" class="material-symbols-outlined block"
                            style="display: none;">dark_mode</span>
                    </button>
                </div>
                @guest
                    <div class="hidden md:flex md:items-center md:space-x-2 rtl:space-x-reverse">
                        <div class="space-x-3 rtl:space-x-reverse text-[1.6rem] rtl:ml-5 ltr:mr-5 leading-5">
                            <a href="/login"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semitbold text-xs text-white uppercase tracking-widest rtl:ml-2 ltr:mr-2">{{ __('Login') }}</a>
                            <a href="/register"
                                class="inline-flex items-center px-4 py-2 font-semibold text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white uppercase tracking-widest">{{ __('Register') }}</a>
                        </div>
                    </div>
                @endguest
                @auth
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <div class="space-x-3 rtl:space-x-reverse text-[1.5rem] rtl:ml-2 ltr:mr-2 leading-5">
                            <a href="{{ route('home') }}" title="{{ __('home page') }}">
                                {!! url()->current() == route('home')
                                    ? '<span class="material-symbols-outlined text-gray-900 dark:text-white ">home</span>'
                                    : '<span class="material-symbols-outlined text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">home</span>' !!}
                            </a>
                            <button onclick="Livewire.dispatch('openModal', { component: 'posts.create-post-modal'})">
                                <span
                                    class="material-symbols-outlined text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">add</span>
                            </button>
                            <a href="{{ route('explore') }}" title="{{ __('Explore') }}">
                                {!! url()->current() == route('explore')
                                    ? '<span class="material-symbols-outlined text-gray-900 dark:text-white ">search</span>'
                                    : '<span class="material-symbols-outlined text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">search</span>' !!}
                            </a>
                        </div>
                    </div>

                    <div class="hidden md:flex md:items-center ">
                        <x-dropdown :align="app()->getLocale() == 'ar' ? 'left' : 'right'" width="48">
                            <x-slot name="trigger">
                                <button class="rtl:mr-3 rtl:ml-6 ltr:ml-3 ltr:mr-6 pb-2 leading-5"
                                    title="{{ __('Follow Requests') }}">
                                    <div class="relative inline-flex items-center justify-center">
                                        <livewire:users.pending-followers-count />
                                        <span
                                            class="material-symbols-outlined text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">person_add</span>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <livewire:users.pending-followers-list />
                            </x-slot>
                        </x-dropdown>
                    </div>
                    <div class="hidden md:block">
                        <x-dropdown :align="app()->getLocale() == 'ar' ? 'left' : 'right'" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:text-gray-900 dark:hover:text-white focus:outline-none transition ease-in-out duration-150">
                                    <div class="rtl:ml-2 ltr:mr-2">
                                        <img src="{{ Auth::user()->image }}" alt=""
                                            class="border border-gray-300 dark:border-gray-600 aspect-square object-cover rounded-full h-8 w-8">
                                    </div>
                                    <div>{{ Auth::user()->name }}</div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('user_profile', ['user' => auth()->user()->username])">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
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
                }" class="flex items-center me-1">
                    <button @click="toggle()" type="button"
                        class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150"
                        :title="dark ? '{{ __('Light') }}' : '{{ __('Dark') }}'">
                        <span x-show="dark" class="material-symbols-outlined block text-xl">light_mode</span>
                        <span x-show="!dark" class="material-symbols-outlined block text-xl"
                            style="display: none;">dark_mode</span>
                    </button>
                </div>
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    @auth
        <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    {{ __('Home') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('explore')" :active="request()->routeIs('explore')">
                    {{ __('Explore') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link class="cursor-pointer"
                    onClick="Livewire.dispatch('openModal',{component:'posts.create-post-modal'})">
                    {{ __('Create New Post') }}
                </x-responsive-nav-link>
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-600 dark:text-gray-400">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    @endauth
</nav>
