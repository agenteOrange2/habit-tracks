<?php

namespace App\Livewire\Rewards;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class RewardHistory extends Component
{
    use WithPagination;

    public $totalSpent;

    public function mount(): void
    {
        $this->totalSpent = Auth::user()->stats->points_spent;
    }

    public function render()
    {
        $claims = Auth::user()->rewardClaims()
            ->with('reward')
            ->latest('claimed_at')
            ->paginate(20);

        return view('livewire.rewards.reward-history', [
            'claims' => $claims,
        ])->layout('layouts.app');
    }
}