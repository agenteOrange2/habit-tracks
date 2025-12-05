<?php

namespace App\Livewire\Challenges;

use Livewire\Component;
use App\Models\Challenge;
use Illuminate\Support\Facades\Auth;

class ChallengeCard extends Component
{
    public Challenge $challenge;
    public $progress = 0;
    public $progressPercentage = 0;
    public $isAccepted = false;
    public $isCompleted = false;

    public function mount(): void
    {
        $this->loadProgress();
    }

    public function loadProgress(): void
    {
        $user = Auth::user();
        
        $userChallenge = $user->challenges()
            ->where('challenge_id', $this->challenge->id)
            ->first();

        if ($userChallenge) {
            $this->isAccepted = true;
            $this->progress = $userChallenge->pivot->progress;
            $this->isCompleted = $userChallenge->pivot->status === 'completed';
            $this->progressPercentage = $this->challenge->getProgressPercentageFor($user);
        }
    }

    public function render()
    {
        return view('livewire.challenges.challenge-card');
    }
}