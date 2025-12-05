<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationSettings extends Component
{
    public $notifications = [
        'habit_reminders' => true,
        'achievement_unlocked' => true,
        'level_up' => true,
        'challenge_completed' => true,
        'streak_warning' => true,
        'daily_summary' => false,
        'weekly_report' => false,
    ];

    public function mount(): void
    {
        // Aquí podrías cargar las preferencias del usuario desde la DB
        // Por ahora usamos valores por defecto
    }

    public function save(): void
    {
        // Aquí guardarías las preferencias en la DB
        
        session()->flash('success', 'Preferencias de notificación actualizadas.');
    }

    public function render()
    {
        return view('livewire.settings.notification-settings')
            ->layout('layouts.app');
    }
}