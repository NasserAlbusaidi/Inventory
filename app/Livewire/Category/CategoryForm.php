<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Component;

class CategoryForm extends Component
{
    public ?Category $category = null; // Use type hinting for model binding
    public $categoryId; // Separate property for route model binding if needed, or rely on $category->id

    public $name = '';
    public $description = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name' . ($this->category ? ',' . $this->category->id : ''),
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function mount(Category $category = null) // Route model binding
    {
        if ($category && $category->exists) {
            $this->category = $category; // Assign the bound model
            $this->name = $category->name;
            $this->description = $category->description;
        } else {
            $this->category = new Category(); // Initialize for creation
        }
    }

    public function saveCategory()
    {
        $this->validate();

        $this->category->name = $this->name;
        $this->category->description = $this->description;
        $this->category->save();

        session()->flash('message', 'Category ' . ($this->category->wasRecentlyCreated ? 'created' : 'updated') . ' successfully.');

        return redirect()->route('categories.index');
    }

    public function render()
    {
        return view('livewire.category.category-form')
            ->layout('layouts.app'); // Or components.layouts.app
    }
}
