# Implementation Plan

- [x] 1. Database setup and models
  - [x] 1.1 Create migration for calendar_events table
    - Define all columns as specified in design
    - Add indexes for user_id, start_time, google_event_id
    - _Requirements: 2.3_
  - [x] 1.2 Create migration for calendar_settings table
    - Define default values for all settings
    - _Requirements: 6.1, 6.2_
  - [x] 1.3 Create migration for google_calendar_tokens table
    - Use text type for encrypted tokens
    - _Requirements: 4.3_
  - [x] 1.4 Create CalendarEvent model with relationships
    - Define fillable, casts, relationships to User and Habit
    - Add scopes for date range queries
    - _Requirements: 2.3_
  - [x] 1.5 Create CalendarSetting model
    - Define fillable and casts
    - Add relationship to User
    - _Requirements: 6.1_
  - [x] 1.6 Create GoogleCalendarToken model with encryption
    - Implement encrypted casts for access_token and refresh_token
    - _Requirements: 4.3_
  - [ ]* 1.7 Write property test for token encryption
    - **Property 6: OAuth token storage encryption**
    - **Validates: Requirements 4.3**

- [x] 2. Calendar Service implementation
  - [x] 2.1 Create CalendarService class
    - Implement getEventsForRange method
    - Implement createEvent with validation
    - Implement updateEvent and deleteEvent
    - _Requirements: 2.3, 3.2, 3.3_
  - [x] 2.2 Implement recurrence pattern generation
    - Support daily, weekly, monthly patterns
    - Generate events up to recurrence_end date
    - _Requirements: 2.4_
  - [ ]* 2.3 Write property test for event persistence
    - **Property 1: Event persistence round-trip**
    - **Validates: Requirements 2.3**
  - [ ]* 2.4 Write property test for required fields validation
    - **Property 2: Required fields validation**
    - **Validates: Requirements 2.2**
  - [ ]* 2.5 Write property test for recurring event generation
    - **Property 3: Recurring event generation**
    - **Validates: Requirements 2.4**
  - [ ]* 2.6 Write property test for event deletion
    - **Property 4: Event deletion removes from storage**
    - **Validates: Requirements 3.3**

- [ ] 3. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 4. Calendar View component
  - [x] 4.1 Create CalendarView Livewire component
    - Implement month/week/day view modes
    - Load events for current view range
    - _Requirements: 1.1, 1.3_
  - [x] 4.2 Create calendar view blade template
    - Build responsive calendar grid
    - Show event indicators on days
    - Implement view mode switching
    - _Requirements: 1.1, 1.4_
  - [x] 4.3 Implement day selection and event display
    - Show events for selected day
    - Navigate to event editor on click
    - _Requirements: 1.2_
  - [ ]* 4.4 Write property test for day event indicators
    - **Property 10: Day event indicator consistency**
    - **Validates: Requirements 1.4**

- [x] 5. Event Editor component
  - [x] 5.1 Create EventEditor Livewire component
    - Load habits for selection dropdown
    - Implement create/edit/delete methods
    - Handle recurrence settings
    - _Requirements: 2.1, 2.2, 3.1_
  - [x] 5.2 Create event editor blade template
    - Form with habit selection, date/time pickers
    - Recurrence pattern selector
    - Reminder settings
    - _Requirements: 2.1, 2.2, 7.1_
  - [x] 5.3 Implement recurring event deletion options
    - Modal to choose single vs all occurrences
    - _Requirements: 3.4_
  - [ ]* 5.5 Write property test for event update persistence
    - **Property 5: Event update persistence**
    - **Validates: Requirements 3.2**

- [ ] 6. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 7. Google Calendar Integration





  - [x] 7.1 Install and configure Google API client


    - Add google/apiclient package
    - Create config/google.php configuration
    - _Requirements: 4.1_
  - [x] 7.2 Create GoogleCalendarService class


    - Implement OAuth URL generation
    - Implement callback handler with token storage
    - Implement token refresh logic
    - _Requirements: 4.2, 4.3_
  - [x] 7.3 Implement Google Calendar CRUD operations


    - Create, update, delete events in Google Calendar
    - Map local events to Google Calendar format
    - _Requirements: 5.1, 5.2, 5.3_

  - [x] 7.4 Create sync queue job for failed operations

    - Queue failed sync operations
    - Implement retry with exponential backoff
    - _Requirements: 5.4_
  - [ ]* 7.5 Write property test for failed sync queuing
    - **Property 7: Failed sync queuing**
    - **Validates: Requirements 5.4**

- [x] 8. Calendar Settings component
  - [x] 8.1 Create CalendarSettings Livewire component
    - Load/save user settings
    - Handle Google connection/disconnection
    - _Requirements: 6.1, 4.1_
  - [x] 8.2 Create settings blade template
    - Default duration, working hours inputs
    - Google Calendar connection button
    - Auto-sync toggle
    - _Requirements: 6.1, 6.2, 6.3, 6.4_
  - [ ]* 8.3 Write property test for default duration application
    - **Property 8: Default duration application**
    - **Validates: Requirements 6.2**
  - [ ]* 8.4 Write property test for auto-sync toggle
    - **Property 9: Auto-sync toggle persistence**
    - **Validates: Requirements 6.4**

- [x] 9. Routes and Navigation
  - [x] 9.1 Add calendar routes to admin.php
    - Calendar index, event create/edit, settings
    - _Requirements: 1.1_
  - [x] 9.2 Add calendar link to sidebar navigation
    - _Requirements: 1.1_
  - [x] 9.3 Add Google OAuth callback route (placeholder)
    - _Requirements: 4.2_

- [ ] 10. Final Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

