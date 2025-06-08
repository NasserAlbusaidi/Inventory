<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;

class Settings extends Component
{
 public $monthly_revenue_target;
    public $profit_margin_target;
    public $low_stock_threshold;

    /**
     * The mount method loads the current settings from the database.
     */
    public function mount()
    {
        $this->monthly_revenue_target = Setting::where('key', 'monthly_revenue_target')->first()->value ?? 6000;
        $this->profit_margin_target = Setting::where('key', 'profit_margin_target')->first()->value ?? 15;
        $this->low_stock_threshold = Setting::where('key', 'low_stock_threshold')->first()->value ?? 10;
    }

    /**
     * This method is called when the form is submitted.
     */
    public function save()
    {
        $this->validate([
            'monthly_revenue_target' => 'required|numeric|min:0',
            'profit_margin_target' => 'required|numeric|min:0|max:100',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);


        // Use updateOrCreate to either update the existing setting or create it if it doesn't exist.
        Setting::updateOrCreate(['key' => 'monthly_revenue_target'], ['value' => $this->monthly_revenue_target]);
        Setting::updateOrCreate(['key' => 'profit_margin_target'], ['value' => $this->profit_margin_target]);
        Setting::updateOrCreate(['key' => 'low_stock_threshold'], ['value' => $this->low_stock_threshold]);


        // Invalidate the settings cache (we will create this helper next)
        app('settings')->flushCache();
        // Flash a success message
        session()->flash('success', 'Settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.settings')->layout('components.layouts.livewire');
    }
}
