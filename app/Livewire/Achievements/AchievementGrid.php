<?php

namespace App\Livewire\Achievements;

use Livewire\Component;
use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;

class AchievementGrid extends Component
{
    public $filter = 'all'; // all, unlocked, locked

    public function setFilter($filter): void
    {
        $this->filter = $filter;
    }

    public function render()
    {
        $user = Auth::user();

        $achievements = Achievement::with(['users' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();

        // Filtrar según el filtro seleccionado
        if ($this->filter === 'unlocked') {
            $achievements = $achievements->filter(function ($achievement) use ($user) {
                return $achievement->isUnlockedBy($user);
            });
        } elseif ($this->filter === 'locked') {
            $achievements = $achievements->filter(function ($achievement) use ($user) {
                return !$achievement->isUnlockedBy($user);
            });
        }

        return view('livewire.achievements.achievement-grid', [
            'achievements' => $achievements,
        ])->layout('layouts.app');
    }
}