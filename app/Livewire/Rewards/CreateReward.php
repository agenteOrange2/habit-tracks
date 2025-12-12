<?php

namespace App\Livewire\Rewards;

use Livewire\Component;
use App\Enums\RewardCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateReward extends Component
{
    public string $name = '';
    public string $description = '';
    public string $category = 'entertainment';
    public int $cost_points = 50;
    public string $icon = '🎁';
    public bool $is_available = true;

    protected function rules(): array
    {
        $validCategories = array_column(RewardCategory::cases(), 'value');
        
        return [
            'name' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|max:500',
            'category' => ['required', 'string', Rule::in($validCategories)],
            'cost_points' => 'required|integer|min:1',
            'icon' => 'required|string|max:10',
            'is_available' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre de la recompensa es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede exceder 100 caracteres.',
            'description.max' => 'La descripción no puede exceder 500 caracteres.',
            'category.required' => 'Debes seleccionar una categoría.',
            'category.in' => 'La categoría seleccionada no es válida.',
            'cost_points.required' => 'El costo en puntos es obligatorio.',
            'cost_points.integer' => 'El costo debe ser un número entero.',
            'cost_points.min' => 'El costo debe ser al menos 1 punto.',
            'icon.required' => 'El icono es obligatorio.',
            'icon.max' => 'El icono no puede exceder 10 caracteres.',
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        Auth::user()->rewards()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'cost_points' => $validated['cost_points'],
            'icon' => $validated['icon'],
            'is_available' => $validated['is_available'],
        ]);

        session()->flash('success', '🎁 ¡Recompensa creada exitosamente!');

        $this->redirect(route('rewards.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.rewards.create-reward', [
            'categories' => RewardCategory::cases(),
        ])->layout('components.layouts.app');
    }
}