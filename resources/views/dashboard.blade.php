@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
        {{-- Placeholder for Date Range Filter --}}
        <div>
            <input type="text" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm" placeholder="Select Date Range (UI Only)">
        </div>
    </div>
@endsection

@section('content')
    {{-- KPIs Section --}}
    <div class="mb-8">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-3">Sales Performance (Last 30 Days)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- KPI Card: Total Revenue --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-start space-x-4">
                <div class="flex-shrink-0 bg-green-100 text-green-600 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Revenue</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">OMR {{ number_format($totalRevenueLast30Days, 2) }}</p>
                    <p class="text-xs {{ $revenueChangePercentage >= 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $revenueChangePercentage >= 0 ? '+' : '' }}{{ number_format($revenueChangePercentage, 1) }}% vs prev. 30 days
                    </p>
                </div>
            </div>
            {{-- KPI Card: Total Sales Orders --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-start space-x-4">
                <div class="flex-shrink-0 bg-blue-100 text-blue-600 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Sales Orders</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($salesCountLast30Days) }}</p>
                </div>
            </div>
            {{-- KPI Card: Average Order Value --}}
             <div class="bg-white p-6 rounded-lg shadow-md flex items-start space-x-4">
                <div class="flex-shrink-0 bg-purple-100 text-purple-600 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Avg. Order Value</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">OMR {{ number_format($averageOrderValueLast30Days, 2) }}</p>
                </div>
            </div>
            {{-- KPI Card: Gross Profit --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-start space-x-4">
                 <div class="flex-shrink-0 {{ $grossProfitLast30Days >= 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Gross Profit</h3>
                    <p class="mt-1 text-2xl font-semibold {{ $grossProfitLast30Days >= 0 ? 'text-gray-900' : 'text-red-600' }}">OMR {{ number_format($grossProfitLast30Days, 2) }}</p>
                </div>
            </div>
            {{-- KPI Card: Total COGS --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-start space-x-4">
                 <div class="flex-shrink-0 bg-orange-100 text-orange-600 p-3 rounded-full">
                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993-1.263 12c-.07.665-.647 1.125-1.263 1.125h-8.174c-.616 0-1.193-.46-1.263-1.125l-1.263-12A1.125 1.125 0 0 1 4.5 9h15a1.125 1.125 0 0 1 1.106 1.507Z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total COGS</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">OMR {{ number_format($totalCogsLast30Days, 2) }}</p>
                </div>
            </div>
            {{-- KPI Card: Profit Margin --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-start space-x-4">
                 <div class="flex-shrink-0 {{ $profitMarginLast30Days >= 0 ? 'bg-teal-100 text-teal-600' : 'bg-red-100 text-red-600' }} p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Profit Margin</h3>
                    <p class="mt-1 text-2xl font-semibold {{ $profitMarginLast30Days >= 0 ? 'text-gray-900' : 'text-red-600' }}">{{ number_format($profitMarginLast30Days, 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-3">Inventory & Purchasing (Last 30 Days)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
             {{-- KPI Card: Total Inventory Value --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-start space-x-4">
                 <div class="flex-shrink-0 bg-cyan-100 text-cyan-600 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10.5 11.25h3M12 15h.008M17.25 7.5h-10.5a.75.75 0 0 0-.75.75v2.25c0 .414.336.75.75.75h10.5a.75.75 0 0 0 .75-.75V8.25a.75.75 0 0 0-.75-.75Z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Inventory Value</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">OMR {{ number_format($totalInventoryValue, 2) }}</p>
                </div>
            </div>
            {{-- KPI Card: Total Products --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-start space-x-4">
                 <div class="flex-shrink-0 bg-indigo-100 text-indigo-600 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Products</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $totalProducts }}</p>
                </div>
            </div>
            {{-- KPI Card: Low Stock Items --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-start space-x-4">
                 <div class="flex-shrink-0 bg-red-100 text-red-600 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.008v.008H12v-.008Z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Low Stock Items</h3>
                    <p class="mt-1 text-2xl font-semibold text-red-600">{{ $lowStockItemsCount }}</p>
                    <p class="mt-1 text-xs text-gray-500">Stock < {{ $lowStockThreshold }} units.</p>
                </div>
            </div>
            {{-- KPI Card: Total Purchase Orders --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-start space-x-4">
                <div class="flex-shrink-0 bg-yellow-100 text-yellow-600 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 5.25 6h1.508a2.251 2.251 0 0 1 2.248 2.192m-4.602.061a48.59 48.59 0 0 1-.044-1.061C3.373 4.876 3.75 4.5 4.255 4.5h5.455c.505 0 .882.376.928.873a48.42 48.42 0 0 1-.044 1.06M15 12H9m12 4.5h-9m5.25 3h-4.5M15 2.25H5.25c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h13.5c.621 0 1.125-.504 1.125-1.125V15M12 9v-3m0 3H9m3 0h3m-3 0v3m0-3V6m3 6v3m0-3h.008v.008H15V12Zm-3 0v3m0-3h.008v.008H12V12Zm-3 0v3m0-3h.008v.008H9V12Z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Purchase Orders</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($totalPurchaseOrdersLast30Days) }}</p>
                </div>
            </div>
            {{-- KPI Card: Total Purchase Amount --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-start space-x-4">
                <div class="flex-shrink-0 bg-lime-100 text-lime-600 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Purchase Amount</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">OMR {{ number_format($totalPurchaseAmountLast30Days, 2) }}</p>
                </div>
            </div>
             <div class="bg-white p-6 rounded-lg shadow-md flex items-start space-x-4">
                <div class="flex-shrink-0 bg-lime-100 text-lime-600 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">current Inventory</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900"> {{ number_format($currentInventory) }}</p>
                </div>
            </div>
        </div>
    </div>


    {{-- Recent Activities (Full Width)
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h3 class="text-lg font-medium text-gray-900">Recent Inventory Activities</h3>
        @if($recentActivities->count() > 0)
            <ul class="mt-3 space-y-3 max-h-96 overflow-y-auto">
                @foreach($recentActivities as $activity)
                    <li class="text-sm text-gray-700 p-3 bg-gray-50 hover:bg-gray-100 rounded-md border border-gray-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-semibold capitalize {{ $activity->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $activity->type }}</span>
                                <span class="font-bold">{{ $activity->quantity > 0 ? '+' : '' }}{{ $activity->quantity }}</span> x
                                <span class="font-medium text-gray-800">{{ $activity->productVariant->product->name ?? 'N/A' }} - {{ $activity->productVariant->variant_name ?? 'N/A' }}</span>
                                @if($activity->referenceable)
                                    <a href="#" class="text-xs text-indigo-500 hover:underline ml-1">(Ref: {{ class_basename($activity->referenceable_type) }} #{{ $activity->referenceable_id }})</a>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 flex-shrink-0 ml-2 text-right">{{ $activity->created_at->format('M d, H:i') }} <br> ({{ $activity->created_at->diffForHumans(null, true) }} ago)</div>
                        </div>
                         @if($activity->user) <div class="text-xs text-gray-500 mt-1">By: {{ $activity->user->name }}</div> @endif
                         @if($activity->location) <div class="text-xs text-gray-500">Location: {{ $activity->location->name }}</div> @endif
                         @if($activity->notes) <div class="text-xs text-gray-600 mt-1 italic">Notes: {{ Str::limit($activity->notes, 70) }}</div> @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-2 text-sm text-gray-600">No recent activities.</p>
        @endif
    </div> --}}


    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Over Time</h3>
            <div style="height: 320px;"><canvas id="revenueOverTimeChart"></canvas></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Gross Profit Over Time</h3>
            <div style="height: 320px;"><canvas id="grossProfitOverTimeChart"></canvas></div>
        </div>
    </div>
     <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Purchases Over Time</h3>
            <div style="height: 320px;"><canvas id="purchasesOverTimeChart"></canvas></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory Value by Category</h3>
            <div style="height: 320px;" class="flex justify-center items-center"><div class="w-full max-w-xs sm:max-w-sm"><canvas id="inventoryValueByCategoryChart"></canvas></div></div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
         <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Selling Variants (Qty)</h3>
             <div style="height: 320px;"><canvas id="topSellingVariantsQtyChart"></canvas></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Profitable Variants (Value)</h3>
            <div style="height: 320px;"><canvas id="topProfitableVariantsChart"></canvas></div>
        </div>
    </div>
     <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sales by Channel</h3>
            <div style="height: 320px;" class="flex justify-center items-center"><div class="w-full max-w-xs sm:max-w-sm"><canvas id="salesByChannelChart"></canvas></div></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top Low Stock Items (Current Qty)</h3>
            <div style="height: 320px;"><canvas id="topLowStockItemsChart"></canvas></div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script> {{-- For better date handling if needed --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const omrCurrencyTooltip = (context) => {
            let label = context.dataset.label || '';
            if (label) label += ': ';
            if (context.parsed.y !== null) label += `OMR ${Number(context.parsed.y).toLocaleString('en-OM', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            return label;
        };
        const omrCurrencyAxisTick = (value) => `OMR ${Number(value).toLocaleString('en-OM', {notation: 'compact', compactDisplay: 'short', minimumFractionDigits: 0, maximumFractionDigits:1 })}`; // Compact display for axis
        const defaultChartColors = [
            'rgba(54, 162, 235, 0.8)', 'rgba(255, 99, 132, 0.8)', 'rgba(75, 192, 192, 0.8)',
            'rgba(255, 206, 86, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)',
            'rgba(199, 199, 199, 0.8)', 'rgba(83, 102, 84, 0.8)'
        ];

        Chart.defaults.font.family = "'Instrument Sans', sans-serif"; // Match body font
        Chart.defaults.plugins.legend.position = 'bottom';


        function createLineChart(ctx, labels, datasetsOptions, yAxisCallback = omrCurrencyAxisTick, tooltipCallback = omrCurrencyTooltip) {
            if (!ctx) return;
            new Chart(ctx, {
                type: 'line', data: { labels: labels, datasets: datasetsOptions },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { callback: yAxisCallback } } },
                    plugins: { tooltip: { callbacks: { label: tooltipCallback } } }
                }
            });
        }
        function createBarChart(ctx, labels, datasetsOptions, indexAxis = 'x', xAxisCallback = null, yAxisCallback = null, legendDisplay = true, tooltipCallback = null) {
            if (!ctx) return;
            const options = {
                responsive: true, maintainAspectRatio: false, indexAxis: indexAxis,
                scales: { x: { beginAtZero: true }, y: { beginAtZero: true } },
                plugins: { legend: { display: legendDisplay } }
            };
            if (xAxisCallback) options.scales.x.ticks = { callback: xAxisCallback };
            if (yAxisCallback) options.scales.y.ticks = { callback: yAxisCallback };
            if (tooltipCallback) options.plugins.tooltip = { callbacks: { label: tooltipCallback } };

            new Chart(ctx, { type: 'bar', data: { labels: labels, datasets: datasetsOptions }, options: options });
        }
        function createPieDoughnutChart(ctx, labels, datasetsOptions, type = 'pie') {
            if (!ctx) return;
            new Chart(ctx, {
                type: type, data: { labels: labels, datasets: datasetsOptions },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } }
            });
        }

        createLineChart(document.getElementById('revenueOverTimeChart'), @json($revenueOverTimeLabels),
            [{ label: 'Daily Revenue', data: @json($revenueOverTimeData), borderColor: defaultChartColors[0], backgroundColor: defaultChartColors[0].replace('0.8', '0.2'), fill: true, tension: 0.1 }]
        );
        createLineChart(document.getElementById('grossProfitOverTimeChart'), @json($grossProfitOverTimeLabels),
            [{ label: 'Daily Gross Profit', data: @json($grossProfitOverTimeData), borderColor: defaultChartColors[2], backgroundColor: defaultChartColors[2].replace('0.8', '0.2'), fill: true, tension: 0.1 }]
        );
        createLineChart(document.getElementById('purchasesOverTimeChart'), @json($purchasesOverTimeLabels),
            [{ label: 'Daily Purchases', data: @json($purchasesOverTimeData), borderColor: defaultChartColors[3], backgroundColor: defaultChartColors[3].replace('0.8', '0.2'), fill: true, tension: 0.1 }]
        );

        createPieDoughnutChart(document.getElementById('salesByChannelChart'), @json($salesByChannelLabels),
            [{ label: 'Sales by Channel', data: @json($salesByChannelData), backgroundColor: defaultChartColors, borderColor: '#fff', borderWidth: 2 }], 'doughnut'
        );
        createPieDoughnutChart(document.getElementById('inventoryValueByCategoryChart'), @json($inventoryValueByCategoryLabels),
            [{ label: 'Inventory Value', data: @json($inventoryValueByCategoryData), backgroundColor: defaultChartColors, borderColor: '#fff', borderWidth: 2 }], 'pie'
        );

        createBarChart(document.getElementById('topSellingVariantsQtyChart'), @json($topSellingVariantsQtyLabels),
            [{ label: 'Quantity Sold', data: @json($topSellingVariantsQtyData), backgroundColor: defaultChartColors[4] }], 'y', (value) => Math.floor(value) === value ? value : null, null, false // X-axis ticks as int for qty
        );
        createBarChart(document.getElementById('topProfitableVariantsChart'), @json($topProfitableVariantsLabels),
            [{ label: 'Total Profit', data: @json($topProfitableVariantsData), backgroundColor: defaultChartColors[5] }], 'y', omrCurrencyAxisTick, null, false, omrCurrencyTooltip
        );
        createBarChart(document.getElementById('topLowStockItemsChart'), @json($topLowStockItemsLabels),
            [{ label: 'Current Stock', data: @json($topLowStockItemsData), backgroundColor: defaultChartColors[1] }], 'y', (value) => Math.floor(value) === value ? value : null, null, false // X-axis ticks as int for qty
        );

    });
</script>
@endpush
