<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\PurchaseOrder;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        // --- Date Ranges ---
        $today = Carbon::now()->endOfDay();
        $endDate = $today->copy();
        $startDateLast30Days = $today->copy()->subDays(29)->startOfDay();
        $endDatePrev30Days = $startDateLast30Days->copy()->subSecond();
        $startDatePrev30Days = $endDatePrev30Days->copy()->subDays(29)->startOfDay();
        $thirtyDaysAgoForChart = Carbon::now()->subDays(29)->startOfDay(); // Used for consistent chart date ranges

        // --- Core Inventory KPIs ---
        $totalProducts = Product::count();
        $lowStockThreshold = 10;
        $lowStockItemsCount = ProductVariant::where('stock_quantity', '>', 0)
                                       ->where('stock_quantity', '<=', $lowStockThreshold)
                                        ->count();
        $outOfStockItemsCount = ProductVariant::where('stock_quantity', '<=', 0)->count();
        $recentActivities = InventoryMovement::with(['productVariant.product', 'user', 'location', 'referenceable'])
                                            ->latest()->take(7)->get();
        $totalInventoryValue = ProductVariant::sum(DB::raw('stock_quantity * cost_price'));
        $totalStockQuantity = ProductVariant::sum('stock_quantity');

        // --- Sales KPIs (Last 30 Days) ---
        $totalRevenueLast30Days = SalesOrder::where('status', 'completed')
                                       ->whereBetween('order_date', [$startDateLast30Days, $endDate])
                                       ->sum('total_amount');
        $totalCogsLast30Days = SalesOrderItem::join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
                                         ->join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
                                         ->where('sales_orders.status', 'completed')
                                         ->whereBetween('sales_orders.order_date', [$startDateLast30Days, $endDate])
                                         ->sum(DB::raw('sales_order_items.quantity * product_variants.cost_price'));
        $grossProfitLast30Days = $totalRevenueLast30Days - $totalCogsLast30Days;
        $salesCountLast30Days = SalesOrder::where('status', 'completed')
                                      ->whereBetween('order_date', [$startDateLast30Days, $endDate])
                                      ->count();
        $averageOrderValueLast30Days = ($salesCountLast30Days > 0) ? ($totalRevenueLast30Days / $salesCountLast30Days) : 0;
        $profitMarginLast30Days = ($totalRevenueLast30Days > 0) ? ($grossProfitLast30Days / $totalRevenueLast30Days * 100) : 0;

        // --- Sales KPIs (Previous 30 Days for Comparison) ---
        $totalRevenuePrev30Days = SalesOrder::where('status', 'completed')
                                       ->whereBetween('order_date', [$startDatePrev30Days, $endDatePrev30Days])
                                       ->sum('total_amount');
        $totalCogsPrev30Days = SalesOrderItem::join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
                                        ->join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
                                        ->where('sales_orders.status', 'completed')
                                        ->whereBetween('sales_orders.order_date', [$startDatePrev30Days, $endDatePrev30Days])
                                        ->sum(DB::raw('sales_order_items.quantity * product_variants.cost_price'));
        $grossProfitPrev30Days = $totalRevenuePrev30Days - $totalCogsPrev30Days;
        $salesCountPrev30Days = SalesOrder::where('status', 'completed')
                                     ->whereBetween('order_date', [$startDatePrev30Days, $endDatePrev30Days])
                                     ->count();
        $averageOrderValuePrev30Days = ($salesCountPrev30Days > 0) ? ($totalRevenuePrev30Days / $salesCountPrev30Days) : 0;

        $calculatePercentageChange = function($current, $previous) {
            if ($previous > 0) return (($current - $previous) / $previous) * 100;
            if ($current > 0) return 100;
            return 0;
        };
        $revenueChangePercentage = $calculatePercentageChange($totalRevenueLast30Days, $totalRevenuePrev30Days);
        $cogsChangePercentage = $calculatePercentageChange($totalCogsLast30Days, $totalCogsPrev30Days);
        $profitChangePercentage = $calculatePercentageChange($grossProfitLast30Days, $grossProfitPrev30Days);
        $salesCountChangePercentage = $calculatePercentageChange($salesCountLast30Days, $salesCountPrev30Days);
        $aovChangePercentage = $calculatePercentageChange($averageOrderValueLast30Days, $averageOrderValuePrev30Days);

        // --- Purchase KPIs ---
        $totalPurchaseOrdersLast30Days = PurchaseOrder::whereIn('status', ['completed', 'received'])->whereBetween('order_date', [$startDateLast30Days, $endDate])->count();
        $totalPurchaseAmountLast30Days = PurchaseOrder::whereIn('status', ['completed', 'received'])->whereBetween('order_date', [$startDateLast30Days, $endDate])->sum('total_amount');
        $totalPurchaseOrdersPrev30Days = PurchaseOrder::whereIn('status', ['completed', 'received'])->whereBetween('order_date', [$startDatePrev30Days, $endDatePrev30Days])->count();
        $totalPurchaseAmountPrev30Days = PurchaseOrder::whereIn('status', ['completed', 'received'])->whereBetween('order_date', [$startDatePrev30Days, $endDatePrev30Days])->sum('total_amount');
        $poCountChangePercentage = $calculatePercentageChange($totalPurchaseOrdersLast30Days, $totalPurchaseOrdersPrev30Days);
        $poAmountChangePercentage = $calculatePercentageChange($totalPurchaseAmountLast30Days, $totalPurchaseAmountPrev30Days);


        // --- CHART DATA ---
        $chartLabels = []; // Generic labels for time-series charts (last 30 days)
        $currentChartDate = $thirtyDaysAgoForChart->copy();
        while ($currentChartDate <= $endDate) {
            $chartLabels[] = $currentChartDate->format('M d');
            $currentChartDate->addDay();
        }

        // 1. Revenue Over Time
        $revenueOverTimeDataRaw = SalesOrder::where('status', 'completed')
                                     ->whereBetween('order_date', [$thirtyDaysAgoForChart, $endDate])
                                     ->select(DB::raw('DATE(order_date) as sale_date'), DB::raw('SUM(total_amount) as daily_revenue'))
                                     ->groupBy('sale_date')->orderBy('sale_date', 'ASC')->pluck('daily_revenue', 'sale_date')->all();
        $revenueOverTimeData = [];
        $currentChartDate = $thirtyDaysAgoForChart->copy();
        while ($currentChartDate <= $endDate) {
            $revenueOverTimeData[] = $revenueOverTimeDataRaw[$currentChartDate->toDateString()] ?? 0;
            $currentChartDate->addDay();
        }
        // Use $chartLabels for $revenueOverTimeLabels for consistency
        $revenueOverTimeLabels = $chartLabels;


        // 2. Sales by Channel
        $salesByChannel = SalesOrder::whereBetween('order_date', [$startDateLast30Days, $endDate])->where('status', 'completed')
                                    ->select('channel', DB::raw('count(*) as count'))->groupBy('channel')
                                    ->pluck('count', 'channel')->all();
        $salesByChannelLabels = !empty($salesByChannel) ? array_keys($salesByChannel) : ['No Data'];
        $salesByChannelData = !empty($salesByChannel) ? array_values($salesByChannel) : [0];

        // 3. Top 5 Selling Product Variants (Qty) - For Table
        $topSellingVariantsQty = SalesOrderItem::join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->where('sales_orders.status', 'completed')->whereBetween('sales_orders.order_date', [$startDateLast30Days, $endDate])
            ->select(DB::raw('CONCAT(products.name, " - ", product_variants.variant_name) as full_variant_name'), DB::raw('SUM(sales_order_items.quantity) as total_quantity_sold'))
            ->groupBy('sales_order_items.product_variant_id', 'products.name', 'product_variants.variant_name')->orderByDesc('total_quantity_sold')->take(5)->get();

        // 4. Gross Profit Over Time
        $dailyCogsRaw = SalesOrderItem::join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
            ->where('sales_orders.status', 'completed')->whereBetween('sales_orders.order_date', [$thirtyDaysAgoForChart, $endDate])
            ->select(DB::raw('DATE(sales_orders.order_date) as sale_date'), DB::raw('SUM(sales_order_items.quantity * product_variants.cost_price) as daily_cogs_total'))
            ->groupBy('sale_date')->orderBy('sale_date', 'ASC')->pluck('daily_cogs_total', 'sale_date')->all();
        $grossProfitOverTimeLabels = $chartLabels; // Reuse generic labels
        $grossProfitOverTimeData = [];
        $currentChartDateForProfit = $thirtyDaysAgoForChart->copy();
        while ($currentChartDateForProfit <= $endDate) {
            $dateString = $currentChartDateForProfit->toDateString();
            $dailyRevenueVal = $revenueOverTimeDataRaw[$dateString] ?? 0;
            $dailyCogsVal = $dailyCogsRaw[$dateString] ?? 0;
            $grossProfitOverTimeData[] = $dailyRevenueVal - $dailyCogsVal;
            $currentChartDateForProfit->addDay();
        }

        // 5. Top 5 Most Profitable Variants (Value) - For Table
        // Assuming sales_order_items has price_per_unit
        $mostProfitableVariants = SalesOrderItem::join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->where('sales_orders.status', 'completed')->whereBetween('sales_orders.order_date', [$startDateLast30Days, $endDate])
            ->select(DB::raw('CONCAT(products.name, " - ", product_variants.variant_name) as full_variant_name'), DB::raw('SUM((sales_order_items.price_per_unit - product_variants.cost_price) * sales_order_items.quantity) as total_profit'))
            ->groupBy('sales_order_items.product_variant_id', 'products.name', 'product_variants.variant_name')->orderByDesc('total_profit')->take(5)->get();

        // 6. Purchases Over Time
        $purchasesOverTimeDataRaw = PurchaseOrder::whereIn('status', ['completed', 'received'])
                                     ->whereBetween('order_date', [$thirtyDaysAgoForChart, $endDate])
                                     ->select(DB::raw('DATE(order_date) as purchase_date'), DB::raw('SUM(total_amount) as daily_purchase_amount'))
                                     ->groupBy('purchase_date')->orderBy('purchase_date', 'ASC')->pluck('daily_purchase_amount', 'purchase_date')->all();
        $purchasesOverTimeLabels = $chartLabels; // Reuse generic labels
        $purchasesOverTimeData = [];
        $currentChartDateForPurchase = $thirtyDaysAgoForChart->copy();
        while ($currentChartDateForPurchase <= $endDate) {
            $purchasesOverTimeData[] = $purchasesOverTimeDataRaw[$currentChartDateForPurchase->toDateString()] ?? 0;
            $currentChartDateForPurchase->addDay();
        }

        // 7. Inventory Value by Category
        $inventoryValueByCategoryRaw = ProductVariant::join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', DB::raw('SUM(product_variants.stock_quantity * product_variants.cost_price) as category_value'))
            ->where('product_variants.stock_quantity', '>', 0)->groupBy('categories.name')->orderByDesc('category_value')->get();
        $inventoryValueByCategoryLabels = []; $inventoryValueByCategoryData = []; $otherValue = 0; $maxCategoriesInChart = 4;
        foreach ($inventoryValueByCategoryRaw as $index => $item) {
            if ($index < $maxCategoriesInChart) { $inventoryValueByCategoryLabels[] = $item->category_name; $inventoryValueByCategoryData[] = (float)$item->category_value; }
            else { $otherValue += (float)$item->category_value; }
        }
        if ($otherValue > 0 || $inventoryValueByCategoryRaw->count() > $maxCategoriesInChart || $inventoryValueByCategoryRaw->isEmpty()) {
            $inventoryValueByCategoryLabels[] = $inventoryValueByCategoryRaw->isEmpty() && $otherValue == 0 ? 'No Data' : 'Other';
            $inventoryValueByCategoryData[] = $inventoryValueByCategoryRaw->isEmpty() && $otherValue == 0 ? 0 : $otherValue;
        }

        // 8. Top Low Stock Items - For Table
        $topLowStockItems = ProductVariant::with('product')->where('stock_quantity', '<', $lowStockThreshold)->where('stock_quantity', '>', 0)->orderBy('stock_quantity', 'ASC')->take(5)->get();

        // 9. Data for Sales by Category Chart
        $salesByCategoryRaw = SalesOrderItem::join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->where('sales_orders.status', 'completed')->whereBetween('sales_orders.order_date', [$startDateLast30Days, $endDate])
            ->select('categories.name as category_name', DB::raw('SUM(sales_order_items.quantity * sales_order_items.price_per_unit) as category_sales_value'))
            ->groupBy('categories.name')->orderByDesc('category_sales_value')->get();
        $salesByCategoryChartLabels = []; $salesByCategoryChartDataValues = []; $otherSalesValue = 0;
        foreach ($salesByCategoryRaw as $index => $item) {
            if ($index < $maxCategoriesInChart) { $salesByCategoryChartLabels[] = $item->category_name; $salesByCategoryChartDataValues[] = (float)$item->category_sales_value; }
            else { $otherSalesValue += (float)$item->category_sales_value; }
        }
        if ($otherSalesValue > 0 || $salesByCategoryRaw->count() > $maxCategoriesInChart || $salesByCategoryRaw->isEmpty()) {
            $salesByCategoryChartLabels[] = $salesByCategoryRaw->isEmpty() && $otherSalesValue == 0 ? 'No Data' : 'Other';
            $salesByCategoryChartDataValues[] = $salesByCategoryRaw->isEmpty() && $otherSalesValue == 0 ? 0 : $otherSalesValue;
        }

        // --- Forecasting: Simple Linear Projection for next 30 days ---
        $projectionDaysCount = 30;
        $daysInHistoricalPeriod = $startDateLast30Days->diffInDays($endDate) + 1;
        $avgDailyRevenueForProjection = ($totalRevenueLast30Days > 0 && $daysInHistoricalPeriod > 0) ? $totalRevenueLast30Days / $daysInHistoricalPeriod : 0;
        $expectedRevenueNext30Days = $avgDailyRevenueForProjection * $projectionDaysCount;

        // Data for combined actual + projected revenue chart
        $revenueForecastLabels = [];
        $revenueForecastActualData = [];
        $revenueForecastProjectedData = [];

        // 1. Add historical labels and actual data (using the $chartLabels which are consistent)
        foreach($chartLabels as $index => $label) {
            $revenueForecastLabels[] = $label;
            // $revenueOverTimeData should correspond to $chartLabels
            $revenueForecastActualData[] = $revenueOverTimeData[$index] ?? 0;
            $revenueForecastProjectedData[] = null; // Null for historical part in projected dataset
        }

        // 2. Add future projected labels and data
        $currentProjectionDate = $endDate->copy()->addDay(); // Start projection from day after historical data ends
        for ($i = 0; $i < $projectionDaysCount; $i++) {
            $revenueForecastLabels[] = $currentProjectionDate->format('M d');
            $revenueForecastActualData[] = null; // Null for projected part in actual dataset
            $revenueForecastProjectedData[] = $avgDailyRevenueForProjection;
            $currentProjectionDate->addDay();
        }


        return view('dashboard', compact(
            'totalProducts', 'lowStockItemsCount', 'outOfStockItemsCount', 'recentActivities', 'totalInventoryValue', 'totalStockQuantity',
            'totalRevenueLast30Days', 'totalCogsLast30Days', 'grossProfitLast30Days',
            'salesCountLast30Days', 'averageOrderValueLast30Days', 'profitMarginLast30Days',
            'revenueChangePercentage', 'cogsChangePercentage', 'profitChangePercentage', 'salesCountChangePercentage', 'aovChangePercentage',
            'totalPurchaseOrdersLast30Days', 'totalPurchaseAmountLast30Days',
            'poCountChangePercentage', 'poAmountChangePercentage',
            'chartLabels', // Used by several time-series charts
            'revenueOverTimeLabels', 'revenueOverTimeData', // Can be derived from chartLabels + revenueOverTimeData
            'salesByChannelLabels', 'salesByChannelData',
            'expectedRevenueNext30Days',
            'topSellingVariantsQty',
            'grossProfitOverTimeLabels', 'grossProfitOverTimeData', // Can be derived from chartLabels + grossProfitOverTimeData
            'mostProfitableVariants',
            'purchasesOverTimeLabels', 'purchasesOverTimeData', // Can be derived from chartLabels + purchasesOverTimeData
            'inventoryValueByCategoryLabels', 'inventoryValueByCategoryData',
            'topLowStockItems',
            'lowStockThreshold',
            'salesByCategoryChartLabels', 'salesByCategoryChartDataValues',
            // ADD THESE FOR THE FORECAST CHART
            'revenueForecastLabels',
            'revenueForecastActualData',
            'revenueForecastProjectedData'
        ));
    }
}
