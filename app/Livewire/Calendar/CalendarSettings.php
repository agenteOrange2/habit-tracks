<?php

namespace App\Livewire\Calendar;

use App\Models\CalendarSetting;
use App\Services\GoogleCalendarService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CalendarSettings extends Component
{
    public int $default_duration = 60;
    public string $working_hours_start = '09:00';
    public string $working_hours_end = '18:00';
    public bool $auto_sync = false;
    public string $default_view = 'month';
    public int $default_reminder = 15;

    protected $rules = [
        'default_duration' => 'required|integer|min:15|max:480',
        'working_hours_start' => 'required',
        'working_hours_end' => 'required',
        'auto_sync' => 'boolean',
        'default_view' => 'required|in:month,week,day',
        'default_reminder' => 'required|integer|min:0|max:1440',
    ];

    public function mount(): void
    {
        $settings = CalendarSetting::getOrCreateForUser(auth()->id());
        
        $this->default_duration = $settings->default_duration;
        $this->working_hours_start = $settings->working_hours_start;
        $this->working_hours_end = $settings->working_hours_end;
        $this->auto_sync = $settings->auto_sync;
        $this->default_view = $settings->default_view;
        $this->default_reminder = $settings->default_reminder;
    }

    #[Computed]
    public function isGoogleConnected(): bool
    {
        $googleService = app(GoogleCalendarService::class);
        return $googleService->isConnected(auth()->user());
    }

    #[Computed]
    public function isGoogleConfigured(): bool
    {
        return !empty(config('google.client_id')) && !empty(config('google.client_secret'));
    }

    public function connectGoogle(): string
    {
        $googleService = app(GoogleCalendarService::class);
        return $googleService->getAuthUrl();
    }

    public function save(): void
    {
        $this->validate();

        CalendarSetting::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'default_duration' => $this->default_duration,
                'working_hours_start' => $this->working_hours_start,
                'working_hours_end' => $this->working_hours_end,
                'auto_sync' => $this->auto_sync,
                'default_view' => $this->default_view,
                'default_reminder' => $this->default_reminder,
            ]
        );

        session()->flash('message', 'Configuración guardada correctamente');
    }

    public function disconnectGoogle(): void
    {
        $googleService = app(GoogleCalendarService::class);
        $googleService->disconnect(auth()->user());
        
        $this->auto_sync = false;
        $this->save();
        session()->flash('message', 'Google Calendar desconectado');
    }

    public function render()
    {
        return view('livewire.calendar.calendar-settings')
            ->layout('components.layouts.app');
    }
}
