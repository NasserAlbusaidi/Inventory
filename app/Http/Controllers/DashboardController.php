<?php

namespace App\Http\Controllers;

use App\Models\Category;
// use App\Models\InventoryLevel; // REMOVED
use App\Models\Location;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\RecurringExpense;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(29)->startOfDay();

        $prevEndDate = $startDate->copy()->subDay()->endOfDay();
        $prevStartDate = $prevEndDate->copy()->subDays(29)->startOfDay();

        $customerIdentifierJsonPath = 'customer_details->>"$.name"';
        $rawCustomerIdentifier = DB::raw($customerIdentifierJsonPath);

        // --- Financial KPIs (Last 30 Days) ---
        $salesOrdersLast30DaysQuery = SalesOrder::whereBetween('created_at', [$startDate, $endDate]);
        $totalRevenueLast30Days = (clone $salesOrdersLast30DaysQuery)->sum('total_amount');

        $costOfGoodsSold = SalesOrderItem::whereHas('salesOrder', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
            ->sum(DB::raw('sales_order_items.quantity * product_variants.cost_price'));

        $costOfGoodsSoldPrev30Days = SalesOrderItem::whereHas('salesOrder', function ($query) use ($prevStartDate, $prevEndDate) {
            $query->whereBetween('created_at', [$prevStartDate, $prevEndDate]);
        })
            ->join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
            ->sum(DB::raw('sales_order_items.quantity * product_variants.cost_price'));

        $cogsChangePercentage = $costOfGoodsSoldPrev30Days != 0
            ? (($costOfGoodsSold - $costOfGoodsSoldPrev30Days) / $costOfGoodsSoldPrev30Days) * 100
            : ($costOfGoodsSold > 0 ? 100 : 0);

        $operationalCost = RecurringExpense::where('start_date', '<=', $endDate)
            ->where(function ($query) use ($endDate) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $endDate);
            })
            ->sum('monthly_cost');

        $totalCostLast30Days = $costOfGoodsSold + $operationalCost;
        $grossProfitLast30Days = $totalRevenueLast30Days - $costOfGoodsSold;

        $totalRevenuePrev30Days = SalesOrder::whereBetween('created_at', [$prevStartDate, $prevEndDate])->sum('total_amount');
        $grossProfitPrev30Days = $totalRevenuePrev30Days - $costOfGoodsSoldPrev30Days;
        $grossProfitChangePercentage = $grossProfitPrev30Days != 0
            ? (($grossProfitLast30Days - $grossProfitPrev30Days) / $grossProfitPrev30Days) * 100
            : ($grossProfitLast30Days > 0 ? 100 : 0);

        $netProfitLast30Days = $totalRevenueLast30Days - $totalCostLast30Days;
        $profitMarginLast30Days = $totalRevenueLast30Days > 0 ? ($netProfitLast30Days / $totalRevenueLast30Days) * 100 : 0;
        $expectedRevenueNext30Days = 0; // Placeholder

        // --- Sales KPIs (Last 30 Days & Change) ---
        $salesCountLast30Days = (clone $salesOrdersLast30DaysQuery)->count();
        $salesCountPrev30Days = SalesOrder::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $salesCountChangePercentage = $salesCountPrev30Days != 0
            ? (($salesCountLast30Days - $salesCountPrev30Days) / $salesCountPrev30Days) * 100
            : ($salesCountLast30Days > 0 ? 100 : 0);

        $averageOrderValue = $salesCountLast30Days > 0 ? $totalRevenueLast30Days / $salesCountLast30Days : 0;
        $averageOrderValuePrev = $salesCountPrev30Days > 0 ? $totalRevenuePrev30Days / $salesCountPrev30Days : 0;
        $averageOrderValueChangePercentage = $averageOrderValuePrev != 0
            ? (($averageOrderValue - $averageOrderValuePrev) / $averageOrderValuePrev) * 100
            : ($averageOrderValue > 0 ? 100 : 0);

        $newCustomersLast30Days = SalesOrder::select(DB::raw($customerIdentifierJsonPath . ' as customer_id_extracted'), DB::raw('MIN(created_at) as first_order_date'))
            ->groupBy(DB::raw($customerIdentifierJsonPath))
            ->having('first_order_date', '>=', $startDate)
            ->having('first_order_date', '<=', $endDate)
            ->get()->count();
        $newCustomersChangePercentage = 0; // Placeholder


        // --- Inventory & Purchasing KPIs (Using ProductVariant global stock) ---
        $totalStockUnits = ProductVariant::where('track_inventory', true)->sum('stock_quantity');

        $inventoryValue = ProductVariant::where('track_inventory', true)
            ->sum(DB::raw('stock_quantity * cost_price'));

        $lowStockThresholdValue = config('inventory.low_stock_threshold', 10); // Get from config or define

        $lowStockProductCount = ProductVariant::where('track_inventory', true)
            ->where('stock_quantity', '>', 0) // Must have some stock
            ->where('stock_quantity', '<=', $lowStockThresholdValue)
            ->count();

        $outOfStockProductCount = ProductVariant::where('track_inventory', true)
            ->where('stock_quantity', '<=', 0)
            ->count();

        $purchaseOrdersCountLast30Days = PurchaseOrder::where('status', 'Received')
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $purchaseOrdersCountPrev30Days = PurchaseOrder::where('status', 'Received')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $purchaseOrdersCountChangePercentage = $purchaseOrdersCountPrev30Days != 0
            ? (($purchaseOrdersCountLast30Days - $purchaseOrdersCountPrev30Days) / $purchaseOrdersCountPrev30Days) * 100
            : ($purchaseOrdersCountLast30Days > 0 ? 100 : 0);

        $purchaseOrderAmountLast30Days = PurchaseOrder::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $purchaseOrderAmountPrev30Days = PurchaseOrder::whereBetween('created_at', [$prevStartDate, $prevEndDate])->sum('total_amount');
        $purchaseOrderAmountChangePercentage = $purchaseOrderAmountPrev30Days != 0
            ? (($purchaseOrderAmountLast30Days - $purchaseOrderAmountPrev30Days) / $purchaseOrderAmountPrev30Days) * 100
            : ($purchaseOrderAmountLast30Days > 0 ? 100 : 0);

        // Total Customers, Pending POs, Completed POs for Quick Stats
        $totalCustomers = SalesOrder::select($rawCustomerIdentifier)->distinct()->count($rawCustomerIdentifier);
        $pendingPOCount = PurchaseOrder::where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate])->count();
        $completedPOCount = PurchaseOrder::where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate])->count();


        // === Chart Data & Tables (Existing queries) ===
        $revenueOverTime = SalesOrder::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total_revenue'))
            ->whereBetween('created_at', [$startDate, $endDate])->groupBy('date')->orderBy('date', 'asc')->get();
        $revenueOverTimeLabels = $revenueOverTime->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d'))->all();
        $revenueOverTimeData = $revenueOverTime->pluck('total_revenue')->map(function ($value) {
            return (float) $value; // Cast each value to a float
        })->all();


        $mostProfitableVariants = SalesOrderItem::with('productVariant.product')
            ->select('product_variant_id', DB::raw('SUM(sales_order_items.quantity * sales_order_items.price) - SUM(sales_order_items.quantity * product_variants.cost_price) as total_profit'))
            ->join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->whereBetween('sales_orders.created_at', [$startDate, $endDate])->groupBy('product_variant_id')->orderByDesc('total_profit')->havingRaw('total_profit > 0')->limit(7)->get();

        $topSellingVariants = SalesOrderItem::with('productVariant.product')
            ->select('product_variant_id', DB::raw('SUM(sales_order_items.quantity) as total_quantity_sold'))
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->whereBetween('sales_orders.created_at', [$startDate, $endDate])->groupBy('product_variant_id')->orderByDesc('total_quantity_sold')->limit(7)->get();

        // topLowStockItems for the list view - uses global stock_quantity
        $topLowStockItems = ProductVariant::with('product')
            ->where('track_inventory', true)
            ->where('stock_quantity', '<=', $lowStockThresholdValue)
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        $salesByCategory = Category::query()
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('sales_order_items', 'product_variants.id', '=', 'sales_order_items.product_variant_id')
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->whereBetween('sales_orders.created_at', [$startDate, $endDate])->select('categories.name', DB::raw('SUM(sales_order_items.quantity * sales_order_items.price) as total_sales'))
            ->groupBy('categories.id', 'categories.name')->orderByDesc('total_sales')->havingRaw('SUM(sales_order_items.quantity * sales_order_items.price) > 0')->limit(5)->get();
        $salesByCategoryChartLabels = $salesByCategory->pluck('name')->all();
        $salesByCategoryChartDataValues = $salesByCategory->pluck('total_sales')->map(function ($value) {
            return (float) $value;
        })->all();

        $salesByChannel = SalesOrder::select('channel', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])->groupBy('channel')->orderByDesc('count')->get();
        $salesByChannelLabels = $salesByChannel->pluck('channel')->map(fn($channel) => ucwords(str_replace('_', ' ', $channel)))->all();
        $salesByChannelData = $salesByChannel->pluck('count')->all();

        $inventoryValueByCategory = Category::withSum(['productVariants as total_inventory_value' => function ($query) {
            $query->where('track_inventory', true);
        }], DB::raw('stock_quantity * cost_price'))
            ->having('total_inventory_value', '>', 0)->orderByDesc('total_inventory_value')->limit(5)->get();
        $inventoryValueByCategoryLabels = $inventoryValueByCategory->pluck('name')->all();
        $inventoryValueByCategoryData = $inventoryValueByCategory->pluck('total_inventory_value')->all();


        $recentPurchases = PurchaseOrder::with('supplier', 'items.productVariant')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        // insert total_amount to each purchase order
        $recentPurchases->each(function ($purchaseOrder) {
            $purchaseOrder->total_amount = $purchaseOrder->items->sum(function ($item) {
                // dd($item->quantity * $item->productVariant->cost_price);
                return $item->quantity * $item->productVariant->cost_price;
            });
        });
        foreach ($recentPurchases as $purchaseOrder) {
            $purchaseOrder->items->each(function ($item) {
                $item->productVariant->load('product'); // Eager load product for each item

            });
        }


        // **Inventory by Location Chart Data (Using your provided query)**
        // This query shows "Quantity of Items Received by Location in Last 30 Days via POs"
        // NOT "Current Stock Quantity by Location".
        // If ProductVariant has a location_id, you could sum ProductVariant->stock_quantity grouped by that location_id for current stock.
        $inventoryByLocation = PurchaseOrder::select(
            'locations.name',
            DB::raw('SUM(purchase_order_items.quantity) as total_stock_received_on_po') // Renamed for clarity
        )
            ->join('purchase_order_items', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
            ->join('locations', 'purchase_orders.receiving_location_id', '=', 'locations.id')
            ->where('purchase_orders.status', 'received') // Only count items from POs actually marked as received
            ->whereBetween('purchase_orders.updated_at', [$startDate, $endDate]) // Consider POs marked received in this period
            ->groupBy('locations.id', 'locations.name')
            ->orderByDesc('total_stock_received_on_po')
            ->limit(5)
            ->get();

        $inventoryByLocationLabels = $inventoryByLocation->pluck('name')->all();
        $inventoryByLocationData = $inventoryByLocation->pluck('total_stock_received_on_po')->all();


        $recentSalesOrders = SalesOrder::orderBy('created_at', 'desc')->limit(5)->get();



        $monthlyTargetAmount = 600; // Example: Get this from settings or define it
        $currentRevenueForMonth = $totalRevenueLast30Days; // Or a more specific "current month" revenue

        // Ensure target is not zero to avoid division by zero
        $percentageAchieved = ($monthlyTargetAmount > 0) ? ($currentRevenueForMonth / $monthlyTargetAmount) * 100 : 0;
        $percentageAchieved = min($percentageAchieved, 100); // Cap at 100% for the gauge display

        // Calculate revenue for the *previous* month (from 60 to 30 days ago) for comparison
        $prevMonthStartDate = $startDate->copy()->subMonth()->startOfMonth();
        $prevMonthEndDate = $startDate->copy()->subMonth()->endOfMonth(); // Or $prevStartDate->copy()->endOfMonth();

        $revenueLastMonth = SalesOrder::whereBetween('created_at', [$prevMonthStartDate, $prevMonthEndDate])
            ->sum('total_amount');

        $revenueIncreaseAmount = $currentRevenueForMonth - $revenueLastMonth;
        $percentageChangeFromLastMonth = 0;
        if ($revenueLastMonth > 0) {
            $percentageChangeFromLastMonth = (($currentRevenueForMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
        } elseif ($currentRevenueForMonth > 0) {
            $percentageChangeFromLastMonth = 100; // Infinite or 100% increase if last month was 0
        }


        // Data for the doughnut chart
        $monthlyTargetChartData = [
            round($percentageAchieved, 2),            // Achieved part
            max(0, 100 - round($percentageAchieved, 2)) // Remaining part (ensure it's not negative)
        ];

        $monthlyTargetChartColors = [
            '#4CAF50', // Green for achieved
            '#E0E0E0'  // Grey for remaining
        ];



        return view('dashboard', compact(
            'totalRevenueLast30Days',
            'costOfGoodsSold',
            'grossProfitLast30Days',
            'netProfitLast30Days',
            'profitMarginLast30Days',
            'totalCostLast30Days',
            'operationalCost',
            'cogsChangePercentage',
            'grossProfitChangePercentage',
            'expectedRevenueNext30Days',

            'monthlyTargetAmount',
            'currentRevenueForMonth',
            'percentageAchieved',
            'percentageChangeFromLastMonth',
            'revenueIncreaseAmount',
            'monthlyTargetChartData',
            'monthlyTargetChartColors',

            'salesCountLast30Days',
            'salesCountChangePercentage',
            'averageOrderValue',
            'averageOrderValueChangePercentage',
            'newCustomersLast30Days',
            'newCustomersChangePercentage',

            'totalStockUnits',
            'inventoryValue',
            'lowStockProductCount',
            'outOfStockProductCount',
            'purchaseOrdersCountLast30Days',
            'purchaseOrdersCountChangePercentage',
            'purchaseOrderAmountLast30Days',
            'purchaseOrderAmountChangePercentage', // Renamed from purchaseOrderValue to be explicit

            'totalCustomers',
            'pendingPOCount',
            'completedPOCount',

            'recentPurchases',

            'revenueOverTimeLabels',
            'revenueOverTimeData',
            'mostProfitableVariants',
            'topSellingVariants',
            'topLowStockItems',
            'salesByCategoryChartLabels',
            'salesByCategoryChartDataValues',
            'salesByChannelLabels',
            'salesByChannelData',
            'inventoryValueByCategoryLabels',
            'inventoryValueByCategoryData',
            'inventoryByLocationLabels',
            'inventoryByLocationData',
            'recentSalesOrders'
        ));
    }
}
