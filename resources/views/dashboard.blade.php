<x-layouts.livewire title="{{ __('Dashboard') }}">
    <div class="space-y-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    Dashboard
                </h1>
                <p class="mt-1 text-gray-500 dark:text-gray-400">
                    A real-time overview of your sales and inventory performance.
                </p>
            </div>

        </div>

        {{-- Financial Summary Section (Last 30 Days) --}}
        <div class="space-y-2">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200">Financial Summary (Last 30 Days)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <x-card>
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-green-100 dark:bg-green-700/30 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-600 dark:text-green-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200">Total Revenue</h3>
                    </div>
                    <p class="mt-3 text-3xl font-semibold {{ ($totalRevenueLast30Days ?? 0) >= 0 ? 'text-green-600 dark:text-green-500' : 'text-red-600 dark:text-red-500' }}">
                        {{ ($totalRevenueLast30Days ?? 0) >= 0 ? 'OMR' : '- OMR' }}
                        {{ number_format(abs($totalRevenueLast30Days ?? 0), 2) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Total sales revenue in the last 30 days.
                    </p>
                </x-card>

                {{-- <x-card>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total COGS</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white {{ ($costOfGoodsSold ?? 0) >= 0 ? 'text-green-600 dark:text-green-500' : 'text-red-600 dark:text-red-500' }}">
                        {{ ($costOfGoodsSold ?? 0) >= 0 ? 'OMR' : '- OMR' }} {{ number_format(abs($costOfGoodsSold ?? 0), 2) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Cost of goods sold in the last 30 days.
                    </p>
                </x-card> --}}
                {{-- <x-card>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Gross Profit</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white {{ ($grossProfitLast30Days ?? 0) >= 0 ? 'text-green-600 dark:text-green-500' : 'text-red-600 dark:text-red-500' }}">
                        {{ ($grossProfitLast30Days ?? 0) >= 0 ? 'OMR' : '- OMR' }} {{ number_format(abs($grossProfitLast30Days ?? 0), 2) }}
                    </p>
                     <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        (Revenue - COGS)
                    </p>
                </x-card> --}}
                <x-card>
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-orange-100 dark:bg-orange-700/30 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-orange-600 dark:text-orange-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200">Total Costs</h3>
                    </div>
                    <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">
                        OMR {{ number_format($totalCostLast30Days ?? 0, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Total costs including COGP and operational expenses in the last 30 days.

                    </p>
                </x-card>
                <x-card>
                    <div class="flex items-center space-x-3">
                        <div class="p-2 {{ ($netProfitLast30Days ?? 0) >= 0 ? 'bg-green-100 dark:bg-green-700/30' : 'bg-red-100 dark:bg-red-700/30' }} rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 {{ ($netProfitLast30Days ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.286-1.046a.75.75 0 00-.358-1.416l-1.48-.37M18.75 4.97l-1.48-.37M5.25 4.97A48.416 48.416 0 0112 4.5c2.291 0 4.545.16 6.75.47m-13.5 0c-1.01.143-2.01.317-3 .52m3-.52l-2.286-1.046a.75.75 0 01.358-1.416l1.48-.37M5.25 4.97l1.48-.37M9 11.25l3-3 3 3M9 11.25l-3 3m3-3l3-3" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200">Net Profit</h3>
                    </div>
                    <p class="mt-3 text-3xl font-semibold {{ ($netProfitLast30Days ?? 0) >= 0 ? 'text-green-600 dark:text-green-500' : 'text-red-600 dark:text-red-500' }}">
                        OMR {{ number_format($netProfitLast30Days ?? 0, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Profit Margin: <span
                            class="font-semibold {{ ($profitMarginLast30Days ?? 0) >= 0 ? 'text-green-600 dark:text-green-500' : 'text-red-600 dark:text-red-500' }}">{{ number_format($profitMarginLast30Days ?? 0, 1) }}%</span>
                    </p>
                </x-card>
                {{-- <x-card>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sales Orders</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                        {{ $salesCountLast30Days ?? 0 }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Total sales orders in the last 30 days.
                    </p>
                </x-card> --}}

            </div>
        </div>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-8">

            {{-- Main Content Area --}}
            <div class="lg:col-span-2 xl:col-span-3 space-y-8">
                {{-- Top Row KPI Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">


                    {{-- Purchase Orders --}}
                    <x-kpi-card title="Total Purchase Orders" value="{{ $purchaseOrdersCountLast30Days ?? 0 }}"
                        color="indigo" change="{{ $purchaseOrdersCountChangePercentage ?? 0 }}"
                        changeType="{{ ($purchaseOrdersCountChangePercentage ?? 0) >= 0 ? 'increase' : 'decrease' }}"
                        changeText="vs prev 30d">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                            </svg>
                        </x-slot:icon>
                    </x-kpi-card>


                    <x-kpi-card title="Total Cost of Goods Sold" value="OMR {{ number_format($costOfGoodsSold ?? 0, 2) }}"
                        color="red" change="{{ $costOfGoodsSoldChangePercentage ?? 0 }}"
                        changeType="{{ ($costOfGoodsSoldChangePercentage ?? 0) >= 0 ? 'increase' : 'decrease' }}"
                        changeText="vs prev 30d">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M12 9v3m0 0v3m0-3h3m-3 0H9m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </x-slot:icon>
                    </x-kpi-card>

                    {{-- operational cost --}}
                    <x-kpi-card title="Operational Cost" value="OMR {{ number_format($operationalCost ?? 0, 2) }}"
                        color="orange" change="{{ $operationalCostChangePercentage ?? 0 }}"
                        changeType="{{ ($operationalCostChangePercentage ?? 0) >= 0 ? 'increase' : 'decrease' }}"
                        changeText="vs prev 30d">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M12 9v3m0 0v3m0-3h3m-3 0H9m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </x-slot:icon>
                    </x-kpi-card>

                    <x-kpi-card title="Profit Margin" value="{{ number_format($profitMarginLast30Days ?? 0, 1) }}%"
                        color="{{ ($profitMarginLast30Days ?? 0) >= config('business.profit_margin_target', 15) ? 'teal' : (($profitMarginLast30Days ?? 0) > 0 ? 'yellow' : 'red') }}"
                        {{-- Example conditional coloring --}} changeText="last 30d">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6">
                                <path fill-rule="evenodd"
                                    d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5A.75.75 0 014.5 3.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM3.75 7.5A.75.75 0 014.5 6.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM3.75 10.5A.75.75 0 014.5 9.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM3.75 13.5A.75.75 0 014.5 12.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM3.75 16.5A.75.75 0 014.5 15.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM17.06 5.272a.75.75 0 011.06 0l3.75 3.75a.75.75 0 010 1.06l-3.75 3.75a.75.75 0 11-1.06-1.06l2.47-2.47H13.5a.75.75 0 010-1.5h5.94l-2.47-2.47a.75.75 0 010-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </x-slot:icon>
                    </x-kpi-card>



                    <x-kpi-card title="Total Sales Orders" value="{{ $salesCountLast30Days ?? 0 }}" color="blue"
                        change="{{ $salesCountChangePercentage ?? 0 }}"
                        changeType="{{ ($salesCountChangePercentage ?? 0) >= 0 ? 'increase' : 'decrease' }}"
                        changeText="vs prev 30d">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </x-slot:icon>
                    </x-kpi-card>


                    <x-kpi-card title="Average Order Value" value="OMR {{ number_format($averageOrderValue ?? 0, 2) }}"
                        color="green" change="{{ $averageOrderValueChangePercentage ?? 0 }}"
                        changeType="{{ ($averageOrderValueChangePercentage ?? 0) >= 0 ? 'increase' : 'decrease' }}"
                        changeText="vs prev 30d">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                            </svg>
                        </x-slot:icon>
                    </x-kpi-card>

                    <x-kpi-card title="New Customers" value="{{ $newCustomersLast30Days ?? 0 }}" color="purple"
                        change="{{ $newCustomersChangePercentage ?? 0 }}"
                        changeType="{{ ($newCustomersChangePercentage ?? 0) >= 0 ? 'increase' : 'decrease' }}"
                        changeText="vs prev 30d">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </x-slot:icon>
                    </x-kpi-card>
                    {{-- low stock items --}}
                    <x-kpi-card title="Low Stock Items" value="{{ $lowStockItemsCountLast30Days ?? 0 }}"
                        color="purple" change="{{ $lowStockItemsCountChangePercentage ?? 0 }}"
                        changeType="{{ ($lowStockItemsCountChangePercentage ?? 0) >= 0 ? 'increase' : 'decrease' }}"
                        changeText="vs prev 30d">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/
                            svg" class="h-6 w-6"
                                fill="none" viewBox="0 0 24
                            24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3m0 0v3m0-3h3m-3 0H9m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </x-slot:icon>
                    </x-kpi-card>


                </div>

                <x-card title="Revenue Analytics (Last 30 Days)">
                    <div class="h-80">
                        @if (!empty($revenueOverTimeLabels) && count($revenueOverTimeLabels) >= 1)
                            <canvas id="revenueOverTimeChart"></canvas>
                        @else
                            <div
                                class="flex flex-col items-center justify-center h-full text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mb-2 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 3v11.25A2.25 2.25 0 006 16.5h12M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-12m6-3.75h.008v.008H12v-.008z" />
                                </svg>
                                <p>No revenue data for the selected period.</p>
                            </div>
                        @endif
                    </div>
                </x-card>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <x-card title="Most Profitable Products (Last 30 Days)">
                        <div class="overflow-x-auto max-h-96">
                            @if ($mostProfitableVariants && $mostProfitableVariants->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0 z-10">
                                        <tr>
                                            <th scope="col"
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Product Variant</th>
                                            <th scope="col"
                                                class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Total Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($mostProfitableVariants as $variant)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $variant->productVariant->product->name }} <br>
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-gray-400">{{ $variant->productVariant->variant_name }}</span>
                                                </td>
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-500 font-mono">
                                                    OMR {{ number_format($variant->total_profit, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div
                                    class="flex flex-col items-center justify-center h-full py-8 text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="w-10 h-10 mb-2 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v12m-3-2.818.879.879A1.5 1.5 0 0010.5 17h3a1.5 1.5 0 001.121-.44l.879-.879M12 21a9 9 0 11-9-9 4.5 4.5 0 005 3.903s-1.255-.001-1.488-.001c-.242 0-.4-.218-.4-.488V12a1 1 0 011-1h1V9.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5V11h1a1 1 0 011 1v1.012c0 .27-.158.488-.4.488-.233 0-1.488.001-1.488.001A4.5 4.5 0 0012 21z" />
                                    </svg>
                                    <p>No profit data for products in this period.</p>
                                </div>
                            @endif
                        </div>
                    </x-card>

                    <x-card title="Top Selling Products (by Qty, Last 30 Days)">
                        <div class="overflow-x-auto max-h-96">
                            @if ($topSellingVariants && $topSellingVariants->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0 z-10">
                                        <tr>
                                            <th scope="col"
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Product Variant</th>
                                            <th scope="col"
                                                class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Units Sold</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($topSellingVariants as $variant)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $variant->productVariant->product->name }} <br>
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-gray-400">{{ $variant->productVariant->variant_name }}</span>
                                                </td>
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700 dark:text-gray-300 font-mono">
                                                    {{ number_format($variant->total_quantity_sold) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div
                                    class="flex flex-col items-center justify-center h-full py-8 text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="w-10 h-10 mb-2 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                    <p>No product sales data for this period.</p>
                                </div>
                            @endif
                        </div>
                    </x-card>
                    {{-- letest purchases --}}
                    <x-card title="Recent Purchases">
                        <div class="space-y-3 max-h-[22rem] overflow-y-auto pretty-scrollbar">
                            @forelse($recentPurchases as $purchase)
                                <div
                                    class="p-3 rounded-md bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">PO
                                            #{{ $purchase->id_formatted ?? $purchase->id }}</span>
                                        <span
                                            class="text-xs text-gray-500 dark:text-gray-400">{{ $purchase->created_at->diffForHumans(null, true) }}
                                            ago</span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">
                                        Supplier: {{ $purchase->supplier->name ?? 'Unknown Supplier' }}
                                    </p>
                                    <div class="mt-2 space-y-1">
                                        @forelse($purchase->items as $item)
                                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                                {{ $item->productVariant->full_name ?? 'Unknown Item' }}
                                                (OMR {{ number_format($item->cost_price_per_unit, 2) }})
                                                x
                                                {{ $item->quantity }}</span>
                                            </p>
                                        @empty
                                            <p class="text-xs text-gray-500 dark:text-gray-400">No items in this PO.
                                            </p>
                                        @endforelse
                                    </div>
                                    <div class="flex justify-between items-end mt-3">
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">OMR
                                            {{ number_format($purchase->total_amount, 2) }}</span>
                                        <span
                                            class="px-2 py-0.5 text-xs font-medium rounded-full capitalize
                                    @switch($purchase->status ?? 'pending')
                                        @case('completed') bg-green-100 text-green-700 dark:bg-green-700/30 dark:text-green-400 @break
                                        @case('received') bg-blue-100 text-blue-700 dark:bg-blue-700/30 dark:text-blue-400 @break
                                        @case('processing') bg-yellow-100 text-yellow-700 dark:bg-yellow-700/30 dark:text-yellow-400 @break
                                        @case('pending') bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300 @break
                                        @case('cancelled') bg-red-100 text-red-700 dark:bg-red-700/30 dark:text-red-400 @break
                                        @default bg-indigo-100 text-indigo-700 dark:bg-indigo-700/30 dark:text-indigo-400
                                    @endswitch
                                ">

                                            {{ str_replace('_', ' ', $purchase->status ?? 'Pending') }}
                                        </span>

                                    </div>
                                </div>
                            @empty
                                <div
                                    class="flex flex-col items-center justify-center h-full
                            py-8 text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="w-10 h-10 mb-2 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 6.75h7.5M8.25 12h7.5m-7.5 5.25h7.5M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 17.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                    <p>No recent purchases found.</p>
                                </div>
                            @endforelse
                        </div>
                    </x-card>

                    <x-card title="Recent Sales Orders">
                        <div class="space-y-3 max-h-[22rem] overflow-y-auto pretty-scrollbar">
                            @forelse($recentSalesOrders as $order)
                                <div
                                    class="p-3 rounded-md bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <span
                                            class="text-sm font-semibold text-blue-600 dark:text-blue-400">#{{ $order->id_formatted ?? $order->id }}</span>
                                        <span
                                            class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->diffForHumans(null, true) }}
                                            ago</span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">
                                        {{ $order->customer->name ?? ($order->customer_details['name'] ?? 'Guest Customer') }}
                                    </p>
                                    <div class="mt-2 space-y-1">
                                        @forelse($order->items as $item)
                                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                                {{ $item->productVariant->full_name ?? 'Unknown Item' }}
                                                (OMR {{ number_format($item->price_per_unit, 2) }})
                                                x
                                                {{ $item->quantity }}</span>
                                            </p>
                                        @empty
                                            <p class="text-xs text-gray-500 dark:text-gray-400">No items in this order.
                                            </p>
                                        @endforelse
                                    </div>
                                    <div class="flex justify-between items-end mt-1">
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">OMR
                                            {{ number_format($order->total_amount, 2) }}</span>
                                        <span
                                            class="px-2 py-0.5 text-xs font-medium rounded-full capitalize
                                    @switch($order->status ?? 'pending')
                                        @case('completed') bg-green-100 text-green-700 dark:bg-green-700/30 dark:text-green-400 @break
                                        @case('shipped') bg-blue-100 text-blue-700 dark:bg-blue-700/30 dark:text-blue-400 @break
                                        @case('processing') bg-yellow-100 text-yellow-700 dark:bg-yellow-700/30 dark:text-yellow-400 @break
                                        @case('pending') bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300 @break
                                        @case('cancelled') bg-red-100 text-red-700 dark:bg-red-700/30 dark:text-red-400 @break
                                        @default bg-indigo-100 text-indigo-700 dark:bg-indigo-700/30 dark:text-indigo-400
                                    @endswitch
                                ">
                                            {{ str_replace('_', ' ', $order->status ?? 'Pending') }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="flex flex-col items-center justify-center h-full
                            py-8 text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="w-10 h-10 mb-2 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 6.75h7.5M8.25 12h7.5m-7.5 5.25h7.5M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 17.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                    <p>No recent orders found.</p>
                                </div>
                            @endforelse
                        </div>
                    </x-card>


                </div>


            </div>

            {{-- Right Sidebar --}}
            <div class="lg:col-span-1 xl:col-span-1 space-y-8">

                {{-- Somewhere in your dashboard --}}
                <x-card title="Monthly Target">
                    <div class="relative h-64 md:h-72 flex flex-col items-center justify-center">
                        {{-- Container for chart and center text --}}
                        <canvas id="monthlyTargetChart" class="absolute top-0 left-0"></canvas>
                        {{-- Text will be drawn by Chart.js or a plugin --}}
                    </div>
                    @php
                        $currentMonthName = now()->format('F');
                    @endphp
                    <div class="text-center mt-[-2rem] md:mt-[0rem] relative z-10">
                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                            {{ $currentMonthName ?? 'Current Month' }} Target</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 px-4">

                        </p>
                    </div>
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
                                OMR {{ number_format($currentRevenueForMonth ?? 0, 0) }}
                            </p>
                        </div>
                    </div>
                </x-card>
                {{-- <x-card title="Recent Sales Orders">
                    <div class="space-y-3 max-h-[22rem] overflow-y-auto pretty-scrollbar">
                        @forelse($recentSalesOrders as $order)
                            <div
                                class="p-3 rounded-md bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex justify-between items-start">
                                    <span
                                        class="text-sm font-semibold text-blue-600 dark:text-blue-400">#{{ $order->id_formatted ?? $order->id }}</span>
                                    <span
                                        class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->diffForHumans(null, true) }}
                                        ago</span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">
                                    {{ $order->customer->name ?? ($order->customer_details['name'] ?? 'Guest Customer') }}
                                </p>
                                <div class="flex justify-between items-end mt-1">
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">OMR
                                        {{ number_format($order->total_amount, 2) }}</span>
                                    <span
                                        class="px-2 py-0.5 text-xs font-medium rounded-full capitalize
                                    @switch($order->status ?? 'pending')
                                        @case('completed') bg-green-100 text-green-700 dark:bg-green-700/30 dark:text-green-400 @break
                                        @case('shipped') bg-blue-100 text-blue-700 dark:bg-blue-700/30 dark:text-blue-400 @break
                                        @case('processing') bg-yellow-100 text-yellow-700 dark:bg-yellow-700/30 dark:text-yellow-400 @break
                                        @case('pending') bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300 @break
                                        @case('cancelled') bg-red-100 text-red-700 dark:bg-red-700/30 dark:text-red-400 @break
                                        @default bg-indigo-100 text-indigo-700 dark:bg-indigo-700/30 dark:text-indigo-400
                                    @endswitch
                                ">
                                        {{ str_replace('_', ' ', $order->status ?? 'Pending') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div
                                class="flex flex-col items-center justify-center h-full py-8 text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mb-2 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 6.75h7.5M8.25 12h7.5m-7.5 5.25h7.5M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 17.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <p>No recent orders found.</p>
                            </div>
                        @endforelse
                    </div>
                </x-card> --}}



                <x-card title="Sales by Category (Revenue, Last 30 Days)">
                    <div class="h-80 flex items-center justify-center">
                        @if (!empty($salesByCategoryChartLabels))
                            <div class="w-full max-w-sm"><canvas id="salesByCategoryChart"></canvas></div>
                        @else
                            <div
                                class="flex flex-col items-center justify-center h-full text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mb-2 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25A2.25 2.25 0 0113.5 8.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                </svg>
                                <p>No sales by category data.</p>
                            </div>
                        @endif
                    </div>
                </x-card>

                <x-card title="Sales by Channel (Orders, Last 30 Days)">
                    <div class="h-80 flex items-center justify-center">
                        @if (!empty($salesByChannelLabels))
                            <div class="w-full"><canvas id="salesByChannelChart"></canvas></div>
                        @else
                            <div
                                class="flex flex-col items-center justify-center h-full text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mb-2 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                                <p>No sales by channel data.</p>
                            </div>
                        @endif
                    </div>
                </x-card>

                <x-card title="Inventory Value by Category">
                    <div class="h-80 flex items-center justify-center">
                        @if (!empty($inventoryValueByCategoryLabels))
                            <div class="w-full max-w-sm"><canvas id="inventoryValueByCategoryChart"></canvas></div>
                        @else
                            <div
                                class="flex flex-col items-center justify-center h-full text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mb-2 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                </svg>
                                <p>No inventory value data.</p>
                            </div>
                        @endif
                    </div>
                </x-card>

                <x-card title="Quick Stats">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Active
                                Customers</span>
                            <span
                                class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $totalCustomers }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending POs (Last
                                30d)</span>
                            <span
                                class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $pendingPOCount ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed POs (Last
                                30d)</span>
                            <span
                                class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $completedPOCount ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">PO Value (Last
                                30d)</span>
                            <span class="text-lg font-bold text-gray-800 dark:text-gray-200">OMR
                                {{ number_format($purchaseOrderValue ?? 0, 0) }}</span>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js">
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const darkMode = document.documentElement.classList.contains('dark');
                const gridColor = darkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';
                const textColor = darkMode ? 'rgba(255, 255, 255, 0.7)' : '#6B7280'; // Tailwind gray-500
                const titleColor = darkMode ? 'rgba(255, 255, 255, 0.9)' : '#374151'; // Tailwind gray-700
                Chart.register(ChartDataLabels);
                Chart.defaults.font.family = "'Instrument Sans', sans-serif"; // Ensure this font is loaded
                Chart.defaults.color = textColor;
                Chart.defaults.borderColor = gridColor;

                const omrCurrencyTooltip = (context) => {
                    let label = context.dataset.label || context.label || '';
                    if (label) label += ': ';
                    let value = context.parsed?.y ?? context.parsed?.x ?? context.raw;
                    if (value !== null && !isNaN(value)) {
                        label +=
                            `OMR ${Number(value).toLocaleString('en-OM', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                    }
                    return label;
                };

                const omrCurrencyAxisTick = (value) =>
                    `OMR ${Number(value).toLocaleString('en-OM', {notation: 'compact', compactDisplay: 'short'})}`;
                const countAxisTick = (value) => Number(value).toLocaleString('en-OM', {
                    notation: 'compact',
                    compactDisplay: 'short'
                });

                const defaultChartColors = ['#F97316', '#3B82F6', '#8B5CF6', '#10B981', '#EF4444', '#6366F1', '#F59E0B',
                    '#0EA5E9'
                ]; // Added more colors
                const defaultBackgroundColors = defaultChartColors.map(hex => `${hex}33`); // ~20% opacity

                const commonChartOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                        },
                        tooltip: {
                            backgroundColor: darkMode ? 'rgba(31, 41, 55, 0.9)' :
                            'rgba(255,255,255,0.9)', // dark:bg-gray-800, bg-white
                            titleColor: titleColor,
                            bodyColor: textColor,
                            borderColor: gridColor,
                            borderWidth: 1,
                            padding: 10,
                            cornerRadius: 4,
                            boxPadding: 4,
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: textColor
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                color: textColor
                            }
                        }
                    }
                };

                const commonDoughnutOptions = {
                    ...commonChartOptions,
                    cutout: '80%',
                    plugins: {
                        ...commonChartOptions.plugins,
                        legend: {
                             position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 15,
                                        color: '#0F172A',
                                    },
                                    display: true
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false
                        }
                    } // No axes for doughnut
                };


                const revenueCtx = document.getElementById('revenueOverTimeChart');
                const revenueLabels = @json($revenueOverTimeLabels ?? []);
                const revenueData =
                    @json($revenueOverTimeData ?? []); // This should now be an array of numbers after PHP fix


                if (revenueCtx && revenueLabels.length >= 1) {
                    new Chart(revenueCtx, {
                        type: 'line',
                        data: {
                            labels: revenueLabels,
                            datasets: [{
                                label: 'Revenue',
                                data: revenueData,
                                borderColor: defaultChartColors[0],
                                backgroundColor: defaultBackgroundColors[0],
                                fill: true,
                                tension: 0.4,
                                pointRadius: revenueLabels.length === 1 ? 3 :
                                0, // Show point if only one
                                pointHoverRadius: 6,
                                pointHitRadius: 20,
                            }]
                        },
                        // Your existing options:
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                     position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 15,
                                        color: '#0F172A',
                                    },
                                    display: true
                                },
                                tooltip: {
                                    backgroundColor: darkMode ? 'rgba(31, 41, 55, 0.9)' :
                                        'rgba(255,255,255,0.9)',
                                    titleColor: titleColor,
                                    bodyColor: textColor,
                                    borderColor: gridColor,
                                    borderWidth: 1,
                                    padding: 10,
                                    cornerRadius: 4,
                                    boxPadding: 4,
                                    callbacks: {
                                        label: omrCurrencyTooltip
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: textColor
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: gridColor
                                    },
                                    ticks: {
                                        color: textColor,
                                        callback: omrCurrencyAxisTick
                                    }
                                }
                            }
                        }
                    });
                    console.log("Chart initialized for revenue.");
                } else {
                    console.log("Chart NOT initialized for revenue. Ctx:", !!revenueCtx, "Labels length:", revenueLabels
                        .length);
                    // If it still hits here, the canvas element might be missing or revenueLabels is empty
                }

                const salesByCategoryCtx = document.getElementById('salesByCategoryChart');
                const salesByCategoryLabelsJs = @json($salesByCategoryChartLabels ?? []); // Renamed for clarity within this block
                const salesByCategoryDataValuesJs = @json($salesByCategoryChartDataValues ?? []); // Renamed

                if (salesByCategoryCtx && salesByCategoryLabelsJs.length > 0) {
                    console.log("Initializing Sales By Category Chart with:", salesByCategoryLabelsJs,
                        salesByCategoryDataValuesJs);
                    new Chart(salesByCategoryCtx, {
                        type: 'doughnut',
                        data: {
                            labels: salesByCategoryLabelsJs,
                            datasets: [{
                                data: salesByCategoryDataValuesJs,
                                backgroundColor: defaultChartColors, // Uses defaultChartColors[0] for the single segment
                                borderColor: darkMode ? '#1f2937' : '#fff',
                                borderWidth: 3,
                                hoverOffset: 8,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '80%', // From your commonDoughnutOptions
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 15,
                                        color: '#0F172A',
                                    },
                                    display: true
                                },
                                tooltip: {
                                    callbacks: {
                                        label: omrCurrencyTooltip
                                    }
                                },
                                datalabels: {
                                    display: true,
                                    color: darkMode ? 'rgba(255,255,255,0.9)' :
                                    '#1F2937',
                                    font: {
                                        weight: 'bold',
                                        size: 10, // Adjust size as needed
                                    },
                                    align: 'center', // For doughnut, 'center' is good
                                    anchor: 'center', // 'center' of the segment
                                    offset: 0, // No offset from the anchor
                                    formatter: (value, context) => {
                                        // value is 40
                                        // context.chart.data.labels is ['Skin Care']
                                        // context.dataIndex is 0
                                        let label = context.chart.data.labels[context.dataIndex] || '';
                                        let formattedVal = 'OMR ' + Number(value).toLocaleString('en-OM', {
                                            minimumFractionDigits: 2
                                        });

                                        // If only one segment, display label and value, perhaps centered
                                        if (context.chart.data.labels.length === 1) {
                                            return label + '\n' + formattedVal; // \n for a new line
                                        }

                                        // Fallback for multiple segments (you can customize this further)
                                        const total = context.chart.data.datasets[0].data.reduce((acc,
                                            val) => acc + Number(val), 0);
                                        const percentage = total > 0 ? ((Number(value) / total) * 100)
                                            .toFixed(1) + '%' : '0%';
                                        return label + '\n' + percentage;
                                    },
                                }
                            }
                        }
                        // If you don't register globally, you'd pass it here:
                        // plugins: [ChartDataLabels]
                    });
                } else {
                    console.log("Sales By Category Chart NOT initialized or no data.");
                }

                // Monthly Target Chart
                const monthlyTargetCtx = document.getElementById('monthlyTargetChart');
                const percentageAchievedJs = {{ $percentageAchieved ?? 0 }};
                const percentageChangeJs = {{ $percentageChangeFromLastMonth ?? 0 }};

                if (monthlyTargetCtx) {
                    new Chart(monthlyTargetCtx, {
                        type: 'doughnut',
                        data: {
                            // No labels needed if we're not showing a legend or tooltips for segments
                            // labels: ['Achieved', 'Remaining'],
                            datasets: [{
                                data: @json($monthlyTargetChartData ?? [0, 100]),
                                backgroundColor: @json($monthlyTargetChartColors ?? ['#F97316', '#FFEDD5']),
                                borderColor: darkMode ? '#1f2937' :
                                '#fff', // Or your card background color
                                borderWidth: 3,
                                cutout: '75%', // Makes it a thinner doughnut, like a gauge
                                circumference: 270, // Makes it an arc (e.g. 270 for 3/4 circle, 180 for semi-circle)
                                rotation: -
                                    135, // Rotates the start of the arc ( -90 for top, -135 for 7:30 position start)
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 15,
                                        color: '#0F172A',
                                    },
                                    display: true
                                },
                                tooltip: {
                                    enabled: true // Disable tooltips for segments
                                },
                                doughnutCenterText: { // Config for our custom plugin
                                    display: true,
                                    mainText: percentageAchievedJs.toFixed(0) + '%',
                                    mainFont: `bold ${window.innerWidth < 768 ? '28px' : '36px'} Inter, sans-serif`, // Responsive font size
                                    mainColor: darkMode ? 'rgba(255,255,255,0.9)' :
                                    '#1F2937', // Tailwind gray-800
                                    subText: (percentageChangeJs >= 0 ? '+' : '') + percentageChangeJs.toFixed(
                                        2) + '% from last month',
                                    subFont: `normal ${window.innerWidth < 768 ? '10px' : '12px'} Inter, sans-serif`,
                                    subColor: percentageChangeJs >= 0 ? (darkMode ? '#34D399' : '#10B981') : (
                                        darkMode ? '#F87171' : '#EF4444'
                                    ) // Green for positive, Red for negative
                                }
                            }
                        }
                    });
                }

                const salesByChannelCtx = document.getElementById('salesByChannelChart');
                if (salesByChannelCtx && @json($salesByChannelLabels ?? []).length > 0) {
                    new Chart(salesByChannelCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($salesByChannelLabels ?? []),
                            datasets: [{
                                label: 'Orders',
                                data: @json($salesByChannelData ?? []),
                                backgroundColor: defaultBackgroundColors,
                                borderColor: defaultChartColors,
                                borderWidth: 2,
                                borderRadius: 4,
                            }]
                        },
                        options: {
                            ...commonChartOptions,
                            plugins: {
                                ...commonChartOptions.plugins, // Ensures other common plugins like tooltip are kept
                                legend: {
                                    display: true,
                                    position: 'top', // Default for bar charts, explicit for clarity
                                    labels: {
                                        color: '#0F172A', // Use the dynamic textColor for legend labels
                                        boxWidth: 12,
                                        padding: 15
                                    }
                                }
                            },
                            indexAxis: 'y',
                            scales: {
                                ...commonChartOptions.scales,
                                y: {
                                    ...commonChartOptions.scales.y,
                                    grid: {
                                        display: false
                                    }
                                },
                                x: {
                                    ...commonChartOptions.scales.x,
                                    ticks: {
                                        precision: 0,
                                        callback: countAxisTick
                                    }
                                }
                            }
                        }
                    });
                }

                const inventoryValueCtx = document.getElementById('inventoryValueByCategoryChart');
                if (inventoryValueCtx && @json($inventoryValueByCategoryLabels ?? []).length > 0) {
                    new Chart(inventoryValueCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($inventoryValueByCategoryLabels ?? []),
                            datasets: [{
                                data: @json($inventoryValueByCategoryData ?? []),
                                backgroundColor: defaultChartColors.slice()
                                    .reverse(), // Use a different set or reverse
                                borderColor: darkMode ? '#1f2937' : '#fff',
                                borderWidth: 3,
                                hoverOffset: 8,
                            }]
                        },
                        options: {
                            ...commonDoughnutOptions,
                            plugins: {
                                ...commonDoughnutOptions.plugins,

                                tooltip: {
                                    callbacks: {
                                        label: omrCurrencyTooltip
                                    }
                                }
                            }
                        }
                    });
                }



                const inventoryByLocationCtx = document.getElementById('inventoryByLocationChart');
                if (inventoryByLocationCtx && @json($inventoryByLocationLabels ?? []).length > 0) {
                    new Chart(inventoryByLocationCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($inventoryByLocationLabels ?? []),
                            datasets: [{
                                data: @json($inventoryByLocationData ?? []),
                                backgroundColor: defaultChartColors.slice(
                                    2), // Use another slice of colors
                                borderColor: darkMode ? '#1f2937' : '#fff',
                                borderWidth: 3,
                                hoverOffset: 8,
                            }]
                        },
                        options: {
                            ...commonDoughnutOptions,
                            plugins: {
                                ...commonDoughnutOptions.plugins,
                                tooltip: {
                                    callbacks: {
                                        label: (context) => { // Custom tooltip for quantity
                                            let label = context.label || '';
                                            if (label) label += ': ';
                                            let value = context.raw;
                                            if (value !== null) {
                                                label += `${Number(value).toLocaleString()} units`;
                                            }
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
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

            .pretty-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }

            .dark .pretty-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #6b7280;
            }
        </style>
    @endpush
</x-layouts.livewire>
