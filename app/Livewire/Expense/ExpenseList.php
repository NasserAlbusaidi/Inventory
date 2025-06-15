<?php

namespace App\Livewire\Expense;

use App\Models\RecurringExpense;
use App\Models\OneTimeExpense;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ExpenseList extends Component
{
    use WithPagination;

    public $search = '';

    // (UPDATED) Delete method needs type to know which model to use
    public function delete($id, $type)
    {
        if ($type === 'recurring') {
            RecurringExpense::find($id)?->delete();
        } elseif ($type === 'one-time') {
            OneTimeExpense::find($id)?->delete();
        }
        session()->flash('message', 'Expense deleted successfully.');
    }

    public function render()
    {
        $perPage = 10;
        $currentPage = $this->getPage();

        // Query for recurring expenses with aliased columns
        $recurring = RecurringExpense::with('location')
            ->select(
                'id',
                'description',
                'location_id',
                'monthly_cost as amount',
                'start_date as expense_date',
                DB::raw("'recurring' as type")
            );

        // Query for one-time expenses with aliased columns
        $oneTime = OneTimeExpense::with('location')
            ->select(
                'id',
                'description',
                'location_id',
                'amount',
                'expense_date',
                DB::raw("'one-time' as type")
            );

        // Apply search filter to both queries
        if ($this->search) {
            $recurring->where(function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('location', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
            });
            $oneTime->where(function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('location', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
            });
        }

        // Combine the results
        $combinedQuery = $oneTime->union($recurring);

        // Because a UNION query can't be paginated directly with orderBy on relationships,
        // we fetch all results, sort them, and then manually paginate the collection.
        $allExpenses = $combinedQuery->get()->sortByDesc('expense_date');

        // Manual Pagination
        $currentPageItems = $allExpenses->slice(($currentPage - 1) * $perPage, $perPage);
        $paginatedExpenses = new LengthAwarePaginator($currentPageItems, $allExpenses->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('livewire.expense.expense-list', [
            'expenses' => $paginatedExpenses,
        ]);
    }
}
