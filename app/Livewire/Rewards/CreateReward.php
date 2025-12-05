<?php

namespace App\Livewire\Rewards;

use Livewire\Component;
use App\Enums\RewardCategory;
use Illuminate\Support\Facades\Auth;

class CreateReward extends Component
{
    public $name = '';
    public $description = '';
    public $category = 'entertainment';
    public $cost_points = 50;
    public $icon = '🎁';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category' => 'required|string',
            'cost_points' => 'required|integer|min:1',
            'icon' => 'required|string',
        ];
    }

    public function save(): void
    {
        $this->validate();

        Auth::user()->rewards()->create([
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'cost_points' => $this->cost_points,
            'icon' => $this->icon,
            'is_available' => true,
        ]);

        session()->flash('success', '¡Recompensa creada exitosamente! 🎁');

        return $this->redirect(route('rewards.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.rewards.create-reward', [
            'categories' => RewardCategory::cases(),
        ])->layout('layouts.app');
    }
}