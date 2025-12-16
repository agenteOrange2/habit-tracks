<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class CategoryList extends Component
{
    public $showDeleteModal = false;
    public $categoryToDeleteId = null;
    public $categoryToDeleteName = '';
    public $deleteError = '';

    protected $listeners = [
        'categoryCreated' => '$refresh',
        'categoryUpdated' => '$refresh',
    ];

    public function mount()
    {
        // Check authorization
        Gate::authorize('viewAny', Category::class);
    }

    public function toggleActive(int $categoryId): void
    {
        try {
            $category = Category::findOrFail($categoryId);
            
            // Check authorization
            Gate::authorize('update', $category);
            
            $category->is_active = !$category->is_active;
            $category->save();
            
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => $category->is_active 
                    ? 'Categoría activada correctamente' 
                    : 'Categoría desactivada correctamente'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Error al cambiar el estado de la categoría'
            ]);
        }
    }

    public function confirmDelete(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);
        
        // Check authorization
        Gate::authorize('delete', $category);
        
        $this->categoryToDeleteId = $category->id;
        $this->categoryToDeleteName = $category->name;
        $this->deleteError = '';
        
        // Check if category can be deleted
        if (!$category->canBeDeleted()) {
            $habitsCount = $category->getHabitsCount();
            $this->deleteError = "No se puede eliminar porque hay {$habitsCount} hábito(s) asociado(s)";
        }
        
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if (!$this->categoryToDeleteId) {
            $this->closeDeleteModal();
            return;
        }
        
        try {
            $category = Category::findOrFail($this->categoryToDeleteId);
            
            // Check authorization
            Gate::authorize('delete', $category);
            
            // Double check if can be deleted
            if (!$category->canBeDeleted()) {
                $habitsCount = $category->getHabitsCount();
                $this->dispatch('notification', [
                    'type' => 'error',
                    'message' => "No se puede eliminar porque hay {$habitsCount} hábito(s) asociado(s)"
                ]);
                $this->closeDeleteModal();
                return;
            }
            
            $categoryName = $category->name;
            $category->delete();
            
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => "Categoría '{$categoryName}' eliminada correctamente"
            ]);
            
            $this->closeDeleteModal();
            
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Error al eliminar la categoría'
            ]);
            $this->closeDeleteModal();
        }
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->categoryToDeleteId = null;
        $this->categoryToDeleteName = '';
        $this->deleteError = '';
    }

    public function updateOrder(array $orderedIds): void
    {
        try {
            // Validate that all IDs exist
            $categories = Category::whereIn('id', $orderedIds)->get();
            
            if ($categories->count() !== count($orderedIds)) {
                throw new \Exception('Invalid category IDs provided');
            }
            
            // Check for duplicate order values before updating
            $orderValues = [];
            foreach ($orderedIds as $index => $categoryId) {
                $newOrder = $index + 1;
                if (in_array($newOrder, $orderValues)) {
                    throw new \Exception('Duplicate order values detected');
                }
                $orderValues[] = $newOrder;
            }
            
            // Update order for each category
            foreach ($orderedIds as $index => $categoryId) {
                Category::where('id', $categoryId)->update(['order' => $index + 1]);
            }
            
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Orden actualizado correctamente'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Error al actualizar el orden: ' . $e->getMessage()
            ]);
        }
    }

    public function moveUp(int $categoryId): void
    {
        try {
            $category = Category::findOrFail($categoryId);
            
            // Find the category above (with lower order value)
            $categoryAbove = Category::where('order', '<', $category->order)
                ->orderBy('order', 'desc')
                ->first();
            
            if ($categoryAbove) {
                // Swap order values
                $tempOrder = $category->order;
                $category->order = $categoryAbove->order;
                $categoryAbove->order = $tempOrder;
                
                $category->save();
                $categoryAbove->save();
                
                $this->dispatch('notification', [
                    'type' => 'success',
                    'message' => 'Orden actualizado correctamente'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Error al mover la categoría'
            ]);
        }
    }

    public function moveDown(int $categoryId): void
    {
        try {
            $category = Category::findOrFail($categoryId);
            
            // Find the category below (with higher order value)
            $categoryBelow = Category::where('order', '>', $category->order)
                ->orderBy('order', 'asc')
                ->first();
            
            if ($categoryBelow) {
                // Swap order values
                $tempOrder = $category->order;
                $category->order = $categoryBelow->order;
                $categoryBelow->order = $tempOrder;
                
                $category->save();
                $categoryBelow->save();
                
                $this->dispatch('notification', [
                    'type' => 'success',
                    'message' => 'Orden actualizado correctamente'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Error al mover la categoría'
            ]);
        }
    }

    public function render()
    {
        $categories = Category::query()
            ->withCount('habits')
            ->ordered()
            ->get();
        
        return view('livewire.admin.categories.category-list', [
            'categories' => $categories,
        ])->layout('components.layouts.app');
    }
}
