<nav x-data="{ openMobileMenu: false }" class="bg-white shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo and Desktop Navigation -->
            <div class="flex items-center"> {{-- <<< CHANGED HERE: Added items-center --}}
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        {{-- You can use an SVG logo here too for better scaling --}}
                        <h1 class="text-xl font-bold text-indigo-600 hover:text-indigo-700 transition-colors">
                            {{ config('app.name', 'InventoryApp') }}
                        </h1>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden sm:-my-px sm:ml-8 sm:flex sm:space-x-8">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- Catalog Dropdown -->
                    <x-dropdown align="left" width="48" :active="request()->routeIs(['products.*', 'categories.*', 'locations.*'])">
                        <x-slot name="trigger">
                            {{ __('Catalog') }}
                        </x-slot>
                        <x-slot name="content">
                            @if (Route::has('products.index'))
                                <x-dropdown-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                                    {{ __('Products') }}
                                </x-dropdown-link>
                            @endif
                            @if (Route::has('categories.index'))
                                <x-dropdown-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                                    {{ __('Categories') }}
                                </x-dropdown-link>
                            @endif
                            @if (Route::has('locations.index'))
                                <x-dropdown-link :href="route('locations.index')" :active="request()->routeIs('locations.*')">
                                    {{ __('Locations') }}
                                </x-dropdown-link>
                            @endif
                        </x-slot>
                    </x-dropdown>

                    <!-- Inventory Dropdown -->
                    <x-dropdown align="left" width="56" :active="request()->routeIs(['inventory.adjustments.*'])">
                        <x-slot name="trigger">
                            {{ __('Inventory') }}
                        </x-slot>
                        <x-slot name="content">
                            @if (Route::has('inventory.adjustments.create')) {{-- Assuming create is a good entry, or use .index --}}
                                <x-dropdown-link :href="route('inventory.adjustments.create')" :active="request()->routeIs('inventory.adjustments.*')">
                                    {{ __('Stock Adjustments') }}
                                </x-dropdown-link>
                            @endif
                            {{-- Add other inventory links here --}}
                        </x-slot>
                    </x-dropdown>

                    <!-- Operations Dropdown -->
                    <x-dropdown align="left" width="56" :active="request()->routeIs(['purchase-orders.*', 'sales-orders.*', 'suppliers.*'])">
                        <x-slot name="trigger">
                            {{ __('Operations') }}
                        </x-slot>
                        <x-slot name="content">
                            @if (Route::has('purchase-orders.index'))
                                <x-dropdown-link :href="route('purchase-orders.index')" :active="request()->routeIs('purchase-orders.*')">
                                    {{ __('Purchase Orders') }}
                                </x-dropdown-link>
                            @endif
                            @if (Route::has('sales-orders.index'))
                                <x-dropdown-link :href="route('sales-orders.index')" :active="request()->routeIs('sales-orders.*')">
                                    {{ __('Sales Orders') }}
                                </x-dropdown-link>
                            @endif
                            @if (Route::has('suppliers.index'))
                                <x-dropdown-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                                    {{ __('Suppliers') }}
                                </x-dropdown-link>
                            @endif
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Settings Dropdown (Example for User Logout, Profile etc.) -->
            {{-- <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
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
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-indigo-600 mr-4">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-sm text-gray-700 hover:text-indigo-600">Register</a>
                    @endif
                @endauth
            </div> --}}


            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="openMobileMenu = ! openMobileMenu" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': openMobileMenu, 'inline-flex': ! openMobileMenu }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! openMobileMenu, 'inline-flex': openMobileMenu }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': openMobileMenu, 'hidden': ! openMobileMenu}" class="hidden sm:hidden transition-all duration-300 ease-in-out">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Mobile Catalog Links -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ __('Catalog') }}</div>
            </div>
            <div class="mt-3 space-y-1">
                @if (Route::has('products.index'))<x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">{{ __('Products') }}</x-responsive-nav-link>@endif
                @if (Route::has('categories.index'))<x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">{{ __('Categories') }}</x-responsive-nav-link>@endif
                @if (Route::has('locations.index'))<x-responsive-nav-link :href="route('locations.index')" :active="request()->routeIs('locations.*')">{{ __('Locations') }}</x-responsive-nav-link>@endif
            </div>
        </div>

        <!-- Mobile Inventory Links -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ __('Inventory') }}</div>
            </div>
            <div class="mt-3 space-y-1">
                 @if (Route::has('inventory.adjustments.create'))<x-responsive-nav-link :href="route('inventory.adjustments.create')" :active="request()->routeIs('inventory.adjustments.*')">{{ __('Stock Adjustments') }}</x-responsive-nav-link>@endif
            </div>
        </div>

        <!-- Mobile Operations Links -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ __('Operations') }}</div>
            </div>
            <div class="mt-3 space-y-1">
                @if (Route::has('purchase-orders.index'))<x-responsive-nav-link :href="route('purchase-orders.index')" :active="request()->routeIs('purchase-orders.*')">{{ __('Purchase Orders') }}</x-responsive-nav-link>@endif
                @if (Route::has('sales-orders.index'))<x-responsive-nav-link :href="route('sales-orders.index')" :active="request()->routeIs('sales-orders.*')">{{ __('Sales Orders') }}</x-responsive-nav-link>@endif
                @if (Route::has('suppliers.index'))<x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">{{ __('Suppliers') }}</x-responsive-nav-link>@endif
            </div>
        </div>


        <!-- Responsive Settings Options (User Profile/Logout for Mobile) -->
        {{-- @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
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
        @else
        <div class="py-1 border-t border-gray-200">
            <x-responsive-nav-link :href="route('login')">
                {{ __('Log In') }}
            </x-responsive-nav-link>
            @if (Route::has('register'))
            <x-responsive-nav-link :href="route('register')">
                {{ __('Register') }}
            </x-responsive-nav-link>
            @endif
        </div>
        @endauth --}}
    </div>
</nav>
