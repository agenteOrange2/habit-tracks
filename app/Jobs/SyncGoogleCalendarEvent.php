<?php

namespace App\Jobs;

use App\Models\CalendarEvent;
use App\Services\GoogleCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncGoogleCalendarEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 5;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 60;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     * Implements exponential backoff: 10s, 30s, 90s, 270s, 810s
     */
    public function backoff(): array
    {
        return [10, 30, 90, 270, 810];
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        public CalendarEvent $event,
        public string $operation // 'create', 'update', 'delete'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(GoogleCalendarService $googleService): void
    {
        // Reload the event to get fresh data
        $this->event->refresh();

        // Skip if event no longer exists (for create/update operations)
        if (!$this->event->exists && $this->operation !== 'delete') {
            Log::info('Skipping Google Calendar sync - event no longer exists', [
                'event_id' => $this->event->id,
                'operation' => $this->operation,
            ]);
            return;
        }


        // Skip if sync is disabled for this event
        if (!$this->event->sync_to_google && $this->operation !== 'delete') {
            Log::info('Skipping Google Calendar sync - sync disabled for event', [
                'event_id' => $this->event->id,
                'operation' => $this->operation,
            ]);
            return;
        }

        // Check if user has Google Calendar connected
        if (!$googleService->isConnected($this->event->user)) {
            Log::warning('Cannot sync to Google Calendar - user not connected', [
                'event_id' => $this->event->id,
                'user_id' => $this->event->user_id,
                'operation' => $this->operation,
            ]);
            return;
        }

        try {
            match ($this->operation) {
                'create' => $this->handleCreate($googleService),
                'update' => $this->handleUpdate($googleService),
                'delete' => $this->handleDelete($googleService),
                default => throw new \InvalidArgumentException("Unknown operation: {$this->operation}"),
            };
        } catch (\Exception $e) {
            Log::error('Google Calendar sync failed', [
                'event_id' => $this->event->id,
                'operation' => $this->operation,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle event creation in Google Calendar.
     */
    protected function handleCreate(GoogleCalendarService $googleService): void
    {
        $googleEventId = $googleService->createEvent($this->event);

        if ($googleEventId) {
            $this->event->update(['google_event_id' => $googleEventId]);
            
            Log::info('Successfully created Google Calendar event', [
                'event_id' => $this->event->id,
                'google_event_id' => $googleEventId,
            ]);
        }
    }

    /**
     * Handle event update in Google Calendar.
     */
    protected function handleUpdate(GoogleCalendarService $googleService): void
    {
        // If no Google event ID, create instead
        if (!$this->event->google_event_id) {
            $this->handleCreate($googleService);
            return;
        }

        $success = $googleService->updateEvent($this->event);

        if ($success) {
            Log::info('Successfully updated Google Calendar event', [
                'event_id' => $this->event->id,
                'google_event_id' => $this->event->google_event_id,
            ]);
        }
    }

    /**
     * Handle event deletion from Google Calendar.
     */
    protected function handleDelete(GoogleCalendarService $googleService): void
    {
        $success = $googleService->deleteEvent($this->event);

        if ($success) {
            Log::info('Successfully deleted Google Calendar event', [
                'event_id' => $this->event->id,
                'google_event_id' => $this->event->google_event_id,
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Google Calendar sync job failed permanently', [
            'event_id' => $this->event->id,
            'operation' => $this->operation,
            'error' => $exception->getMessage(),
        ]);

        // Optionally notify the user about the sync failure
        // You could dispatch a notification here
    }
}
