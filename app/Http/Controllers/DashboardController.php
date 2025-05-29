<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductVariant; //
use App\Models\SalesOrder; //
use App\Models\SalesOrderItem; //
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Existing KPIs
        $totalProducts = Product::count();
        $lowStockThreshold = 10;
        $lowStockItemsCount = ProductVariant::where('stock_quantity', '<', $lowStockThreshold)->count();
        $recentActivities = InventoryMovement::with(['productVariant.product', 'user', 'location'])
                                            ->latest()
                                            ->take(5)
                                            ->get();

        // --- New Financial KPIs (Last 30 Days) ---
        $totalRevenueLast30Days = SalesOrder::where('status', 'completed') // Consider only completed sales
                                       ->where('order_date', '>=', $thirtyDaysAgo)
                                       ->sum('total_amount');

        $totalCogsLast30Days = SalesOrderItem::join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
                                         ->join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
                                         ->where('sales_orders.status', 'completed')
                                         ->where('sales_orders.order_date', '>=', $thirtyDaysAgo)
                                         ->sum(DB::raw('sales_order_items.quantity * product_variants.cost_price'));

        $grossProfitLast30Days = $totalRevenueLast30Days - $totalCogsLast30Days;

        // --- Chart Data ---
        // 1. Sales by Channel
        $salesByChannel = SalesOrder::where('order_date', '>=', $thirtyDaysAgo) // Also filter by last 30 days for consistency
                                    ->select('channel', DB::raw('count(*) as count'))
                                    ->groupBy('channel')
                                    ->pluck('count', 'channel')
                                    ->all();
        $salesByChannelLabels = array_keys($salesByChannel);
        $salesByChannelData = array_values($salesByChannel);

        // 2. Top 5 Selling Product Variants (Last 30 Days by Quantity)
        $topSellingVariants = SalesOrderItem::join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->where('sales_orders.order_date', '>=', $thirtyDaysAgo)
            ->select(DB::raw('CONCAT(products.name, " - ", product_variants.variant_name) as full_variant_name'), DB::raw('SUM(sales_order_items.quantity) as total_quantity_sold'))
            ->groupBy('sales_order_items.product_variant_id', 'products.name', 'product_variants.variant_name')
            ->orderByDesc('total_quantity_sold')
            ->take(5)
            ->pluck('total_quantity_sold', 'full_variant_name')
            ->all();
        $topSellingVariantsLabels = array_keys($topSellingVariants);
        $topSellingVariantsData = array_values($topSellingVariants);

        // 3. Revenue Over Time (Daily for Last 30 Days)
        $revenueOverTime = SalesOrder::where('status', 'completed')
                                     ->where('order_date', '>=', $thirtyDaysAgo)
                                     ->select(
                                         DB::raw('DATE(order_date) as sale_date'),
                                         DB::raw('SUM(total_amount) as daily_revenue')
                                     )
                                     ->groupBy('sale_date')
                                     ->orderBy('sale_date', 'ASC')
                                     ->pluck('daily_revenue', 'sale_date')
                                     ->all();

        // Prepare data for Chart.js (ensure all days in the last 30 are present, even if revenue is 0)
        $revenueOverTimeLabels = [];
        $revenueOverTimeData = [];
        $currentDate = Carbon::now()->subDays(29); // Start from 30 days ago to include today
        for ($i = 0; $i < 30; $i++) {
            $dateString = $currentDate->toDateString();
            $revenueOverTimeLabels[] = $currentDate->format('M d'); // Format for display e.g., May 29
            $revenueOverTimeData[] = $revenueOverTime[$dateString] ?? 0;
            $currentDate->addDay();
        }


        return view('dashboard', [
            'totalProducts' => $totalProducts,
            'lowStockItemsCount' => $lowStockItemsCount,
            'recentActivities' => $recentActivities,

            'totalRevenueLast30Days' => $totalRevenueLast30Days,
            'totalCogsLast30Days' => $totalCogsLast30Days,
            'grossProfitLast30Days' => $grossProfitLast30Days,

            'salesByChannelLabels' => $salesByChannelLabels,
            'salesByChannelData' => $salesByChannelData,
            'topSellingVariantsLabels' => $topSellingVariantsLabels,
            'topSellingVariantsData' => $topSellingVariantsData,
            'revenueOverTimeLabels' => $revenueOverTimeLabels,
            'revenueOverTimeData' => $revenueOverTimeData,
        ]);
    }
}
