# Design Document

## Overview

Este diseño implementa un sistema de gestión dinámica para categorías y niveles de dificultad de hábitos, reemplazando los enums estáticos actuales con modelos de base de datos gestionables a través de una interfaz de administración. El sistema mantiene compatibilidad con los hábitos existentes mediante una migración de datos y proporciona una interfaz estilo Notion consistente con el diseño actual de la aplicación.

## Architecture

### Database Schema

```
categories
- id: bigint (PK)
- name: string(50) unique
- slug: string(50) unique
- icon: string(10)
- color: string(7)
- order: integer default 0
- is_active: boolean default true
- created_at: timestamp
- updated_at: timestamp

difficulties
- id: bigint (PK)
- name: string(50) unique
- slug: string(50) unique
- points: integer
- icon: string(10)
- order: integer default 0
- is_active: boolean default true
- created_at: timestamp
- updated_at: timestamp

habits (modificaciones)
- category_id: bigint (FK to categories) nullable
- difficulty_id: bigint (FK to difficulties) nullable
- category: string (deprecated, mantener temporalmente)
- difficulty: string (deprecated, mantener temporalmente)
```

### Component Structure

```
app/
├── Models/
│   ├── Category.php
│   └── Difficulty.php
├── Livewire/
│   └── Admin/
│       ├── Categories/
│       │   ├── CategoryList.php
│       │   ├── CategoryForm.php
│       │   └── CategoryReorder.php
│       └── Difficulties/
│           ├── DifficultyList.php
│           └── DifficultyForm.php
├── Services/
│   └── HabitSettingsMigrationService.php
└── Policies/
    ├── CategoryPolicy.php
    └── DifficultyPolicy.php

resources/views/livewire/admin/
├── categories/
│   ├── category-list.blade.php
│   └── category-form.blade.php
└── difficulties/
    ├── difficulty-list.blade.php
    └── difficulty-form.blade.php
```

## Components and Interfaces

### Models

#### Category Model
```php
class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'color', 'order', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'order' => 'integer'];
    
    // Relationships
    public function habits(): HasMany
    public function activeHabits(): HasMany
    
    // Scopes
    public function scopeActive(Builder $query): Builder
    public function scopeOrdered(Builder $query): Builder
    
    // Methods
    public function canBeDeleted(): bool
    public function getHabitsCount(): int
}
```

#### Difficulty Model
```php
class Difficulty extends Model
{
    protected $fillable = ['name', 'slug', 'points', 'icon', 'order', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'order' => 'integer', 'points' => 'integer'];
    
    // Relationships
    public function habits(): HasMany
    public function activeHabits(): HasMany
    
    // Scopes
    public function scopeActive(Builder $query): Builder
    public function scopeOrdered(Builder $query): Builder
    
    // Methods
    public function canBeDeleted(): bool
    public function getHabitsCount(): int
}
```

### Livewire Components

#### CategoryList Component
- Displays all categories in a Notion-style table
- Supports inline editing of order
- Shows active/inactive status
- Provides quick actions (edit, delete, toggle active)
- Displays habits count per category

#### CategoryForm Component
- Modal form for create/edit
- Fields: name, icon (emoji picker), color (color picker), order
- Real-time validation
- Preview of how category will look

#### DifficultyList Component
- Displays all difficulties in a Notion-style table
- Shows points, icon, and order
- Provides quick actions
- Displays habits count per difficulty

#### DifficultyForm Component
- Modal form for create/edit
- Fields: name, points, icon (emoji picker), order
- Real-time validation
- Preview of points calculation

## Data Models

### Category Data Structure
```json
{
  "id": 1,
  "name": "Productividad",
  "slug": "productivity",
  "icon": "💼",
  "color": "#3B82F6",
  "order": 1,
  "is_active": true,
  "habits_count": 5,
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z"
}
```

### Difficulty Data Structure
```json
{
  "id": 1,
  "name": "Fácil",
  "slug": "easy",
  "points": 10,
  "icon": "⭐",
  "order": 1,
  "is_active": true,
  "habits_count": 8,
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z"
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Category name uniqueness
*For any* two categories in the system, their names must be unique (case-insensitive)
**Validates: Requirements 1.2**

### Property 2: Difficulty name uniqueness
*For any* two difficulties in the system, their names must be unique (case-insensitive)
**Validates: Requirements 2.2**

### Property 3: Category deletion protection
*For any* category with active habits, deletion attempts must be rejected
**Validates: Requirements 1.4**

### Property 4: Difficulty deletion protection
*For any* difficulty with active habits, deletion attempts must be rejected
**Validates: Requirements 2.4**

### Property 5: Active items only in forms
*For any* habit creation or edit form, only active categories and difficulties should be available for selection
**Validates: Requirements 3.1, 3.2**

### Property 6: Order uniqueness within type
*For any* set of categories, no two categories should have the same order value
**Validates: Requirements 6.3**

### Property 7: Points validation range
*For any* difficulty, the points value must be between 1 and 1000 inclusive
**Validates: Requirements 6.2**

### Property 8: Migration data preservation
*For any* habit before migration, after migration it must have equivalent category and difficulty relationships
**Validates: Requirements 4.4**

### Property 9: Icon emoji validation
*For any* category or difficulty icon, it must be a valid single emoji character
**Validates: Requirements 6.5**

### Property 10: Inactive item visibility in edit
*For any* habit with an inactive category or difficulty, when editing the habit, the inactive option must be visible but marked as deprecated
**Validates: Requirements 3.5**

## Error Handling

### Validation Errors
- **Duplicate Name**: "Ya existe una categoría/dificultad con este nombre"
- **Invalid Points**: "Los puntos deben estar entre 1 y 1000"
- **Invalid Icon**: "El icono debe ser un emoji válido"
- **Empty Name**: "El nombre es requerido y debe tener al menos 3 caracteres"

### Business Logic Errors
- **Cannot Delete**: "No se puede eliminar porque hay X hábitos asociados"
- **Cannot Deactivate**: "No se puede desactivar porque hay hábitos activos usando esta opción"
- **Migration Failed**: "Error en la migración de datos. Revirtiendo cambios..."

### Database Errors
- **Foreign Key Constraint**: "Error de integridad referencial. Contacte al administrador"
- **Connection Error**: "Error de conexión a la base de datos"

## Testing Strategy

### Unit Tests
- Model validation rules
- Scope queries (active, ordered)
- Relationship definitions
- canBeDeleted() method logic
- Slug generation from name

### Property-Based Tests
- Property 1: Test category name uniqueness with random category names
- Property 2: Test difficulty name uniqueness with random difficulty names
- Property 3: Test deletion protection with various habit counts
- Property 4: Test difficulty deletion protection with various habit counts
- Property 5: Test form loading with mixed active/inactive items
- Property 6: Test order uniqueness after reordering operations
- Property 7: Test points validation with random values
- Property 8: Test migration with random habit data
- Property 9: Test icon validation with various emoji and non-emoji strings
- Property 10: Test inactive item visibility in edit forms

### Integration Tests
- Complete CRUD flow for categories
- Complete CRUD flow for difficulties
- Habit creation with dynamic categories/difficulties
- Migration process end-to-end
- Reordering functionality

### UI Tests
- Modal form interactions
- Emoji picker functionality
- Color picker functionality
- Drag-and-drop reordering
- Notification displays

## Migration Strategy

### Phase 1: Database Setup
1. Create categories and difficulties tables
2. Add foreign keys to habits table (nullable)
3. Keep old enum columns temporarily

### Phase 2: Data Seeding
1. Seed categories from HabitCategory enum
2. Seed difficulties from HabitDifficulty enum
3. Verify all data inserted correctly

### Phase 3: Data Migration
1. Update all habits to link to new category_id
2. Update all habits to link to new difficulty_id
3. Verify all habits have valid relationships

### Phase 4: Code Updates
1. Update Habit model to use relationships
2. Update forms to load from database
3. Update validation rules
4. Deploy and test

### Phase 5: Cleanup (Future)
1. Remove old enum columns (after verification period)
2. Remove enum files (keep for reference)
3. Update documentation

## UI Design Guidelines

### Notion-Style Table
- Clean borders with #E9E9E7
- Hover state with #FBFBFA background
- Inline editing for order field
- Icon and color preview in cells
- Action buttons appear on hover

### Modal Forms
- White background with subtle shadow
- Emoji picker with search functionality
- Color picker with preset palette
- Real-time preview of selection
- Clear save/cancel actions

### Notifications
- Success: Green background with checkmark
- Error: Red background with warning icon
- Info: Blue background with info icon
- Auto-dismiss after 3 seconds

## Performance Considerations

- Cache active categories and difficulties
- Eager load relationships when displaying habits
- Index on is_active and order columns
- Batch updates for reordering
- Lazy load habits count only when needed
