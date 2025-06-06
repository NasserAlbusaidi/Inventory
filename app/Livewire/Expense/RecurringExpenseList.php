<?php

namespace App\Livewire\Expense;

use App\Models\RecurringExpense;
use Livewire\Component;
use Livewire\WithPagination;

class RecurringExpenseList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'location_id';

    public function delete(RecurringExpense $expense)
    {
        // Optional: Add authorization check here
        $expense->delete();
        session()->flash('message', 'Expense deleted successfully.');
    }

    public function render()
    {
        $expenses = RecurringExpense::with('location')
            ->when($this->search, function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('location', function ($subQuery) {
                          $subQuery->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.expense.recurring-expense-list', [
            'expenses' => $expenses,
        ]);
    }
}
