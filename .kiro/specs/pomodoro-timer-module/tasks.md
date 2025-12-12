# Implementation Plan

- [x] 1. Create PomodoroService for business logic


  - Create `app/Services/PomodoroService.php` class
  - Implement `canStartSession(User $user): bool` method to check energy
  - Implement `startSession(User $user, ?int $habitId, int $duration): PomodoroSession` method
  - Implement `completeSession(PomodoroSession $session): void` method
  - Implement `interruptSession(PomodoroSession $session): void` method
  - Implement `getTodayStats(User $user): array` method
  - Implement `getRecentSessions(User $user, int $limit = 10): Collection` method
  - Add proper error handling and validation
  - _Requirements: 1.1, 1.2, 1.3, 7.3, 7.4, 7.5_

- [ ]* 1.1 Write property test for energy consumption
  - **Property 1: Energy consumption consistency**
  - **Validates: Requirements 1.2**

- [ ]* 1.2 Write property test for session creation atomicity
  - **Property 2: Session creation atomicity**
  - **Validates: Requirements 1.3**


- [x] 2. Add scopes and methods to PomodoroSession model

  - Add `scopeCompleted($query)` to filter completed sessions
  - Add `scopeInterrupted($query)` to filter interrupted sessions
  - Add `scopeToday($query)` to filter today's sessions
  - Add `scopeForUser($query, User $user)` to filter by user
  - Add `getFormattedDurationAttribute()` computed property
  - Add `getStatusAttribute()` computed property
  - _Requirements: 7.1, 7.2, 8.1, 8.3_

- [x] 3. Add computed properties to User model



  - Add `getTodayPomodorosAttribute(): int` to count today's completed pomodoros
  - Add `getTodayFocusTimeAttribute(): int` to sum today's focus time
  - Add relationship method `pomodoroSessions()` if not exists
  - _Requirements: 7.1, 7.2_

- [x] 4. Update PomodoroTimer Livewire component


  - Update component properties: `$selectedHabit`, `$duration`, `$timerState`, `$remainingSeconds`, `$currentSession`
  - Implement `mount()` to load initial data (habits, energy, stats)
  - Implement `startTimer()` method with energy validation
  - Implement `pauseTimer()` method to freeze countdown
  - Implement `resumeTimer()` method to continue countdown
  - Implement `stopTimer()` method to mark session as interrupted
  - Implement `completeTimer()` method to finalize session and update stats
  - Implement `selectHabit(int $habitId)` method
  - Implement `setDuration(int $minutes)` method with validation
  - Implement `loadRecentSessions()` method
  - Add computed property `getEnergyStatus()` using EnergyService
  - _Requirements: 1.1, 1.2, 1.3, 2.1, 2.2, 3.1, 3.2, 5.1, 5.2, 5.3, 5.4, 6.1_

- [ ]* 4.1 Write property test for habit association
  - **Property 4: Habit association preservation**
  - **Validates: Requirements 2.4**

- [ ]* 4.2 Write property test for duration validation
  - **Property 8: Duration bounds validation**
  - **Validates: Requirements 3.1, 3.4**

- [x] 5. Create Pomodoro timer view


  - Create `resources/views/livewire/pomodoro/pomodoro-timer.blade.php`
  - Implement header with back button and page title
  - Implement energy display bar with current/max values and percentage
  - Implement large timer display showing MM:SS format
  - Implement circular progress indicator
  - Implement habit selector dropdown with active habits
  - Implement duration selector with 15/25/50 minute buttons
  - Implement control buttons (Start/Pause/Resume/Stop) with conditional rendering
  - Style using Tailwind CSS matching Notion design system
  - Add responsive breakpoints for mobile/tablet/desktop
  - _Requirements: 2.1, 3.1, 4.1, 5.1, 5.2, 5.3, 6.1, 9.1, 9.2, 9.3, 9.4_


- [x] 6. Implement client-side timer with Alpine.js

  - Add Alpine.js component to timer view
  - Implement `remainingSeconds` reactive data property
  - Implement `isRunning` and `isPaused` state flags
  - Implement `startCountdown()` method to begin interval
  - Implement `pauseCountdown()` method to clear interval
  - Implement `resumeCountdown()` method to restart interval
  - Implement `tick()` method to decrement seconds and check for completion
  - Implement `formatTime(seconds)` helper to display MM:SS
  - Sync with Livewire component state using `wire:model` and events
  - Dispatch `timerCompleted` event when countdown reaches zero
  - _Requirements: 4.1, 4.2, 4.3, 5.1, 5.2, 5.3, 5.4_

- [ ]* 6.1 Write property test for timer countdown accuracy
  - **Property 3: Timer countdown accuracy**
  - **Validates: Requirements 4.1, 4.2**

- [x] 7. Implement statistics cards section

  - Create statistics cards component in view
  - Display "Today" card with pomodoros count and focus time
  - Display "Total" card with all-time pomodoros and focus time
  - Use icons (🍅 for pomodoros, ⏱ for time)
  - Format time as "X min" or "X hr Y min" for large values
  - Update statistics reactively when session completes
  - Style cards with Notion-inspired design
  - _Requirements: 7.1, 7.2, 9.1, 9.2_

- [ ]* 7.1 Write property test for statistics increment
  - **Property 5: Statistics increment on completion**
  - **Validates: Requirements 7.3, 7.4**

- [ ]* 7.2 Write property test for interrupted sessions exclusion
  - **Property 6: Interrupted sessions exclusion**
  - **Validates: Requirements 7.5**

- [x] 8. Implement recent sessions history section

  - Create session history list component in view
  - Load and display 10 most recent sessions using `loadRecentSessions()`
  - Display habit name (or "General focus" if no habit)
  - Display duration and completion status (✅ completed, ⚠️ interrupted)
  - Display formatted timestamp (e.g., "Today at 10:30 AM", "Yesterday at 8:45 PM")
  - Style completed and interrupted sessions differently
  - Add empty state message when no sessions exist
  - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

- [ ]* 8.1 Write property test for timestamp consistency
  - **Property 10: Timestamp consistency**
  - **Validates: Requirements 1.3, 4.3**

- [x] 9. Implement energy validation and warnings

  - Check energy level before enabling start button
  - Display warning message when energy < 10
  - Show time until sufficient energy regenerates
  - Display energy bar with color coding (green > 50, yellow 20-50, red < 20)
  - Add tooltip explaining energy system on hover
  - Disable start button and show disabled state when insufficient energy
  - _Requirements: 1.5, 6.1, 6.2, 6.4, 6.5_

- [ ]* 9.1 Write property test for energy insufficiency prevention
  - **Property 7: Energy insufficiency prevention**
  - **Validates: Requirements 1.5**

- [x] 10. Implement notifications system

  - Add success notification when session completes
  - Display points earned and energy consumed in notification
  - Request browser notification permission on first visit
  - Send browser notification when session completes (if permission granted)
  - Update page title with "✅ Pomodoro Complete!" when on different tab
  - Reset page title when user returns to tab
  - Add notification sound (optional, with mute toggle)
  - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

- [x] 11. Update routes to use PomodoroTimer component


  - Update `routes/admin.php` to route `/pomodoro` to PomodoroTimer component
  - Remove placeholder redirect
  - Ensure route uses `admin.*` naming convention
  - Apply authentication and verification middleware
  - _Requirements: 1.1_

- [x] 12. Add Livewire polling for energy updates


  - Add `wire:poll.5s` to energy display component
  - Implement listener for `energyUpdated` event
  - Refresh energy status when event is dispatched
  - Update UI reactively when energy changes
  - _Requirements: 6.3_

- [x] 13. Implement state transition validation

  - Add validation to prevent starting session when one is already running
  - Add validation to prevent pausing when timer is not running
  - Add validation to prevent resuming when timer is not paused
  - Display appropriate error messages for invalid state transitions
  - Log state transition errors for debugging
  - _Requirements: 5.1, 5.2, 5.3, 5.4_

- [ ]* 13.1 Write property test for session state transitions
  - **Property 9: Session state transitions**
  - **Validates: Requirements 5.1, 5.2, 5.3, 5.4**

- [x] 14. Add loading states and transitions

  - Add `wire:loading` indicators to start/pause/stop buttons
  - Add loading spinner when starting session
  - Add smooth transitions for state changes (idle → running → completed)
  - Add fade-in animation for notifications
  - Add pulse animation for timer when running
  - Ensure all animations use Tailwind CSS utilities
  - _Requirements: 9.5_


- [ ] 15. Implement error handling
  - Add try-catch blocks in all PomodoroService methods
  - Handle database connection errors gracefully
  - Rollback energy consumption if session creation fails
  - Display user-friendly error messages
  - Log errors for debugging
  - Add error recovery suggestions in UI
  - _Requirements: 1.1, 1.2, 1.3_


- [ ] 16. Add accessibility features
  - Add ARIA labels to all interactive elements
  - Ensure keyboard navigation works (Tab, Enter, Space)
  - Add `role="timer"` to timer display
  - Add `aria-live="polite"` to timer for screen reader updates
  - Ensure color contrast meets WCAG AA standards
  - Add focus indicators to all focusable elements
  - Test with screen reader (NVDA or JAWS)


  - _Requirements: 9.1, 9.2, 9.3, 9.4_

- [ ] 17. Optimize performance
  - Add database indexes on `pomodoro_sessions.user_id` and `started_at`
  - Add database index on `pomodoro_sessions.completed_at`
  - Implement caching for today's stats (1 minute TTL)
  - Implement caching for recent sessions (30 seconds TTL)

  - Use eager loading for habit relationships in recent sessions
  - Minimize Livewire re-renders by using `wire:model.lazy`
  - _Requirements: 7.1, 7.2, 8.1_

- [x] 18. Checkpoint - Ensure all tests pass

  - Ensure all tests pass, ask the user if questions arise.

- [ ]* 19. Write integration tests
  - Test full flow: select habit → start → complete → verify stats
  - Test pause/resume maintains correct time
  - Test stop marks session as interrupted
  - Test energy consumption and regeneration
  - Test multiple sessions in sequence
  - Test insufficient energy prevents session start
  - _Requirements: 1.1, 1.2, 1.3, 5.1, 5.2, 5.3, 5.4, 7.3, 7.4_


- [ ] 20. Manual testing and polish
  - Test on Chrome, Firefox, Safari
  - Test on mobile devices (iOS and Android)
  - Test with different screen sizes
  - Test with slow network connection
  - Test browser notification permissions (allow/deny)
  - Verify all animations are smooth
  - Verify responsive design works correctly
  - Test accessibility with keyboard only
  - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5, 10.3, 10.4_

- [x] 21. Final checkpoint - Ensure all tests pass


  - Ensure all tests pass, ask the user if questions arise.
