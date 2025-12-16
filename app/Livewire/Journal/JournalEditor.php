<?php

namespace App\Livewire\Journal;

use Livewire\Component;
use App\Models\JournalEntry;
use App\Enums\Mood;
use Illuminate\Support\Facades\Auth;

class JournalEditor extends Component
{
    public ?JournalEntry $entry = null;
    public string $content = '';
    public ?string $mood = null;
    public ?int $energyLevel = null;
    public ?int $categoryId = null;

    public function mount(?JournalEntry $entry = null): void
    {
        if ($entry && $entry->exists) {
            $this->entry = $entry;
            $this->content = $entry->content;
            $this->mood = $entry->mood?->value;
            $this->energyLevel = $entry->energy_level;
            $this->categoryId = $entry->category_id;
        }
    }

    public function setMood(string $mood): void
    {
        $this->mood = $mood;
        $this->save();
    }

    public function setEnergyLevel(int $level): void
    {
        $this->energyLevel = $level;
        $this->save();
    }
    
    public function setCategory(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
        $this->save();
    }

    public function updateContent(string $content): void
    {
        $this->content = $content;
        $this->save();
    }

    protected function save(): void
    {
        $data = [
            'content' => $this->content ?: '',
            'mood' => $this->mood,
            'energy_level' => $this->energyLevel,
            'category_id' => $this->categoryId,
        ];

        if (!$this->entry) {
            $this->entry = Auth::user()->journalEntries()->create($data);
        } else {
            $this->entry->update($data);
        }
    }

    public function render()
    {
        return view('livewire.journal.journal-editor', [
            'moods' => Mood::cases(),
            'date' => $this->entry?->created_at ?? now(),
            'categories' => Auth::user()->journalCategories,
        ])->layout('components.layouts.app');
    }
}
