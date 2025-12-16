<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Achievement;
use App\Enums\AchievementType;
use Illuminate\Support\Facades\Auth;

class AchievementManager extends Component
{
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // Form fields
    public string $name = '';
    public string $description = '';
    public string $icon = '🏆';
    public string $category = 'habits';
    public string $requirement_type = 'total_habits';
    public int $requirement_value = 1;
    public int $points_reward = 10;
    public bool $is_secret = false;

    protected $rules = [
        'name' => 'required|string|max:100',
        'description' => 'required|string|max:255',
        'icon' => 'required|string|max:10',
        'category' => 'required|string|max:50',
        'requirement_type' => 'required|string',
        'requirement_value' => 'required|integer|min:1',
        'points_reward' => 'required|integer|min:1|max:1000',
        'is_secret' => 'boolean',
    ];

    public function openModal(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function editAchievement(int $id): void
    {
        $achievement = Achievement::findOrFail($id);

        $this->editingId = $id;
        $this->name = $achievement->name;
        $this->description = $achievement->description;
        $this->icon = $achievement->icon;
        $this->category = $achievement->category;
        $this->requirement_type = $achievement->requirement_type;
        $this->requirement_value = $achievement->requirement_value;
        $this->points_reward = $achievement->points_reward;
        $this->is_secret = $achievement->is_secret;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'category' => $this->category,
            'requirement_type' => $this->requirement_type,
            'requirement_value' => $this->requirement_value,
            'points_reward' => $this->points_reward,
            'is_secret' => $this->is_secret,
        ];

        if ($this->isEditing && $this->editingId) {
            Achievement::find($this->editingId)->update($data);
            session()->flash('success', 'Logro actualizado correctamente');
        } else {
            Achievement::create($data);
            session()->flash('success', 'Logro creado correctamente');
        }

        $this->closeModal();
    }

    public function deleteAchievement(int $id): void
    {
        $achievement = Achievement::findOrFail($id);

        // Verificar si algún usuario ya lo desbloqueó
        $unlockedCount = $achievement->users()->wherePivot('unlocked_at', '!=', null)->count();

        if ($unlockedCount > 0) {
            session()->flash('error', "No puedes eliminar este logro. {$unlockedCount} usuario(s) ya lo desbloquearon.");
            return;
        }

        $achievement->delete();
        session()->flash('success', 'Logro eliminado correctamente');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->description = '';
        $this->icon = '🏆';
        $this->category = 'habits';
        $this->requirement_type = 'total_habits';
        $this->requirement_value = 1;
        $this->points_reward = 10;
        $this->is_secret = false;
    }

    public function getAchievementTypes(): array
    {
        return [
            'total_habits' => 'Total de Hábitos Completados',
            'habit_streak' => 'Racha de Días',
            'pomodoros' => 'Pomodoros Completados',
            'points' => 'Puntos Ganados',
            'consecutive_days' => 'Días Perfectos Consecutivos',
            'specific_category' => 'Categoría Específica',
        ];
    }

    public function getCategories(): array
    {
        return [
            'habits' => '📋 Hábitos',
            'streaks' => '🔥 Rachas',
            'pomodoro' => '🍅 Pomodoro',
            'points' => '💰 Puntos',
            'daily' => '✨ Diario',
            'special' => '⭐ Especial',
        ];
    }

    public function getCommonIcons(): array
    {
        return ['🏆', '🎯', '⭐', '🔥', '💪', '🚀', '💎', '👑', '🎖️', '🌟', '🍅', '📅', '✨', '🌈', '🎧', '⚡', '🧠', '💰', '🌱', '🦉'];
    }

    public function render()
    {
        $user = Auth::user();
        $userAchievementIds = $user->achievements()
            ->wherePivot('unlocked_at', '!=', null)
            ->pluck('achievements.id')
            ->toArray();

        $allAchievements = Achievement::orderBy('category')
            ->orderBy('requirement_value')
            ->get();

        // Separar por categorías de estado
        $unlocked = $allAchievements->filter(fn($a) => in_array($a->id, $userAchievementIds));
        $secret = $allAchievements->filter(fn($a) => $a->is_secret && !in_array($a->id, $userAchievementIds));
        $locked = $allAchievements->filter(fn($a) => !$a->is_secret && !in_array($a->id, $userAchievementIds));

        return view('livewire.admin.achievement-manager', [
            'unlockedAchievements' => $unlocked,
            'lockedAchievements' => $locked,
            'secretAchievements' => $secret,
            'userAchievementIds' => $userAchievementIds,
            'achievementTypes' => $this->getAchievementTypes(),
            'categories' => $this->getCategories(),
            'commonIcons' => $this->getCommonIcons(),
        ])->layout('components.layouts.app');
    }
}
