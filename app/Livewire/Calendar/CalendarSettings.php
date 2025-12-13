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
    public bool $googleConnected = false;
    public bool $googleConfigured = false;

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
        
        $this->refreshGoogleStatus();
    }

    public function refreshGoogleStatus(): void
    {
        $googleService = app(GoogleCalendarService::class);
        $this->googleConnected = $googleService->isConnected(auth()->user());
        $this->googleConfigured = !empty(config('google.client_id')) && !empty(config('google.client_secret'));
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
        
        // Refresh Google connection status
        $this->refreshGoogleStatus();
        
        session()->flash('message', 'Google Calendar desconectado');
    }

    public function syncExistingEvents(): void
    {
        $googleService = app(GoogleCalendarService::class);
        
        if (!$googleService->isConnected(auth()->user())) {
            session()->flash('error', 'Debes conectar Google Calendar primero');
            return;
        }

        // Count already synced events (only parent/standalone events, not children)
        $alreadySynced = \App\Models\CalendarEvent::where('user_id', auth()->id())
            ->where('sync_to_google', true)
            ->whereNotNull('google_event_id')
            ->whereNull('parent_event_id')
            ->count();

        // Get all events that should be synced but don't have a google_event_id
        // Exclude child events (parent_event_id not null) - parent handles recurrence via RRULE
        $events = \App\Models\CalendarEvent::where('user_id', auth()->id())
            ->where('sync_to_google', true)
            ->whereNull('google_event_id')
            ->whereNull('parent_event_id')
            ->get();

        $synced = 0;
        $failed = 0;

        foreach ($events as $event) {
            try {
                $googleEventId = $googleService->createEvent($event);
                if ($googleEventId) {
                    $event->update(['google_event_id' => $googleEventId]);
                    $synced++;
                }
            } catch (\Exception $e) {
                $failed++;
                \Illuminate\Support\Facades\Log::error('Failed to sync event: ' . $e->getMessage(), [
                    'event_id' => $event->id,
                ]);
            }
        }

        if ($synced > 0) {
            $msg = "Se sincronizaron {$synced} eventos nuevos a Google Calendar";
            if ($alreadySynced > 0) {
                $msg .= " ({$alreadySynced} ya estaban sincronizados)";
            }
            if ($failed > 0) {
                $msg .= " - {$failed} fallaron";
            }
            session()->flash('message', $msg);
        } elseif ($events->isEmpty() && $alreadySynced > 0) {
            session()->flash('message', "Todos los eventos ya están sincronizados ({$alreadySynced} eventos)");
        } elseif ($events->isEmpty()) {
            session()->flash('message', 'No hay eventos marcados para sincronizar');
        } else {
            session()->flash('error', 'No se pudo sincronizar ningún evento');
        }
    }

    public function render()
    {
        return view('livewire.calendar.calendar-settings')
            ->layout('components.layouts.app');
    }
}
