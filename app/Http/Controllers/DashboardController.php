<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\PurchaseOrder; // Assuming this model exists
// use App\Models\PurchaseOrderItem; // Assuming this model exists if needed for detailed PO COGS
use App\Models\Category; // Assuming this model exists
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        // --- Date Ranges ---
        $today = Carbon::now();
        $thirtyDaysAgo = $today->copy()->subDays(30);
        $sixtyDaysAgo = $today->copy()->subDays(60); // For comparison

        // --- Core Inventory KPIs ---
        $totalProducts = Product::count();
        $lowStockThreshold = 10;
        $lowStockItemsCount = ProductVariant::where('stock_quantity', '<', $lowStockThreshold)->count();
        $recentActivities = InventoryMovement::with(['productVariant.product', 'user', 'location', 'referenceable']) // Added referenceable
                                            ->latest()
                                            ->take(7) // Slightly more activities
                                            ->get();
        $totalInventoryValue = ProductVariant::sum(DB::raw('stock_quantity * cost_price'));


        // --- Sales KPIs (Last 30 Days) ---
        $totalRevenueLast30Days = SalesOrder::where('status', 'completed')
                                       ->whereBetween('order_date', [$thirtyDaysAgo, $today])
                                       ->sum('total_amount');
        $totalCogsLast30Days = SalesOrderItem::join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
                                         ->join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
                                         ->where('sales_orders.status', 'completed')
                                         ->whereBetween('sales_orders.order_date', [$thirtyDaysAgo, $today])
                                         ->sum(DB::raw('sales_order_items.quantity * product_variants.cost_price'));
        $grossProfitLast30Days = $totalRevenueLast30Days - $totalCogsLast30Days;
        $salesCountLast30Days = SalesOrder::where('status', 'completed')
                                      ->whereBetween('order_date', [$thirtyDaysAgo, $today])
                                      ->count();
        $averageOrderValueLast30Days = ($salesCountLast30Days > 0) ? ($totalRevenueLast30Days / $salesCountLast30Days) : 0;
        $profitMarginLast30Days = ($totalRevenueLast30Days > 0) ? ($grossProfitLast30Days / $totalRevenueLast30Days * 100) : 0;

        // --- Sales KPIs (Previous 30 Days for Comparison) ---
        $totalRevenuePrev30Days = SalesOrder::where('status', 'completed')
                                       ->whereBetween('order_date', [$sixtyDaysAgo, $thirtyDaysAgo->copy()->subSecond()]) // Ensure no overlap
                                       ->sum('total_amount');
        $revenueChangePercentage = 0;
        if ($totalRevenuePrev30Days > 0) {
            $revenueChangePercentage = (($totalRevenueLast30Days - $totalRevenuePrev30Days) / $totalRevenuePrev30Days) * 100;
        } elseif ($totalRevenueLast30Days > 0) {
            $revenueChangePercentage = 100; // If prev was 0 and current is > 0
        }


        // --- Purchase KPIs (Last 30 Days) ---
        $totalPurchaseOrdersLast30Days = PurchaseOrder::where('status', 'Received') // Or relevant status
                                          ->whereBetween('order_date', [$thirtyDaysAgo, $today])
                                          ->count();
        $totalPurchaseAmountLast30Days = PurchaseOrder::where('status', 'Received')
                                          ->whereBetween('order_date', [$thirtyDaysAgo, $today])
                                          ->sum('total_amount'); // Assuming PurchaseOrder has total_amount

        // --- CHART DATA ---

        // 1. Revenue Over Time
        $revenueOverTimeDataRaw = SalesOrder::where('status', 'completed')
                                     ->whereBetween('order_date', [$thirtyDaysAgo, $today])
                                     ->select(DB::raw('DATE(order_date) as sale_date'), DB::raw('SUM(total_amount) as daily_revenue'))
                                     ->groupBy('sale_date')->orderBy('sale_date', 'ASC')->pluck('daily_revenue', 'sale_date')->all();
        $revenueOverTimeLabels = []; $revenueOverTimeData = [];
        $currentDate = $thirtyDaysAgo->copy();
        while ($currentDate <= $today) {
            $dateString = $currentDate->toDateString();
            $revenueOverTimeLabels[] = $currentDate->format('M d');
            $revenueOverTimeData[] = $revenueOverTimeDataRaw[$dateString] ?? 0;
            $currentDate->addDay();
        }

        // 2. Sales by Channel
        $salesByChannel = SalesOrder::whereBetween('order_date', [$thirtyDaysAgo, $today])->where('status', 'completed')
                                    ->select('channel', DB::raw('count(*) as count'))->groupBy('channel')
                                    ->pluck('count', 'channel')->all();
        $salesByChannelLabels = array_keys($salesByChannel); $salesByChannelData = array_values($salesByChannel);

        // 3. Top 5 Selling Product Variants (Qty)
        $topSellingVariantsQty = SalesOrderItem::join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->where('sales_orders.status', 'completed')->whereBetween('sales_orders.order_date', [$thirtyDaysAgo, $today])
            ->select(DB::raw('CONCAT(products.name, " - ", product_variants.variant_name) as full_variant_name'), DB::raw('SUM(sales_order_items.quantity) as total_quantity_sold'))
            ->groupBy('sales_order_items.product_variant_id', 'products.name', 'product_variants.variant_name')->orderByDesc('total_quantity_sold')->take(5)
            ->pluck('total_quantity_sold', 'full_variant_name')->all();
        $topSellingVariantsQtyLabels = array_keys($topSellingVariantsQty); $topSellingVariantsQtyData = array_values($topSellingVariantsQty);

        // 4. Gross Profit Over Time
        $dailyCogsRaw = SalesOrderItem::join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
            ->where('sales_orders.status', 'completed')->whereBetween('sales_orders.order_date', [$thirtyDaysAgo, $today])
            ->select(DB::raw('DATE(sales_orders.order_date) as sale_date'), DB::raw('SUM(sales_order_items.quantity * product_variants.cost_price) as daily_cogs_total'))
            ->groupBy('sale_date')->orderBy('sale_date', 'ASC')->pluck('daily_cogs_total', 'sale_date')->all();
        $grossProfitOverTimeLabels = $revenueOverTimeLabels; $grossProfitOverTimeData = [];
        $currentDate = $thirtyDaysAgo->copy();
        while ($currentDate <= $today) {
            $dateString = $currentDate->toDateString();
            $dailyRevenueVal = $revenueOverTimeDataRaw[$dateString] ?? 0; $dailyCogsVal = $dailyCogsRaw[$dateString] ?? 0;
            $grossProfitOverTimeData[] = $dailyRevenueVal - $dailyCogsVal;
            $currentDate->addDay();
        }

        // 5. Top 5 Most Profitable Variants (Value) - Assuming sales_order_items.price exists
        $topProfitableVariants = SalesOrderItem::join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->where('sales_orders.status', 'completed')->whereBetween('sales_orders.order_date', [$thirtyDaysAgo, $today])
            ->select(DB::raw('CONCAT(products.name, " - ", product_variants.variant_name) as full_variant_name'), DB::raw('SUM((sales_order_items.price - product_variants.cost_price) * sales_order_items.quantity) as total_profit'))
            ->groupBy('sales_order_items.product_variant_id', 'products.name', 'product_variants.variant_name')->orderByDesc('total_profit')->take(5)
            ->pluck('total_profit', 'full_variant_name')->all();
        $topProfitableVariantsLabels = array_keys($topProfitableVariants); $topProfitableVariantsData = array_values($topProfitableVariants);

        // 6. Purchases Over Time
        $purchasesOverTimeDataRaw = PurchaseOrder::where('status', 'completed')
                                     ->whereBetween('order_date', [$thirtyDaysAgo, $today])
                                     ->select(DB::raw('DATE(order_date) as purchase_date'), DB::raw('SUM(total_amount) as daily_purchase_amount'))
                                     ->groupBy('purchase_date')->orderBy('purchase_date', 'ASC')->pluck('daily_purchase_amount', 'purchase_date')->all();
        $purchasesOverTimeLabels = $revenueOverTimeLabels; // Reuse labels
        $purchasesOverTimeData = [];
        $currentDate = $thirtyDaysAgo->copy();
        while ($currentDate <= $today) {
            $dateString = $currentDate->toDateString();
            $purchasesOverTimeData[] = $purchasesOverTimeDataRaw[$dateString] ?? 0;
            $currentDate->addDay();
        }

        // 7. Inventory Value by Category (Top 5 + Other)
        $inventoryValueByCategoryRaw = ProductVariant::join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', DB::raw('SUM(product_variants.stock_quantity * product_variants.cost_price) as category_value'))
            ->groupBy('categories.name')->orderByDesc('category_value')->get();

        $inventoryValueByCategoryLabels = []; $inventoryValueByCategoryData = [];
        $otherValue = 0;
        foreach ($inventoryValueByCategoryRaw as $index => $item) {
            if ($index < 4) { // Top 4 directly
                $inventoryValueByCategoryLabels[] = $item->category_name;
                $inventoryValueByCategoryData[] = $item->category_value;
            } else {
                $otherValue += $item->category_value;
            }
        }
        if ($otherValue > 0 || count($inventoryValueByCategoryRaw) > 4) {
            $inventoryValueByCategoryLabels[] = 'Other';
            $inventoryValueByCategoryData[] = $otherValue;
        }


        // 8. Top Low Stock Items (Chart)
        $topLowStockItems = ProductVariant::with('product')
                              ->where('stock_quantity', '<', $lowStockThreshold)
                              ->where('stock_quantity', '>', 0) // Optionally, exclude out-of-stock
                              ->orderBy('stock_quantity', 'ASC')
                              ->take(5)
                              ->get();
        $topLowStockItemsLabels = $topLowStockItems->map(fn($pv) => $pv->product->name . ' - ' . $pv->variant_name)->toArray();
        $topLowStockItemsData = $topLowStockItems->pluck('stock_quantity')->toArray();


        return view('dashboard', compact(
            'totalProducts', 'lowStockItemsCount', 'recentActivities', 'totalInventoryValue',
            'totalRevenueLast30Days', 'totalCogsLast30Days', 'grossProfitLast30Days',
            'salesCountLast30Days', 'averageOrderValueLast30Days', 'profitMarginLast30Days',
            'revenueChangePercentage',
            'totalPurchaseOrdersLast30Days', 'totalPurchaseAmountLast30Days',
            'revenueOverTimeLabels', 'revenueOverTimeData',
            'salesByChannelLabels', 'salesByChannelData',
            'topSellingVariantsQtyLabels', 'topSellingVariantsQtyData',
            'grossProfitOverTimeLabels', 'grossProfitOverTimeData',
            'topProfitableVariantsLabels', 'topProfitableVariantsData',
            'purchasesOverTimeLabels', 'purchasesOverTimeData',
            'inventoryValueByCategoryLabels', 'inventoryValueByCategoryData',
            'topLowStockItemsLabels', 'topLowStockItemsData', 'lowStockThreshold'
        ));
    }
}
