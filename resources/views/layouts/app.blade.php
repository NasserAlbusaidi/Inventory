<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body
        x-data="{ sidebarOpen: false }"
        class="font-sans antialiased bg-gray-100 dark:bg-gray-900"
    >
        <div class="relative min-h-screen md:flex">
            <!-- Sidebar -->
            <x-layouts.sidebar />

            <!-- Main Content Area -->
            <div class="flex-1">
                <!-- Top Bar -->
                <header class="flex items-center justify-between h-16 px-4 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 sm:px-6 lg:px-8">
                    <!-- Hamburger Menu for Mobile -->
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 md:hidden">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Page Heading (if provided) -->
                    <div class="flex-1">
                        @if (isset($header))
                            {{ $header }}
                        @endif
                    </div>

                    <!-- User Profile Dropdown -->
                    @auth
                    <div class="flex items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                {{ Auth::user()->name }}
                            </x-slot>
                            <x-slot name="content">
                                {{-- <x-dropdown-link :href="route('profile.edit')"> {{ __('Profile') }} </x-dropdown-link> --}}
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endauth
                </header>

                <!-- Page Content -->
                <main class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
