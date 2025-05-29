<nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <h1 class="text-xl font-semibold text-gray-700">{{ config('app.name', 'Perfume Inventory') }}</h1>
                    </a>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                        Dashboard
                    </a>

                    @if(Route::has('categories.index'))
                    <a href="{{ route('categories.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('categories.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                        Categories
                    </a>
                    @endif

                    @if(Route::has('locations.index'))
                    <a href="{{ route('locations.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('locations.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                        Locations
                    </a>
                    @endif

                    {{-- Placeholder for Products Link --}}
                    @if(Route::has('products.create')) {{-- Or a future products.index route --}}
                    {{-- <a href="{{ route('products.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('products.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                        Products
                    </a> --}}
                    @endif

                    {{-- Placeholder for Suppliers Link --}}
                    {{-- @if(Route::has('suppliers.index'))
                    <a href="{{ route('suppliers.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('suppliers.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                        Suppliers
                    </a>
                    @endif --}}

                    {{-- Add other common nav links here as you build them --}}
                </div>
            </div>

            {{-- Optional: User menu, etc. --}}
            {{-- <div class="hidden sm:flex sm:items-center sm:ml-6">
                </div> --}}
        </div>
    </div>
</nav>
