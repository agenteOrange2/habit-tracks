<?php

namespace App\Livewire\Journal;

use Livewire\Component;
use App\Enums\Mood;
use Illuminate\Support\Facades\Auth;

class CreateEntry extends Component
{
    public $content = '';
    public $mood = 'neutral';
    public $energy_level = 50;
    public $habit_log_id = null;

    protected function rules(): array
    {
        return [
            'content' => 'required|string|min:10',
            'mood' => 'required|string',
            'energy_level' => 'nullable|integer|min:0|max:100',
            'habit_log_id' => 'nullable|exists:habit_logs,id',
        ];
    }

    public function save(): void
    {
        $this->validate();

        Auth::user()->journalEntries()->create([
            'content' => $this->content,
            'mood' => $this->mood,
            'energy_level' => $this->energy_level,
            'habit_log_id' => $this->habit_log_id,
        ]);

        session()->flash('success', '¡Entrada de diario guardada! 📝');

        return $this->redirect(route('admin.journal.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.journal.create-entry', [
            'moods' => Mood::cases(),
        ])->layout('layouts.app');
    }
}