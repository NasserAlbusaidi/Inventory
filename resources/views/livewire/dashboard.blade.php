<div class="space-y-8">
    {{-- This outer div adds a visual loading indicator while the component is updating --}}
    <div wire:loading.class.remove="hidden" wire:target="dateRange,customStartDate,customEndDate" class="hidden">
        {{-- Skeleton for Header and Date Selector --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div class="space-y-1">
                <div class="h-8 w-48 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="h-5 w-80 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
            </div>
            <div class="h-12 w-64 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
        </div>

        {{-- Skeletons for Stat Cards --}}
        <div class="space-y-2">
            <div class="h-6 w-1/3 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse mb-4"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="h-32 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="h-32 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="h-32 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-8 mt-8">
            <div class="lg:col-span-2 xl:col-span-3 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="h-28 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                    <div class="h-28 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                    <div class="h-28 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                    <div class="h-28 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                </div>
                <div class="h-80 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="h-96 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                    <div class="h-96 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                </div>
            </div>
            <div class="lg:col-span-1 xl:col-span-1 space-y-8">
                <div class="h-80 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="h-80 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
            </div>
        </div>
    </div>


    {{-- =================================== --}}
    {{--  Actual Dashboard Content          --}}
    {{-- =================================== --}}
    <div wire:loading.class="hidden" wire:target="dateRange,customStartDate,customEndDate">
        <div class="space-y-8">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Dashboard</h1>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">A real-time overview of your sales and inventory
                        performance.</p>
                </div>

                {{-- Date Range Selector --}}
                <div class="flex items-center gap-3 bg-white dark:bg-gray-800/50 rounded-lg shadow-sm p-2">
                    <label for="date_range" class="text-sm font-medium text-gray-600 dark:text-gray-300 ml-1">Date
                        Range:</label>
                    <select wire:model.live="dateRange" id="date_range"
                        class="text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="last_30_days">Last 30 Days</option>
                        <option value="this_month">This Month</option>
                        <option value="this_week">This Week</option>
                        <option value="today">Today</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>

                    @if ($dateRange == 'custom')
                        <div class="flex items-center gap-2">
                            <input type="date" wire:model.live="customStartDate"
                                class="text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="text-sm text-gray-500 dark:text-gray-400">to</span>
                            <input type="date" wire:model.live="customEndDate"
                                class="text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200">Financial Summary</h2>

                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Last updated: {{ $lastUpdated->diffForHumans() }}
                        </span>
                        <button wire:click="refreshData" wire:loading.attr="disabled"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-wait">

                            {{-- Loading spinner, shown only when 'refreshData' is running --}}
                            <svg wire:loading wire:target="refreshData" class="animate-spin -ml-1 mr-2 h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>

                            {{-- Refresh icon, shown when not loading --}}
                            <svg wire:loading.remove wire:target="refreshData" xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 -ml-1 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 4v5h5M20 20v-5h-5M4 4l5 5M20 20l-5-5"></path>
                            </svg>

                            <span>Refresh</span>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Total Revenue Card --}}
                    <x-card>
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-green-100 dark:bg-green-700/30 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="w-6 h-6 text-green-600 dark:text-green-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200">Total Revenue</h3>
                        </div>
                        <p class="mt-3 text-3xl font-semibold text-green-600 dark:text-green-500"
                            x-data="{ instance: null, initialValue: {{ $totalRevenue ?? 0 }} }" x-init="instance = new countUp.CountUp($el, initialValue, { decimalPlaces: 2, prefix: 'OMR ' });
                            if (!instance.error) { instance.start(); }
                            Livewire.on('update-charts', event => instance.update(event.data.totalRevenue));">
                            OMR {{ number_format($totalRevenue ?? 0, 2) }}
                        </p>

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total sales revenue in the selected
                            period.</p>
                    </x-card>

                    {{-- Total Costs Card --}}
                    <x-card>
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-orange-100 dark:bg-orange-700/30 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="w-6 h-6 text-orange-600 dark:text-orange-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200">Total Costs</h3>
                        </div>
                        <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white" x-data="{ instance: null, initialValue: {{ $totalCost ?? 0 }} }"
                            x-init="instance = new countUp.CountUp($el, initialValue, { decimalPlaces: 2, prefix: 'OMR ' });
                            if (!instance.error) { instance.start(); }
                            Livewire.on('update-charts', event => instance.update(event.data.totalCost));">
                            OMR {{ number_format($totalCost ?? 0, 2) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Cost of Goods
                            ({{ number_format($totalPurchaseValue ?? 0, 2) }}) + Operational
                            ({{ number_format($operationalCost ?? 0, 2) }})</p>
                    </x-card>

                    {{-- Net Profit Card --}}
                    <x-card>
                        <div class="flex items-center space-x-3">
                            <div
                                class="p-2 {{ ($netProfit ?? 0) >= 0 ? 'bg-green-100 dark:bg-green-700/30' : 'bg-red-100 dark:bg-red-700/30' }} rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="w-6 h-6 {{ ($netProfit ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.286-1.046a.75.75 0 00-.358-1.416l-1.48-.37M18.75 4.97l-1.48-.37M5.25 4.97A48.416 48.416 0 0112 4.5c2.291 0 4.545.16 6.75.47m-13.5 0c-1.01.143-2.01.317-3 .52m3-.52l-2.286-1.046a.75.75 0 01.358-1.416l1.48-.37M5.25 4.97l1.48-.37M9 11.25l3-3 3 3M9 11.25l-3 3m3-3l3-3" />
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200">Net Profit</h3>
                        </div>
                        <p class="mt-3 text-3xl font-semibold {{ ($netProfit ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}"
                            x-data="{ instance: null, initialValue: {{ $netProfit ?? 0 }} }" x-init="instance = new countUp.CountUp($el, initialValue, { decimalPlaces: 2, prefix: 'OMR ' });
                            if (!instance.error) { instance.start(); }
                            Livewire.on('update-charts', event => instance.update(event.data.netProfit));">
                            OMR {{ number_format($netProfit ?? 0, 2) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Profit Margin: <span
                                class="font-semibold {{ ($profitMargin ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($profitMargin ?? 0, 1) }}%</span>
                        </p>
                    </x-card>
                </div>
            </div>

            {{-- Main Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                {{-- Main Content Area --}}
                <div class="lg:col-span-2 xl:col-span-3 space-y-8">
                    {{-- Top Row KPI Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {{-- Operational Cost --}}
                        <x-kpi-card title="Operational Cost"
                            value="OMR {{ number_format($operationalCost ?? 0, 2) }}" color="orange"
                            changeText="in selected period">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </x-slot:icon>
                        </x-kpi-card>

                        {{-- Total Cost of Goods Sold --}}
                        <x-kpi-card title="Total Cost of Goods Sold"
                            value="OMR {{ number_format($costOfGoodsSold ?? 0, 2) }}" color="red"
                            changeText="in selected period">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a1.5 1.5 0 100-2.121 1.5 1.5 0 000 2.121zM9 12a3 3 0 116 0 3 3 0 01-6 0z" />
                                </svg>
                            </x-slot:icon>
                        </x-kpi-card>
                        <x-kpi-card title="Total Purchase Value"
                            value="OMR {{ number_format($totalPurchaseValue ?? 0, 2) }}" color="indigo"
                            changeText="spent on inventory">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </x-slot:icon>
                        </x-kpi-card>
                        {{-- Profit Margin --}}
                        <x-kpi-card title="Profit Margin" value="{{ number_format($profitMargin ?? 0, 1) }}%"
                            color="{{ ($profitMargin ?? 0) >= $profitMarginTarget ? 'teal' : (($profitMargin ?? 0) > 0 ? 'yellow' : 'red') }}"
                            changeText="in selected period">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-6 h-6">
                                    <path fill-rule="evenodd"
                                        d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5A.75.75 0 014.5 3.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM3.75 7.5A.75.75 0 014.5 6.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM3.75 10.5A.75.75 0 014.5 9.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM3.75 13.5A.75.75 0 014.5 12.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM3.75 16.5A.75.75 0 014.5 15.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM17.06 5.272a.75.75 0 011.06 0l3.75 3.75a.75.75 0 010 1.06l-3.75 3.75a.75.75 0 11-1.06-1.06l2.47-2.47H13.5a.75.75 0 010-1.5h5.94l-2.47-2.47a.75.75 0 010-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </x-slot:icon>
                        </x-kpi-card>
                        {{-- Total Purchase Orders --}}
                        <x-kpi-card title="Total Purchase Orders" value="{{ $purchaseOrdersCount ?? 0 }}"
                            color="indigo" change="{{ $purchaseOrdersCountChange ?? 0 }}"
                            changeType="{{ ($purchaseOrdersCountChange ?? 0) >= 0 ? 'increase' : 'decrease' }}">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </x-slot:icon>
                        </x-kpi-card>
                        {{-- Average purchase value --}}
                        <x-kpi-card title="Average Purchase Value"
                            value="OMR {{ number_format($averagePurchaseValue ?? 0, 2) }}" color="purple"
                            changeText="in selected period">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                </svg>
                            </x-slot:icon>
                        </x-kpi-card>



                        {{-- Total Sales Orders --}}
                        <x-kpi-card title="Total Sales Orders" value="{{ $salesCount ?? 0 }}" color="blue"
                            change="{{ $salesCountChange ?? 0 }}"
                            changeType="{{ ($salesCountChange ?? 0) >= 0 ? 'increase' : 'decrease' }}">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </x-slot:icon>
                        </x-kpi-card>
                        {{-- Average Order Value --}}
                        <x-kpi-card title="Average Order Value"
                            value="OMR {{ number_format($averageOrderValue ?? 0, 2) }}" color="green"
                            changeText="in selected period">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                </svg>
                            </x-slot:icon>
                        </x-kpi-card>



                        {{-- New Customers --}}
                        <x-kpi-card title="New Customers" value="{{ $totalCustomers ?? 0 }}" color="purple"
                            change="{{ $customerChangePercentage ?? 0 }}"
                            changeType="{{ ($customerChangePercentage ?? 0) >= 0 ? 'increase' : 'decrease' }}">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </x-slot:icon>
                        </x-kpi-card>
                        {{-- New Card 2: Repeat Customer Rate --}}
                        <x-kpi-card title="Repeat Customer Rate"
                            value="{{ number_format($repeatCustomerRate ?? 0, 1) }}%" color="teal"
                            changeText="of customers returned">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.125-1.274-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.125-1.274.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </x-slot:icon>
                        </x-kpi-card>







                        {{-- Low Stock Items --}}
                        <x-kpi-card title="Low Stock Items" value="{{ $lowStockItemsCount ?? 0 }}" color="yellow"
                            changeText="items need restocking"
                            href="{{ route('products.index', ['filter[status]' => 'low_stock']) }}">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </x-slot:icon>
                        </x-kpi-card>
                        <x-kpi-card title="Dead Stock Items" value="{{ $deadStockItemsCount ?? 0 }}" color="red"
                            changeText="items not sold in 90 days" {{-- You would create a route/filter for this --}}
                            href="{{ route('products.index', ['filter[status]' => 'dead_stock']) }}">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </x-slot:icon>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Items not sold in the last 90
                                days.</span>
                        </x-kpi-card>




                    </div>

                    <x-card title="Revenue Analytics">
                        <div class="h-80" wire:ignore>
                            @if (!empty($revenueOverTimeLabels) && count($revenueOverTimeLabels) > 0)
                                <canvas id="revenueOverTimeChart"></canvas>
                            @else
                                <div class="flex items-center justify-center h-full text-gray-500">No revenue data for
                                    the selected period.</div>
                            @endif
                        </div>
                    </x-card>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- ===== Most Profitable Items (Corrected) ===== --}}
                        <x-card title="Most Profitable Products">
                            <div class="overflow-x-auto max-h-96">
                                @if ($mostProfitableItems && $mostProfitableItems->count() > 0)
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0 z-10">
                                            <tr>
                                                <th scope="col"
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Product / Variant</th>
                                                <th scope="col"
                                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Total Profit</th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach ($mostProfitableItems as $item)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                    <td
                                                        class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $item['display_name'] }}</td>
                                                    <td
                                                        class="px-4 py-3 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-500 font-mono">
                                                        OMR {{ number_format($item['total_profit'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="flex items-center justify-center h-full py-8 text-gray-500">No profit
                                        data for products.</div>
                                @endif
                            </div>
                        </x-card>

                        {{-- ===== Top Selling Items (Corrected) ===== --}}
                        <x-card title="Top Selling Products (by Qty)">
                            <div class="overflow-x-auto max-h-96">
                                @if ($topSellingItems && $topSellingItems->count() > 0)
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0 z-10">
                                            <tr>
                                                <th scope="col"
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Product / Variant</th>
                                                <th scope="col"
                                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Units Sold</th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach ($topSellingItems as $item)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                    <td
                                                        class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $item['display_name'] }}</td>
                                                    <td
                                                        class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700 dark:text-gray-300 font-mono">
                                                        {{ number_format($item['total_quantity_sold']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="flex items-center justify-center h-full py-8 text-gray-500">No product
                                        sales data.</div>
                                @endif
                            </div>
                        </x-card>
                    </div>
                </div>

                {{-- Right Sidebar --}}
                <div class="lg:col-span-1 xl:col-span-1 space-y-8">
                    <x-card title="Monthly Target">
                        <div class="relative h-48 flex flex-col items-center justify-center" wire:ignore>
                            {{-- The canvas for our chart --}}
                            <canvas id="monthlyTargetChart" class="absolute top-0 left-0"></canvas>

                            {{-- The text overlay in the center --}}
                            <div class="text-center">
                                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                                    {{-- Display the percentage achieved --}}
                                    {{ number_format($percentageAchieved ?? 0, 1) }}<span class="text-xl">%</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Achieved
                                </p>
                            </div>
                        </div>

                        {{-- The summary section at the bottom --}}
                        <div
                            class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 divide-x divide-gray-200 dark:divide-gray-700 text-center">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Target</p>
                                <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                    OMR {{ number_format($monthlyTargetAmount ?? 0, 0) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Revenue</p>
                                <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                    OMR {{ number_format($totalRevenue ?? 0, 0) }}
                                </p>
                            </div>
                        </div>
                    </x-card>
                    {{-- ===== Recent Purchases (Corrected) ===== --}}
                    <x-card title="Recent Purchases">
                        <div class="space-y-3 max-h-[22rem] overflow-y-auto pretty-scrollbar">
                            @forelse($recentPurchases as $purchase)
                                <div
                                    class="p-3 rounded-md bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <div class="flex justify-between items-start">
                                        <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">PO
                                            #{{ $purchase->order_number }}</span>
                                        <span
                                            class="text-xs text-gray-500 dark:text-gray-400">{{ $purchase->created_at->diffForHumans(null, true) }}
                                            ago</span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">Supplier:
                                        {{ $purchase->supplier->name ?? 'N/A' }}</p>
                                    <div class="flex justify-between items-end mt-1">
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">OMR
                                            {{ number_format($purchase->total_amount, 2) }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="flex items-center justify-center h-full py-8 text-gray-500">No recent
                                    purchases found.</div>
                            @endforelse
                        </div>
                    </x-card>

                    {{-- ===== Recent Sales (Corrected) ===== --}}
                    <x-card title="Recent Sales Orders">
                        <div class="space-y-3 max-h-[22rem] overflow-y-auto pretty-scrollbar">
                            @forelse($recentSales as $order)
                                <div
                                    class="p-3 rounded-md bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <div class="flex justify-between items-start">
                                        <span
                                            class="text-sm font-semibold text-blue-600 dark:text-blue-400">#{{ $order->order_number }}</span>
                                        <span
                                            class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->diffForHumans(null, true) }}
                                            ago</span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">
                                        {{ $order->customer_details['name'] ?? 'Guest Customer' }}</p>
                                    <div class="flex justify-between items-end mt-1">
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">OMR
                                            {{ number_format($order->total_amount, 2) }}</span>
                                        <span
                                            class="px-2 py-0.5 text-xs font-medium rounded-full capitalize @switch($order->status) @case('fulfilled') bg-green-100 text-green-700 @break @default bg-gray-100 text-gray-700 @endswitch">{{ str_replace('_', ' ', $order->status) }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="flex items-center justify-center h-full py-8 text-gray-500">No recent
                                    orders found.</div>
                            @endforelse
                        </div>
                    </x-card>

                    {{-- Charts --}}
                    <x-card title="Sales by Category">
                        <div class="h-64 flex items-center justify-center" wire:ignore>
                            @if ($salesByCategoryLabels->isNotEmpty())
                                <div class="w-full max-w-sm"><canvas id="salesByCategoryChart"></canvas></div>
                            @else
                                <p class="text-gray-500">No sales by category data.</p>
                            @endif
                        </div>
                    </x-card>

                    <x-card title="Sales by Channel">
                        <div class="h-64 flex items-center justify-center" wire:ignore>
                            @if ($salesByChannelLabels->isNotEmpty())
                                <div class="w-full"><canvas id="salesByChannelChart"></canvas></div>
                            @else
                                <p class="text-gray-500">No sales by channel data.</p>
                            @endif
                        </div>
                    </x-card>
                </div>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/countup.js@2.8.0/dist/countUp.umd.js"></script>


            <script>
                document.addEventListener('livewire:init', () => {
                    let chartInstances = {};
                    const initCharts = (data) => {
                        Object.values(chartInstances).forEach(chart => chart.destroy());
                        chartInstances = {};

                        const darkMode = document.documentElement.classList.contains('dark');
                        const gridColor = darkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';
                        const textColor = darkMode ? 'rgba(255, 255, 255, 0.7)' : '#6B7280';
                        Chart.defaults.color = textColor;

                        const defaultChartColors = ['#F97316', '#3B82F6', '#8B5CF6', '#10B981', '#EF4444', '#6366F1',
                            '#F59E0B', '#0EA5E9'
                        ];
                        const defaultBackgroundColors = defaultChartColors.map(hex => `${hex}33`);

                        // --- Revenue Chart ---
                        const revenueCtx = document.getElementById('revenueOverTimeChart');
                        if (revenueCtx && data.revenueOverTimeLabels?.length > 0) {
                            chartInstances.revenue = new Chart(revenueCtx, {
                                type: 'line',
                                data: {
                                    labels: data.revenueOverTimeLabels,
                                    datasets: [{
                                        label: 'Revenue',
                                        data: data.revenueOverTimeData,
                                        borderColor: defaultChartColors[0],
                                        backgroundColor: defaultBackgroundColors[0],
                                        fill: true,
                                        tension: 0.4
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            grid: {
                                                color: gridColor
                                            }
                                        },
                                        x: {
                                            grid: {
                                                color: gridColor
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        // --- Sales by Category Chart ---
                        const salesByCategoryCtx = document.getElementById('salesByCategoryChart');
                        if (salesByCategoryCtx && data.salesByCategoryLabels?.length > 0) {
                            chartInstances.salesByCategory = new Chart(salesByCategoryCtx, {
                                type: 'doughnut',
                                data: {
                                    labels: data.salesByCategoryLabels,
                                    datasets: [{
                                        data: data.salesByCategoryData,
                                        backgroundColor: defaultChartColors
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        }
                                    }
                                }
                            });
                        }

                        // --- Sales by Channel Chart ---
                        const salesByChannelCtx = document.getElementById('salesByChannelChart');
                        if (salesByChannelCtx && data.salesByChannelLabels?.length > 0) {
                            chartInstances.salesByChannel = new Chart(salesByChannelCtx, {
                                type: 'bar',
                                data: {
                                    labels: data.salesByChannelLabels,
                                    datasets: [{
                                        label: 'Orders',
                                        data: data.salesByChannelData,
                                        backgroundColor: defaultBackgroundColors,
                                        borderColor: defaultChartColors,
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    indexAxis: 'y',
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        x: {
                                            beginAtZero: true,
                                            ticks: {
                                                precision: 0
                                            },
                                            grid: {
                                                color: gridColor
                                            }
                                        },
                                        y: {
                                            grid: {
                                                display: false
                                            }
                                        }
                                    }
                                }
                            });
                        }
                        // --- Monthly Target Chart ---
                        const monthlyTargetCtx = document.getElementById('monthlyTargetChart');
                        if (monthlyTargetCtx && data.monthlyTargetChartData) {
                            chartInstances.monthlyTarget = new Chart(monthlyTargetCtx, {
                                type: 'doughnut',
                                data: {
                                    datasets: [{
                                        data: data.monthlyTargetChartData,
                                        // A nice blue for achieved, and a subtle gray for the remainder
                                        backgroundColor: [
                                            '#3B82F6', // a vibrant blue
                                            '#E5E7EB' // a light gray
                                        ],
                                        // Make the borders match the background colors
                                        borderColor: [
                                            '#3B82F6',
                                            '#E5E7EB'
                                        ],
                                        borderWidth: 1,
                                        // This makes it a "gauge" by cutting out most of the circle
                                        circumference: 270,
                                        rotation: -135,
                                        cutout: '75%', // Makes the donut thinner or thicker
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        // Disable all default tooltips and legends
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            enabled: false
                                        }
                                    },
                                    animation: {
                                        // Add some subtle animation
                                        animateScale: true,
                                        animateRotate: true
                                    }
                                }
                            });
                        }
                    };

                    initCharts(@json($__data));
                    Livewire.on('update-charts', (event) => {
                        initCharts(event.data);
                    });
                });
            </script>
            <style>
                .pretty-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }

                .pretty-scrollbar::-webkit-scrollbar-track {
                    background: transparent;
                }

                .pretty-scrollbar::-webkit-scrollbar-thumb {
                    background: #cbd5e1;
                    border-radius: 3px;
                }

                .dark .pretty-scrollbar::-webkit-scrollbar-thumb {
                    background: #4b5563;
                }
            </style>
        @endpush
    </div>
</div>
