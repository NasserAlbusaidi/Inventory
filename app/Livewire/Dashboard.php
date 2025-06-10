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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // Properties for date selection
    public string $dateRange = 'last_30_days';
    public ?string $customStartDate = null;
    public ?string $customEndDate = null;
    public int $updateCounter = 0;


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

    public function render()
    {
        $cacheKey = 'dashboard_data';

        $data = Cache::remember($cacheKey, now()->addMinutes(1), function () {
            // --- Date and Comparison Period Setup ---
            $durationInDays = $this->endDate->diffInDays($this->startDate);
            $prevEndDate = $this->startDate->copy()->subDay()->endOfDay();
            $prevStartDate = $prevEndDate->copy()->subDays($durationInDays)->startOfDay();
            $customerIdentifierJsonPath = 'customer_details->>"$.name"';

            // --- Base Queries ---
            $salesOrdersInPeriod = SalesOrder::whereBetween('created_at', [$this->startDate, $this->endDate]);

            // --- Core Financial KPIs ---
            $totalRevenue = (clone $salesOrdersInPeriod)->sum('total_amount');

            // (FIXED) Cost of Goods Sold
            $salesOrderItemsForCost = SalesOrderItem::whereHas('salesOrder', fn($q) => $q->whereBetween('created_at', [$this->startDate, $this->endDate]))
                ->with('saleable:id,cost_price,track_inventory')->get();

            $costOfGoodsSold = $salesOrderItemsForCost->sum(function ($item) {
                if ($item->saleable && $item->saleable->track_inventory && $item->saleable->cost_price !== null) {
                    return $item->quantity * $item->saleable->cost_price;
                }
                return 0;
            });

            $purchaseOrdersInPeriod = PurchaseOrder::whereBetween('created_at', [$this->startDate, $this->endDate]);


            $operationalCost = RecurringExpense::where('start_date', '<=', $this->endDate)
                ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $this->endDate))
                ->sum('monthly_cost');

            $totalPurchaseValue = (clone $purchaseOrdersInPeriod)->sum('total_amount');
            $totalCost = $totalPurchaseValue + $operationalCost;
            $netProfit = $totalRevenue - $totalCost;
            $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

            // --- Sales KPIs & Comparisons ---
            $salesCount = (clone $salesOrdersInPeriod)->count();
            $salesCountPrev = SalesOrder::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
            $salesCountChange = $salesCountPrev > 0 ? (($salesCount - $salesCountPrev) / $salesCountPrev) * 100 : ($salesCount > 0 ? 100 : 0);



            $purchaseOrdersCount = PurchaseOrder::whereBetween('created_at', [$this->startDate, $this->endDate])->count();
            $purchaseOrdersCountPrev = PurchaseOrder::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
            $purchaseOrdersCountChange = $purchaseOrdersCountPrev > 0 ? (($purchaseOrdersCount - $purchaseOrdersCountPrev) / $purchaseOrdersCountPrev) * 100 : ($purchaseOrdersCount > 0 ? 100 : 0);

            $averageOrderValue = $salesCount > 0 ? $totalRevenue / $salesCount : 0;
            $averagePurchaseValue = $purchaseOrdersCount > 0 ? $totalPurchaseValue / $purchaseOrdersCount : 0;

            // --- (NEW) Inventory Health KPIs ---
            $deadStockCutoffDate = now()->subDays(90);

            // Get the IDs of all items sold in the last 90 days
            $soldItemIds = SalesOrderItem::whereHas('salesOrder', fn($q) => $q->where('created_at', '>=', $deadStockCutoffDate))
                ->select('saleable_id', 'saleable_type')
                ->distinct()
                ->get()
                ->map(fn($item) => $item->saleable_type . '_' . $item->saleable_id) // Create a unique key like 'App\Models\Product_1'
                ->flip(); // Flip so we can use isset() for fast lookups

            // Find inventory items that are NOT in the recently sold list
            $deadStockItemsCount = LocationInventory::where('stock_quantity', '>', 0)
                ->get()
                ->filter(function ($inventory) use ($soldItemIds) {
                    $key = $inventory->inventoriable_type . '_' . $inventory->inventoriable_id;
                    return !isset($soldItemIds[$key]);
                })
                ->count();

            // --- (NEW) Customer Loyalty KPI ---
            $customerOrders = SalesOrder::whereBetween('created_at', [$this->startDate, $this->endDate])
                ->select(DB::raw($customerIdentifierJsonPath . ' as customer_name'))
                ->get()
                ->groupBy('customer_name')
                ->filter(fn($group) => $group->first()->customer_name !== null); // Filter out null/guest customers

            $totalUniqueCustomers = $customerOrders->count();
            $repeatCustomers = $customerOrders->filter(fn($orders) => $orders->count() > 1)->count();

            $repeatCustomerRate = $totalUniqueCustomers > 0 ? ($repeatCustomers / $totalUniqueCustomers) * 100 : 0;

            // Customer Count
            $totalCustomersInPeriod = (clone $salesOrdersInPeriod)->selectRaw("COUNT(DISTINCT {$customerIdentifierJsonPath}) as count")->value('count');
            $totalCustomersPrevPeriod = SalesOrder::whereBetween('created_at', [$prevStartDate, $prevEndDate])->selectRaw("COUNT(DISTINCT {$customerIdentifierJsonPath}) as count")->value('count');
            $customerChangePercentage = $totalCustomersPrevPeriod > 0 ? (($totalCustomersInPeriod - $totalCustomersPrevPeriod) / $totalCustomersPrevPeriod) * 100 : ($totalCustomersInPeriod > 0 ? 100 : 0);

            // --- (FIXED) Top Selling & Most Profitable Items ---
            $allSoldItems = SalesOrderItem::whereHas('salesOrder', fn($q) => $q->whereBetween('created_at', [$this->startDate, $this->endDate]))
                ->with(['saleable' => function ($morphTo) {
                    $morphTo->morphWith([ProductVariant::class => ['product:id,name,sku']]);
                }])->get();

            $itemStats = $allSoldItems->groupBy(function ($item) {
                if (!$item->saleable) return 'deleted_item';
                return get_class($item->saleable) . '_' . $item->saleable->id;
            })->map(function ($items) {
                $firstItem = $items->first()->saleable;
                if (!$firstItem) return null;
                $displayName = ($firstItem instanceof ProductVariant) ? "{$firstItem->product->name} - {$firstItem->variant_name}" : $firstItem->name;
                return [
                    'display_name' => $displayName,
                    'total_quantity_sold' => $items->sum('quantity'),
                    'total_profit' => $items->sum(fn($i) => optional($firstItem)->cost_price !== null ? $i->quantity * ($i->price_per_unit - $firstItem->cost_price) : 0),
                ];
            })->filter();

            $topSellingItems = $itemStats->sortByDesc('total_quantity_sold')->take(7);
            $mostProfitableItems = $itemStats->sortByDesc('total_profit')->where('total_profit', '>', 0)->take(7);

            // --- Recent Orders ---
            $recentPurchases = PurchaseOrder::with(['supplier', 'items.purchasable' => fn($morphTo) => $morphTo->morphWith([ProductVariant::class => ['product:id,name'], Product::class => []])])->latest()->limit(5)->get();
            $recentSales = SalesOrder::with(['items.saleable' => fn($morphTo) => $morphTo->morphWith([ProductVariant::class => ['product:id,name'], Product::class => []])])->latest()->limit(5)->get();

            // --- Settings --- //
            $lowStockThreshold = app('settings')->get('low_stock_threshold', 5);
            $monthlyTargetAmount = (float) app('settings')->get('monthly_revenue_target', 6000);
            $profitMarginTarget = (float) app('settings')->get('profit_margin_target', 15);

            $monthlyTargetAmount = (float) app('settings')->get('monthly_revenue_target', 6000);

            // ALWAYS calculate revenue for the *current calendar month* for this specific KPI
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
            $currentRevenueForMonth = SalesOrder::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('total_amount');

            // Calculate the percentage achieved based on the correct monthly revenue
            $percentageAchieved = ($monthlyTargetAmount > 0) ? ($currentRevenueForMonth / $monthlyTargetAmount) * 100 : 0;

            // --- Sales By Category ---
            $salesByCategory = $allSoldItems->groupBy(function ($item) {
                if (!$item->saleable) return 'Other';
                $product = ($item->saleable instanceof ProductVariant) ? $item->saleable->product : $item->saleable;
                return optional($product->category)->name ?? 'Uncategorized';
            })->map(fn($items) => $items->sum(fn($i) => $i->quantity * $i->price_per_unit))->sortDesc()->take(5);

            // --- Inventory Stats ---
            $inventoryValueByCategory = LocationInventory::with(['inventoriable' => fn($morphTo) => $morphTo->morphWith([ProductVariant::class => ['product.category'], Product::class => ['category']])])
                ->get()->groupBy(fn($inv) => optional(optional($inv->inventoriable)->product ?? $inv->inventoriable)->category->name ?? 'Uncategorized')
                ->map(fn($invs) => $invs->sum(fn($inv) => $inv->stock_quantity * (optional($inv->inventoriable)->cost_price ?? 0)))
                ->filter(fn($val) => $val > 0)->sortDesc()->take(5);

            $lowStockItemsCount = LocationInventory::where('stock_quantity', '<=', $lowStockThreshold)->where('stock_quantity', '>', 0)->count();
            $percentageAchieved = ($monthlyTargetAmount > 0) ? ($totalRevenue / $monthlyTargetAmount) * 100 : 0;

            // --- Chart Data & Other Stats ---
            $revenueOverTime = (clone $salesOrdersInPeriod)->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total_revenue'))->groupBy('date')->orderBy('date')->get();
            $salesByChannel = (clone $salesOrdersInPeriod)->select('channel', DB::raw('COUNT(*) as count'))->groupBy('channel')->get();
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

                // settings
                'profitMarginTarget' => $profitMarginTarget,
                'monthlyTargetAmount' => $monthlyTargetAmount,
                'currentRevenueForMonth' => $currentRevenueForMonth,
                'percentageAchieved' => $percentageAchieved,
                'monthlyTargetChartData' => [
                    min(100, round($percentageAchieved, 2)), // Cap at 100% for the 'achieved' part
                    max(0, 100 - round($percentageAchieved, 2)) // The 'remaining' part
                ],



                // Chart-specific data
                'revenueOverTimeLabels' => $revenueOverTime->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d')),
                'revenueOverTimeData' => $revenueOverTime->pluck('total_revenue'),
                'salesByCategoryLabels' => $salesByCategory->keys(),
                'salesByCategoryData' => $salesByCategory->values(),
                'inventoryValueByCategoryLabels' => $inventoryValueByCategory->keys(),
                'inventoryValueByCategoryData' => $inventoryValueByCategory->values(),
                'salesByChannelLabels' => $salesByChannel->pluck('channel')->map(fn($c) => ucwords(str_replace('_', ' ', $c))),
                'salesByChannelData' => $salesByChannel->pluck('count')->all(),
            ];
        });
        $this->updateCounter++;

        $this->dispatch('update-charts', data: $data);
        return view('livewire.dashboard', $data);
    }
}
