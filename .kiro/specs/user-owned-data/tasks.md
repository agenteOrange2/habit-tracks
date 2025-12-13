# Implementation Plan

- [x] 1. Modify database schema



  - [x] 1.1 Update categories table to add user_id column

    - Add `user_id` foreign key column to categories table
    - Remove unique constraint on `name` and `slug` (now unique per user)
    - Add composite unique constraint on `user_id` + `name`
    - _Requirements: 1.1_

  - [x] 1.2 Update difficulties table to add user_id column

    - Add `user_id` foreign key column to difficulties table
    - Remove unique constraint on `name` and `slug` (now unique per user)
    - Add composite unique constraint on `user_id` + `name`
    - _Requirements: 2.1_

- [x] 2. Update Models



  - [x] 2.1 Update Category model

    - Add `user_id` to fillable
    - Add `user()` BelongsTo relationship
    - Add `scopeForUser()` to filter by authenticated user
    - Add global scope to automatically filter by user
    - _Requirements: 1.2, 4.1_


  - [x] 2.2 Update Difficulty model

    - Add `user_id` to fillable
    - Add `user()` BelongsTo relationship
    - Add `scopeForUser()` to filter by authenticated user


    - Add global scope to automatically filter by user
    - _Requirements: 2.2, 4.2_





  - [x] 2.3 Update User model
    - Add `categories()` HasMany relationship
    - Add `difficulties()` HasMany relationship
    - _Requirements: 1.1, 2.1_

- [x] 3. Create Authorization Policies
  - [x] 3.1 Create CategoryPolicy
    - Implement view, update, delete methods checking user_id ownership
    - Register policy in AuthServiceProvider
    - _Requirements: 1.3, 1.4, 4.4_
  - [x] 3.2 Create DifficultyPolicy
    - Implement view, update, delete methods checking user_id ownership
    - Register policy in AuthServiceProvider
    - _Requirements: 2.3, 2.4, 4.4_

- [x] 4. Create Default Data Service

  - [x] 4.1 Create DefaultDataService class
    - Implement `createDefaultCategories(User $user)` method
    - Implement `createDefaultDifficulties(User $user)` method
    - Define default categories: Salud, Trabajo, Estudio, Personal
    - Define default difficulties: Fácil, Normal, Difícil, Extremo
    - _Requirements: 1.5, 2.5_
  - [x] 4.2 Create RegisteredUserListener

    - Listen to Registered event
    - Call DefaultDataService to create default data for new user
    - _Requirements: 1.5, 2.5_

- [x] 5. Update Controllers/Livewire Components



  - [x] 5.1 Update Category management components

    - Ensure all queries use the forUser scope
    - Add user_id when creating new categories
    - Apply policy authorization on edit/delete
    - _Requirements: 1.1, 1.2, 1.3, 1.4_

  - [x] 5.2 Update Difficulty management components

    - Ensure all queries use the forUser scope
    - Add user_id when creating new difficulties
    - Apply policy authorization on edit/delete
    - _Requirements: 2.1, 2.2, 2.3, 2.4_

  - [x] 5.3 Update Habit components

    - Ensure category/difficulty dropdowns only show user's own data
    - _Requirements: 4.1, 4.2_

- [x] 6. Update Google Calendar Integration



  - [x] 6.1 Update CalendarSettings component

    - Remove requirement for admin .env credentials
    - Allow users to input their own Google OAuth credentials or use app-level OAuth
    - Show connection status per user
    - _Requirements: 3.1, 3.2_

  - [x] 6.2 Update GoogleCalendarService

    - Ensure token retrieval is always scoped to authenticated user
    - Verify user ownership before any token operation
    - _Requirements: 3.3, 3.4, 3.5, 4.3_

- [x] 7. Migrate Existing Data



  - [x] 7.1 Create data migration script

    - Assign existing categories to first admin user or create copies for all users
    - Assign existing difficulties to first admin user or create copies for all users
    - Handle habits that reference old category/difficulty IDs
    - _Requirements: 1.1, 2.1_


- [x] 8. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ]* 9. Property-Based Tests (Optional)
  - [ ]* 9.1 Write property test for user data isolation
    - **Property 1: User Data Isolation for Categories**
    - **Property 2: User Data Isolation for Difficulties**
    - **Validates: Requirements 1.2, 2.2, 4.1, 4.2**
  - [ ]* 9.2 Write property test for ownership verification
    - **Property 3: Ownership Verification**
    - **Validates: Requirements 1.3, 1.4, 2.3, 2.4, 4.3, 4.4**
  - [ ]* 9.3 Write property test for creation association
    - **Property 4: Category Creation Association**
    - **Property 5: Difficulty Creation Association**
    - **Validates: Requirements 1.1, 2.1**


- [x] 10. Final Checkpoint

  - Ensure all tests pass, ask the user if questions arise.
