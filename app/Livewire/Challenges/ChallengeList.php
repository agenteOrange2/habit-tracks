<?php

namespace App\Livewire\Challenges;

use Livewire\Component;
use App\Models\Challenge;
use Illuminate\Support\Facades\Auth;

class ChallengeList extends Component
{
    public $activeChallenges;
    public $userChallenges;

    protected $listeners = [
        'challengeAccepted' => '$refresh',
        'challengeCompleted' => '$refresh',
    ];

    public function mount(): void
    {
        $this->loadChallenges();
    }

    public function loadChallenges(): void
    {
        $user = Auth::user();

        // Desafíos activos disponibles
        $this->activeChallenges = Challenge::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->get();

        // Desafíos del usuario
        $this->userChallenges = $user->challenges()
            ->wherePivot('status', 'active')
            ->with(['users' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get();
    }

    public function acceptChallenge(Challenge $challenge): void
    {
        $user = Auth::user();

        // Verificar si ya aceptó el desafío
        if ($user->challenges()->where('challenge_id', $challenge->id)->exists()) {
            session()->flash('info', 'Ya has aceptado este desafío.');
            return;
        }

        $user->challenges()->attach($challenge->id, [
            'status' => 'active',
            'progress' => 0,
        ]);

        session()->flash('success', "¡Desafío '{$challenge->name}' aceptado! 🎯");

        $this->dispatch('challengeAccepted');
        $this->loadChallenges();
    }

    public function render()
    {
        return view('livewire.challenges.challenge-list')
            ->layout('layouts.app');
    }
}