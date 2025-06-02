<x-layouts.livewire title="{{ __('Dashboard') }}">
    <div class="py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            {{-- Header: Dashboard Title & Date Range --}}
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    Inventory Dashboard
                </h1>

            </div>

            {{-- KPI Section --}}
            <div class="space-y-8">
                {{-- Sales Performance KPIs --}}
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Sales Performance (Last 30 Days)</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-6">

                        {{-- Revenue --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenue</h3>
                                <span class="p-2 bg-blue-100 dark:bg-blue-700 rounded-full text-blue-500 dark:text-blue-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                </span>
                            </div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">OMR {{ number_format($totalRevenueLast30Days ?? 0, 2) }}</p>
                            <p class="text-xs mt-1 {{ ($revenueChangePercentage ?? 0) >= 0 ? 'text-green-500' : 'text-red-500' }} flex items-center">
                                @if(($revenueChangePercentage ?? 0) >= 0)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" /></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.707-7.293l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414z" clip-rule="evenodd" /></svg>
                                @endif
                                {{ number_format(abs($revenueChangePercentage ?? 0), 1) }}% vs prev 30d
                            </p>
                        </div>

                        {{-- COGS --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Cost of Goods Sold</h3>
                                <span class="p-2 bg-orange-100 dark:bg-orange-700 rounded-full text-orange-500 dark:text-orange-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 11.667 0l3.181-3.183m-4.994 0-3.182-3.182m0 0a3.375 3.375 0 0 0-4.243 0L6.51 7.71m0 0L3.328 4.53M8.25 4.5l3.182 3.182M12 12a3.375 3.375 0 0 0-4.243 0L6.51 13.79m0 0L3.328 16.97m0 0h4.992m2.986-4.993v4.992m0 0h4.992m-4.993 0 3.181-3.182m0 0a3.375 3.375 0 0 0 4.243 0l3.181-3.182m0 0L20.672 7.71m0 0h-4.992m2.986 4.992v-4.993" /></svg>
                                </span>
                            </div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">OMR {{ number_format($totalCogsLast30Days ?? 0, 2) }}</p>
                            <p class="text-xs mt-1 {{ ($cogsChangePercentage ?? 0) <= 0 ? 'text-green-500' : 'text-red-500' }} flex items-center">
                                @if(($cogsChangePercentage ?? 0) <= 0) <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.707-7.293l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414z" clip-rule="evenodd" /></svg>
                                @else <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" /></svg>
                                @endif
                                {{ number_format(abs($cogsChangePercentage ?? 0), 1) }}%
                                @if(($cogsChangePercentage ?? 0) <= 0) decrease @else increase @endif
                            </p>
                        </div>

                        {{-- Gross Profit --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Gross Profit</h3>
                                <span class="p-2 bg-green-100 dark:bg-green-700 rounded-full text-green-500 dark:text-green-300">
                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28L20.25 15M12 15V3m0 12l-4-4m4 4 4-4" /></svg>
                                </span>
                            </div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">OMR {{ number_format($grossProfitLast30Days ?? 0, 2) }}</p>
                             <p class="text-xs mt-1 {{ ($profitChangePercentage ?? 0) >= 0 ? 'text-green-500' : 'text-red-500' }} flex items-center">
                                @if(($profitChangePercentage ?? 0) >= 0) <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" /></svg>
                                @else <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.707-7.293l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414z" clip-rule="evenodd" /></svg>
                                @endif
                                {{ number_format(abs($profitChangePercentage ?? 0), 1) }}%
                                @if(($profitChangePercentage ?? 0) >= 0) increase @else decrease @endif
                            </p>
                        </div>

                        {{-- Sales Count --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                             <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Sales Count</h3>
                                <span class="p-2 bg-purple-100 dark:bg-purple-700 rounded-full text-purple-500 dark:text-purple-300">
                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                                </span>
                            </div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $salesCountLast30Days ?? 0 }}</p>
                            <p class="text-xs mt-1 {{ ($salesCountChangePercentage ?? 0) >= 0 ? 'text-green-500' : 'text-red-500' }} flex items-center">
                                @if(($salesCountChangePercentage ?? 0) >= 0) <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" /></svg>
                                @else <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.707-7.293l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414z" clip-rule="evenodd" /></svg>
                                @endif
                                {{ number_format(abs($salesCountChangePercentage ?? 0), 1) }}%
                                @if(($salesCountChangePercentage ?? 0) >= 0) increase @else decrease @endif
                            </p>
                        </div>

                        {{-- Avg Order Value --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                             <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg. Order Value</h3>
                                <span class="p-2 bg-indigo-100 dark:bg-indigo-700 rounded-full text-indigo-500 dark:text-indigo-300">
                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V13.5Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V18Zm2.498-6.75h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V13.5Zm0 2.25h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V18Zm2.504-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5Zm0 2.25h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V18Zm2.498-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5ZM8.25 6h7.5v2.25h-7.5V6ZM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0 0 12 2.25Z" /></svg>
                                </span>
                            </div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">OMR {{ number_format($averageOrderValueLast30Days ?? 0, 2) }}</p>
                            <p class="text-xs mt-1 {{ ($aovChangePercentage ?? 0) >= 0 ? 'text-green-500' : 'text-red-500' }} flex items-center">
                                @if(($aovChangePercentage ?? 0) >= 0) <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" /></svg>
                                @else <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.707-7.293l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414z" clip-rule="evenodd" /></svg>
                                @endif
                                {{ number_format(abs($aovChangePercentage ?? 0), 1) }}%
                                @if(($aovChangePercentage ?? 0) >= 0) increase @else decrease @endif
                            </p>
                        </div>

                         {{-- Profit Margin --}}
                         <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                             <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Profit Margin</h3>
                                <span class="p-2 bg-pink-100 dark:bg-pink-700 rounded-full text-pink-500 dark:text-pink-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c-1.01-.143-2.01-.317-3-.52m3 .52L2.62 15.696c-.122.499.106 1.028.589 1.202a5.989 5.989 0 0 0 2.031.352 5.989 5.989 0 0 0 2.031-.352c.483-.174.711-.703.59-1.202L5.25 4.971Z" /></svg>
                                </span>
                            </div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($profitMarginLast30Days ?? 0, 1) }}%</p>
                        </div>

                        {{-- Expected Revenue --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Expected Revenue</h3>
                                <span class="p-2 bg-cyan-100 dark:bg-cyan-700 rounded-full text-cyan-500 dark:text-cyan-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                                    </svg>
                                </span>
                            </div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">OMR {{ number_format($expectedRevenueNext30Days ?? 0, 2) }}</p>
                            <p class="text-xs mt-1 text-gray-400 dark:text-gray-500">Next 30 Days Projection</p>
                        </div>
                    </div>
                </div>

                {{-- Inventory & Purchasing KPIs --}}
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Inventory & Purchasing</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
                        {{-- Total Stock Units --}}
                         <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Stock Units</h3>
                                 <span class="p-2 bg-gray-100 dark:bg-gray-700 rounded-full text-gray-500 dark:text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10.5 11.25h3M12 15h.008M17.25 7.5h-10.5a.75.75 0 0 0-.75.75v2.25c0 .414.336.75.75.75h10.5a.75.75 0 0 0 .75-.75V8.25a.75.75 0 0 0-.75-.75Z" /></svg>
                                </span>
                            </div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($totalStockQuantity ?? 0) }}</p>
                            <p class="text-xs mt-1 text-gray-400 dark:text-gray-500">Total units in stock</p>
                        </div>

                        {{-- Inventory Value --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Inventory Value</h3>
                                 <span class="p-2 bg-teal-100 dark:bg-teal-700 rounded-full text-teal-500 dark:text-teal-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                                </span>
                            </div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">OMR {{ number_format($totalInventoryValue ?? 0, 2) }}</p>
                            <p class="text-xs mt-1 text-gray-400 dark:text-gray-500">Total cost value</p>
                        </div>

                        {{-- Low Stock Products --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Low Stock Products</h3>
                                 <span class="p-2 bg-yellow-100 dark:bg-yellow-700 rounded-full text-yellow-500 dark:text-yellow-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.008v.008H12v-.008Z" /></svg>
                                </span>
                            </div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $lowStockItemsCount ?? 0 }}</p>
                            <p class="text-xs mt-1 text-gray-400 dark:text-gray-500">Below {{ $lowStockThreshold ?? 10 }} units</p>
                        </div>

                        {{-- Out of Stock --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Out of Stock</h3>
                                 <span class="p-2 bg-red-100 dark:bg-red-700 rounded-full text-red-500 dark:text-red-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                </span>
                            </div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $outOfStockItemsCount ?? 0 }}</p>
                             <p class="text-xs mt-1 text-gray-400 dark:text-gray-500">Products at zero stock</p>
                        </div>

                        {{-- Purchase Orders --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Purchase Orders</h3>
                                <span class="p-2 bg-sky-100 dark:bg-sky-700 rounded-full text-sky-500 dark:text-sky-300">
                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677V6.75M12 12.75V6.75m0 6V6.75m0 6v3.75m-3.75 0H12m0 0h3.75m-3.75 0V18m0-3.75h3.75V18m-3.75 0h3.75m0-3.75h3.75M12 12.75v-3.75m0 3.75h3.75m-3.75 0H8.25m3.75 0v-3.75M8.25 9V6.75M12 6.75h3.75M8.25 6.75H6" /></svg>
                                </span>
                            </div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $totalPurchaseOrdersLast30Days ?? 0 }}</p>
                            <p class="text-xs mt-1 {{ ($poCountChangePercentage ?? 0) >= 0 ? 'text-green-500' : 'text-red-500' }} flex items-center">
                                @if(($poCountChangePercentage ?? 0) >= 0) <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" /></svg>
                                @else <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.707-7.293l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414z" clip-rule="evenodd" /></svg>
                                @endif
                                {{ number_format(abs($poCountChangePercentage ?? 0), 1) }}%
                                @if(($poCountChangePercentage ?? 0) >= 0) increase @else decrease @endif
                            </p>
                        </div>

                        {{-- PO Amount --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                             <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Purchase Orders Amount</h3>
                                <span class="p-2 bg-lime-100 dark:bg-lime-700 rounded-full text-lime-500 dark:text-lime-300">
                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>
                                </span>
                            </div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">OMR {{ number_format($totalPurchaseAmountLast30Days ?? 0, 2) }}</p>
                            <p class="text-xs mt-1 {{ ($poAmountChangePercentage ?? 0) >= 0 ? 'text-green-500' : 'text-red-500' }} flex items-center">
                                @if(($poAmountChangePercentage ?? 0) >= 0) <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" /></svg>
                                @else <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.707-7.293l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414z" clip-rule="evenodd" /></svg>
                                @endif
                                {{ number_format(abs($poAmountChangePercentage ?? 0), 1) }}%
                                @if(($poAmountChangePercentage ?? 0) >= 0) increase @else decrease @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Charts Section --}}
            <div class="mt-8 space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Revenue Over Time</h3>
                        <div class="h-80">
                            @if(!empty($revenueOverTimeLabels) && count($revenueOverTimeLabels) > 0 && !(count($revenueOverTimeLabels) == 1 && ($revenueOverTimeLabels[0] ?? '') == 'No Data'))
                                <canvas id="revenueOverTimeChart"></canvas>
                            @else
                                <p class="text-center text-gray-500 dark:text-gray-400 pt-16">No revenue data available for the selected period.</p>
                            @endif
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Gross Profit Over Time</h3>
                        <div class="h-80">
                             @if(!empty($grossProfitOverTimeLabels) && count($grossProfitOverTimeLabels) > 0 && !(count($grossProfitOverTimeLabels) == 1 && ($grossProfitOverTimeLabels[0] ?? '') == 'No Data'))
                                <canvas id="profitOverTimeChart"></canvas>
                            @else
                                <p class="text-center text-gray-500 dark:text-gray-400 pt-16">No profit data available for the selected period.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- REVENUE FORECAST CHART --}}
                <div class="grid grid-cols-1 gap-8">
                     <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Revenue: Actual vs. Forecast (Next 30 Days)</h3>
                        <div class="h-96">
                            @if(!empty($revenueForecastLabels) && count($revenueForecastLabels) > 0)
                                <canvas id="revenueForecastChart"></canvas>
                            @else
                                <p class="text-center text-gray-500 dark:text-gray-400 pt-16">Forecasting data not available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Sales by Category</h3>
                         <div class="h-80 flex items-center justify-center">
                            @if(!empty($salesByCategoryChartLabels) && count($salesByCategoryChartLabels) > 0 && !(count($salesByCategoryChartLabels) == 1 && ($salesByCategoryChartLabels[0] ?? '') == 'No Data'))
                                <div class="w-full max-w-sm"><canvas id="salesByCategoryChart"></canvas></div>
                            @else
                                <p class="text-center text-gray-500 dark:text-gray-400">No sales by category data.</p>
                            @endif
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Sales by Channel</h3>
                        <div class="h-80 flex items-center justify-center">
                            @if(!empty($salesByChannelLabels) && count($salesByChannelLabels) > 0 && !(count($salesByChannelLabels) == 1 && ($salesByChannelLabels[0] ?? '') == 'No Data'))
                                <div class="w-full max-w-sm"><canvas id="salesByChannelChart"></canvas></div>
                            @else
                                <p class="text-center text-gray-500 dark:text-gray-400">No sales by channel data.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tables Section --}}
            <div class="mt-8 space-y-8">
                 <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Top Selling Product Variants (Qty, Last 30 Days)</h3>
                        <div class="overflow-x-auto max-h-96">
                            @if($topSellingVariantsQty && $topSellingVariantsQty->count() > 0)
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product Variant</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty Sold</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($topSellingVariantsQty as $variant)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $variant->full_variant_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-300">{{ $variant->total_quantity_sold }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                                <p class="text-center text-gray-500 dark:text-gray-400 py-8">No top selling products in this period.</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Most Profitable Product Variants (Last 30 Days)</h3>
                        <div class="overflow-x-auto max-h-96">
                             @if($mostProfitableVariants && $mostProfitableVariants->count() > 0)
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product Variant</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Profit</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($mostProfitableVariants as $variant)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $variant->full_variant_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-300">OMR {{ number_format($variant->total_profit, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                             @else
                                <p class="text-center text-gray-500 dark:text-gray-400 py-8">No data on most profitable products in this period.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Recent Inventory Movements</h3>
                    <div class="max-h-96 overflow-y-auto">
                        @if($recentActivities && $recentActivities->count() > 0)
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentActivities as $activity)
                                    <li class="py-4">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm flex-grow">
                                                <p class="font-medium text-gray-800 dark:text-gray-100 truncate">
                                                    {{ $activity->productVariant->product->name ?? 'N/A' }} - {{ $activity->productVariant->variant_name ?? 'N/A' }}
                                                </p>
                                                <p>
                                                    <span class="capitalize p-1 text-xs rounded {{
                                                        $activity->quantity > 0 ? ($activity->type == 'purchase_receipt' || $activity->type == 'adjustment_in' || $activity->type == 'return_in' ? 'bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200' : 'bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-200') :
                                                        ($activity->type == 'sale' || $activity->type == 'adjustment_out' || $activity->type == 'stock_take_variance' ? 'bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200' : 'bg-yellow-100 dark:bg-yellow-800 text-yellow-700 dark:text-yellow-200')
                                                    }}">
                                                        {{ str_replace('_', ' ', $activity->type) }}: <span class="font-semibold">{{ $activity->quantity }}</span>
                                                    </span>
                                                    @if($activity->reason)
                                                        <span class="text-gray-600 dark:text-gray-400 text-xs italic">({{ Str::limit($activity->reason, 30) }})</span>
                                                    @endif
                                                </p>
                                                 @if($activity->referenceable)
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        Ref: {{ class_basename($activity->referenceable_type) }}
                                                        <a href="#" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                            #{{ $activity->referenceable->order_number ?? $activity->referenceable->reference_number ?? $activity->referenceable->id }}
                                                        </a>
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 text-right flex-shrink-0 ml-4">
                                                <p>{{ $activity->created_at->format('M d, Y H:i') }}</p>
                                                @if($activity->user)<p class="truncate">by {{ $activity->user->name }}</p>@endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-center text-gray-500 dark:text-gray-400 py-8">No recent inventory movements.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Date Range Picker Dependencies (jQuery, Moment.js are typical) --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script> --}}
    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> --}}

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const darkMode = document.documentElement.classList.contains('dark');
            const gridColor = darkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';
            const textColor = darkMode ? 'rgba(255, 255, 255, 0.7)' : '#6B7280';
            const titleColor = darkMode ? 'rgba(255, 255, 255, 0.9)' : '#374151';

            Chart.defaults.font.family = "'Instrument Sans', sans-serif";
            Chart.defaults.color = textColor;
            Chart.defaults.borderColor = gridColor;

            const omrCurrencyTooltip = (context) => {
                let label = context.dataset.label || context.label || '';
                if (label) label += ': ';
                let value = context.parsed.y ?? context.parsed.x ?? context.raw;
                if (value !== null && !isNaN(value)) {
                    label += `OMR ${Number(value).toLocaleString('en-OM', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                } else if (typeof value === 'string'){
                     label += value;
                }
                return label;
            };
            const omrCurrencyAxisTick = (value) => `OMR ${Number(value).toLocaleString('en-OM', {notation: 'compact', compactDisplay: 'short', minimumFractionDigits: 0, maximumFractionDigits:1 })}`;
            const integerAxisTick = (value) => Number.isInteger(Number(value)) ? Number(value) : '';

            const defaultChartColors = ['#3B82F6', '#F97316', '#10B981', '#8B5CF6', '#6366F1', '#EC4899', '#F59E0B', '#0EA5E9', '#D946EF', '#14B8A6', '#F43F5E', '#6EE7B7'];
            const defaultBackgroundColors = defaultChartColors.map(hex => hexToRgba(hex, 0.2));

            function hexToRgba(hex, alpha = 0.2) {
                const r = parseInt(hex.slice(1, 3), 16), g = parseInt(hex.slice(3, 5), 16), b = parseInt(hex.slice(5, 7), 16);
                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            }

            function createChart(ctx, type, labels, datasets, customOptions = {}) {
                 if (!ctx || !labels || labels.length === 0 || (labels.length === 1 && (labels[0] === 'No Data' || labels[0] === 'No Low Stock'))) {
                    console.warn(`Chart not created for canvas ID "${ctx.id}", missing context or valid labels.`);
                    return null;
                }
                const baseOptions = {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { position: (type === 'pie' || type === 'doughnut') ? 'top' : 'bottom', labels: { color: titleColor }},
                        tooltip: {
                            backgroundColor: darkMode ? 'rgba(31, 41, 55, 0.9)' : 'rgba(255,255,255,0.9)',
                            titleColor: darkMode ? '#E5E7EB' : '#1F2937', bodyColor: darkMode ? '#D1D5DB' : '#4B5563',
                            borderColor: darkMode ? 'rgba(75, 85, 99, 0.9)' : 'rgba(229, 231, 235, 0.9)', borderWidth: 1,
                            callbacks: { label: omrCurrencyTooltip }
                        }
                    },
                    scales: {},
                    interaction: { intersect: false, mode: 'index' },
                };
                if (type === 'line' || type === 'bar') {
                    baseOptions.scales = {
                        x: { grid: { color: gridColor, drawOnChartArea: type === 'bar' && customOptions.indexAxis !== 'y' ? false : true }, ticks: { color: textColor, autoSkip: true, maxTicksLimit: (labels.length > 15 ? 15: undefined) } },
                        y: { beginAtZero: true, grid: { color: gridColor, borderDash: [2,3] }, ticks: { color: textColor, callback: omrCurrencyAxisTick } }
                    };
                }
                if (type === 'bar' && customOptions.indexAxis === 'y') {
                     baseOptions.scales.x.ticks.callback = omrCurrencyAxisTick;
                     baseOptions.scales.y.ticks.callback = undefined;
                }
                return new Chart(ctx, { type: type, data: { labels: labels, datasets: datasets }, options: {...baseOptions, ...customOptions}});
            }

            // --- Initialize Charts ---
            createChart(document.getElementById('revenueOverTimeChart'), 'line', @json($revenueOverTimeLabels ?? ['No Data']),
                [{ label: 'Revenue', data: @json($revenueOverTimeData ?? [0]), borderColor: defaultChartColors[0], backgroundColor: defaultBackgroundColors[0], fill: true, tension: 0.3, pointRadius: 2, pointHoverRadius: 5 }]
            );
            createChart(document.getElementById('profitOverTimeChart'), 'line', @json($grossProfitOverTimeLabels ?? ['No Data']),
                [{ label: 'Gross Profit', data: @json($grossProfitOverTimeData ?? [0]), borderColor: defaultChartColors[2], backgroundColor: defaultBackgroundColors[2], fill: true, tension: 0.3, pointRadius: 2, pointHoverRadius: 5  }]
            );
             createChart(document.getElementById('revenueForecastChart'), 'line', @json($revenueForecastLabels ?? ['No Data']),
                [
                    { label: 'Actual Revenue', data: @json($revenueForecastActualData ?? []), borderColor: defaultChartColors[0], fill: false, tension: 0.3, pointRadius: 2, pointHoverRadius: 5, spanGaps: false },
                    { label: 'Projected Revenue', data: @json($revenueForecastProjectedData ?? []), borderColor: defaultChartColors[1], borderDash: [5, 5], fill: false, tension: 0.3, pointRadius: 2, pointHoverRadius: 5, spanGaps: false }
                ],
                { scales: { x: { ticks: { autoSkip: true, maxTicksLimit: 20 }}}}
            );
            createChart(document.getElementById('salesByCategoryChart'), 'doughnut', @json($salesByCategoryChartLabels ?? ['No Data']),
                [{ label: 'Sales Value', data: @json($salesByCategoryChartDataValues ?? [0]), backgroundColor: defaultChartColors, borderColor: darkMode ? '#374151' : '#fff', borderWidth: 2, hoverOffset: 8 }]
            );
            createChart(document.getElementById('salesByChannelChart'), 'pie', @json($salesByChannelLabels ?? ['No Data']),
                [{ label: 'Sales Count', data: @json($salesByChannelData ?? [0]), backgroundColor: defaultChartColors.slice(0, (@json($salesByChannelLabels ?? [])).length || 1), borderColor: darkMode ? '#374151' : '#fff', borderWidth: 2, hoverOffset: 8 }],
                { plugins: { tooltip: { callbacks: { label: (context) => `${context.label}: ${context.raw}` }}}} // Custom tooltip for count
            );
        });
    </script>
    @endpush
</x-layouts.livewire>
