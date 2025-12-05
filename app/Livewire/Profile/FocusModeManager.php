<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\FocusMode;
use App\Enums\RewardCategory;
use Illuminate\Support\Facades\Auth;

class FocusModeManager extends Component
{
    public $focusModes;
    public $showCreateForm = false;

    // Formulario
    public $name = '';
    public $start_time = '09:00';
    public $end_time = '17:00';
    public $blocked_categories = [];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'blocked_categories' => 'array',
        ];
    }

    public function mount(): void
    {
        $this->loadFocusModes();
    }

    public function loadFocusModes(): void
    {
        $this->focusModes = Auth::user()->focusModes;
    }

    public function toggleCreateForm(): void
    {
        $this->showCreateForm = !$this->showCreateForm;
        
        if (!$this->showCreateForm) {
            $this->reset(['name', 'start_time', 'end_time', 'blocked_categories']);
        }
    }

    public function save(): void
    {
        $this->validate();

        Auth::user()->focusModes()->create([
            'name' => $this->name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'blocked_categories' => $this->blocked_categories,
            'is_active' => true,
        ]);

        session()->flash('success', '¡Modo Focus creado exitosamente! 🔒');

        $this->reset(['name', 'start_time', 'end_time', 'blocked_categories', 'showCreateForm']);
        $this->loadFocusModes();
    }

    public function toggleActive(FocusMode $focusMode): void
    {
        $focusMode->update([
            'is_active' => !$focusMode->is_active,
        ]);

        $this->loadFocusModes();
    }

    public function delete(FocusMode $focusMode): void
    {
        $focusMode->delete();
        
        session()->flash('success', 'Modo Focus eliminado.');
        
        $this->loadFocusModes();
    }

    public function render()
    {
        return view('livewire.profile.focus-mode-manager', [
            'categories' => RewardCategory::cases(),
        ])->layout('layouts.app');
    }
}