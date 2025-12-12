# Implementation Plan - Reward System Enhancement

- [x] 1. Create StatisticsService for reward analytics
  - Implement service class with methods for calculating reward statistics
  - Add methods: getTotalPointsSpent, getTotalRewardsClaimed, getMostClaimedCategories, getMostClaimedRewards, getAveragePointsPerClaim
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [ ]* 1.1 Write property test for statistics calculations
  - **Property 17: Total points spent calculation**
  - **Property 18: Total claims count**
  - **Property 21: Average calculation correctness**
  - **Validates: Requirements 7.1, 7.2, 7.5**

- [ ]* 1.2 Write property test for ranking statistics
  - **Property 19: Category ranking correctness**
  - **Property 20: Reward ranking correctness**
  - **Validates: Requirements 7.3, 7.4**

- [x] 2. Enhance CreateReward component with full validation
  - Add comprehensive validation rules for all fields
  - Implement form submission with error handling
  - Add success/error flash messages
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 9.1, 9.2, 9.3, 9.4, 9.5_

- [ ]* 2.1 Write property test for valid reward creation
  - **Property 1: Valid reward creation**
  - **Validates: Requirements 1.2, 9.1, 9.2, 9.3, 9.4**

- [x] 3. Enhance EditReward component with update and delete functionality
  - Implement authorization check using RewardPolicy
  - Add update method with validation
  - Add delete method with confirmation
  - Handle availability toggle
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 3.5_

- [ ]* 3.1 Write property test for reward updates
  - **Property 2: Reward update preserves identity**
  - **Validates: Requirements 2.2**

- [ ]* 3.2 Write property test for authorization enforcement
  - **Property 3: Authorization enforcement**
  - **Validates: Requirements 2.3, 3.3**

- [ ]* 3.3 Write property test for availability and deletion history preservation
  - **Property 4: Availability toggle preserves history**
  - **Property 5: Deletion preserves claim history**
  - **Validates: Requirements 2.4, 3.4**

- [x] 4. Create RewardPolicy for authorization
  - Implement viewAny, view, create, update, delete methods
  - Ensure users can only manage their own rewards
  - Register policy in AuthServiceProvider
  - _Requirements: 2.3, 3.3_

- [x] 5. Enhance RewardShop component with improved filtering and Focus Mode integration
  - Improve category filtering logic
  - Add visual indicators for affordability
  - Enhance Focus Mode blocking display
  - Add pagination support
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [ ]* 5.1 Write property test for category filtering
  - **Property 6: Category filtering correctness**
  - **Validates: Requirements 4.2**

- [ ]* 5.2 Write property test for Focus Mode filtering
  - **Property 7: Focus Mode filtering**
  - **Validates: Requirements 4.3, 10.1, 10.4**

- [ ]* 5.3 Write property test for affordability indication
  - **Property 8: Affordability indication**
  - **Validates: Requirements 4.4**

- [x] 6. Enhance reward claiming logic with comprehensive validation
  - Validate sufficient points before claiming
  - Check Focus Mode restrictions
  - Implement points deduction transaction
  - Create claim record with proper data
  - Add real-time UI updates
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

- [ ]* 6.1 Write property test for points deduction invariant
  - **Property 9: Points deduction invariant**
  - **Validates: Requirements 5.1**

- [ ]* 6.2 Write property test for claim record creation
  - **Property 10: Claim record creation**
  - **Validates: Requirements 5.2**

- [ ]* 6.3 Write property test for insufficient points rejection
  - **Property 11: Insufficient points rejection**
  - **Validates: Requirements 5.3**

- [ ]* 6.4 Write property test for Focus Mode claim blocking
  - **Property 12: Focus Mode claim blocking**
  - **Validates: Requirements 5.4, 10.2**

- [x] 7. Create RewardHistory component
  - Display all user claims with pagination
  - Show reward name, date, points spent, and notes
  - Implement chronological ordering (most recent first)
  - Add toggle for "was_enjoyed" status
  - Add inline notes editing
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5_

- [ ]* 7.1 Write property test for history ordering
  - **Property 13: History chronological ordering**
  - **Validates: Requirements 6.1**

- [ ]* 7.2 Write property test for claim display completeness
  - **Property 14: Claim display completeness**
  - **Validates: Requirements 6.2**

- [ ]* 7.3 Write property test for enjoyed status and notes persistence
  - **Property 15: Enjoyed status update**
  - **Property 16: Notes persistence**
  - **Validates: Requirements 6.3, 6.4**

- [x] 8. Create reward statistics view component
  - Display total points spent
  - Display total rewards claimed
  - Show most claimed categories chart
  - Show most claimed rewards list
  - Display average points per claim
  - Integrate with StatisticsService
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [x] 9. Enhance ActiveRewards dashboard component
  - Limit display to maximum 3 rewards
  - Prioritize affordable rewards
  - Add progress indicators for each reward
  - Handle empty state with call-to-action
  - Add real-time updates on claim
  - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

- [ ]* 9.1 Write property test for dashboard reward limit
  - **Property 22: Dashboard reward limit**
  - **Validates: Requirements 8.1**

- [ ]* 9.2 Write property test for affordability prioritization
  - **Property 23: Dashboard affordability prioritization**
  - **Validates: Requirements 8.2**

- [ ]* 9.3 Write property test for progress indicator accuracy
  - **Property 24: Progress indicator accuracy**
  - **Validates: Requirements 8.5**

- [x] 10. Implement Focus Mode integration tests
  - Test reward blocking when Focus Mode is active
  - Test reward restoration when Focus Mode is deactivated
  - Test that creation is allowed during Focus Mode
  - Verify visual indicators for blocked rewards
  - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

- [ ]* 10.1 Write property test for Focus Mode round-trip
  - **Property 25: Focus Mode round-trip**
  - **Validates: Requirements 10.3**

- [ ]* 10.2 Write property test for Focus Mode creation independence
  - **Property 26: Focus Mode creation independence**
  - **Validates: Requirements 10.5**

- [x] 11. Create Blade views for all components
  - Create/update view for RewardShop with filtering UI
  - Create view for CreateReward form
  - Create view for EditReward form
  - Create view for RewardHistory with pagination
  - Create view for reward statistics dashboard
  - Update ActiveRewards view with progress indicators
  - Create RewardCard partial for reusable display
  - _Requirements: All UI-related requirements_

- [x] 12. Add routes for reward management
  - Add route for reward shop (rewards.index)
  - Add route for create reward (rewards.create)
  - Add route for edit reward (rewards.edit)
  - Add route for reward history (rewards.history)
  - Add route for reward statistics (rewards.stats)
  - Ensure all routes use auth middleware
  - _Requirements: All requirements_

- [x] 13. Create database seeders for testing
  - Create RewardSeeder with sample rewards
  - Create RewardClaimSeeder with sample claims
  - Add rewards and claims to DatabaseSeeder
  - _Requirements: All requirements_

- [ ]* 14. Write integration tests for complete user flows
  - Test complete reward creation flow
  - Test complete reward claiming flow
  - Test complete reward editing flow
  - Test complete reward deletion flow
  - Test Focus Mode integration flow
  - _Requirements: All requirements_

- [x] 15. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
