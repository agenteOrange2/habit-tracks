# Implementation Plan

- [x] 1. Setup dashboard route and base structure
  - Create route in `routes/web.php` for dashboard
  - Update sidebar navigation to include new menu items
  - Ensure authentication middleware is applied
  - _Requirements: 1.1, 1.5, 2.2_

- [x] 2. Create dashboard index Livewire component
  - [x] 2.1 Create main dashboard Livewire component
    - Create `app/Livewire/Dashboard/Index.php` class component
    - Create `resources/views/livewire/dashboard/index.blade.php` view
    - Define properties for greeting, completion rate, and today's habits
    - Implement mount method to initialize data and calculate greeting
    - Add computed properties for user level, stats, and streak
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

- [-] 3. Implement stats cards component
  - [x] 3.1 Create stats cards Livewire component
    - Create `app/Livewire/Dashboard/StatsCards.php` class component
    - Create `resources/views/livewire/dashboard/stats-cards.blade.php` view
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

- [x] 4. Implement pomodoro timer component
  - [x] 4.1 Create pomodoro timer Livewire component
    - Create `app/Livewire/Dashboard/PomodoroTimer.php` class component
    - Create `resources/views/livewire/dashboard/pomodoro-timer.blade.php` view
    - Define properties for timer (1500 seconds) and running status
    - Implement toggleTimer method
    - Implement tick method with wire:poll
    - Implement formatTime helper method
    - Style timer matching design2.html
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [ ]* 4.2 Write property test for timer countdown behavior
    - **Property 11: Timer Countdown Behavior**
    - **Validates: Requirements 4.3**

  - [ ]* 4.3 Write property test for timer toggle functionality
    - **Property 12: Timer Toggle Functionality**
    - **Validates: Requirements 4.2, 4.4**

- [x] 5. Implement habits list component
  - [x] 5.1 Create habits list Livewire component
    - Create `app/Livewire/Dashboard/HabitsList.php` class component
    - Create `resources/views/livewire/dashboard/habits-list.blade.php` view
    - Define property for habits array
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

  - [x] 5.4 Implement habit completion functionality
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

- [x] 6. Update sidebar navigation
  - [x] 6.1 Update sidebar component with new navigation items
    - Update `resources/views/components/layouts/app/sidebar.blade.php`
    - Add "Resumen General" (Dashboard) link
    - Add "Estadísticas" link (placeholder route)
    - Add "Configuración" link (settings route)
    - Highlight active navigation item
    - Update logo to "FocusFlow" branding
    - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [x] 7. Implement responsive design
  - [x] 7.1 Add responsive styles to all components
    - Ensure stats cards stack on mobile (< 768px)
    - Ensure stats cards show 3-column grid on desktop
    - Test sidebar collapse on mobile
    - Verify timer display on small screens
    - Verify habits list on mobile
    - _Requirements: 7.1, 7.2, 7.3, 7.4_

- [x] 8. Add empty states and error handling
  - [x] 8.1 Implement empty state for no habits
    - Show friendly message when no habits scheduled
    - Add call-to-action to create first habit
    - _Requirements: 5.6_

  - [x] 8.2 Add error handling for habit completion
    - Catch and log errors during service calls
    - Display user-friendly error notifications
    - Ensure UI state remains consistent on errors
    - _Requirements: 5.5_

- [ ] 9. Implement energy bar component
  - [x] 9.1 Create energy bar Livewire component
    - Verify `app/Livewire/Dashboard/EnergyBar.php` exists and is properly configured
    - Verify `resources/views/livewire/dashboard/energy-bar.blade.php` view exists
    - Ensure component integrates with EnergyService
    - Implement refresh method with energyUpdated listener
    - Display energy percentage with current and maximum values
    - Apply warning color when energy is below 30%
    - Style component using Tailwind CSS
    - _Requirements: 9.1, 9.2, 9.3, 9.5_

  - [ ]* 9.2 Write property test for energy bar display
    - **Property 14: Energy Bar Display**
    - **Validates: Requirements 9.1, 9.2**

  - [ ]* 9.3 Write property test for energy percentage bounds
    - **Property 15: Energy Percentage Bounds**
    - **Validates: Requirements 9.3**

  - [ ]* 9.4 Write property test for energy warning state
    - **Property 16: Energy Warning State**
    - **Validates: Requirements 9.5**

- [x] 10. Implement quick actions component
  - [x] 10.1 Create quick actions Livewire component
    - Verify `app/Livewire/Dashboard/QuickActions.php` exists and is properly configured
    - Verify `resources/views/livewire/dashboard/quick-actions.blade.php` view exists
    - Ensure all four actions are defined: Nuevo Hábito, Pomodoro, Recompensas, Diario
    - Implement navigation to corresponding routes
    - Display icons and colors for each action
    - Apply responsive styles for mobile and desktop
    - Style component using Tailwind CSS
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

  - [ ]* 10.2 Write property test for quick actions navigation
    - **Property 17: Quick Actions Navigation**
    - **Validates: Requirements 10.3**

  - [ ]* 10.3 Write property test for quick actions completeness
    - **Property 18: Quick Actions Completeness**
    - **Validates: Requirements 10.2**

- [ ] 11. Implement streak calendar component
  - [x] 11.1 Create streak calendar Livewire component
    - Verify `app/Livewire/Dashboard/StreakCalendar.php` exists and is properly configured
    - Verify `resources/views/livewire/dashboard/streak-calendar.blade.php` view exists
    - Integrate with StatisticsService to fetch 365 days of data
    - Display heatmap with color intensity based on activity
    - Show neutral color for days without activity
    - Implement tooltip on hover with detailed information
    - Style component using Tailwind CSS
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5_

  - [ ]* 11.2 Write property test for heatmap data range
    - **Property 19: Heatmap Data Range**
    - **Validates: Requirements 11.1**

- [x] 12. Implement weekly progress component
  - [x] 12.1 Create weekly progress Livewire component
    - Verify `app/Livewire/Dashboard/WeeklyProgress.php` exists and is properly configured
    - Verify `resources/views/livewire/dashboard/weekly-progress.blade.php` view exists
    - Load progress for 7 days starting from beginning of week
    - Display day name, date, and completion percentage for each day
    - Calculate percentage as (completed / scheduled) * 100
    - Highlight current day visually
    - Apply success style for 100% completion
    - Apply neutral style for 0% completion
    - Style component using Tailwind CSS
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5, 12.6_

  - [ ]* 12.2 Write property test for weekly progress day count
    - **Property 20: Weekly Progress Day Count**
    - **Validates: Requirements 12.1**

  - [ ]* 12.3 Write property test for weekly progress calculation
    - **Property 21: Weekly Progress Calculation**
    - **Validates: Requirements 12.3**

  - [ ]* 12.4 Write property test for today highlight
    - **Property 22: Today Highlight in Weekly Progress**
    - **Validates: Requirements 12.4**

- [x] 13. Integrate new components into dashboard
  - [x] 13.1 Update dashboard overview to include all components
    - Add energy bar component to dashboard layout
    - Add quick actions component to dashboard layout
    - Add streak calendar component to dashboard layout
    - Add weekly progress component to dashboard layout
    - Ensure proper grid layout and spacing
    - Verify responsive behavior on all screen sizes
    - _Requirements: 1.1, 1.4, 7.1, 7.2, 7.3, 7.4_

- [ ] 14. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 15. Polish and optimize
  - [x] 15.1 Optimize database queries
    - Add eager loading for habits relationships
    - Cache user stats for 5 minutes
    - Verify no N+1 query issues
    - _Requirements: 8.4_

  - [x] 15.2 Add loading states
    - Add wire:loading indicators to habit checkboxes
    - Add loading state to timer button
    - Add skeleton loaders for stats cards
    - _Requirements: 1.1_

  - [ ]* 15.3 Write integration tests for full dashboard flow
    - Test dashboard loads with all components
    - Test completing multiple habits updates all stats
    - Test timer functionality end-to-end
    - _Requirements: 1.1, 5.5, 4.3_

- [ ] 16. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
