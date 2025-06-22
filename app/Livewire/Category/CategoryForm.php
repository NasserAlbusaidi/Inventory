<?php

namespace App\Livewire\Category;

use App\Models\Activity;
use App\Models\Category;
use Livewire\Component;

class CategoryForm extends Component
{
    public ?Category $categoryInstance = null; // Renamed
    public $name = '';
    public $description = '';

    protected function rules(): array
    {
        $categoryId = $this->categoryInstance && $this->categoryInstance->exists ? $this->categoryInstance->id : null;
        return [
            'name' => 'required|string|max:255|unique:categories,name' . ($categoryId ? ',' . $categoryId : ''),
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function mount($category = null) // $category here can be an ID or null
    {
        if ($category) {
            $this->categoryInstance = Category::find($category);
            if ($this->categoryInstance) {
                $this->name = $this->categoryInstance->name;
                $this->description = $this->categoryInstance->description;
            } else {
                $this->categoryInstance = new Category();
            }
        } else {
            $this->categoryInstance = new Category();
        }
    }

    public function saveCategory()
    {
        $this->validate();
        if(!$this->categoryInstance) {
            $this->categoryInstance = new Category();
        }
        $this->categoryInstance->name = $this->name;
        $this->categoryInstance->description = $this->description;
        $this->categoryInstance->save();

        // Save Activity Log
        Activity::create([
            'type' => $this->categoryInstance->wasRecentlyCreated ? 'category_created' : 'category_updated',
            'description' => 'Category ' . ($this->categoryInstance->wasRecentlyCreated ? 'created' : 'updated') . ': ' . $this->name,
        ]);

        session()->flash('message', 'Category ' . ($this->categoryInstance->wasRecentlyCreated ? 'created' : 'updated') . ' successfully.');
        return redirect()->route('categories.index');
    }

    public function render()
    {
        return view('livewire.category.category-form')
               ->layoutData(['title' => $this->categoryInstance && $this->categoryInstance->exists ? 'Edit Category' : 'Create Category'])
               ->layout('components.layouts.livewire');
    }
}
