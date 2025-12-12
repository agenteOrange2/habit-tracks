# Design Document - Reward System Enhancement

## Overview

El sistema de recompensas mejorado proporciona una experiencia completa de gestión de incentivos personalizados para usuarios de la aplicación de seguimiento de hábitos. El sistema permite a los usuarios crear, editar, eliminar y canjear recompensas utilizando puntos ganados a través de la completación de hábitos y sesiones de Pomodoro.

El diseño se basa en la arquitectura existente de Laravel + Livewire, aprovechando los modelos `Reward` y `RewardClaim` ya existentes, y expandiendo la funcionalidad con componentes adicionales para gestión completa, historial detallado y estadísticas.

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        User Interface                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │ Reward Shop  │  │ Reward Forms │  │   History    │      │
│  │  Component   │  │  Components  │  │  Component   │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    Livewire Components                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │ RewardShop   │  │ CreateReward │  │RewardHistory │      │
│  │ EditReward   │  │ RewardCard   │  │ActiveRewards │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                      Service Layer                           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │PointsService │  │ FocusMode    │  │  Statistics  │      │
│  │              │  │   Service    │  │   Service    │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                       Data Layer                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │    Reward    │  │ RewardClaim  │  │  UserStats   │      │
│  │    Model     │  │    Model     │  │    Model     │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
```

### Component Responsibilities

**Livewire Components:**
- `RewardShop`: Gestiona la visualización y filtrado de recompensas disponibles
- `CreateReward`: Maneja la creación de nuevas recompensas
- `EditReward`: Maneja la edición de recompensas existentes
- `RewardCard`: Componente reutilizable para mostrar una recompensa individual
- `RewardHistory`: Muestra el historial de canjes del usuario
- `ActiveRewards`: Muestra recompensas destacadas en el dashboard

**Services:**
- `PointsService`: Gestiona la lógica de puntos (ganar, gastar, validar)
- `FocusModeService`: Gestiona las restricciones de Focus Mode
- `StatisticsService`: Calcula estadísticas agregadas de recompensas

**Models:**
- `Reward`: Representa una recompensa individual
- `RewardClaim`: Representa un canje de recompensa
- `UserStats`: Almacena estadísticas del usuario incluyendo puntos

## Components and Interfaces

### Reward Model

```php
class Reward extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'cost_points',
        'icon',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'category' => RewardCategory::class,
    ];

    // Relationships
    public function user(): BelongsTo;
    public function claims(): HasMany;

    // Business Logic
    public function canBeClaimed(User $user): bool;
    public function getTimesClaimedAttribute(): int;
}
```

### RewardClaim Model

```php
class RewardClaim extends Model
{
    protected $fillable = [
        'reward_id',
        'user_id',
        'points_spent',
        'claimed_at',
        'was_enjoyed',
        'notes',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'was_enjoyed' => 'boolean',
    ];

    // Relationships
    public function reward(): BelongsTo;
    public function user(): BelongsTo;
}
```

### RewardShop Component

```php
class RewardShop extends Component
{
    public string $categoryFilter = 'all';
    public int $availablePoints;
    public bool $focusModeActive = false;

    public function mount(FocusModeService $focusModeService): void;
    public function setCategoryFilter(string $category): void;
    public function claimReward(
        Reward $reward,
        PointsService $pointsService,
        FocusModeService $focusModeService
    ): void;
    public function render();
}
```

### CreateReward Component

```php
class CreateReward extends Component
{
    public string $name = '';
    public string $description = '';
    public string $category = '';
    public int $cost_points = 10;
    public string $icon = '🎁';
    public bool $is_available = true;

    protected function rules(): array;
    public function save(): void;
    public function render();
}
```

### EditReward Component

```php
class EditReward extends Component
{
    public Reward $reward;
    public string $name;
    public string $description;
    public string $category;
    public int $cost_points;
    public string $icon;
    public bool $is_available;

    public function mount(Reward $reward): void;
    protected function rules(): array;
    public function update(): void;
    public function delete(): void;
    public function render();
}
```

### RewardHistory Component

```php
class RewardHistory extends Component
{
    use WithPagination;

    public function toggleEnjoyed(RewardClaim $claim): void;
    public function updateNotes(RewardClaim $claim, string $notes): void;
    public function render();
}
```

### PointsService

```php
class PointsService
{
    public function awardPoints(User $user, int $points, string $reason): bool;
    public function spendPoints(User $user, int $points): bool;
    public function hasEnoughPoints(User $user, int $required): bool;
    public function getAvailablePoints(User $user): int;
    public function getTotalEarned(User $user): int;
    public function getTotalSpent(User $user): int;
}
```

### StatisticsService (New)

```php
class StatisticsService
{
    public function getRewardStatistics(User $user): array;
    public function getTotalPointsSpent(User $user): int;
    public function getTotalRewardsClaimed(User $user): int;
    public function getMostClaimedCategories(User $user, int $limit = 5): Collection;
    public function getMostClaimedRewards(User $user, int $limit = 5): Collection;
    public function getAveragePointsPerClaim(User $user): float;
}
```

## Data Models

### Database Schema

**rewards table** (existing):
```
- id: bigint (PK)
- user_id: bigint (FK)
- name: string(100)
- description: text (nullable)
- category: string(50)
- cost_points: integer
- icon: string(10)
- is_available: boolean (default: true)
- created_at: timestamp
- updated_at: timestamp
```

**reward_claims table** (existing):
```
- id: bigint (PK)
- reward_id: bigint (FK)
- user_id: bigint (FK)
- points_spent: integer
- claimed_at: timestamp
- was_enjoyed: boolean (nullable)
- notes: text (nullable)
- created_at: timestamp
- updated_at: timestamp
```

### Relationships

- `User` hasMany `Reward`
- `User` hasMany `RewardClaim`
- `Reward` belongsTo `User`
- `Reward` hasMany `RewardClaim`
- `RewardClaim` belongsTo `User`
- `RewardClaim` belongsTo `Reward`

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property Reflection

After analyzing all acceptance criteria, I've identified the following redundancies:
- Property 5.4 and 10.2 are identical (both test Focus Mode blocking during claim)
- Properties related to UI messages (1.5, 2.5, 3.1, 3.5, 9.5) provide limited testing value and can be consolidated into integration tests
- Edge cases (1.3, 1.4, 4.5, 6.5, 8.3) will be handled by property test generators rather than separate properties

The following properties represent the unique, high-value correctness guarantees:

### Property 1: Valid reward creation
*For any* valid reward data (name 3-100 chars, description ≤500 chars, cost ≥1, valid category), creating a reward should result in a reward associated with the user that can be retrieved from the database.
**Validates: Requirements 1.2, 9.1, 9.2, 9.3, 9.4**

### Property 2: Reward update preserves identity
*For any* reward and valid update data, updating the reward should change only the specified fields while preserving the reward ID and user association.
**Validates: Requirements 2.2**

### Property 3: Authorization enforcement
*For any* user and reward not owned by that user, attempts to edit or delete the reward should be denied with a 403 error.
**Validates: Requirements 2.3, 3.3**

### Property 4: Availability toggle preserves history
*For any* reward with existing claims, marking it as unavailable should hide it from the shop but all claim records should remain accessible.
**Validates: Requirements 2.4**

### Property 5: Deletion preserves claim history
*For any* reward with existing claims, deleting the reward should remove it from the rewards table but all associated claim records should remain in the database.
**Validates: Requirements 3.4**

### Property 6: Category filtering correctness
*For any* category filter and set of rewards, the filtered results should contain only rewards matching that category, and should contain all rewards of that category.
**Validates: Requirements 4.2**

### Property 7: Focus Mode filtering
*For any* active Focus Mode configuration with blocked categories, the reward shop should not display rewards from blocked categories as claimable.
**Validates: Requirements 4.3, 10.1, 10.4**

### Property 8: Affordability indication
*For any* reward and user, the UI should indicate the reward as claimable if and only if the user's available points are greater than or equal to the reward's cost.
**Validates: Requirements 4.4**

### Property 9: Points deduction invariant
*For any* successful reward claim, the user's available points after the claim should equal their available points before the claim minus the reward's cost.
**Validates: Requirements 5.1**

### Property 10: Claim record creation
*For any* successful reward claim, a RewardClaim record should be created with the correct reward_id, user_id, points_spent, and claimed_at timestamp.
**Validates: Requirements 5.2**

### Property 11: Insufficient points rejection
*For any* reward and user where the user's available points are less than the reward's cost, attempting to claim the reward should fail and the user's points should remain unchanged.
**Validates: Requirements 5.3**

### Property 12: Focus Mode claim blocking
*For any* reward in a category blocked by active Focus Mode, attempting to claim the reward should fail regardless of available points.
**Validates: Requirements 5.4, 10.2**

### Property 13: History chronological ordering
*For any* user's reward claims, the history view should display claims in descending order by claimed_at timestamp (most recent first).
**Validates: Requirements 6.1**

### Property 14: Claim display completeness
*For any* reward claim displayed in history, all required fields (reward name, claimed_at, points_spent, notes) should be present in the rendered output.
**Validates: Requirements 6.2**

### Property 15: Enjoyed status update
*For any* reward claim, toggling the was_enjoyed status should update the database record and the change should persist across page reloads.
**Validates: Requirements 6.3**

### Property 16: Notes persistence
*For any* reward claim and notes text, saving notes should update the claim record and the notes should be retrievable in subsequent queries.
**Validates: Requirements 6.4**

### Property 17: Total points spent calculation
*For any* user, the total points spent statistic should equal the sum of points_spent across all their reward claims.
**Validates: Requirements 7.1**

### Property 18: Total claims count
*For any* user, the total rewards claimed statistic should equal the count of their reward claim records.
**Validates: Requirements 7.2**

### Property 19: Category ranking correctness
*For any* user's claims, the most claimed categories should be ordered by claim count in descending order, with accurate counts for each category.
**Validates: Requirements 7.3**

### Property 20: Reward ranking correctness
*For any* user's claims, the most claimed rewards should be ordered by claim count in descending order, with accurate counts for each reward.
**Validates: Requirements 7.4**

### Property 21: Average calculation correctness
*For any* user with at least one claim, the average points per claim should equal the total points spent divided by the number of claims.
**Validates: Requirements 7.5**

### Property 22: Dashboard reward limit
*For any* user, the dashboard should display at most 3 rewards, regardless of how many rewards are available.
**Validates: Requirements 8.1**

### Property 23: Dashboard affordability prioritization
*For any* user with both affordable and unaffordable rewards, the dashboard should prioritize displaying affordable rewards (those with cost ≤ available points) over unaffordable ones.
**Validates: Requirements 8.2**

### Property 24: Progress indicator accuracy
*For any* reward displayed with a progress indicator, the progress percentage should equal (user's available points / reward cost) * 100, capped at 100%.
**Validates: Requirements 8.5**

### Property 25: Focus Mode round-trip
*For any* user and reward shop state, activating Focus Mode and then deactivating it should restore access to all previously accessible rewards.
**Validates: Requirements 10.3**

### Property 26: Focus Mode creation independence
*For any* user with active Focus Mode, creating a new reward should succeed regardless of the reward's category, but claiming restrictions should still apply.
**Validates: Requirements 10.5**

## Error Handling

### Validation Errors

**Reward Creation/Update:**
- Name: Required, 3-100 characters
- Description: Optional, max 500 characters
- Category: Required, must be valid RewardCategory enum value
- Cost Points: Required, integer, minimum 1
- Icon: Optional, max 10 characters

**Error Messages:**
```php
[
    'name.required' => 'El nombre de la recompensa es obligatorio.',
    'name.min' => 'El nombre debe tener al menos 3 caracteres.',
    'name.max' => 'El nombre no puede exceder 100 caracteres.',
    'description.max' => 'La descripción no puede exceder 500 caracteres.',
    'category.required' => 'Debes seleccionar una categoría.',
    'category.in' => 'La categoría seleccionada no es válida.',
    'cost_points.required' => 'El costo en puntos es obligatorio.',
    'cost_points.integer' => 'El costo debe ser un número entero.',
    'cost_points.min' => 'El costo debe ser al menos 1 punto.',
]
```

### Authorization Errors

- **403 Forbidden**: Usuario intenta editar/eliminar recompensa que no le pertenece
- **404 Not Found**: Recompensa no existe

### Business Logic Errors

- **Insufficient Points**: Usuario intenta canjear sin suficientes puntos
- **Focus Mode Blocked**: Usuario intenta canjear recompensa bloqueada por Focus Mode
- **Reward Unavailable**: Usuario intenta canjear recompensa marcada como no disponible

### Error Response Format

```php
// Flash messages for user feedback
session()->flash('error', 'mensaje de error');
session()->flash('success', 'mensaje de éxito');

// Validation errors (handled by Livewire)
$this->addError('field', 'mensaje de error');
```

## Testing Strategy

### Unit Testing

**Reward Model Tests:**
- Test `canBeClaimed()` method with various point balances
- Test `getTimesClaimedAttribute()` calculation
- Test relationship methods

**Service Tests:**
- Test `PointsService` methods for correct point calculations
- Test `StatisticsService` aggregation methods
- Test `FocusModeService` blocking logic

**Policy Tests:**
- Test `RewardPolicy` authorization rules
- Test ownership verification

### Property-Based Testing

We will use **Pest PHP** with the **pest-plugin-faker** for property-based testing. Each property test will run a minimum of 100 iterations with randomly generated data.

**Property Test Configuration:**
```php
// tests/Pest.php
uses(Tests\TestCase::class)->in('Feature', 'Unit');

// Custom generators for reward testing
function validRewardData(): array {
    return [
        'name' => fake()->words(rand(1, 10), true), // 3-100 chars
        'description' => fake()->optional()->sentence(20), // ≤500 chars
        'category' => fake()->randomElement(RewardCategory::cases())->value,
        'cost_points' => fake()->numberBetween(1, 1000),
        'icon' => fake()->randomElement(['🎁', '🎉', '🎮', '🍕', '🎬']),
        'is_available' => fake()->boolean(80), // 80% true
    ];
}
```

**Property Test Examples:**

```php
// Feature: reward-system-enhancement, Property 1: Valid reward creation
test('property: valid reward creation', function () {
    $user = User::factory()->create();
    
    foreach (range(1, 100) as $i) {
        $data = validRewardData();
        
        $reward = $user->rewards()->create($data);
        
        expect($reward)->toBeInstanceOf(Reward::class)
            ->and($reward->user_id)->toBe($user->id)
            ->and($reward->name)->toBe($data['name'])
            ->and($reward->category)->toBe($data['category']);
    }
});

// Feature: reward-system-enhancement, Property 9: Points deduction invariant
test('property: points deduction invariant', function () {
    foreach (range(1, 100) as $i) {
        $user = User::factory()->create();
        $initialPoints = fake()->numberBetween(100, 1000);
        $user->stats()->create(['available_points' => $initialPoints]);
        
        $cost = fake()->numberBetween(1, $initialPoints);
        $reward = Reward::factory()->for($user)->create(['cost_points' => $cost]);
        
        $pointsService = app(PointsService::class);
        $pointsService->spendPoints($user, $cost);
        
        $reward->claims()->create([
            'user_id' => $user->id,
            'points_spent' => $cost,
            'claimed_at' => now(),
        ]);
        
        $user->refresh();
        expect($user->stats->available_points)->toBe($initialPoints - $cost);
    }
});
```

### Integration Testing

**Component Tests:**
- Test full reward creation flow (form → validation → save → redirect)
- Test reward claiming flow (shop → claim → points deduction → history)
- Test Focus Mode integration (activate → block → deactivate → restore)

**Feature Tests:**
- Test complete user journeys (create reward → earn points → claim → view history)
- Test authorization flows (attempt unauthorized access → verify 403)
- Test pagination and filtering

### Edge Case Testing

Property test generators will handle edge cases:
- Empty reward lists
- Exactly 12 rewards (pagination boundary)
- Zero available points
- Reward cost equals available points
- Very long names/descriptions (boundary testing)
- All categories blocked by Focus Mode

## Implementation Notes

### Existing Code Reuse

The following components already exist and should be enhanced:
- `app/Models/Reward.php` - Add any missing methods
- `app/Models/RewardClaim.php` - Already complete
- `app/Livewire/Rewards/RewardShop.php` - Enhance filtering and UI
- `app/Services/PointsService.php` - Already complete
- `app/Services/FocusModeService.php` - Already complete

### New Components to Create

- `app/Livewire/Rewards/RewardHistory.php` - New component
- `app/Services/StatisticsService.php` - New service
- Views for all components
- Comprehensive test suite

### Technology Stack

- **Framework**: Laravel 11
- **Frontend**: Livewire 3 + Alpine.js
- **UI**: Tailwind CSS + Flux UI components
- **Testing**: Pest PHP
- **Database**: MySQL/PostgreSQL

### Performance Considerations

- Use eager loading for reward claims to avoid N+1 queries
- Cache statistics calculations for users with many claims
- Paginate reward lists and history to limit query size
- Index foreign keys and frequently queried columns

### Security Considerations

- Enforce authorization policies on all reward operations
- Validate all user input before database operations
- Use mass assignment protection ($fillable)
- Sanitize user-generated content (notes, descriptions)
- Rate limit reward claiming to prevent abuse
