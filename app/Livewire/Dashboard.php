<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\RecurringExpense;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\LocationInventory;
use Carbon\Carbon;
use App\Models\OneTimeExpense;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // Properties for date selection
    public string $dateRange = 'last_30_days';
    public ?string $customStartDate = null;
    public ?string $customEndDate = null;
    public int $updateCounter = 0;

    public $lastUpdated;


    // Calculated date properties
    protected Carbon $startDate;
    protected Carbon $endDate;

    public function mount()
    {
        $this->calculateDateRange();
    }

    public function updatedDateRange()
    {
        $this->calculateDateRange();
    }
    public function updatedCustomStartDate()
    {
        $this->calculateDateRange();
    }
    public function updatedCustomEndDate()
    {
        $this->calculateDateRange();
    }

    protected function calculateDateRange(): void
    {
        $now = Carbon::now();
        switch ($this->dateRange) {
            case 'today':
                $this->startDate = $now->copy()->startOfDay();
                $this->endDate = $now->copy()->endOfDay();
                break;
            case 'this_week':
                $this->startDate = $now->copy()->startOfWeek();
                $this->endDate = $now->copy()->endOfWeek();
                break;
            case 'this_month':
                $this->startDate = $now->copy()->startOfMonth();
                $this->endDate = $now->copy()->endOfMonth();
                break;
            case 'this_year':
                $this->startDate = $now->copy()->startOfYear();
                $this->endDate = $now->copy()->endOfYear();
                break;
            case 'custom':
                $this->startDate = $this->customStartDate ? Carbon::parse($this->customStartDate)->startOfDay() : $now->copy()->startOfDay();
                $this->endDate = $this->customEndDate ? Carbon::parse($this->customEndDate)->endOfDay() : $now->copy()->endOfDay();
                break;
            case 'last_30_days':
            default:
                $this->startDate = $now->copy()->subDays(29)->startOfDay();
                $this->endDate = $now->copy()->endOfDay();
                break;
        }
    }

    public function refreshData()
    {
        $this->calculateDateRange();
        $this->lastUpdated = now();
        $this->updateCounter++;
        // Clear the cache to ensure fresh data is fetched next time.
        Cache::forget('dashboard_data');
        $this->dispatch('refresh-dashboard');
    }

    public function render()
    {
        $this->lastUpdated = now();
        // The cache key now includes the date range to ensure fresh data when dates change.
        $cacheKey = 'dashboard_data';

        // Increased cache time slightly as this is a heavy query.
        $data = Cache::remember($cacheKey, now()->addMinutes(1), function () {

            // --- Date and Comparison Period Setup ---
            $durationInDays = $this->endDate->diffInDays($this->startDate);
            $prevEndDate = $this->startDate->copy()->subDay()->endOfDay();
            $prevStartDate = $prevEndDate->copy()->subDays($durationInDays)->startOfDay();
            $customerIdentifierJsonPath = 'customer_details->>"$.name"';

            // --- Base Queries (with corrected table prefixes) ---
            $salesOrdersInPeriod = SalesOrder::whereBetween('sales_orders.created_at', [$this->startDate, $this->endDate]);
            $purchaseOrdersInPeriod = PurchaseOrder::whereBetween('purchase_orders.created_at', [$this->startDate, $this->endDate]);

            // --- Core Financial KPIs ---
            $totalRevenue = (clone $salesOrdersInPeriod)->sum('total_amount');

            // Cost of Goods Sold (slightly optimized)
            $salesOrderItemsForCost = SalesOrderItem::whereHas('salesOrder', fn($q) => $q->whereBetween('sales_orders.created_at', [$this->startDate, $this->endDate]))
                ->with('saleable:id,cost_price')->get(); // Select only needed columns

            $costOfGoodsSold = $salesOrderItemsForCost->sum(fn($item) => $item->quantity * ($item->saleable->cost_price ?? 0));

            // 1. Calculate One-Time Expenses within the period
            $oneTimeOperationalCost = OneTimeExpense::whereBetween('expense_date', [$this->startDate, $this->endDate])->sum('amount');

            // 2. Calculate prorated Recurring Expenses
            $recurringExpenses = RecurringExpense::where('start_date', '<=', $this->endDate)
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', $this->startDate);
                })
                ->get();

            $recurringOperationalCost = $recurringExpenses->sum(function ($expense) {
                // Determine the overlap period for this specific expense
                $periodStart = $this->startDate->max($expense->start_date);
                $periodEnd = $this->endDate->min($expense->end_date ?? $this->endDate);

                // Calculate the number of full and partial months in the overlap
                $months = $periodStart->floatDiffInMonths($periodEnd);

                return $expense->monthly_cost * $months;
            });

            // 3. Combine them for the total operational cost
            $operationalCost = $oneTimeOperationalCost + $recurringOperationalCost;


            $totalPurchaseValue = (clone $purchaseOrdersInPeriod)->sum('total_amount');
            $totalCost = $totalPurchaseValue + $operationalCost;
            $netProfit = $totalRevenue - $totalCost;
            $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

            // --- Sales KPIs & Comparisons (with corrected table prefixes) ---
            $salesCount = (clone $salesOrdersInPeriod)->count();
            $salesCountPrev = SalesOrder::whereBetween('sales_orders.created_at', [$prevStartDate, $prevEndDate])->count();
            $salesCountChange = $salesCountPrev > 0 ? (($salesCount - $salesCountPrev) / $salesCountPrev) * 100 : ($salesCount > 0 ? 100 : 0);

            $purchaseOrdersCount = (clone $purchaseOrdersInPeriod)->count();
            $purchaseOrdersCountPrev = PurchaseOrder::whereBetween('purchase_orders.created_at', [$prevStartDate, $prevEndDate])->count();
            $purchaseOrdersCountChange = $purchaseOrdersCountPrev > 0 ? (($purchaseOrdersCount - $purchaseOrdersCountPrev) / $purchaseOrdersCountPrev) * 100 : ($purchaseOrdersCount > 0 ? 100 : 0);

            $averageOrderValue = $salesCount > 0 ? $totalRevenue / $salesCount : 0;
            $averagePurchaseValue = $purchaseOrdersCount > 0 ? $totalPurchaseValue / $purchaseOrdersCount : 0;

            // --- Customer Loyalty & Counts (with corrected table prefixes) ---
            $totalCustomersInPeriod = (clone $salesOrdersInPeriod)->distinct(DB::raw($customerIdentifierJsonPath))->count(DB::raw($customerIdentifierJsonPath));
            $totalCustomersPrevPeriod = SalesOrder::whereBetween('sales_orders.created_at', [$prevStartDate, $prevEndDate])->distinct(DB::raw($customerIdentifierJsonPath))->count(DB::raw($customerIdentifierJsonPath));
            $customerChangePercentage = $totalCustomersPrevPeriod > 0 ? (($totalCustomersInPeriod - $totalCustomersPrevPeriod) / $totalCustomersPrevPeriod) * 100 : ($totalCustomersInPeriod > 0 ? 100 : 0);

            $customerOrders = (clone $salesOrdersInPeriod)->select(DB::raw($customerIdentifierJsonPath . ' as customer_name'))->get()->groupBy('customer_name')->filter(fn($group) => $group->first()->customer_name !== null);
            $repeatCustomers = $customerOrders->filter(fn($orders) => $orders->count() > 1)->count();
            $repeatCustomerRate = $totalCustomersInPeriod > 0 ? ($repeatCustomers / $totalCustomersInPeriod) * 100 : 0;

            // --- Inventory Health KPIs (FIXED for MorphMap) ---
            $deadStockCutoffDate = now()->subDays(90);
            $soldItemKeys = SalesOrderItem::whereHas('salesOrder', fn($q) => $q->where('sales_orders.created_at', '>=', $deadStockCutoffDate))
                ->select('saleable_id', 'saleable_type')->distinct()->get()
                ->map(fn($item) => $item->saleable_type . '_' . $item->saleable_id)->flip();

            $deadStockItemsCount = LocationInventory::where('stock_quantity', '>', 0)->get()
                ->filter(fn($inv) => !isset($soldItemKeys[$inv->inventoriable_type . '_' . $inv->inventoriable_id]))->count();

            // --- Top Selling & Most Profitable Items ---
            $allSoldItems = SalesOrderItem::whereHas('salesOrder', fn($q) => $q->whereBetween('sales_orders.created_at', [$this->startDate, $this->endDate]))
                ->with(['saleable' => fn($morphTo) => $morphTo->morphWith([ProductVariant::class => ['product:id,name,sku']])])->get();
            $itemStats = $allSoldItems->groupBy('saleable')->map(function ($items) {
                $firstItemSaleable = $items->first()->saleable;
                if (!$firstItemSaleable) return null;
                $displayName = ($firstItemSaleable instanceof ProductVariant) ? "{$firstItemSaleable->product->name} - {$firstItemSaleable->variant_name}" : $firstItemSaleable->name;
                return [
                    'display_name' => $displayName,
                    'total_quantity_sold' => $items->sum('quantity'),
                    'total_profit' => $items->sum(fn($i) => $i->quantity * ($i->price_per_unit - ($i->saleable->cost_price ?? 0))),
                ];
            })->filter();

            $topSellingItems = $itemStats->sortByDesc('total_quantity_sold')->take(7);
            // dd($topSellingItems);
            $mostProfitableItems = $itemStats->sortByDesc('total_profit')->take(7);
            // dd($mostProfitableItems);
            // --- Recent Orders ---
            $recentPurchases = PurchaseOrder::with('supplier')->latest('order_date')->limit(5)->get();
            $recentSales = SalesOrder::latest('order_date')->limit(5)->get();

            // --- Settings & Targets ---
            $settings = app('settings');
            $lowStockThreshold = $settings->get('low_stock_threshold', 5);
            $monthlyTargetAmount = (float) $settings->get('monthly_revenue_target', 6000);
            $profitMarginTarget = (float) $settings->get('profit_margin_target', 15);
            $currentRevenueForMonth = SalesOrder::whereBetween('sales_orders.created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_amount');
            $percentageAchieved = ($monthlyTargetAmount > 0) ? ($currentRevenueForMonth / $monthlyTargetAmount) * 100 : 0;

            // --- Chart Data ---
            $revenueOverTime = (clone $salesOrdersInPeriod)->select(DB::raw('DATE(sales_orders.created_at) as date'), DB::raw('SUM(total_amount) as total_revenue'))->groupBy('date')->orderBy('date')->get();

            $salesByCategory = $allSoldItems->groupBy(function ($item) {
                if (!$item->saleable) return 'Other';

                // This is the key part:
                $product = ($item->saleable instanceof ProductVariant)
                    ? $item->saleable->product // A. If it's a variant, get the parent product.
                    : $item->saleable;         // B. If it's a simple product, use it directly.

                // Then, try to get the category name from that product.
                return optional($product->category)->name ?? 'Uncategorized';
            })->map(fn($items) => $items->sum(fn($i) => $i->quantity * $i->price_per_unit))->sortDesc()->take(5);

            $inventoryValueByCategory = LocationInventory::with(['inventoriable' => fn($morphTo) => $morphTo->morphWith([ProductVariant::class => ['product.category'], Product::class => ['category']])])
                ->get()->groupBy(fn($inv) => optional(optional($inv->inventoriable)->product ?? $inv->inventoriable)->category->name ?? 'Uncategorized')
                ->map(fn($invs) => $invs->sum(fn($inv) => $inv->stock_quantity * (optional($inv->inventoriable)->cost_price ?? 0)))
                ->filter(fn($val) => $val > 0)->sortDesc()->take(5);

            $lowStockItemsCount = LocationInventory::where('stock_quantity', '<=', $lowStockThreshold)->where('stock_quantity', '>', 0)->count();

            // (FIXED) Sales By Channel Query
            $salesByChannel = (clone $salesOrdersInPeriod)
                ->join('sales_channels', 'sales_orders.sales_channel_id', '=', 'sales_channels.id')
                ->select('sales_channels.name as channel_name', DB::raw('COUNT(sales_orders.id) as count'))
                ->groupBy('sales_channels.name')->orderBy('count', 'desc')->get();

            $pendingPOCount = PurchaseOrder::where('status', 'ordered')->count();

            // --- Assembling the final data array ---
            return [
                'totalRevenue' => $totalRevenue,
                'costOfGoodsSold' => $costOfGoodsSold,
                'netProfit' => $netProfit,
                'profitMargin' => $profitMargin,
                'totalCost' => $totalCost,
                'operationalCost' => $operationalCost,
                'salesCount' => $salesCount,
                'salesCountChange' => $salesCountChange,
                'averageOrderValue' => $averageOrderValue,
                'totalCustomers' => $totalCustomersInPeriod,
                'customerChangePercentage' => $customerChangePercentage,
                'purchaseOrdersCount' => $purchaseOrdersCount,
                'purchaseOrdersCountChange' => $purchaseOrdersCountChange,
                'lowStockItemsCount' => $lowStockItemsCount,
                'topSellingItems' => $topSellingItems,
                'mostProfitableItems' => $mostProfitableItems,
                'recentPurchases' => $recentPurchases,
                'recentSales' => $recentSales,
                'pendingPOCount' => $pendingPOCount,
                'totalPurchaseValue' => $totalPurchaseValue,
                'deadStockItemsCount' => $deadStockItemsCount,
                'repeatCustomerRate' => $repeatCustomerRate,
                'averagePurchaseValue' => $averagePurchaseValue,

                'profitMarginTarget' => $profitMarginTarget,
                'monthlyTargetAmount' => $monthlyTargetAmount,
                'currentRevenueForMonth' => $currentRevenueForMonth,
                'percentageAchieved' => $percentageAchieved,
                'monthlyTargetChartData' => [min(100, round($percentageAchieved, 2)), max(0, 100 - round($percentageAchieved, 2))],

                'revenueOverTimeLabels' => $revenueOverTime->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d')),
                'revenueOverTimeData' => $revenueOverTime->pluck('total_revenue'),
                'salesByCategoryLabels' => $salesByCategory->keys(),
                'salesByCategoryData' => $salesByCategory->values(),
                'inventoryValueByCategoryLabels' => $inventoryValueByCategory->keys(),
                'inventoryValueByCategoryData' => $inventoryValueByCategory->values(),

                // (FIXED) Use the correct alias 'channel_name' for the labels
                'salesByChannelLabels' => $salesByChannel->pluck('channel_name'),
                'salesByChannelData' => $salesByChannel->pluck('count'),
            ];
        });

        $this->updateCounter++;
        $this->dispatch('update-charts', data: $data);
        return view('livewire.dashboard', $data);
    }
}
