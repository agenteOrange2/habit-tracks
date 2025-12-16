<?php

namespace App\Livewire\Layout;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarProfile extends Component
{
    #[On('profile-updated')]
    public function refreshProfile(): void
    {
        // Clear computed properties to force refresh
        unset($this->user);
        unset($this->level);
        unset($this->playerClass);
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function level()
    {
        return $this->user?->level;
    }

    #[Computed]
    public function playerClass(): array
    {
        return $this->user?->player_class_config ?? [
            'id' => 'programador',
            'name' => 'Programador',
            'icon' => '💻',
            'bg' => 'bg-blue-100',
            'text' => 'text-blue-700',
        ];
    }

    public function render()
    {
        return view('livewire.layout.sidebar-profile');
    }
}
