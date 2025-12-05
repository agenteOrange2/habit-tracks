<?php

namespace App\Livewire\Pomodoro;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class SessionHistory extends Component
{
    use WithPagination;

    public $filterDate = null;
    public $filterHabit = null;

    public function render()
    {
        $query = Auth::user()->pomodoroSessions()
            ->with('habit')
            ->whereNotNull('completed_at')
            ->latest('started_at');

        if ($this->filterDate) {
            $query->whereDate('started_at', $this->filterDate);
        }

        if ($this->filterHabit) {
            $query->where('habit_id', $this->filterHabit);
        }

        $sessions = $query->paginate(20);

        $habits = Auth::user()->habits()
            ->where('is_active', true)
            ->get();

        return view('livewire.pomodoro.session-history', [
            'sessions' => $sessions,
            'habits' => $habits,
        ])->layout('layouts.app');
    }
}