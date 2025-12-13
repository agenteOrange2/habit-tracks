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
        $settings = auth()->user()->calendarSetting;
        if ($settings) {
            $this->viewMode = $settings->default_view;
        }
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

    public function previousMonth(): void
    {
        $this->currentDate = $this->currentMonth->subMonth()->format('Y-m-d');
        $this->selectedDate = null;
    }

    public function nextMonth(): void
    {
        $this->currentDate = $this->currentMonth->addMonth()->format('Y-m-d');
        $this->selectedDate = null;
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
