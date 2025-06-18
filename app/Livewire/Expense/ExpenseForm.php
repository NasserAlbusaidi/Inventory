<?php

namespace App\Livewire\Expense;

use App\Models\Location;
use App\Models\RecurringExpense;
use App\Models\OneTimeExpense; // <-- Add this
use Livewire\Component;

class ExpenseForm extends Component
{
    // Common Properties
    public ?int $expenseId = null;
    public string $description = '';
    public $location_id = '';

    // (NEW) To control the form's state
    public string $expense_type = 'recurring';

    // Recurring Expense Properties
    public $monthly_cost = '';
    public ?string $start_date = null;
    public ?string $end_date = null;

    // One-Time Expense Properties
    public $amount = '';
    public ?string $expense_date = null;

    public $allLocations;

    // (UPDATED) Dynamic validation rules
    protected function rules()
    {
        if ($this->expense_type === 'recurring') {
            return [
                'description' => 'required|string|max:255',
                'location_id' => 'nullable|exists:locations,id',
                'monthly_cost' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ];
        } else { // 'one-time'
            return [
                'description' => 'required|string|max:255',
                'location_id' => 'nullable|exists:locations,id',
                'amount' => 'required|numeric|min:0',
                'expense_date' => 'required|date',
            ];
        }
    }

    // (UPDATED) Mount method to handle both types
    public function mount($type = 'recurring', $id = null)
    {
        $this->allLocations = Location::orderBy('name')->get();
        $this->expense_type = in_array($type, ['recurring', 'one-time']) ? $type : 'recurring';

        // Set defaults for new expenses
        $this->start_date = now()->format('Y-m-d');
        $this->expense_date = now()->format('Y-m-d');

        if ($id) {
            $this->expenseId = $id;
            if ($this->expense_type === 'recurring') {
                $expense = RecurringExpense::findOrFail($id);
                $this->description = $expense->description;
                $this->location_id = $expense->location_id;
                $this->monthly_cost = $expense->monthly_cost;
                $this->start_date = $expense->start_date->format('Y-m-d');
                $this->end_date = $expense->end_date ? $expense->end_date->format('Y-m-d') : null;
            } else {
                $expense = OneTimeExpense::findOrFail($id);
                $this->description = $expense->description;
                $this->location_id = $expense->location_id;
                $this->amount = $expense->amount;
                $this->expense_date = $expense->expense_date->format('Y-m-d');
            }
        }
    }

    // (UPDATED) Save method to handle both types
    public function saveExpense()
    {
        $this->validate();

        if ($this->expense_type === 'recurring') {
            $data = [
                'description' => $this->description,
                'location_id' => $this->location_id ?: null,
                'monthly_cost' => $this->monthly_cost,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ];
            RecurringExpense::updateOrCreate(['id' => $this->expenseId], $data);
        } else { // 'one-time'
            $data = [
                'description' => $this->description,
                'location_id' => $this->location_id ?: null,
                'amount' => $this->amount,
                'expense_date' => $this->expense_date,
            ];
            OneTimeExpense::updateOrCreate(['id' => $this->expenseId], $data);
        }

        session()->flash('message', 'Expense saved successfully.');
        return $this->redirectRoute('expenses.index');
    }

    public function render()
    {
        return view('livewire.expense.expense-form');
    }
}
