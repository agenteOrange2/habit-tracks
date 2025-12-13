<?php

namespace App\Livewire\Calendar;

use App\Models\CalendarEvent;
use App\Models\CalendarSetting;
use App\Models\Habit;
use App\Services\CalendarService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class EventEditor extends Component
{
    public ?CalendarEvent $event = null;
    
    public ?int $habit_id = null;
    public string $title = '';
    public string $description = '';
    
    #[Url]
    public string $date = '';
    
    public string $start_time = '09:00';
    public string $end_time = '10:00';
    public ?string $recurrence_type = null;
    public array $recurrence_days = [];
    public ?string $recurrence_end = null;
    public bool $sync_to_google = false;
    public ?int $reminder_minutes = 15;
    public ?string $color = '#3b82f6';

    protected CalendarService $calendarService;

    protected $rules = [
        'title' => 'required|string|max:255',
        'date' => 'required|date',
        'start_time' => 'required',
        'end_time' => 'required',
        'habit_id' => 'nullable|exists:habits,id',
        'recurrence_type' => 'nullable|in:daily,weekly,monthly',
        'recurrence_days' => 'array',
        'recurrence_end' => 'nullable|date|after:date',
        'reminder_minutes' => 'nullable|integer|min:0',
    ];

    public function boot(CalendarService $calendarService): void
    {
        $this->calendarService = $calendarService;
    }

    public function mount(?CalendarEvent $event = null): void
    {
        if ($event && $event->exists) {
            $this->event = $event;
            $this->habit_id = $event->habit_id;
            $this->title = $event->title;
            $this->description = $event->description ?? '';
            $this->date = $event->start_time->format('Y-m-d');
            $this->start_time = $event->start_time->format('H:i');
            $this->end_time = $event->end_time->format('H:i');
            $this->recurrence_type = $event->recurrence_type;
            $this->recurrence_days = $event->recurrence_days ?? [];
            $this->recurrence_end = $event->recurrence_end?->format('Y-m-d');
            $this->sync_to_google = $event->sync_to_google;
            $this->reminder_minutes = $event->reminder_minutes;
            $this->color = $event->color ?? '#3b82f6';
        } else {
            // Use date from URL query parameter or default to today
            if (empty($this->date)) {
                $this->date = now()->format('Y-m-d');
            }
            $settings = CalendarSetting::getOrCreateForUser(auth()->id());
            $this->start_time = $settings->working_hours_start;
            $endTime = \Carbon\Carbon::parse($settings->working_hours_start)->addMinutes($settings->default_duration);
            $this->end_time = $endTime->format('H:i');
            $this->reminder_minutes = $settings->default_reminder;
        }
    }

    #[Computed]
    public function habits()
    {
        return Habit::where('user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function isEditing(): bool
    {
        return $this->event && $this->event->exists;
    }

    public function selectHabit(int $habitId): void
    {
        $habit = Habit::find($habitId);
        if ($habit && $habit->user_id === auth()->id()) {
            $this->habit_id = $habitId;
            $this->title = $habit->name;
            $this->color = $habit->color ?? '#3b82f6';
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'habit_id' => $this->habit_id,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'start_time' => $this->date . ' ' . $this->start_time,
            'end_time' => $this->date . ' ' . $this->end_time,
            'recurrence_type' => $this->recurrence_type,
            'recurrence_days' => $this->recurrence_type === 'weekly' ? $this->recurrence_days : null,
            'recurrence_end' => $this->recurrence_type ? $this->recurrence_end : null,
            'sync_to_google' => $this->sync_to_google,
            'reminder_minutes' => $this->reminder_minutes,
            'color' => $this->color,
        ];

        if ($this->isEditing) {
            $this->calendarService->updateEvent($this->event, $data);
            session()->flash('message', 'Evento actualizado correctamente');
        } else {
            $this->calendarService->createEvent(auth()->user(), $data);
            session()->flash('message', 'Evento creado correctamente');
        }

        $this->redirect(route('admin.calendar.index'), navigate: true);
    }

    public function delete(): void
    {
        if ($this->event) {
            $this->calendarService->deleteEvent($this->event);
            session()->flash('message', 'Evento eliminado correctamente');
        }
        $this->redirect(route('admin.calendar.index'), navigate: true);
    }

    public function deleteAll(): void
    {
        if ($this->event) {
            $this->calendarService->deleteEvent($this->event, deleteAll: true);
            session()->flash('message', 'Todos los eventos recurrentes eliminados');
        }
        $this->redirect(route('admin.calendar.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.calendar.event-editor')
            ->layout('components.layouts.app');
    }
}
