<?php

namespace App\Livewire\Journal;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JournalEntry;
use App\Enums\Mood;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JournalList extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $filterMood = null;
    public ?string $filterMonth = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterMood' => ['except' => null, 'as' => 'mood'],
        'filterMonth' => ['except' => null, 'as' => 'month'],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setMoodFilter(?string $mood): void
    {
        $this->filterMood = $mood;
        $this->resetPage();
    }

    public function setMonthFilter(?string $month): void
    {
        $this->filterMonth = $month;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->filterMood = null;
        $this->filterMonth = null;
        $this->resetPage();
    }

    public function deleteEntry(JournalEntry $entry): void
    {
        if ($entry->user_id !== Auth::id()) {
            return;
        }
        $entry->delete();
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = JournalEntry::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where('content', 'like', '%' . $this->search . '%');
        }

        if ($this->filterMood) {
            $query->where('mood', $this->filterMood);
        }

        if ($this->filterMonth) {
            $date = Carbon::parse($this->filterMonth);
            $query->whereYear('created_at', $date->year)
                  ->whereMonth('created_at', $date->month);
        }

        // Get available months for filter
        $months = JournalEntry::where('user_id', $user->id)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        // Stats
        $totalEntries = JournalEntry::where('user_id', $user->id)->count();
        $thisMonthEntries = JournalEntry::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('livewire.journal.journal-list', [
            'entries' => $query->paginate(10),
            'moods' => Mood::cases(),
            'months' => $months,
            'totalEntries' => $totalEntries,
            'thisMonthEntries' => $thisMonthEntries,
        ])->layout('components.layouts.app');
    }
}
