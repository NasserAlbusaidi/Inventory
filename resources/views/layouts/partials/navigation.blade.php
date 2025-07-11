<nav x-data="{ openMobileMenu: false }"
    class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <h1
                            class="text-xl font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                            {{ config('app.name', 'Inventory') }}
                        </h1>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden sm:-my-px sm:ms-24 sm:flex sm:space-x-8">
                    @foreach ($navigation as $item)
                        <x-dropdown align="left" width="56" :active="request()->routeIs($item['routes'])">
                            <x-slot name="trigger"> {{ __($item['name']) }} </x-slot>
                            <x-slot name="content">
                                @foreach ($item['children'] as $child)
                                    <x-dropdown-link :href="route($child['route'])" :active="request()->routeIs($child['active'] ?? $child['route'])">
                                        {{ __($child['name']) }}
                                    </x-dropdown-link>
                                @endforeach
                            </x-slot>
                        </x-dropdown>
                    @endforeach
                </div>

            </div>
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                    {{-- dark mode toggle --}}
                    <!-- Inside your navigation component -->
                    <button @click="darkMode = !darkMode" ...>
                        {{-- Sun Icon (Light Mode) --}}
                        <svg x-show="!darkMode" class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm-.707 9.192a1 1 0 010-1.414l.707-.707a1 1 0 111.414 1.414l-.707.707a1 1 0 01-1.414 0zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z">
                            </path>
                        </svg>
                        {{-- Moon Icon (Dark Mode) --}}
                        <svg x-show="darkMode" class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </button>
                </div>

            <!-- Desktop Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <div class="ml-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <div class="flex items-center cursor-pointer">
                                    <span>{{ Auth::user()->name }}</span>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </x-slot>
                            <x-slot name="content">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
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
                {{-- dark mode toggle  --}}
                <button @click="darkMode = !darkMode" ...>
                    <!-- Sun Icon (Light Mode) -->
                    <svg x-show="!darkMode" class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm-.707 9.192a1 1 0 010-1.414l.707-.707a1 1 0 111.414 1.414l-.707.707a1 1 0 01-1.414 0zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z">
                        </path>
                    </svg>
                    <!-- Moon Icon (Dark Mode) -->
                    <svg x-show="darkMode" class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                </button>
            </div>
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="openMobileMenu = ! openMobileMenu"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                    aria-controls="mobile-menu" :aria-expanded="openMobileMenu.toString()">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': openMobileMenu, 'inline-flex': !openMobileMenu }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !openMobileMenu, 'inline-flex': openMobileMenu }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>


        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div :class="{ 'block': openMobileMenu, 'hidden': !openMobileMenu }" class="sm:hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            @foreach ($navigation as $item)
                <div class="px-2 pt-2 pb-1">
                    <!-- Mobile Parent Link (not clickable) -->
                    <h3
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400">
                        {{ __($item['name']) }}
                    </h3>
                    <!-- Mobile Child Links -->
                    @foreach ($item['children'] as $child)
                        <x-responsive-nav-link :href="route($child['route'])" :active="request()->routeIs($child['active'] ?? $child['route'])">
                            {{ __($child['name']) }}
                        </x-responsive-nav-link>
                    @endforeach
                </div>
            @endforeach
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
