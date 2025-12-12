# Implementation Plan - Pomodoro Advanced Features

- [x] 1. Create database migration for user_pomodoro_settings


  - Create migration file for `user_pomodoro_settings` table
  - Add columns: user_id, short_break_duration, long_break_duration, auto_start_breaks, sound_enabled, cycle_count
  - Add unique constraint on user_id
  - Add foreign key constraint to users table
  - _Requirements: 11.1, 11.4, 12.1, 12.5_



- [x] 2. Update pomodoro_sessions table schema





  - Create migration to add `session_type` column (VARCHAR, default 'pomodoro')
  - Add `resumed_from_id` column (BIGINT, nullable, foreign key to pomodoro_sessions)
  - Add `remaining_seconds` column (INT, nullable)
  - Add index on session_type for filtering

  - _Requirements: 4.1, 6.3, 6.4_

- [x] 3. Create UserPomodoroSettings model

  - Create `app/Models/UserPomodoroSettings.php` model
  - Define fillable fields and casts
  - Add relationship to User model
  - Add default values in model
  - Create factory for testing
  - _Requirements: 11.4, 12.5_

- [x] 4. Update User model with settings relationship



  - Add `pomodoroSettings()` relationship method
  - Add `getOrCreatePomodoroSettings()` helper method
  - Update User factory to create default settings
  - _Requirements: 11.4, 12.5_

- [x] 5. Enhance PomodoroSession model


  - Add `session_type` to fillable array
  - Add `resumed_from_id` and `remaining_seconds` to fillable
  - Create `scopeBreaks($query)` scope
  - Create `scopePomodoros($query)` scope
  - Add `canBeResumed(): bool` method
  - Add `getResumedSession()` method
  - Add `isBreak(): bool` method
  - Add `getSessionTypeLabel(): string` method
  - _Requirements: 4.1, 6.1, 6.2, 7.2, 7.3_

- [x] 6. Enhance PomodoroService with new methods



  - Add `resumeSession(PomodoroSession $session, int $remainingMinutes): PomodoroSession`
  - Add `startBreak(User $user, string $type, int $duration): PomodoroSession`
  - Add `getActiveCycle(User $user): array`
  - Add `incrementCycle(User $user): void`
  - Add `resetCycle(User $user): void`
  - Add `getUserSettings(User $user): array`
  - Add `updateUserSettings(User $user, array $settings): void`
  - Add validation for break types and durations
  - _Requirements: 4.1, 4.2, 4.3, 6.2, 10.1, 10.2, 10.3, 11.2, 11.3, 11.4_

- [ ]* 6.1 Write property test for cycle counter consistency
  - **Property 3: Cycle counter consistency**
  - **Validates: Requirements 10.2, 10.3**

- [ ]* 6.2 Write property test for break type determination
  - **Property 4: Break type determination**
  - **Validates: Requirements 4.2, 4.3**

- [ ]* 6.3 Write property test for resume time preservation
  - **Property 5: Resume session time preservation**
  - **Validates: Requirements 6.2, 6.3**

- [x] 7. Create localStorage manager utility


  - Create `resources/js/pomodoro-storage.js` module
  - Implement `saveTimerState(state)` function
  - Implement `loadTimerState()` function
  - Implement `clearTimerState()` function
  - Implement `saveWidgetPosition(x, y)` function
  - Implement `loadWidgetPosition()` function
  - Implement `saveRecentDurations(durations)` function
  - Implement `loadRecentDurations()` function
  - Add error handling for quota exceeded
  - _Requirements: 1.3, 1.5, 2.5, 3.4, 3.5_

- [ ]* 7.1 Write property test for localStorage persistence
  - **Property 1: Timer state persistence**
  - **Validates: Requirements 1.3, 1.5**

- [ ]* 7.2 Write property test for localStorage sync frequency
  - **Property 8: localStorage sync frequency**
  - **Validates: Requirements 1.3**

- [x] 8. Create Alpine.js global store


  - Create `resources/js/pomodoro-store.js` with Alpine.store definition
  - Define all state properties (timerState, remainingSeconds, cycleCount, etc.)
  - Implement `startTimer(duration, habitId, type)` method
  - Implement `pauseTimer()` method
  - Implement `resumeTimer()` method
  - Implement `stopTimer()` method
  - Implement `completeTimer()` method
  - Implement `startBreak(type)` method
  - Implement `skipBreak()` method
  - Implement `incrementCycle()` method
  - Implement `resetCycle()` method
  - Implement `saveToLocalStorage()` method
  - Implement `loadFromLocalStorage()` method
  - Implement `syncWithBackend()` method
  - Add interval for countdown (every 1 second)
  - Add interval for backend sync (every 30 seconds)
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 4.1, 4.4, 5.1, 5.2, 10.2, 10.3_

- [x] 9. Implement cross-tab synchronization



  - Add `storage` event listener in Alpine.js store
  - Implement `handleStorageEvent(event)` method
  - Sync timer state when localStorage changes in another tab
  - Prevent duplicate timer starts across tabs
  - Update all tabs when timer completes
  - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

- [ ]* 9.1 Write property test for cross-tab sync
  - **Property 2: Cross-tab synchronization**
  - **Validates: Requirements 8.1, 8.2, 8.3, 8.4**

- [x] 10. Create FloatingTimerWidget Livewire component


  - Create `app/Livewire/Pomodoro/FloatingTimerWidget.php`
  - Define properties: visible, collapsed, position
  - Implement `mount()` to load saved state
  - Implement `toggle()` to show/hide widget
  - Implement `updatePosition(int $x, int $y)` to save position
  - Implement `syncState()` to refresh from Alpine store
  - Add listeners for timer events (timerStarted, timerPaused, etc.)
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

- [x] 11. Create floating widget view


  - Create `resources/views/livewire/pomodoro/floating-timer-widget.blade.php`
  - Implement expanded state UI with timer, controls, and stats
  - Implement minimized state UI with just countdown
  - Add collapse/expand button
  - Add close button
  - Style with Tailwind CSS (semi-transparent, rounded, shadow)
  - Add draggable functionality with Alpine.js
  - Ensure responsive positioning
  - Add hover effects (opacity change)
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 15.1, 15.2, 15.3, 15.4, 15.5_

- [x] 12. Integrate widget into main layout


  - Add FloatingTimerWidget component to `resources/views/components/layouts/app.blade.php`
  - Ensure widget appears on all authenticated pages
  - Add conditional rendering (only show when timer active)
  - Connect widget to Alpine.js global store
  - Test widget visibility across different pages
  - _Requirements: 2.1, 1.1, 1.2_



- [x] 13. Update PomodoroTimer component for custom durations

  - Add `customDuration` property to component
  - Add `recentCustomDurations` property
  - Implement `setCustomDuration(int $minutes)` method with validation (1-120)
  - Load recent custom durations from localStorage
  - Save custom duration to recent list when used
  - Limit recent list to 3 items
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [ ]* 13.1 Write property test for custom duration bounds
  - **Property 6: Custom duration bounds**
  - **Validates: Requirements 3.2**

- [x] 14. Add custom duration UI to timer view



  - Add custom duration input field to duration selector
  - Add validation feedback for invalid inputs
  - Display recent custom durations as quick-select buttons
  - Update timer display when custom duration selected
  - Style input field to match Notion design
  - _Requirements: 3.1, 3.2, 3.5_

- [x] 15. Implement break timer logic in component



  - Add `breakType` property ('short_break' | 'long_break')
  - Implement `startBreak(string $type)` method
  - Implement `skipBreak()` method
  - Add logic to determine break type based on cycle count
  - Add auto-start break based on user settings
  - Display break timer with different styling
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 5.1, 5.2, 5.3_

- [x] 16. Add break timer UI to view


  - Add break timer display (different color scheme)
  - Add "Skip Break" button during breaks
  - Display break type label ("Descanso Corto" / "Descanso Largo")
  - Add visual distinction between Pomodoro and break timers
  - Show motivational message during breaks
  - _Requirements: 4.4, 5.1, 5.2_


- [x] 17. Implement cycle tracking

  - Add cycle counter display to timer view
  - Show progress indicator (●●●○ style)
  - Display "Next: Short/Long Break" message
  - Increment cycle on Pomodoro completion
  - Reset cycle after long break
  - Sync cycle count with backend
  - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

- [x] 18. Add resume functionality to interrupted sessions


  - Add "Resume" button to interrupted sessions in history
  - Implement `resumeSession(int $sessionId)` method in component
  - Calculate remaining time from interrupted session
  - Create new session linked to original via `resumed_from_id`
  - Display "Resuming..." indicator when starting resumed session
  - Disable resume button if session already resumed
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5_

- [x] 19. Implement session history filters


  - Add filter tabs: "Todas", "Completadas", "Interrumpidas"
  - Add `sessionFilter` property to component ('all' | 'completed' | 'interrupted')
  - Implement `setFilter(string $filter)` method
  - Filter sessions based on selected filter
  - Save filter selection to localStorage
  - Load filter selection on mount
  - Update UI to highlight active filter
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [ ]* 19.1 Write property test for filter consistency
  - **Property 10: Filter consistency**
  - **Validates: Requirements 7.2, 7.3, 7.4**

- [x] 20. Enhance session history display



  - Show session type badge (Pomodoro/Break)
  - Display resume status for interrupted sessions
  - Show link to original session for resumed sessions
  - Add quick actions (Resume, View Details)
  - Improve visual distinction between completed and interrupted
  - _Requirements: 6.1, 7.2, 7.3, 8.1_

- [x] 21. Create Pomodoro settings section


  - Add settings card to timer page
  - Add short break duration input (1-30 minutes)
  - Add long break duration input (5-60 minutes)
  - Add "Auto-start breaks" toggle
  - Add "Sound enabled" toggle
  - Implement `updateSettings()` method
  - Save settings to backend immediately on change
  - Load settings on component mount
  - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5, 12.1, 12.2, 12.3, 12.4, 12.5_

- [x] 22. Implement keyboard shortcuts


  - Add global keyboard event listener
  - Implement Space bar → toggle play/pause
  - Implement Escape → stop timer
  - Implement 'B' → start break
  - Implement 'N' → start new Pomodoro
  - Add visual confirmation toast for shortcuts
  - Prevent shortcuts when typing in input fields
  - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5_

- [x] 23. Add sound notifications


  - Create or source notification sound files
  - Add sounds to `public/sounds/` directory
  - Implement `playSound(type)` function in Alpine store
  - Play completion sound when Pomodoro ends
  - Play break start sound
  - Play break end sound
  - Respect user's sound_enabled setting
  - Add fallback for browsers without audio support
  - _Requirements: 9.1, 9.2, 9.3, 9.5_


- [x] 24. Add visual animations and cues

  - Add pulsing animation to floating widget when running
  - Add completion animation (confetti or celebration)
  - Add smooth transitions for state changes
  - Add progress circle animation
  - Add fade-in/out for notifications
  - Ensure animations are performant (use CSS transforms)
  - _Requirements: 9.4, 15.2_

- [x] 25. Implement widget dragging functionality


  - Add Alpine.js drag behavior to widget
  - Track mouse/touch position during drag
  - Update widget position in real-time
  - Constrain widget to viewport bounds
  - Save final position to localStorage
  - Add visual feedback during drag (cursor change)
  - _Requirements: 2.5, 15.5_

- [x] 26. Add statistics to floating widget

  - Display today's Pomodoros count in expanded widget
  - Display today's focus time in expanded widget
  - Display cycle progress (X/4) in expanded widget
  - Update statistics in real-time
  - Hide statistics in minimized state
  - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5_

- [x] 27. Implement backend sync mechanism


  - Add periodic sync every 30 seconds in Alpine store
  - Sync timer state to backend via Livewire
  - Handle sync failures gracefully (queue for retry)
  - Show offline indicator when sync fails
  - Sync immediately on critical events (start, complete, stop)
  - Resolve conflicts (server state wins)
  - _Requirements: 1.3, 1.4, 1.5_



- [x] 28. Add manual break start button


  - Add "Start Break" button when auto-start is disabled
  - Show button after Pomodoro completion
  - Determine break type based on cycle count
  - Start appropriate break when clicked
  - Hide button when auto-start is enabled

  - _Requirements: 12.3, 12.4_

- [x] 29. Implement timer accuracy improvements

  - Add drift correction to setInterval
  - Sync with server time periodically
  - Handle tab sleep/wake cycles
  - Adjust remaining time on wake
  - Log accuracy metrics for debugging
  - _Requirements: 1.1, 1.2, 1.4_

- [x] 30. Add error handling and recovery



  - Handle localStorage quota exceeded
  - Handle cross-tab conflicts
  - Handle backend sync failures
  - Handle widget positioning errors
  - Display user-friendly error messages
  - Implement automatic recovery where possible
  - Log errors for debugging
  - _Requirements: 1.1, 1.2, 1.3, 8.1, 8.2_

- [ ]* 30.1 Write property test for break skip non-counting
  - **Property 9: Break skip non-counting**
  - **Validates: Requirements 5.3**


- [ ] 31. Run database migrations
  - Execute migration for user_pomodoro_settings table
  - Execute migration for pomodoro_sessions updates
  - Verify migrations applied successfully
  - Create default settings for existing users
  - _Requirements: 11.1, 11.4, 12.5_



- [x] 32. Checkpoint - Ensure all tests pass

  - Ensure all tests pass, ask the user if questions arise.

- [ ] 33. Manual testing - Persistence
  - Test timer continues when navigating pages
  - Test timer restores after browser close/reopen
  - Test localStorage saves every second
  - Test timer completes on different page
  - Test multiple browser tabs sync correctly
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 8.1, 8.2, 8.3, 8.4, 8.5_

- [ ] 34. Manual testing - Floating widget
  - Test widget appears when timer starts
  - Test widget collapse/expand
  - Test widget dragging and positioning
  - Test widget visibility across pages
  - Test widget statistics display
  - Test widget on mobile devices
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 14.1, 14.2, 14.3, 15.1, 15.2, 15.3, 15.4, 15.5_

- [ ] 35. Manual testing - Custom durations and breaks
  - Test custom duration input validation
  - Test recent custom durations
  - Test short break after 1-3 Pomodoros
  - Test long break after 4 Pomodoros
  - Test skip break functionality
  - Test auto-start breaks toggle
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 4.1, 4.2, 4.3, 4.4, 5.1, 5.2, 5.3, 12.1, 12.2, 12.3_

- [ ] 36. Manual testing - Session management
  - Test resume interrupted session
  - Test session history filters
  - Test cycle counter increments correctly
  - Test cycle resets after long break
  - Test settings save and load
  - _Requirements: 6.1, 6.2, 6.3, 7.1, 7.2, 7.3, 10.1, 10.2, 10.3, 11.1, 11.4_

- [ ] 37. Manual testing - Keyboard shortcuts and sounds
  - Test Space bar play/pause
  - Test Escape stop
  - Test 'B' start break
  - Test 'N' start new Pomodoro
  - Test sound notifications
  - Test sound toggle
  - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5, 9.1, 9.2, 9.3, 9.5_

- [ ] 38. Performance optimization
  - Optimize localStorage writes (debounce)
  - Optimize cross-tab event handling
  - Optimize widget rendering (CSS transforms)
  - Minimize Alpine.js re-renders
  - Test with slow network
  - Profile and fix any performance issues
  - _Requirements: 1.3, 8.5, 15.5_

- [ ] 39. Accessibility improvements
  - Add ARIA labels to all controls
  - Test keyboard navigation
  - Test with screen reader
  - Ensure focus management
  - Test high contrast mode
  - Add visual feedback for keyboard shortcuts
  - _Requirements: 13.5, 15.1_

- [ ] 40. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
