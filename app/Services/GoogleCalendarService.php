<?php

namespace App\Services;

use App\Models\CalendarEvent;
use App\Models\GoogleCalendarToken;
use App\Models\User;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event as GoogleEvent;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\EventReminder;
use Google\Service\Calendar\EventReminders;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected GoogleClient $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setApplicationName(config('google.application_name'));
        $this->client->setClientId(config('google.client_id'));
        $this->client->setClientSecret(config('google.client_secret'));
        $this->client->setRedirectUri(url(config('google.redirect_uri')));
        $this->client->setScopes(config('google.scopes'));
        $this->client->setAccessType(config('google.access_type'));
        $this->client->setPrompt(config('google.prompt'));
    }

    /**
     * Get the OAuth authorization URL.
     */
    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Handle the OAuth callback and store tokens.
     */
    public function handleCallback(User $user, string $code): GoogleCalendarToken
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \Exception('Failed to fetch access token: ' . $token['error_description'] ?? $token['error']);
        }

        return GoogleCalendarToken::updateOrCreate(
            ['user_id' => $user->id],
            [
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? null,
                'expires_at' => now()->addSeconds($token['expires_in']),
                'calendar_id' => 'primary',
            ]
        );
    }


    /**
     * Refresh the access token if expired.
     */
    public function refreshToken(User $user): ?GoogleCalendarToken
    {
        $token = $user->googleCalendarToken;

        if (!$token) {
            return null;
        }

        if (!$token->isExpiringSoon()) {
            return $token;
        }

        if (!$token->refresh_token) {
            Log::warning('No refresh token available for user: ' . $user->id);
            return null;
        }

        $this->client->setAccessToken([
            'access_token' => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'expires_in' => 0,
        ]);

        $newToken = $this->client->fetchAccessTokenWithRefreshToken($token->refresh_token);

        if (isset($newToken['error'])) {
            Log::error('Failed to refresh token: ' . ($newToken['error_description'] ?? $newToken['error']));
            return null;
        }

        $token->update([
            'access_token' => $newToken['access_token'],
            'refresh_token' => $newToken['refresh_token'] ?? $token->refresh_token,
            'expires_at' => now()->addSeconds($newToken['expires_in']),
        ]);

        return $token->fresh();
    }

    /**
     * Get an authenticated Google Calendar service for a user.
     */
    public function getCalendarService(User $user): ?GoogleCalendar
    {
        $token = $this->refreshToken($user);

        if (!$token) {
            return null;
        }

        $this->client->setAccessToken([
            'access_token' => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'expires_in' => $token->expires_at->diffInSeconds(now()),
        ]);

        return new GoogleCalendar($this->client);
    }

    /**
     * Check if a user has a valid Google Calendar connection.
     */
    public function isConnected(User $user): bool
    {
        $token = $user->googleCalendarToken;

        if (!$token) {
            return false;
        }

        // If token is expired and we can't refresh it, not connected
        if ($token->isExpired() && !$token->refresh_token) {
            return false;
        }

        return true;
    }

    /**
     * Disconnect Google Calendar for a user.
     */
    public function disconnect(User $user): bool
    {
        $token = $user->googleCalendarToken;

        if (!$token) {
            return true;
        }

        try {
            // Revoke the token
            $this->client->revokeToken($token->access_token);
        } catch (\Exception $e) {
            Log::warning('Failed to revoke Google token: ' . $e->getMessage());
        }

        $token->delete();

        return true;
    }


    /**
     * Create an event in Google Calendar.
     */
    public function createEvent(CalendarEvent $event): ?string
    {
        $service = $this->getCalendarService($event->user);

        if (!$service) {
            return null;
        }

        try {
            $googleEvent = $this->mapToGoogleEvent($event);
            $calendarId = $event->user->googleCalendarToken->calendar_id ?? 'primary';
            
            $createdEvent = $service->events->insert($calendarId, $googleEvent);
            
            return $createdEvent->getId();
        } catch (\Exception $e) {
            Log::error('Failed to create Google Calendar event: ' . $e->getMessage(), [
                'event_id' => $event->id,
                'user_id' => $event->user_id,
            ]);
            throw $e;
        }
    }

    /**
     * Update an event in Google Calendar.
     */
    public function updateEvent(CalendarEvent $event): bool
    {
        if (!$event->google_event_id) {
            return false;
        }

        $service = $this->getCalendarService($event->user);

        if (!$service) {
            return false;
        }

        try {
            $googleEvent = $this->mapToGoogleEvent($event);
            $calendarId = $event->user->googleCalendarToken->calendar_id ?? 'primary';
            
            $service->events->update($calendarId, $event->google_event_id, $googleEvent);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update Google Calendar event: ' . $e->getMessage(), [
                'event_id' => $event->id,
                'google_event_id' => $event->google_event_id,
                'user_id' => $event->user_id,
            ]);
            throw $e;
        }
    }

    /**
     * Delete an event from Google Calendar.
     */
    public function deleteEvent(CalendarEvent $event): bool
    {
        if (!$event->google_event_id) {
            return true;
        }

        $service = $this->getCalendarService($event->user);

        if (!$service) {
            return false;
        }

        try {
            $calendarId = $event->user->googleCalendarToken->calendar_id ?? 'primary';
            
            $service->events->delete($calendarId, $event->google_event_id);
            
            return true;
        } catch (\Google\Service\Exception $e) {
            // If event not found (404), consider it deleted
            if ($e->getCode() === 404) {
                return true;
            }
            
            Log::error('Failed to delete Google Calendar event: ' . $e->getMessage(), [
                'event_id' => $event->id,
                'google_event_id' => $event->google_event_id,
                'user_id' => $event->user_id,
            ]);
            throw $e;
        }
    }

    /**
     * Map a CalendarEvent to a Google Calendar Event.
     */
    protected function mapToGoogleEvent(CalendarEvent $event): GoogleEvent
    {
        $googleEvent = new GoogleEvent();
        
        $googleEvent->setSummary($event->title);
        
        if ($event->description) {
            $googleEvent->setDescription($event->description);
        }

        // Set start time
        $start = new EventDateTime();
        $start->setDateTime($event->start_time->toRfc3339String());
        $start->setTimeZone(config('app.timezone', 'UTC'));
        $googleEvent->setStart($start);

        // Set end time
        $end = new EventDateTime();
        $end->setDateTime($event->end_time->toRfc3339String());
        $end->setTimeZone(config('app.timezone', 'UTC'));
        $googleEvent->setEnd($end);

        // Set reminders if configured
        if ($event->reminder_minutes) {
            $reminders = new EventReminders();
            $reminders->setUseDefault(false);
            
            $reminder = new EventReminder();
            $reminder->setMethod('popup');
            $reminder->setMinutes($event->reminder_minutes);
            
            $reminders->setOverrides([$reminder]);
            $googleEvent->setReminders($reminders);
        }

        // Set recurrence if applicable
        if ($event->isRecurring() && !$event->parent_event_id) {
            $rrule = $this->buildRecurrenceRule($event);
            if ($rrule) {
                $googleEvent->setRecurrence([$rrule]);
            }
        }

        return $googleEvent;
    }

    /**
     * Build an RRULE string for recurring events.
     */
    protected function buildRecurrenceRule(CalendarEvent $event): ?string
    {
        if (!$event->recurrence_type) {
            return null;
        }

        $rule = 'RRULE:';

        switch ($event->recurrence_type) {
            case 'daily':
                $rule .= 'FREQ=DAILY';
                break;
            case 'weekly':
                $rule .= 'FREQ=WEEKLY';
                if ($event->recurrence_days && count($event->recurrence_days) > 0) {
                    $days = array_map(fn($d) => $this->getDayAbbreviation($d), $event->recurrence_days);
                    $rule .= ';BYDAY=' . implode(',', $days);
                }
                break;
            case 'monthly':
                $rule .= 'FREQ=MONTHLY';
                break;
            default:
                return null;
        }

        if ($event->recurrence_end) {
            $rule .= ';UNTIL=' . $event->recurrence_end->format('Ymd\THis\Z');
        }

        return $rule;
    }

    /**
     * Convert day number to RRULE day abbreviation.
     */
    protected function getDayAbbreviation(int $day): string
    {
        $days = [
            0 => 'SU',
            1 => 'MO',
            2 => 'TU',
            3 => 'WE',
            4 => 'TH',
            5 => 'FR',
            6 => 'SA',
        ];

        return $days[$day] ?? 'MO';
    }
}
