# Design Document: User-Owned Data

## Overview

Este diseño transforma las categorías, dificultades y tokens de Google Calendar de datos globales a datos propiedad de cada usuario. Cada usuario tendrá control total sobre sus propios datos sin afectar a otros usuarios.

## Architecture

### Cambios en Base de Datos

Las tablas `categories` y `difficulties` necesitan agregar una columna `user_id` para asociar cada registro con un usuario específico. La tabla `google_calendar_tokens` ya tiene esta estructura.

```
┌─────────────┐     ┌──────────────┐     ┌─────────────────────┐
│   users     │────<│  categories  │     │ google_calendar_    │
│             │     │  + user_id   │     │ tokens              │
│             │────<│              │     │ (ya tiene user_id)  │
│             │     └──────────────┘     └─────────────────────┘
│             │
│             │────<┌──────────────┐
│             │     │ difficulties │
│             │     │  + user_id   │
└─────────────┘     └──────────────┘
```

### Flujo de OAuth Individual

```
Usuario → Clic "Conectar Google" → OAuth Google → Callback → Guardar tokens por usuario
```

## Components and Interfaces

### Models

**Category Model** - Agregar relación con User y scope global
```php
// Relación
public function user(): BelongsTo

// Scope para filtrar por usuario autenticado
public function scopeForUser($query, $userId = null)
```

**Difficulty Model** - Misma estructura que Category
```php
public function user(): BelongsTo
public function scopeForUser($query, $userId = null)
```

**User Model** - Agregar relaciones inversas
```php
public function categories(): HasMany
public function difficulties(): HasMany
```

### Services

**DefaultDataService** - Crear datos por defecto para nuevos usuarios
```php
public function createDefaultCategories(User $user): void
public function createDefaultDifficulties(User $user): void
```

### Policies

**CategoryPolicy** - Autorización para categorías
```php
public function view(User $user, Category $category): bool
public function update(User $user, Category $category): bool
public function delete(User $user, Category $category): bool
```

**DifficultyPolicy** - Autorización para dificultades
```php
public function view(User $user, Difficulty $difficulty): bool
public function update(User $user, Difficulty $difficulty): bool
public function delete(User $user, Difficulty $difficulty): bool
```

## Data Models

### Categories Table (modificada)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key to users (NEW) |
| name | varchar(50) | Category name |
| slug | varchar(50) | URL-friendly name |
| icon | varchar(10) | Emoji icon |
| color | varchar(7) | Hex color |
| order | int | Display order |
| is_active | boolean | Active status |

### Difficulties Table (modificada)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key to users (NEW) |
| name | varchar(50) | Difficulty name |
| slug | varchar(50) | URL-friendly name |
| points | int | Points awarded |
| icon | varchar(10) | Emoji icon |
| order | int | Display order |
| is_active | boolean | Active status |

### Default Categories
```php
[
    ['name' => 'Salud', 'icon' => '💪', 'color' => '#10B981'],
    ['name' => 'Trabajo', 'icon' => '💼', 'color' => '#3B82F6'],
    ['name' => 'Estudio', 'icon' => '📚', 'color' => '#8B5CF6'],
    ['name' => 'Personal', 'icon' => '🌟', 'color' => '#F59E0B'],
]
```

### Default Difficulties
```php
[
    ['name' => 'Fácil', 'points' => 10, 'icon' => '🟢'],
    ['name' => 'Normal', 'points' => 25, 'icon' => '🟡'],
    ['name' => 'Difícil', 'points' => 50, 'icon' => '🟠'],
    ['name' => 'Extremo', 'points' => 100, 'icon' => '🔴'],
]
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: User Data Isolation for Categories
*For any* user, when querying categories, the result set SHALL contain only categories where user_id matches the authenticated user's id.
**Validates: Requirements 1.2, 4.1**

### Property 2: User Data Isolation for Difficulties
*For any* user, when querying difficulties, the result set SHALL contain only difficulties where user_id matches the authenticated user's id.
**Validates: Requirements 2.2, 4.2**

### Property 3: Ownership Verification
*For any* user attempting to modify (update/delete) a category or difficulty, the operation SHALL succeed only if the resource's user_id matches the authenticated user's id.
**Validates: Requirements 1.3, 1.4, 2.3, 2.4, 4.3, 4.4**

### Property 4: Category Creation Association
*For any* category created by a user, the category's user_id SHALL equal the creating user's id.
**Validates: Requirements 1.1**

### Property 5: Difficulty Creation Association
*For any* difficulty created by a user, the difficulty's user_id SHALL equal the creating user's id.
**Validates: Requirements 2.1**

### Property 6: Token User Association
*For any* Google Calendar token stored, the token SHALL be retrievable only by the user whose id matches the token's user_id.
**Validates: Requirements 3.2, 3.3, 3.4**

## Error Handling

| Error | Response |
|-------|----------|
| User tries to access another user's category | 403 Forbidden |
| User tries to access another user's difficulty | 403 Forbidden |
| User tries to access another user's calendar tokens | 403 Forbidden |
| Category not found | 404 Not Found |
| Difficulty not found | 404 Not Found |

## Testing Strategy

### Unit Tests
- Test model relationships (User hasMany Categories/Difficulties)
- Test scopes filter correctly by user_id
- Test default data creation for new users

### Property-Based Tests (usando Pest con Faker)
- Property 1-2: Generate random users and data, verify isolation
- Property 3: Attempt cross-user modifications, verify denial
- Property 4-5: Create resources, verify user_id assignment
- Property 6: Store and retrieve tokens, verify user association

### Integration Tests
- Test full CRUD flow for categories with user isolation
- Test full CRUD flow for difficulties with user isolation
- Test Google OAuth flow stores tokens correctly
