<?php

namespace App\Livewire\Rewards;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RewardClaim;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\Auth;

class RewardHistory extends Component
{
    use WithPagination;

    public int $totalSpent = 0;
    public int $totalClaims = 0;
    public ?int $editingNotesId = null;
    public string $editingNotes = '';

    public function mount(StatisticsService $statisticsService): void
    {
        $user = Auth::user();
        $this->totalSpent = $statisticsService->getTotalPointsSpent($user);
        $this->totalClaims = $statisticsService->getTotalRewardsClaimed($user);
    }

    public function toggleEnjoyed(int $claimId): void
    {
        $claim = RewardClaim::where('user_id', Auth::id())->findOrFail($claimId);
        
        $claim->update([
            'was_enjoyed' => !$claim->was_enjoyed,
        ]);

        session()->flash('success', $claim->was_enjoyed 
            ? '😊 ¡Marcado como disfrutado!' 
            : '📝 Marcado como no disfrutado');
    }

    public function startEditingNotes(int $claimId, ?string $currentNotes): void
    {
        $this->editingNotesId = $claimId;
        $this->editingNotes = $currentNotes ?? '';
    }

    public function cancelEditingNotes(): void
    {
        $this->editingNotesId = null;
        $this->editingNotes = '';
    }

    public function saveNotes(): void
    {
        if (!$this->editingNotesId) {
            return;
        }

        $claim = RewardClaim::where('user_id', Auth::id())
            ->findOrFail($this->editingNotesId);
        
        $claim->update([
            'notes' => $this->editingNotes ?: null,
        ]);

        $this->editingNotesId = null;
        $this->editingNotes = '';

        session()->flash('success', '📝 Notas guardadas correctamente');
    }

    public function render()
    {
        $claims = Auth::user()->rewardClaims()
            ->with('reward')
            ->latest('claimed_at')
            ->paginate(20);

        return view('livewire.rewards.reward-history', [
            'claims' => $claims,
        ])->layout('components.layouts.app');
    }
}