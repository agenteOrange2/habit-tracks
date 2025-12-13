<?php

namespace App\Livewire\Admin\Difficulties;

use App\Models\Difficulty;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class DifficultyForm extends Component
{
    public $showModal = false;
    public $difficultyId = null;
    public $name = '';
    public $icon = '⭐';
    public $points = 10;
    public $order = 0;
    public $isEditing = false;

    protected $listeners = [
        'openDifficultyForm' => 'openCreate',
        'editDifficulty' => 'openEdit',
    ];

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('difficulties', 'name')
                    ->where('user_id', auth()->id())
                    ->ignore($this->difficultyId),
            ],
            'icon' => 'required|string|max:10',
            'points' => 'required|integer|min:1|max:1000',
            'order' => 'integer|min:0',
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre es requerido',
        'name.min' => 'El nombre debe tener al menos 3 caracteres',
        'name.max' => 'El nombre no puede exceder 50 caracteres',
        'name.unique' => 'Ya existe una dificultad con este nombre',
        'icon.required' => 'El icono es requerido',
        'points.required' => 'Los puntos son requeridos',
        'points.integer' => 'Los puntos deben ser un número entero',
        'points.min' => 'Los puntos deben ser al menos 1',
        'points.max' => 'Los puntos no pueden exceder 1000',
    ];

    public function openCreate()
    {
        $this->resetForm();
        $this->isEditing = false;
        
        // Get the next order number
        $this->order = Difficulty::max('order') + 1;
        
        $this->showModal = true;
    }

    public function openEdit($difficultyId)
    {
        try {
            $difficulty = Difficulty::findOrFail($difficultyId);
            
            // Check authorization
            Gate::authorize('update', $difficulty);
            
            $this->difficultyId = $difficulty->id;
            $this->name = $difficulty->name;
            $this->icon = $difficulty->icon ?? '⭐';
            $this->points = $difficulty->points;
            $this->order = $difficulty->order;
            $this->isEditing = true;
            
            $this->showModal = true;
            
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Error al cargar la dificultad'
            ]);
        }
    }

    public function save()
    {
        $this->validate();
        
        try {
            if ($this->isEditing) {
                // Update existing difficulty
                $difficulty = Difficulty::findOrFail($this->difficultyId);
                
                // Check authorization
                Gate::authorize('update', $difficulty);
                
                $difficulty->update([
                    'name' => $this->name,
                    'icon' => $this->icon,
                    'points' => $this->points,
                    'order' => $this->order,
                ]);
                
                $this->dispatch('difficultyUpdated');
                $this->dispatch('notification', [
                    'type' => 'success',
                    'message' => 'Dificultad actualizada correctamente'
                ]);
                
            } else {
                // Create new difficulty
                Gate::authorize('create', Difficulty::class);
                
                Difficulty::create([
                    'name' => $this->name,
                    'icon' => $this->icon,
                    'points' => $this->points,
                    'order' => $this->order,
                    'is_active' => true,
                ]);
                
                $this->dispatch('difficultyCreated');
                $this->dispatch('notification', [
                    'type' => 'success',
                    'message' => 'Dificultad creada correctamente'
                ]);
            }
            
            $this->closeModal();
            
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Error al guardar la dificultad'
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
        $this->difficultyId = null;
        $this->name = '';
        $this->icon = '⭐';
        $this->points = 10;
        $this->order = 0;
    }

    public function render()
    {
        return view('livewire.admin.difficulties.difficulty-form');
    }
}
