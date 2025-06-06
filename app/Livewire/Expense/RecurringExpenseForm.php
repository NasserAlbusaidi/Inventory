<?php

namespace App\Livewire\Expense;

use App\Models\Location;
use App\Models\RecurringExpense;
use Livewire\Component;

class RecurringExpenseForm extends Component
{
    // Properties to hold all form data
    public ?int $expenseId = null;
    public string $description = '';
    public $location_id = ''; // Use mixed type for initial empty value
    public $monthly_cost = '';
    public ?string $start_date = null;
    public ?string $end_date = null;

    // To populate the locations dropdown
    public $allLocations;

    // Validation rules now map directly to our public properties
    protected function rules()
    {
        return [
            'description' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'monthly_cost' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    /**
     * Mount the component and populate data for editing or set defaults for creating.
     */
    public function mount($expense = null)
    {
       $this->allLocations = Location::orderBy('name')->get();
        $this->start_date = now()->format('Y-m-d');


        if ($expense) {
            $expense = RecurringExpense::findOrFail($expense);
            $this->expenseId = $expense->id;
            $this->description = $expense->description;
            $this->location_id = $expense->location_id;
            $this->monthly_cost = $expense->monthly_cost;
            // Ensure dates are formatted correctly for the HTML date input
            $this->start_date = $expense->start_date->format('Y-m-d');
            $this->end_date = $expense->end_date ? $expense->end_date->format('Y-m-d') : null;
        }

    }

    /**
     * Save or update the recurring expense.
     */
    public function saveExpense()
    {

        // dd($this->expenseId, $this->description, $this->location_id, $this->monthly_cost, $this->start_date, $this->end_date);
        $this->validate();

        $data = [
            'description' => $this->description,
            'location_id' => $this->location_id,
            'monthly_cost' => $this->monthly_cost,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];

        if ($this->expenseId) {
            // Update existing expense
            $expense = RecurringExpense::find($this->expenseId);
            $expense->update($data);
            session()->flash('message', 'Recurring expense updated successfully.');
        } else {
            // Create new expense
            RecurringExpense::create($data);
            session()->flash('message', 'Recurring expense created successfully.');
        }

        $this->redirectRoute('expenses.index');
    }

    public function render()
    {

        return view('livewire.expense.recurring-expense-form');
    }
}
