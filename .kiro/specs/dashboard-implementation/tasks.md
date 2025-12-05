# Implementation Plan

- [ ] 1. Setup dashboard route and base structure
  - Create route in `routes/web.php` for dashboard
  - Update sidebar navigation to include new menu items
  - Ensure authentication middleware is applied
  - _Requirements: 1.1, 1.5, 2.2_

- [ ] 2. Create dashboard index Volt component
  - [ ] 2.1 Create main dashboard Volt component with functional API
    - Create `resources/views/livewire/dashboard/index.blade.php`
    - Define state for greeting, completion rate, and today's habits
    - Implement computed properties for user level, stats, and streak
    - Create mount method to initialize data
    - _Requirements: 1.1, 1.2, 1.3_

  - [ ]* 2.2 Write property test for greeting time consistency
    - **Property 1: Greeting Time Consistency**
    - **Validates: Requirements 6.1, 6.2, 6.3**

  - [ ]* 2.3 Write property test for user name display
    - **Property 2: User Name Display**
    - **Validates: Requirements 1.2, 2.4**

  - [ ]* 2.4 Write property test for authentication guard
    - **Property 3: Authentication Guard**
    - **Validates: Requirements 1.5**

- [ ] 3. Implement stats cards component
  - [ ] 3.1 Create stats cards Volt component
    - Create `resources/views/livewire/dashboard/stats-cards.blade.php`
    - Implement level/XP card with progress bar
    - Implement streak card with 7-day visualization
    - Implement completion rate card
    - Style cards using Tailwind CSS matching design2.html
    - _Requirements: 3.1, 3.2, 3.3, 3.5, 3.6_

  - [ ]* 3.2 Write property test for user stats display
    - **Property 4: User Stats Display**
    - **Validates: Requirements 3.1, 3.3, 3.5**

  - [ ]* 3.3 Write property test for XP progress calculation
    - **Property 5: XP Progress Calculation**
    - **Validates: Requirements 3.2**

  - [ ]* 3.4 Write property test for completion rate calculation
    - **Property 6: Completion Rate Calculation**
    - **Validates: Requirements 3.6**

- [ ] 4. Implement pomodoro timer component
  - [ ] 4.1 Create pomodoro timer Volt component
    - Create `resources/views/livewire/dashboard/pomodoro-timer.blade.php`
    - Define state for timer (1500 seconds) and running status
    - Implement toggleTimer method
    - Implement tick method with wire:poll
    - Implement formatTime helper
    - Style timer matching design2.html
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [ ]* 4.2 Write property test for timer countdown behavior
    - **Property 11: Timer Countdown Behavior**
    - **Validates: Requirements 4.3**

  - [ ]* 4.3 Write property test for timer toggle functionality
    - **Property 12: Timer Toggle Functionality**
    - **Validates: Requirements 4.2, 4.4**

- [ ] 5. Implement habits list component
  - [ ] 5.1 Create habits list Volt component
    - Create `resources/views/livewire/dashboard/habits-list.blade.php`
    - Define state for habits array
    - Implement mount method to load today's scheduled habits
    - Implement query to filter habits using isScheduledForToday()
    - Display habits with checkbox, name, category, and XP
    - Style list matching design2.html
    - _Requirements: 5.1, 5.2, 5.6_

  - [ ]* 5.2 Write property test for today's habits filter
    - **Property 7: Today's Habits Filter**
    - **Validates: Requirements 5.1**

  - [ ]* 5.3 Write property test for habit display completeness
    - **Property 8: Habit Display Completeness**
    - **Validates: Requirements 5.2**

  - [ ] 5.4 Implement habit completion functionality
    - Create toggleHabit method
    - Create completeHabit method with service integration
    - Integrate PointsService to award points
    - Integrate StreakService to update streaks
    - Integrate AchievementService to check unlocks
    - Wrap service calls in database transaction
    - Add error handling and user notifications
    - _Requirements: 5.3, 5.5, 8.1, 8.2, 8.3_

  - [ ]* 5.5 Write property test for habit completion toggle
    - **Property 9: Habit Completion Toggle**
    - **Validates: Requirements 5.3, 5.5**

  - [ ]* 5.6 Write property test for service integration on completion
    - **Property 10: Service Integration on Completion**
    - **Validates: Requirements 8.1, 8.2, 8.3**

  - [ ]* 5.7 Write property test for category color mapping
    - **Property 13: Category Color Mapping**
    - **Validates: Requirements 8.5**

- [ ] 6. Update sidebar navigation
  - [ ] 6.1 Update sidebar component with new navigation items
    - Update `resources/views/components/layouts/app/sidebar.blade.php`
    - Add "Resumen General" (Dashboard) link
    - Add "Estadísticas" link (placeholder route)
    - Add "Configuración" link (settings route)
    - Highlight active navigation item
    - Update logo to "FocusFlow" branding
    - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [ ] 7. Implement responsive design
  - [ ] 7.1 Add responsive styles to all components
    - Ensure stats cards stack on mobile (< 768px)
    - Ensure stats cards show 3-column grid on desktop
    - Test sidebar collapse on mobile
    - Verify timer display on small screens
    - Verify habits list on mobile
    - _Requirements: 7.1, 7.2, 7.3, 7.4_

- [ ] 8. Add empty states and error handling
  - [ ] 8.1 Implement empty state for no habits
    - Show friendly message when no habits scheduled
    - Add call-to-action to create first habit
    - _Requirements: 5.6_

  - [ ] 8.2 Add error handling for habit completion
    - Catch and log errors during service calls
    - Display user-friendly error notifications
    - Ensure UI state remains consistent on errors
    - _Requirements: 5.5_

- [ ] 9. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 10. Polish and optimize
  - [ ] 10.1 Optimize database queries
    - Add eager loading for habits relationships
    - Cache user stats for 5 minutes
    - Verify no N+1 query issues
    - _Requirements: 8.4_

  - [ ] 10.2 Add loading states
    - Add wire:loading indicators to habit checkboxes
    - Add loading state to timer button
    - Add skeleton loaders for stats cards
    - _Requirements: 1.1_

  - [ ]* 10.3 Write integration tests for full dashboard flow
    - Test dashboard loads with all components
    - Test completing multiple habits updates all stats
    - Test timer functionality end-to-end
    - _Requirements: 1.1, 5.5, 4.3_

- [ ] 11. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
