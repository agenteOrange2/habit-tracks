<?php

namespace App\Livewire\Challenges;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class CompletedChallenges extends Component
{
    use WithPagination;

    public $totalCompleted;
    public $totalPointsEarned;

    public function mount(): void
    {
        $user = Auth::user();
        
        $this->totalCompleted = $user->challenges()
            ->wherePivot('status', 'completed')
            ->count();

        $this->totalPointsEarned = $user->challenges()
            ->wherePivot('status', 'completed')
            ->sum('points_reward');
    }

    public function render()
    {
        $challenges = Auth::user()->challenges()
            ->wherePivot('status', 'completed')
            ->withPivot(['completed_at', 'progress'])
            ->latest('user_challenges.completed_at')
            ->paginate(12);

        return view('livewire.challenges.completed-challenges', [
            'challenges' => $challenges,
        ])->layout('layouts.app');
    }
}