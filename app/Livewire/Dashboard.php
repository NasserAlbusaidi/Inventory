<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\RecurringExpense;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // These properties will hold the state of our date selector
    public string $dateRange = 'last_30_days';
    public ?string $customStartDate = null;
    public ?string $customEndDate = null;

    // These will hold the calculated dates
    protected Carbon $startDate;
    protected Carbon $endDate;

    /**
     * The mount method is like a constructor, it runs once when the component is first loaded.
     */
    public function mount()
    {
        $this->calculateDateRange();
    }

    /**
     * This is a Livewire hook that runs whenever the $dateRange property is updated.
     */
    public function updatedDateRange()
    {
        $this->calculateDateRange();
        // The render() method will be automatically called by Livewire after this.
    }

    /**
     * Hooks for the custom date inputs. They trigger a re-render when changed.
     */
    public function updatedCustomStartDate()
    {
        $this->calculateDateRange();
    }

    public function updatedCustomEndDate()
    {
        $this->calculateDateRange();
    }


    /**
     * Helper method to calculate the start and end dates based on the selected range.
     */
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
                // Use the custom dates, with fallbacks to today if they aren't set yet.
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

    /**
     * The render method is the heart of the component. It fetches all the data and returns the view.
     * It will re-run automatically whenever a public property changes.
     */
    public function render()
    {


        $cacheKey = 'dashboard_data';
        $data = Cache::remember($cacheKey, now()->addMinutes(1), function () {


            $durationInDays = $this->endDate->diffInDays($this->startDate);
            $prevEndDate = $this->startDate->copy()->subDay()->endOfDay();
            $prevStartDate = $prevEndDate->copy()->subDays($durationInDays)->startOfDay();

            $customerIdentifierJsonPath = 'customer_details->>"$.name"';
            $rawCustomerIdentifier = DB::raw($customerIdentifierJsonPath);

            // --- Financial KPIs ---
            $salesOrdersQuery = SalesOrder::whereBetween('created_at', [$this->startDate, $this->endDate]);
            $totalRevenueLast30Days = (clone $salesOrdersQuery)->sum('total_amount');

            $costOfGoodsSold = SalesOrderItem::whereHas('salesOrder', function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
            })
                ->join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')
                ->sum(DB::raw('sales_order_items.quantity * product_variants.cost_price'));

                $costOfGoods = PurchaseOrder::whereBetween('created_at', [$this->startDate, $this->endDate])
                ->sum('total_amount');
            $operationalCost = RecurringExpense::where('start_date', '<=', $this->endDate)
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', $this->endDate);
                })
                ->sum('monthly_cost');

            // Note: For simplicity, we are keeping the operational cost as a monthly flat rate for now.
            // A more accurate calculation would prorate this based on the selected date range.

            $totalCostLast30Days = $costOfGoods + $operationalCost; // Simplified from your original for clarity
            $netProfitLast30Days = $totalRevenueLast30Days - $totalCostLast30Days;
            $profitMarginLast30Days = $totalRevenueLast30Days > 0 ? ($netProfitLast30Days / $totalRevenueLast30Days) * 100 : 0;

            // --- Sales KPIs & Comparisons ---
            $salesCountLast30Days = (clone $salesOrdersQuery)->count();
            $salesCountPrevPeriod = SalesOrder::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
            $salesCountChangePercentage = $salesCountPrevPeriod != 0
                ? (($salesCountLast30Days - $salesCountPrevPeriod) / $salesCountPrevPeriod) * 100
                : ($salesCountLast30Days > 0 ? 100 : 0);



            $totalRevenuePrevPeriod = SalesOrder::whereBetween('created_at', [$prevStartDate, $prevEndDate])->sum('total_amount');
            $averageOrderValue = $salesCountLast30Days > 0 ? $totalRevenueLast30Days / $salesCountLast30Days : 0;
            $averageOrderValuePrev = $salesCountPrevPeriod > 0 ? $totalRevenuePrevPeriod / $salesCountPrevPeriod : 0;
            $averageOrderValueChangePercentage = $averageOrderValuePrev != 0 ? (($averageOrderValue - $averageOrderValuePrev) / $averageOrderValuePrev) * 100 : ($averageOrderValue > 0 ? 100 : 0);

            $purchaseOrdersCountLast30Days = PurchaseOrder::whereBetween('created_at', [$this->startDate, $this->endDate])->count();
            $purchaseOrdersCountPrevPeriod = PurchaseOrder::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
            $purchaseOrdersCountChangePercentage = $purchaseOrdersCountPrevPeriod != 0 ? (($purchaseOrdersCountLast30Days - $purchaseOrdersCountPrevPeriod) / $purchaseOrdersCountPrevPeriod) * 100 : ($purchaseOrdersCountLast30Days > 0 ? 100 : 0);


            $newCustomersLast30Days = SalesOrder::select($rawCustomerIdentifier)
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->distinct()
                ->count($rawCustomerIdentifier);
            $newCustomersPrevPeriod = SalesOrder::select($rawCustomerIdentifier)
                ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
                ->distinct()
                ->count($rawCustomerIdentifier);
            $newCustomersChangePercentage = $newCustomersPrevPeriod != 0
                ? (($newCustomersLast30Days - $newCustomersPrevPeriod) / $newCustomersPrevPeriod) * 100
                : ($newCustomersLast30Days > 0 ? 100 : 0);

            $mostProfitableVariants = SalesOrderItem::with('productVariant.product')->select('product_variant_id', DB::raw('SUM(sales_order_items.quantity * sales_order_items.price) - SUM(sales_order_items.quantity * product_variants.cost_price) as total_profit'))->join('product_variants', 'sales_order_items.product_variant_id', '=', 'product_variants.id')->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')->whereBetween('sales_orders.created_at', [$this->startDate, $this->endDate])->groupBy('product_variant_id')->orderByDesc('total_profit')->havingRaw('total_profit > 0')->limit(7)->get();
            $topSellingVariants = SalesOrderItem::with('productVariant.product')->select('product_variant_id', DB::raw('SUM(sales_order_items.quantity) as total_quantity_sold'))->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')->whereBetween('sales_orders.created_at', [$this->startDate, $this->endDate])->groupBy('product_variant_id')->orderByDesc('total_quantity_sold')->limit(7)->get();
            $recentPurchases = PurchaseOrder::with(['supplier', 'items.productVariant.product'])->whereBetween('created_at', [$this->startDate, $this->endDate])->orderBy('created_at', 'desc')->limit(5)->get();
            $recentSalesOrders = SalesOrder::with(['items.productVariant.product'])->orderBy('created_at', 'desc')->limit(5)->get();
            $revenueOverTime = SalesOrder::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total_revenue'))->whereBetween('created_at', [$this->startDate, $this->endDate])->groupBy('date')->orderBy('date', 'asc')->get();
            $revenueOverTimeLabels = $revenueOverTime->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d'))->all();
            $revenueOverTimeData = $revenueOverTime->pluck('total_revenue')->map(fn($value) => (float) $value)->all();
            $salesByCategory = Category::query()->join('products', 'categories.id', '=', 'products.category_id')->join('product_variants', 'products.id', '=', 'product_variants.product_id')->join('sales_order_items', 'product_variants.id', '=', 'sales_order_items.product_variant_id')->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')->whereBetween('sales_orders.created_at', [$this->startDate, $this->endDate])->select('categories.name', DB::raw('SUM(sales_order_items.quantity * sales_order_items.price) as total_sales'))->groupBy('categories.id', 'categories.name')->orderByDesc('total_sales')->havingRaw('SUM(sales_order_items.quantity * sales_order_items.price) > 0')->limit(5)->get();
            $salesByCategoryChartLabels = $salesByCategory->pluck('name')->all();
            $salesByCategoryChartDataValues = $salesByCategory->pluck('total_sales')->map(fn($value) => (float) $value)->all();
            $salesByChannel = SalesOrder::select('channel', DB::raw('COUNT(*) as count'))->whereBetween('created_at', [$this->startDate, $this->endDate])->groupBy('channel')->orderByDesc('count')->get();
            $salesByChannelLabels = $salesByChannel->pluck('channel')->map(fn($channel) => ucwords(str_replace('_', ' ', $channel)))->all();
            $salesByChannelData = $salesByChannel->pluck('count')->all();

            $totalSalesOrdersCount = SalesOrder::count();
            $totalPurchaseOrdersCount = PurchaseOrder::count();

            $totalCustomers = SalesOrder::select($rawCustomerIdentifier)->distinct()->count($rawCustomerIdentifier);
            $pendingPOCount = PurchaseOrder::where('status', 'pending')->count();
            $completedPOCount = PurchaseOrder::where('status', 'completed')->count();
            $purchaseOrderValue = PurchaseOrder::whereBetween('created_at', [$this->startDate, $this->endDate])->sum('total_amount');
            $lowStockItemsCountLast30Days = ProductVariant::where('track_inventory', true)
                ->where('stock_quantity', '<=', config('business.low_stock_threshold', 5)) // Assuming a low stock threshold from config
                ->whereHas('salesOrderItems', function ($query) {
                    $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
                })
                ->count();
            $lowStockItemsCountChangePercentage = 0; // This would require a previous period comparison, similar to sales count

            $inventoryValueByCategory = Category::withSum(['productVariants as total_inventory_value' => fn($query) => $query->where('track_inventory', true)], DB::raw('stock_quantity * cost_price'))->having('total_inventory_value', '>', 0)->orderByDesc('total_inventory_value')->limit(5)->get();
            $inventoryValueByCategoryLabels = $inventoryValueByCategory->pluck('name')->all();
            $inventoryValueByCategoryData = $inventoryValueByCategory->pluck('total_inventory_value')->all();

            // Monthly Target Chart (Recalculating based on current selection)
            $monthlyTargetAmount = app('settings')->get('monthly_revenue_target', 6000);
            $currentRevenueForMonth = $totalRevenueLast30Days;
            $percentageAchieved = ($monthlyTargetAmount > 0) ? ($currentRevenueForMonth / $monthlyTargetAmount) * 100 : 0;
            $monthlyTargetChartData = [round($percentageAchieved, 2), max(0, 100 - round($percentageAchieved, 2))];
            $monthlyTargetChartColors = ['#4CAF50', '#E0E0E0'];
            $percentageChangeFromLastMonth = 0; // This comparison logic would need to be updated to compare to the "previous equivalent period"

            return [
                'totalRevenueLast30Days' => $totalRevenueLast30Days,
                'costOfGoodsSold' => $costOfGoodsSold,
                'netProfitLast30Days' => $netProfitLast30Days,
                'profitMarginLast30Days' => $profitMarginLast30Days,
                'totalCostLast30Days' => $totalCostLast30Days,
                'operationalCost' => $operationalCost,
                'salesCountLast30Days' => $salesCountLast30Days,
                'salesCountChangePercentage' => $salesCountChangePercentage,
                'averageOrderValue' => $averageOrderValue,
                'averageOrderValueChangePercentage' => $averageOrderValueChangePercentage,
                'newCustomersLast30Days' => $newCustomersLast30Days,
                'newCustomersChangePercentage' => $newCustomersChangePercentage,
                'purchaseOrdersCountLast30Days' => $purchaseOrdersCountLast30Days,
                'purchaseOrdersCountChangePercentage' => $purchaseOrdersCountChangePercentage,
                'lowStockItemsCountLast30Days' => $lowStockItemsCountLast30Days,
                'lowStockItemsCountChangePercentage' => $lowStockItemsCountChangePercentage,
                'mostProfitableVariants' => $mostProfitableVariants,
                'topSellingVariants' => $topSellingVariants,
                'recentPurchases' => $recentPurchases,
                'recentSalesOrders' => $recentSalesOrders,
                'dateRange' => [
                    'start' => $this->startDate->toDateString(),
                    'end' => $this->endDate->toDateString(),
                ],
                'costOfGoods' => $costOfGoods,
                'totalSalesOrdersCount' => $totalSalesOrdersCount,
                'totalPurchaseOrdersCount' => $totalPurchaseOrdersCount,
                'totalCustomers' => $totalCustomers,
                'pendingPOCount' => $pendingPOCount,
                'completedPOCount' => $completedPOCount,
                'purchaseOrderValue' => $purchaseOrderValue,
                'monthlyTargetAmount' => $monthlyTargetAmount,
                'currentRevenueForMonth' => $currentRevenueForMonth,
                'percentageAchieved' => $percentageAchieved,
                'percentageChangeFromLastMonth' => $percentageChangeFromLastMonth,
                'revenueOverTimeLabels' => $revenueOverTime->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d'))->all(),
                'revenueOverTimeData' => $revenueOverTime->pluck('total_revenue')->map(fn($value) => (float) $value)->all(),
                'salesByCategoryChartLabels' => $salesByCategory->pluck('name')->all(),
                'salesByCategoryChartDataValues' => $salesByCategory->pluck('total_sales')->map(fn($value) => (float) $value)->all(),
                'salesByChannelLabels' => $salesByChannel->pluck('channel')->map(fn($channel) => ucwords(str_replace('_', ' ', $channel)))->all(),
                'salesByChannelData' => $salesByChannel->pluck('count')->all(),
                'inventoryValueByCategoryLabels' => $inventoryValueByCategory->pluck('name')->all(),
                'inventoryValueByCategoryData' => $inventoryValueByCategory->pluck('total_inventory_value')->all(),
                'monthlyTargetChartData' => [round($percentageAchieved, 2), max(0, 100 - round($percentageAchieved, 2))],
                'monthlyTargetChartColors' => ['#4CAF50', '#E0E0E0'],
            ];
        });

        $this->dispatch('update-charts', data: $data);
        return view('livewire.dashboard', $data);
    }
}
