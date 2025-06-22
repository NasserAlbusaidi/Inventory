<?php

namespace App\Livewire\Category;

use App\Models\Activity;
use App\Models\Category; //
use Livewire\Component;
use Livewire\WithPagination;

class CategoryList extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function mount()
    {
        // Initialize search term if needed
        $this->search = request()->query('search', '');
    }
    /**
     * Reset pagination when the component is mounted or search term changes.
     */
    public function updatedSearch()
    {
        $this->resetPage(); // Reset pagination when search term changes
    }

    public function updatingSearch()
    {
        $this->resetPage(); // Reset pagination when search term changes
    }
    public function deleteCategory($categoryId)
    {
        $category = Category::find($categoryId);
        if ($category) {
            // Products associated with this category will have their category_id set to null
            // due to onDelete('set null') in the products migration.
            $category->delete();
            Activity::create([
                'type' => 'category_deleted',
                'description' => 'Category deleted: ' . $category->name,
            ]);
            session()->flash('message', 'Category deleted successfully.');
        } else {
            session()->flash('error', 'Category not found.');
        }

        // Resetting page after deletion might be needed if on a page that no longer exists
        $this->resetPage();
    }

    public function render()
    {
        $categories = Category::withCount('products') // Eager load product count
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString(); // Maintain search query in pagination links


        return view('livewire.category.category-list')
            ->with('categories', $categories)
        ->layout('components.layouts.livewire');
    }
}
