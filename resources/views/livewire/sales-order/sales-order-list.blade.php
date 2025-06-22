<div class="py-8">
    {{-- On mobile, use less horizontal padding for more space --}}
    <div class="max-w-full mx-auto px-2 sm:px-6 lg:px-8">

        {{-- =================================================== --}}
        {{-- Header Section --}}
        {{-- =================================================== --}}
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Sales Orders</h1>
            <a href="{{ route('sales-orders.create') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-500 shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 -ml-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Create New SO
            </a>
        </div>

        {{-- =================================================== --}}
        {{-- Flash Messages --}}
        {{-- =================================================== --}}
        @if ($allLocations->isEmpty())
            <div class="mb-6 bg-yellow-50 dark:bg-yellow-800/50 border-l-4 border-yellow-400 dark:border-yellow-600 p-4 shadow-md rounded-md"
                role="alert">
                <div class="flex">
                    <div class="flex-shrink-0"><svg class="h-5 w-5 text-yellow-500 dark:text-yellow-400"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                clip-rule="evenodd" />
                        </svg></div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700 dark:text-yellow-200">No locations found! Please create a
                            location first.</p>
                        <span
                            class=" underline mt-2 inline-flex items-center px-3 py-1 text-sm  bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200">
                            <a href="{{ route('locations.create') }}">Create Location</a>
                        </span>
                    </div>
                </div>
            </div>
        @endif
        @if($allChannels->isEmpty())
            <div class="mb-6 bg-yellow-50 dark:bg-yellow-800/50 border-l-4 border-yellow-400 dark:border-yellow-600 p-4 shadow-md rounded-md"
                role="alert">
                <div class="flex">
                    <div class="flex-shrink-0"><svg class="h-5 w-5 text-yellow-500 dark:text-yellow-400"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                clip-rule="evenodd" />
                        </svg></div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700 dark:text-yellow-200">No sales channels found! Please create a
                            sales channel first.</p>
                        <span
                            class=" underline mt-2 inline-flex items-center px-3 py-1 text-sm bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200">
                            <a href="{{ route('sales-channels.create') }}">Create Sales Channel</a>
                        </span>
                    </div>
                </div>
            </div>
        @endif
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 dark:bg-green-800/50 border-l-4 border-green-400 dark:border-green-600 p-4 shadow-md rounded-md" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0"><svg class="h-5 w-5 text-green-400 dark:text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg></div>
                    <div class="ml-3"><p class="text-sm text-green-700 dark:text-green-200">{{ session('message') }}</p></div>
                </div>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-800/50 border-l-4 border-red-400 dark:border-red-600 p-4 shadow-md rounded-md" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400 dark:text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg></div>
                    <div class="ml-3"><p class="text-sm text-red-700 dark:text-red-200">{{ session('error') }}</p></div>
                </div>
            </div>
        @endif

        {{-- =================================================== --}}
        {{-- Filters Section --}}
        {{-- =================================================== --}}
        <div class="mb-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Sales Orders</label>
                    <div class="relative">
                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg></div>
                        <input type="text" id="search" wire:model.live.debounce.300ms="search" placeholder="Search SO # or Customer..."
                            class="form-input block w-full pl-10 pr-3 py-2 border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label for="channelFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Channel</label>
                    <select id="channelFilter" wire:model.live="channelFilter" class="form-select block w-full py-2 px-3 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900 dark:text-gray-200">
                        <option value="">All Channels</option>
                        @foreach($soChannels as $channel)
                            <option value="{{ $channel }}">{{ ucfirst($channel) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select id="statusFilter" wire:model.live="statusFilter" class="form-select block w-full py-2 px-3 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900 dark:text-gray-200">
                        <option value="">All Statuses</option>
                         @foreach($soStatuses as $status)
                            <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- =================================================== --}}
        {{-- Sales Orders List (Desktop Table + Mobile Cards) --}}
        {{-- =================================================== --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
            {{-- Desktop Table View --}}
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 hidden md:table">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SO #</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Channel</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total (OMR)</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($salesOrders as $so)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300"><a href="{{ route('sales-orders.edit', $so) }}">{{ $so->order_number }}</a></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $so->customer_details['name'] ?? ($so->customer_details['email'] ?? 'N/A') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $so->SalesChannel->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $so->order_date ? \Carbon\Carbon::parse($so->order_date)->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap"><span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full @switch($so->status) @case('pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 @break @case('processing') bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 @break @case ('fulfilled') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 @break @case('completed') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 @break @case('shipped') bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300 @break @case('cancelled') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300 @break @case('refunded') bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200 @break @default bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200 @endswitch">{{ ucfirst(str_replace('_', ' ', $so->status)) }}</span></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 text-right font-mono">{{ number_format($so->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                <a href="{{ route('sales-orders.edit', $so) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold">View/Edit</a>
                                @if(in_array($so->status, ['pending', 'cancelled']))
                                    <button wire:click="deleteSalesOrder({{ $so->id }})" wire:confirm="Are you sure?" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-semibold">Delete</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-3"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c.51 0 .962-.343 1.087-.835l.383-1.437M7.5 14.25V5.106c0-.621.504-1.125 1.125-1.125h9.75c.621 0 1.125.504 1.125 1.125v9.144M7.5 14.25h11.218c.51 0 .962-.343 1.087-.835l.383-1.437a1.875 1.875 0 0 0-1.087-2.335H11.25M-1.5 6h15" /></svg>
                                    No Sales Orders found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Mobile Card View --}}
            <div class="md:hidden">
                <div class="px-2 py-2 space-y-3">
                    @forelse ($salesOrders as $so)
                        {{-- (CORRECTED) The card is now a div to contain the link and the menu separately --}}
                        <div class="relative bg-white dark:bg-gray-800/50 p-4 rounded-lg shadow ring-1 ring-gray-200 dark:ring-gray-700">
                            <div class="flex justify-between items-start">
                                {{-- The main content area is a link --}}
                                <a href="{{ route('sales-orders.edit', $so) }}" class="flex-1">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <p class="font-bold text-indigo-600 dark:text-indigo-400">{{ $so->order_number }}</p>
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $so->customer_details['name'] ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $so->order_date ? \Carbon\Carbon::parse($so->order_date)->format('d M, Y') : '-' }}</p>
                                        </div>
                                        <div>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full @switch($so->status)@case('pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 @break @case('processing') bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 @break @case('fulfilled') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 @break @case('completed') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 @break @case('shipped') bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300 @break @case('cancelled') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300 @break @case('refunded') bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200 @break @default bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200 @endswitch">
                                                {{ ucfirst(str_replace('_', ' ', $so->status)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                                        <div class="flex justify-between items-center text-sm"><p class="text-gray-500 dark:text-gray-400">Total</p><p class="font-semibold font-mono text-gray-800 dark:text-gray-200">OMR {{ number_format($so->total_amount, 2) }}</p></div>
                                        <div class="flex justify-between items-center text-sm mt-1"><p class="text-gray-500 dark:text-gray-400">Channel</p><p class="font-medium text-gray-700 dark:text-gray-300">{{ $so->SalesChannel->name ?? 'N/A' }}</p></div>
                                    </div>
                                </a>
                                {{-- The kebab menu is outside the link, in the top right corner of the parent div --}}
                                <div x-data="{ open: false }" class="absolute top-2 right-2">
                                    <button @click="open = !open" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 p-1 -m-1 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-900 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-20">
                                        <a href="{{ route('sales-orders.edit', $so) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">View/Edit</a>
                                        @if(in_array($so->status, ['pending', 'cancelled']))
                                            <button wire:click="deleteSalesOrder({{ $so->id }})" wire:confirm="Are you sure?" class="w-full text-left block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-800">Delete</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                         <div class="text-center py-12 text-sm text-gray-500 dark:text-gray-400">
                             <div class="flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-3"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c.51 0 .962-.343 1.087-.835l.383-1.437M7.5 14.25V5.106c0-.621.504-1.125 1.125-1.125h9.75c.621 0 1.125.504 1.125 1.125v9.144M7.5 14.25h11.218c.51 0 .962-.343 1.087-.835l.383-1.437a1.875 1.875 0 0 0-1.087-2.335H11.25M-1.5 6h15" /></svg>
                                No Sales Orders found.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($salesOrders->hasPages())
            <div class="mt-6 px-2">
                {{ $salesOrders->links() }}
            </div>
        @endif
    </div>
</div>
