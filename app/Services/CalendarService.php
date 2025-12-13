<?php

namespace App\Services;

use App\Jobs\SyncGoogleCalendarEvent;
use App\Models\CalendarEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CalendarService
{
    public function getEventsForRange(User $user, Carbon $start, Carbon $end): Collection
    {
        return CalendarEvent::forUser($user->id)
            ->forDateRange($start, $end)
            ->orderBy('start_time')
            ->get();
    }

    public function getEventsForDate(User $user, Carbon $date): Collection
    {
        return CalendarEvent::forUser($user->id)
            ->forDate($date)
            ->orderBy('start_time')
            ->get();
    }

    public function createEvent(User $user, array $data): CalendarEvent
    {
        $this->validateEventData($data);

        $event = CalendarEvent::create([
            'user_id' => $user->id,
            'habit_id' => $data['habit_id'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'recurrence_type' => $data['recurrence_type'] ?? null,
            'recurrence_days' => $data['recurrence_days'] ?? null,
            'recurrence_end' => $data['recurrence_end'] ?? null,
            'sync_to_google' => $data['sync_to_google'] ?? false,
            'reminder_minutes' => $data['reminder_minutes'] ?? null,
            'color' => $data['color'] ?? null,
        ]);

        if ($event->isRecurring()) {
            $this->generateRecurringEvents($event);
        }

        // Queue Google Calendar sync if enabled
        if ($event->sync_to_google) {
            SyncGoogleCalendarEvent::dispatch($event, 'create');
        }

        return $event;
    }

    public function updateEvent(CalendarEvent $event, array $data): CalendarEvent
    {
        $this->validateEventData($data);

        $event->update([
            'habit_id' => $data['habit_id'] ?? $event->habit_id,
            'title' => $data['title'] ?? $event->title,
            'description' => $data['description'] ?? $event->description,
            'start_time' => $data['start_time'] ?? $event->start_time,
            'end_time' => $data['end_time'] ?? $event->end_time,
            'recurrence_type' => $data['recurrence_type'] ?? $event->recurrence_type,
            'recurrence_days' => $data['recurrence_days'] ?? $event->recurrence_days,
            'recurrence_end' => $data['recurrence_end'] ?? $event->recurrence_end,
            'sync_to_google' => $data['sync_to_google'] ?? $event->sync_to_google,
            'reminder_minutes' => $data['reminder_minutes'] ?? $event->reminder_minutes,
            'color' => $data['color'] ?? $event->color,
        ]);

        $event = $event->fresh();

        // Queue Google Calendar sync if enabled
        if ($event->sync_to_google) {
            SyncGoogleCalendarEvent::dispatch($event, 'update');
        }

        return $event;
    }

    public function deleteEvent(CalendarEvent $event, bool $deleteAll = false): void
    {
        // Queue Google Calendar deletion before removing from database
        if ($event->sync_to_google && $event->google_event_id) {
            SyncGoogleCalendarEvent::dispatch($event, 'delete');
        }

        if ($deleteAll && $event->parent_event_id) {
            // Delete parent and all siblings
            $parentId = $event->parent_event_id;
            $siblings = CalendarEvent::where('parent_event_id', $parentId)->get();
            foreach ($siblings as $sibling) {
                if ($sibling->sync_to_google && $sibling->google_event_id) {
                    SyncGoogleCalendarEvent::dispatch($sibling, 'delete');
                }
            }
            CalendarEvent::where('parent_event_id', $parentId)->delete();
            
            $parent = CalendarEvent::find($parentId);
            if ($parent) {
                if ($parent->sync_to_google && $parent->google_event_id) {
                    SyncGoogleCalendarEvent::dispatch($parent, 'delete');
                }
                $parent->delete();
            }
        } elseif ($deleteAll && $event->childEvents()->exists()) {
            // Delete this event and all children
            foreach ($event->childEvents as $child) {
                if ($child->sync_to_google && $child->google_event_id) {
                    SyncGoogleCalendarEvent::dispatch($child, 'delete');
                }
            }
            $event->childEvents()->delete();
            $event->delete();
        } else {
            $event->delete();
        }
    }

    public function generateRecurringEvents(CalendarEvent $parentEvent): Collection
    {
        $events = collect();

        if (!$parentEvent->isRecurring() || !$parentEvent->recurrence_end) {
            return $events;
        }

        $currentDate = Carbon::parse($parentEvent->start_time)->addDay();
        $endDate = Carbon::parse($parentEvent->recurrence_end);
        $duration = $parentEvent->getDurationInMinutes();

        while ($currentDate->lte($endDate)) {
            $shouldCreate = false;

            switch ($parentEvent->recurrence_type) {
                case 'daily':
                    $shouldCreate = true;
                    break;
                case 'weekly':
                    $shouldCreate = in_array($currentDate->dayOfWeek, $parentEvent->recurrence_days ?? []);
                    break;
                case 'monthly':
                    $shouldCreate = $currentDate->day === Carbon::parse($parentEvent->start_time)->day;
                    break;
            }

            if ($shouldCreate) {
                $startTime = $currentDate->copy()->setTimeFrom($parentEvent->start_time);
                $event = CalendarEvent::create([
                    'user_id' => $parentEvent->user_id,
                    'habit_id' => $parentEvent->habit_id,
                    'title' => $parentEvent->title,
                    'description' => $parentEvent->description,
                    'start_time' => $startTime,
                    'end_time' => $startTime->copy()->addMinutes($duration),
                    'parent_event_id' => $parentEvent->id,
                    'sync_to_google' => $parentEvent->sync_to_google,
                    'reminder_minutes' => $parentEvent->reminder_minutes,
                    'color' => $parentEvent->color,
                ]);
                $events->push($event);
            }

            $currentDate->addDay();
        }

        return $events;
    }

    protected function validateEventData(array $data): void
    {
        if (empty($data['title'])) {
            throw ValidationException::withMessages(['title' => 'El título es requerido']);
        }

        if (empty($data['start_time'])) {
            throw ValidationException::withMessages(['start_time' => 'La fecha de inicio es requerida']);
        }

        if (empty($data['end_time'])) {
            throw ValidationException::withMessages(['end_time' => 'La fecha de fin es requerida']);
        }

        $start = Carbon::parse($data['start_time']);
        $end = Carbon::parse($data['end_time']);

        if ($end->lte($start)) {
            throw ValidationException::withMessages(['end_time' => 'La fecha de fin debe ser posterior a la de inicio']);
        }
    }
}
