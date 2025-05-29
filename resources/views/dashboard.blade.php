@extends('layouts.app') {{-- Or components.layouts.app if you moved it --}}

@section('title', 'Dashboard')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            {{ __("Welcome to your Perfume Inventory Dashboard!") }}
        </div>
    </div>

    {{-- KPIs Row 1 --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900">Total Revenue (Last 30 Days)</h3>
            <p class="mt-2 text-3xl font-semibold text-green-600">
                OMR {{ number_format($totalRevenueLast30Days, 2) }}
            </p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900">Total COGS (Last 30 Days)</h3>
            <p class="mt-2 text-3xl font-semibold text-orange-600">
                OMR {{ number_format($totalCogsLast30Days, 2) }}
            </p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900">Gross Profit (Last 30 Days)</h3>
            <p class="mt-2 text-3xl font-semibold {{ $grossProfitLast30Days >= 0 ? 'text-green-600' : 'text-red-600' }}">
                OMR {{ number_format($grossProfitLast30Days, 2) }}
            </p>
        </div>
    </div>


    {{-- KPIs Row 2 (Existing) --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900">Total Products</h3>
            <p class="mt-2 text-3xl font-semibold text-gray-700">{{ $totalProducts }}</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900">Low Stock Items</h3>
            <p class="mt-2 text-3xl font-semibold text-red-500">{{ $lowStockItemsCount }}</p>
            <p class="mt-1 text-sm text-gray-600">Variants with stock below 10 units.</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900">Recent Activities</h3>
            @if($recentActivities->count() > 0)
                <ul class="mt-2 space-y-2 max-h-60 overflow-y-auto">
                    @foreach($recentActivities as $activity)
                        <li class="text-sm text-gray-700 p-2 bg-gray-50 rounded-md">
                            <div>
                                <span class="font-medium capitalize">{{ $activity->type }}</span>:
                                {{ $activity->quantity }} x
                                <span class="text-indigo-600">{{ $activity->productVariant->product->name ?? 'N/A' }} - {{ $activity->productVariant->variant_name ?? 'N/A' }}</span>
                            </div>
                            <div class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-2 text-sm text-gray-600">No recent activities.</p>
            @endif
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Over Time (Last 30 Days)</h3>
            <div class="h-80">
                <canvas id="revenueOverTimeChart"></canvas>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sales by Channel (Last 30 Days)</h3>
            <div class="h-80">
                <canvas id="salesByChannelChart"></canvas>
            </div>
        </div>
    </div>
    <div class="mt-6 grid grid-cols-1 gap-6"> {{-- New row for full-width chart --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Selling Variants (Last 30 Days by Qty)</h3>
             <div class="h-80">
                <canvas id="topSellingVariantsChart"></canvas>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const omrCurrencyFormatter = (value) => `OMR ${Number(value).toLocaleString('en-OM', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

        // Revenue Over Time Chart
        const revenueOverTimeCtx = document.getElementById('revenueOverTimeChart');
        if (revenueOverTimeCtx) {
            new Chart(revenueOverTimeCtx, {
                type: 'line',
                data: {
                    labels: @json($revenueOverTimeLabels),
                    datasets: [{
                        label: 'Daily Revenue',
                        data: @json($revenueOverTimeData),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    return 'OMR ' + Number(value).toLocaleString('en-OM', {minimumFractionDigits: 0}); // Basic OMR formatting
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: OMR ${Number(context.parsed.y).toLocaleString('en-OM', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Sales by Channel Chart
        const salesByChannelCtx = document.getElementById('salesByChannelChart');
        if (salesByChannelCtx) {
            new Chart(salesByChannelCtx, {
                type: 'pie',
                data: {
                    labels: @json($salesByChannelLabels),
                    datasets: [{
                        label: 'Sales by Channel',
                        data: @json($salesByChannelData),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)'
                        ],
                        borderColor: ['#fff'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } }
                }
            });
        }

        // Top Selling Variants Chart
        const topSellingVariantsCtx = document.getElementById('topSellingVariantsChart');
        if (topSellingVariantsCtx) {
            new Chart(topSellingVariantsCtx, {
                type: 'bar',
                data: {
                    labels: @json($topSellingVariantsLabels),
                    datasets: [{
                        label: 'Quantity Sold (Last 30 Days)',
                        data: @json($topSellingVariantsData),
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                    plugins: { legend: { display: false } }
                }
            });
        }
    });
</script>
@endpush
