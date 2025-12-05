<?php

namespace App\Livewire\Rewards;

use Livewire\Component;
use App\Models\Reward;
use App\Enums\RewardCategory;

class EditReward extends Component
{
    public Reward $reward;
    public $name;
    public $description;
    public $category;
    public $cost_points;
    public $icon;
    public $is_available;

    public function mount(Reward $reward): void
    {
        $this->authorize('update', $reward);

        $this->reward = $reward;
        $this->name = $reward->name;
        $this->description = $reward->description;
        $this->category = $reward->category;
        $this->cost_points = $reward->cost_points;
        $this->icon = $reward->icon;
        $this->is_available = $reward->is_available;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category' => 'required|string',
            'cost_points' => 'required|integer|min:1',
            'icon' => 'required|string',
            'is_available' => 'boolean',
        ];
    }

    public function update(): void
    {
        $this->validate();

        $this->reward->update([
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'cost_points' => $this->cost_points,
            'icon' => $this->icon,
            'is_available' => $this->is_available,
        ]);

        session()->flash('success', '¡Recompensa actualizada exitosamente! ✅');

        return $this->redirect(route('rewards.index'), navigate: true);
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->reward);

        $this->reward->delete();

        session()->flash('success', 'Recompensa eliminada correctamente.');

        return $this->redirect(route('rewards.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.rewards.edit-reward', [
            'categories' => RewardCategory::cases(),
        ])->layout('layouts.app');
    }
}