<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CategoryForm extends Component
{
    public $showModal = false;
    public $categoryId = null;
    public $name = '';
    public $icon = '📁';
    public $color = '#3B82F6';
    public $order = 0;
    public $isEditing = false;

    protected $listeners = [
        'openCategoryForm' => 'openCreate',
        'editCategory' => 'openEdit',
    ];

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('categories', 'name')->ignore($this->categoryId),
            ],
            'icon' => 'required|string|max:10',
            'color' => 'required|string|max:7',
            'order' => 'integer|min:0',
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre es requerido',
        'name.min' => 'El nombre debe tener al menos 3 caracteres',
        'name.max' => 'El nombre no puede exceder 50 caracteres',
        'name.unique' => 'Ya existe una categoría con este nombre',
        'icon.required' => 'El icono es requerido',
        'color.required' => 'El color es requerido',
    ];

    public function openCreate()
    {
        $this->resetForm();
        $this->isEditing = false;
        
        // Get the next order number
        $this->order = Category::max('order') + 1;
        
        $this->showModal = true;
    }

    public function openEdit($categoryId)
    {
        try {
            $category = Category::findOrFail($categoryId);
            
            // Check authorization
            Gate::authorize('update', $category);
            
            $this->categoryId = $category->id;
            $this->name = $category->name;
            $this->icon = $category->icon ?? '📁';
            $this->color = $category->color;
            $this->order = $category->order;
            $this->isEditing = true;
            
            $this->showModal = true;
            
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Error al cargar la categoría'
            ]);
        }
    }

    public function save()
    {
        $this->validate();
        
        try {
            if ($this->isEditing) {
                // Update existing category
                $category = Category::findOrFail($this->categoryId);
                
                // Check authorization
                Gate::authorize('update', $category);
                
                $category->update([
                    'name' => $this->name,
                    'icon' => $this->icon,
                    'color' => $this->color,
                    'order' => $this->order,
                ]);
                
                $this->dispatch('categoryUpdated');
                $this->dispatch('notification', [
                    'type' => 'success',
                    'message' => 'Categoría actualizada correctamente'
                ]);
                
            } else {
                // Create new category
                Gate::authorize('create', Category::class);
                
                Category::create([
                    'name' => $this->name,
                    'icon' => $this->icon,
                    'color' => $this->color,
                    'order' => $this->order,
                    'is_active' => true,
                ]);
                
                $this->dispatch('categoryCreated');
                $this->dispatch('notification', [
                    'type' => 'success',
                    'message' => 'Categoría creada correctamente'
                ]);
            }
            
            $this->closeModal();
            
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Error al guardar la categoría'
            ]);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->icon = '📁';
        $this->color = '#3B82F6';
        $this->order = 0;
    }

    public function getAvailableColorsProperty()
    {
        return [
            '#3B82F6', // Blue - Productivity
            '#10B981', // Green - Health
            '#8B5CF6', // Purple - Learning
            '#F59E0B', // Yellow - Social
            '#EC4899', // Pink - Creative
            '#6366F1', // Indigo - Household
            '#14B8A6', // Teal - Finance
            '#F97316', // Orange - Personal
            '#EF4444', // Red - Extra
        ];
    }

    public function render()
    {
        return view('livewire.admin.categories.category-form');
    }
}
