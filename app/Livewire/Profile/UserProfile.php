<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserProfile extends Component
{
    public $user;
    public $stats;
    public $level;

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->stats = $this->user->stats;
        $this->level = $this->user->level;
    }

    public function render()
    {
        return view('livewire.profile.user-profile')
            ->layout('layouts.app');
    }
}