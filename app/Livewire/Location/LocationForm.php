<?php

namespace App\Livewire\Location;

use App\Models\Location;
use Livewire\Component;

class LocationForm extends Component
{
    public ?Location $location = null; // Type hint for model binding
    public $name = '';
    public $description = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:locations,name' . ($this->location && $this->location->exists ? ',' . $this->location->id : ''),
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function mount(Location $location = null) // Route model binding
    {
        if ($location && $location->exists) {
            $this->location = $location;
            $this->name = $location->name;
            $this->description = $location->description;
        } else {
            $this->location = new Location(); // Initialize for creation
        }
    }

    public function saveLocation()
    {
        $this->validate();

        $this->location->name = $this->name;
        $this->location->description = $this->description;
        $this->location->save();

        session()->flash('message', 'Location ' . ($this->location->wasRecentlyCreated ? 'created' : 'updated') . ' successfully.');

        return redirect()->route('locations.index');
    }

    public function render()
    {
        return view('livewire.location.location-form')
               ->layoutData(['title' => $this->location && $this->location->exists ? 'Edit Location' : 'Create Location']) // Pass title to layout
               ->layout('components.layouts.livewire');
    }
}
