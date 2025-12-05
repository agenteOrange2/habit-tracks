<?php

namespace App\Livewire\Journal;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class JournalList extends Component
{
    use WithPagination;

    public $filterMood = 'all';
    public $filterDate = null;

    public function setMoodFilter($mood): void
    {
        $this->filterMood = $mood;
        $this->resetPage();
    }

    public function render()
    {
        $query = Auth::user()->journalEntries()
            ->with('habitLog.habit')
            ->latest();

        if ($this->filterMood !== 'all') {
            $query->where('mood', $this->filterMood);
        }

        if ($this->filterDate) {
            $query->whereDate('created_at', $this->filterDate);
        }

        $entries = $query->paginate(10);

        return view('livewire.journal.journal-list', [
            'entries' => $entries,
        ])->layout('layouts.app');
    }
}