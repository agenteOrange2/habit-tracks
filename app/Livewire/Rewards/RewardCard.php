<?php

namespace App\Livewire\Rewards;

use Livewire\Component;
use App\Models\Reward;
use Illuminate\Support\Facades\Auth;

class RewardCard extends Component
{
    public Reward $reward;
    public $canClaim = false;
    public $timesClaimed = 0;

    public function mount(): void
    {
        $this->checkAvailability();
    }

    public function checkAvailability(): void
    {
        $user = Auth::user();
        $this->canClaim = $this->reward->canBeClaimed($user);
        $this->timesClaimed = $this->reward->times_claimed;
    }

    public function render()
    {
        return view('livewire.rewards.reward-card');
    }
}