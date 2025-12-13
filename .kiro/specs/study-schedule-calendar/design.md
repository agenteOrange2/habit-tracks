# Design Document: Study Schedule Calendar

## Overview

Sistema de calendario para gestionar horarios de estudio basados en hábitos existentes, con integración opcional a Google Calendar. El sistema permite visualizar, crear, editar y sincronizar eventos de estudio.

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      Frontend (Livewire)                     │
├─────────────────────────────────────────────────────────────┤
│  CalendarView  │  EventEditor  │  CalendarSettings          │
└───────┬────────┴───────┬───────┴──────────┬─────────────────┘
        │                │                   │
        ▼                ▼                   ▼
┌─────────────────────────────────────────────────────────────┐
│                    Service Layer                             │
├─────────────────────────────────────────────────────────────┤
│  CalendarService  │  GoogleCalendarService  │  SyncService  │
└───────┬───────────┴───────────┬─────────────┴───────┬───────┘
        │                       │                      │
        ▼                       ▼                      ▼
┌─────────────────┐   ┌─────────────────┐   ┌─────────────────┐
│    Database     │   │  Google API     │   │   Job Queue     │
│  (MySQL/SQLite) │   │  (OAuth + REST) │   │   (Laravel)     │
└─────────────────┘   └─────────────────┘   └─────────────────┘
```

## Components and Interfaces

### Livewire Components

1. **CalendarView** - Vista principal del calendario
   - Props: `$currentDate`, `$viewMode`, `$events`
   - Methods: `changeMonth()`, `changeView()`, `selectDay()`

2. **EventEditor** - Modal/página para crear/editar eventos
   - Props: `$event`, `$habits`, `$isEditing`
   - Methods: `save()`, `delete()`, `setRecurrence()`

3. **CalendarSettings** - Configuración del calendario
   - Props: `$settings`, `$isGoogleConnected`
   - Methods: `connectGoogle()`, `disconnectGoogle()`, `saveSettings()`

### Services

1. **CalendarService**
```php
interface CalendarServiceInterface {
    public function getEventsForRange(User $user, Carbon $start, Carbon $end): Collection;
    public function createEvent(User $user, array $data): CalendarEvent;
    public function updateEvent(CalendarEvent $event, array $data): CalendarEvent;
    public function deleteEvent(CalendarEvent $event, bool $deleteAll = false): void;
    public function generateRecurringEvents(CalendarEvent $event): Collection;
}
```

2. **GoogleCalendarService**
```php
interface GoogleCalendarServiceInterface {
    public function getAuthUrl(): string;
    public function handleCallback(string $code): array;
    public function createEvent(CalendarEvent $event): string;
    public function updateEvent(CalendarEvent $event): void;
    public function deleteEvent(string $googleEventId): void;
    public function refreshToken(User $user): void;
}
```

## Data Models

### CalendarEvent
```php
Schema::create('calendar_events', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('habit_id')->nullable()->constrained()->nullOnDelete();
    $table->string('title');
    $table->text('description')->nullable();
    $table->dateTime('start_time');
    $table->dateTime('end_time');
    $table->string('recurrence_type')->nullable(); // daily, weekly, monthly
    $table->json('recurrence_days')->nullable(); // [1,3,5] for Mon,Wed,Fri
    $table->date('recurrence_end')->nullable();
    $table->foreignId('parent_event_id')->nullable(); // for recurring instances
    $table->string('google_event_id')->nullable();
    $table->boolean('sync_to_google')->default(false);
    $table->integer('reminder_minutes')->nullable();
    $table->string('color')->nullable();
    $table->timestamps();
});
```

### CalendarSetting
```php
Schema::create('calendar_settings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->integer('default_duration')->default(60); // minutes
    $table->time('working_hours_start')->default('09:00');
    $table->time('working_hours_end')->default('18:00');
    $table->boolean('auto_sync')->default(false);
    $table->string('default_view')->default('month'); // month, week, day
    $table->integer('default_reminder')->default(15); // minutes
    $table->timestamps();
});
```

### GoogleCalendarToken
```php
Schema::create('google_calendar_tokens', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->text('access_token');
    $table->text('refresh_token');
    $table->timestamp('expires_at');
    $table->string('calendar_id')->nullable();
    $table->timestamps();
});
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Event persistence round-trip
*For any* valid event data, creating an event and then retrieving it should return equivalent data.
**Validates: Requirements 2.3**

### Property 2: Required fields validation
*For any* event creation attempt missing date, start_time, or duration, the system should reject the creation.
**Validates: Requirements 2.2**

### Property 3: Recurring event generation
*For any* event with recurrence pattern, the number of generated events should match the expected count based on pattern and end date.
**Validates: Requirements 2.4**

### Property 4: Event deletion removes from storage
*For any* deleted event, querying for that event should return null/empty.
**Validates: Requirements 3.3**

### Property 5: Event update persistence
*For any* valid event modification, the updated fields should be persisted and retrievable.
**Validates: Requirements 3.2**

### Property 6: OAuth token storage encryption
*For any* stored Google OAuth token, the access_token and refresh_token should be encrypted.
**Validates: Requirements 4.3**

### Property 7: Failed sync queuing
*For any* failed Google Calendar sync operation, the operation should be added to a retry queue.
**Validates: Requirements 5.4**

### Property 8: Default duration application
*For any* new event created when default_duration is set, the event duration should equal the default unless explicitly overridden.
**Validates: Requirements 6.2**

### Property 9: Auto-sync toggle persistence
*For any* change to auto_sync setting, the new value should be persisted and affect subsequent sync behavior.
**Validates: Requirements 6.4**

### Property 10: Day event indicator consistency
*For any* day in the calendar, if events exist for that day, a visual indicator should be present.
**Validates: Requirements 1.4**

## Error Handling

1. **Google API Errors**
   - Token expired: Auto-refresh using refresh_token
   - Rate limiting: Queue with exponential backoff
   - Network errors: Queue for retry, notify user

2. **Validation Errors**
   - Missing required fields: Show inline validation messages
   - Invalid date ranges: Prevent end_time before start_time

3. **Sync Conflicts**
   - Local wins by default
   - Log conflicts for user review

## Testing Strategy

### Unit Tests
- CalendarService methods
- Event validation logic
- Recurrence pattern generation
- Date/time calculations

### Property-Based Tests (using Pest + Faker)
- Event CRUD round-trip
- Recurrence generation correctness
- Token encryption verification
- Settings persistence

### Integration Tests
- Google OAuth flow (mocked)
- Event sync operations (mocked)
- Full calendar rendering

