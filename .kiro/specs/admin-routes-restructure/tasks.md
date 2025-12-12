# Implementation Plan

- [x] 1. Backup and prepare routes file





  - Create a backup of the current `routes/web.php` file
  - Review all current route definitions and their dependencies
  - _Requirements: 6.1, 6.2, 6.3_

- [x] 2. Create separate admin routes file and configure bootstrap

  - [x] 2.1 Create routes/admin.php file
    - Create new `routes/admin.php` file for all administrative routes
    - Add descriptive comments explaining the file purpose
    - _Requirements: 6.1, 6.3, 6.4_
  
  - [x] 2.2 Configure bootstrap/app.php to load admin routes
    - Register `routes/admin.php` in `bootstrap/app.php` using `then` callback
    - Apply `prefix('admin')` to all routes in the file
    - Apply `middleware(['web', 'auth', 'verified'])` to all routes
    - _Requirements: 6.1, 6.2_
  
  - [x] 2.3 Clean up routes/web.php
    - Keep only public routes in `routes/web.php`
    - Implement welcome page route for unauthenticated users at `/`
    - Implement `/welcome` route
    - Add conditional redirect from `/` to `/admin/dashboard` for authenticated users
    - Add comment referencing that admin routes are in `routes/admin.php`
    - _Requirements: 5.1, 5.2, 5.3, 6.3_

- [x] 3. Implement all admin routes in routes/admin.php

  - [x] 3.1 Implement dashboard routes
    - Add dashboard route at `/dashboard` (becomes `/admin/dashboard` with prefix)
    - Use route name `admin.dashboard`
    - _Requirements: 1.1, 1.2_
  
  - [x] 3.2 Implement habits routes
    - Add all habits routes at `/habits/*` (becomes `/admin/habits/*` with prefix)
    - Use route names: `admin.habits.index`, `admin.habits.create`, `admin.habits.edit`, `admin.habits.stats`
    - _Requirements: 2.1, 2.2, 2.3, 2.4_
  
  - [x] 3.3 Implement settings routes
    - Add all settings routes at `/settings/*` (becomes `/admin/settings/*` with prefix)
    - Maintain existing middleware for two-factor authentication
    - Use route names: `admin.settings.profile`, `admin.settings.password`, `admin.settings.appearance`, `admin.settings.two-factor`
    - Add redirect from `/settings` to `/admin/settings/profile`
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_
  
  - [x] 3.4 Implement other admin routes (pomodoro, rewards, journal)
    - Add pomodoro, rewards, and journal routes at `/pomodoro`, `/rewards/*`, `/journal/*`
    - Use route names with `admin.*` pattern
    - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [x] 4. Add backward compatibility redirects





  - [x] 4.1 Implement redirects for old route patterns in routes/web.php



    - Add redirect from `/dashboard` to `/admin/dashboard`
    - Add redirect from `/habits` to `/admin/habits`
    - Add redirect from `/habits/*` to `/admin/habits/*` (if needed)
    - Add redirect from `/settings` to `/admin/settings/profile`
    - _Requirements: 1.4, 2.5_

- [x] 5. Update route references in Livewire components





  - [x] 5.1 Update dashboard component route references


    - Search and replace `route('dashboard')` with `route('admin.dashboard')`
    - Update any hardcoded URLs to use route helpers
    - _Requirements: 1.1_
  
  - [x] 5.2 Update habits component route references


    - Update all route references in habit-related Livewire components
    - Replace `route('habits.*')` with `route('admin.habits.*')`
    - _Requirements: 2.1, 2.2, 2.3, 2.4_
  
  - [x] 5.3 Update settings component route references


    - Update all route references in settings-related Livewire components
    - Replace `route('profile.edit')` with `route('admin.settings.profile')`
    - Replace other settings route names accordingly
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_
  
  - [x] 5.4 Update navigation and menu components


    - Update sidebar/navigation menu route references
    - Update breadcrumb components if present
    - _Requirements: 6.5_

- [x] 6. Update route references in Blade views





  - [x] 6.1 Update dashboard view route references


    - Search for route references in dashboard blade files
    - Update to use new admin route names
    - _Requirements: 1.1_
  


  - [ ] 6.2 Update habits view route references
    - Search for route references in habits blade files
    - Update to use new admin.habits route names


    - _Requirements: 2.1, 2.2, 2.3, 2.4_
  
  - [x] 6.3 Update settings view route references


    - Search for route references in settings blade files
    - Update to use new admin.settings route names
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_
  
  - [ ] 6.4 Update layout and navigation view route references
    - Update main layout file route references
    - Update any shared navigation components
    - _Requirements: 6.5_

- [ ]* 7. Write tests for route structure
  - [ ]* 7.1 Write unit tests for route definitions
    - Test that admin routes exist with correct names
    - Test that middleware is applied correctly
    - Test that route parameters are defined correctly
    - Test that routes/admin.php is properly loaded
    - _Requirements: 6.1, 6.2_
  
  - [ ]* 7.2 Write property test for authenticated route protection
    - **Property 1: Authenticated route protection**
    - **Validates: Requirements 1.3**
    - Generate random admin routes and verify unauthenticated access redirects to login
  
  - [ ]* 7.3 Write property test for route redirection consistency
    - **Property 2: Route redirection consistency**
    - **Validates: Requirements 2.5**
    - Generate old route patterns and verify they redirect to new admin routes
  
  - [ ]* 7.4 Write property test for public route accessibility
    - **Property 3: Public route accessibility**
    - **Validates: Requirements 5.2, 5.3**
    - Verify public routes are accessible to both authenticated and unauthenticated users
  
  - [ ]* 7.5 Write property test for route naming consistency
    - **Property 4: Route naming consistency**
    - **Validates: Requirements 6.5**
    - Generate admin routes and verify names follow the pattern `admin.{resource}.{action}`

- [ ] 8. Manual testing and verification
  - Test login flow and redirect to admin dashboard
  - Test navigation through all admin sections
  - Test backward compatibility redirects
  - Verify all links work correctly
  - Verify routes/admin.php is properly loaded
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 3.5, 4.1, 4.2, 4.3, 4.4_

- [ ] 9. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
