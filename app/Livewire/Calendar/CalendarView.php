<?php

namespace App\Livewire\Calendar;

use App\Models\CalendarEvent;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CalendarView extends Component
{
    public string $viewMode = 'month'; // month, week, day
    public string $currentDate;
    public ?string $selectedDate = null;

    protected CalendarService $calendarService;

    public function boot(CalendarService $calendarService): void
    {
        $this->calendarService = $calendarService;
    }

    public function mount(): void
    {
        $this->currentDate = now()->format('Y-m-d');
        $settings = \App\Models\CalendarSetting::getOrCreateForUser(auth()->id());
        $this->viewMode = $settings->default_view;
    }

    #[Computed]
    public function currentMonth(): Carbon
    {
        return Carbon::parse($this->currentDate);
    }

    #[Computed]
    public function calendarDays(): array
    {
        $date = $this->currentMonth;
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        
        // Start from Sunday of the week containing the first day
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        // End on Saturday of the week containing the last day
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        $days = [];
        $current = $startOfCalendar->copy();

        while ($current->lte($endOfCalendar)) {
            $days[] = [
                'date' => $current->format('Y-m-d'),
                'day' => $current->day,
                'isCurrentMonth' => $current->month === $date->month,
                'isToday' => $current->isToday(),
                'isSelected' => $this->selectedDate === $current->format('Y-m-d'),
            ];
            $current->addDay();
        }

        return $days;
    }

    #[Computed]
    public function events(): Collection
    {
        $date = $this->currentMonth;
        $start = $date->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $end = $date->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        return $this->calendarService->getEventsForRange(auth()->user(), $start, $end);
    }

    #[Computed]
    public function eventsByDate(): array
    {
        $grouped = [];
        foreach ($this->events as $event) {
            $dateKey = $event->start_time->format('Y-m-d');
            if (!isset($grouped[$dateKey])) {
                $grouped[$dateKey] = [];
            }
            $grouped[$dateKey][] = $event;
        }
        return $grouped;
    }

    #[Computed]
    public function selectedDayEvents(): Collection
    {
        if (!$this->selectedDate) {
            return collect();
        }
        return $this->calendarService->getEventsForDate(auth()->user(), Carbon::parse($this->selectedDate));
    }

    #[Computed]
    public function weekDays(): array
    {
        $date = Carbon::parse($this->currentDate);
        $startOfWeek = $date->copy()->startOfWeek(Carbon::SUNDAY);
        
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $current = $startOfWeek->copy()->addDays($i);
            $days[] = [
                'date' => $current->format('Y-m-d'),
                'day' => $current->day,
                'dayName' => $current->translatedFormat('D'),
                'isToday' => $current->isToday(),
                'isSelected' => $this->selectedDate === $current->format('Y-m-d'),
            ];
        }
        return $days;
    }

    #[Computed]
    public function weekEvents(): Collection
    {
        $date = Carbon::parse($this->currentDate);
        $start = $date->copy()->startOfWeek(Carbon::SUNDAY);
        $end = $date->copy()->endOfWeek(Carbon::SATURDAY);
        return $this->calendarService->getEventsForRange(auth()->user(), $start, $end);
    }

    #[Computed]
    public function dayEvents(): Collection
    {
        return $this->calendarService->getEventsForDate(auth()->user(), Carbon::parse($this->currentDate));
    }

    #[Computed]
    public function timeSlots(): array
    {
        $slots = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $slots[] = sprintf('%02d:00', $hour);
        }
        return $slots;
    }

    public function previous(): void
    {
        $date = Carbon::parse($this->currentDate);
        match ($this->viewMode) {
            'month' => $this->currentDate = $date->subMonth()->format('Y-m-d'),
            'week' => $this->currentDate = $date->subWeek()->format('Y-m-d'),
            'day' => $this->currentDate = $date->subDay()->format('Y-m-d'),
        };
        $this->selectedDate = null;
    }

    public function next(): void
    {
        $date = Carbon::parse($this->currentDate);
        match ($this->viewMode) {
            'month' => $this->currentDate = $date->addMonth()->format('Y-m-d'),
            'week' => $this->currentDate = $date->addWeek()->format('Y-m-d'),
            'day' => $this->currentDate = $date->addDay()->format('Y-m-d'),
        };
        $this->selectedDate = null;
    }

    public function previousMonth(): void
    {
        $this->previous();
    }

    public function nextMonth(): void
    {
        $this->next();
    }

    public function goToToday(): void
    {
        $this->currentDate = now()->format('Y-m-d');
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function selectDay(string $date): void
    {
        $this->selectedDate = $date;
    }

    public function changeView(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function render()
    {
        return view('livewire.calendar.calendar-view')
            ->layout('components.layouts.app');
    }
}
