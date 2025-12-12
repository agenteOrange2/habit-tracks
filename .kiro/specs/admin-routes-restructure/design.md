# Design Document

## Overview

Este diseño describe la reestructuración del sistema de rutas de Laravel para organizar todas las rutas administrativas bajo el prefijo `/admin`, mejorando la claridad de la estructura de URLs y facilitando el mantenimiento del código.

## Architecture

### Estructura de Rutas

La aplicación tendrá dos áreas principales de rutas:

1. **Rutas Públicas**: Accesibles sin autenticación
   - `/` - Página de bienvenida (para usuarios no autenticados)
   - `/welcome` - Alias de la página de bienvenida
   - Rutas de autenticación (manejadas por Fortify)

2. **Rutas Administrativas**: Requieren autenticación, bajo prefijo `/admin`
   - `/admin/dashboard` - Dashboard principal
   - `/admin/habits/*` - Gestión de hábitos
   - `/admin/settings/*` - Configuraciones de usuario
   - `/admin/pomodoro` - Timer Pomodoro
   - `/admin/rewards/*` - Sistema de recompensas
   - `/admin/journal/*` - Diario personal

### Middleware Strategy

- **Rutas públicas**: Sin middleware de autenticación
- **Rutas administrativas**: Middleware `['auth', 'verified']` aplicado al grupo completo
- **Rutas de configuración sensibles**: Middleware adicional `password.confirm` cuando sea necesario

## Components and Interfaces

### Route Groups

```php
// Grupo principal de administración
Route::prefix('admin')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        // Todas las rutas administrativas
    });
```

### Route Naming Convention

Los nombres de ruta seguirán el patrón: `admin.{resource}.{action}`

Ejemplos:
- `admin.dashboard` → `/admin/dashboard`
- `admin.habits.index` → `/admin/habits`
- `admin.habits.create` → `/admin/habits/create`
- `admin.habits.edit` → `/admin/habits/{habit}/edit`
- `admin.settings.profile` → `/admin/settings/profile`

### Redirects

Se implementarán redirecciones para mantener compatibilidad y mejorar UX:

1. `/` → `/admin/dashboard` (solo para usuarios autenticados)
2. `/dashboard` → `/admin/dashboard`
3. `/habits` → `/admin/habits`
4. `/settings` → `/admin/settings/profile`

## Data Models

No se requieren cambios en los modelos de datos. Esta es una refactorización de rutas únicamente.

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Authenticated route protection
*For any* administrative route under `/admin`, accessing it without authentication should redirect to the login page
**Validates: Requirements 1.3**

### Property 2: Route redirection consistency
*For any* old route pattern (without `/admin` prefix), the system should redirect to the corresponding new route with `/admin` prefix
**Validates: Requirements 2.5**

### Property 3: Public route accessibility
*For any* public route (like `/welcome`), both authenticated and unauthenticated users should be able to access it
**Validates: Requirements 5.2, 5.3**

### Property 4: Route naming consistency
*For any* administrative route, its name should follow the pattern `admin.{resource}.{action}`
**Validates: Requirements 6.5**

## Error Handling

### 404 Errors
- Rutas no existentes bajo `/admin` mostrarán la página 404 estándar de Laravel
- No se requiere manejo especial de errores

### Authentication Errors
- Laravel Fortify maneja automáticamente las redirecciones de autenticación
- Usuarios no autenticados serán redirigidos a `/login`

### Authorization Errors
- Las políticas existentes (HabitPolicy, etc.) continuarán funcionando sin cambios
- Errores 403 se manejarán con la página de error estándar

## Testing Strategy

### Unit Tests

Se crearán tests para verificar:
- Redirecciones correctas desde rutas antiguas a nuevas
- Nombres de ruta correctos
- Middleware aplicado correctamente

### Property-Based Tests

Utilizaremos **Pest PHP** como framework de testing. Aunque Pest no tiene soporte nativo para property-based testing como QuickCheck, implementaremos tests que validen las propiedades usando generación de datos con factories.

Cada test de propiedad:
- Ejecutará al menos 100 iteraciones con datos generados
- Estará etiquetado con el formato: `**Feature: admin-routes-restructure, Property {number}: {property_text}**`
- Validará una única propiedad de corrección

### Integration Tests

Tests de integración verificarán:
- Flujo completo de navegación desde login hasta dashboard
- Acceso a diferentes secciones administrativas
- Comportamiento de redirecciones en diferentes estados de autenticación

## Implementation Notes

### Cambios en Componentes Livewire

Los componentes Livewire no requieren cambios en su código, pero las referencias a rutas en las vistas deben actualizarse:

```php
// Antes
route('dashboard')

// Después
route('admin.dashboard')
```

### Cambios en Navegación

Los componentes de navegación (menús, breadcrumbs) deben actualizarse para usar los nuevos nombres de ruta.

### Backward Compatibility

Las redirecciones aseguran que enlaces antiguos sigan funcionando, pero se recomienda actualizar todos los enlaces internos a la nueva estructura.

## Performance Considerations

- Las redirecciones agregan una solicitud HTTP adicional, pero el impacto es mínimo
- El agrupamiento de rutas con prefijo es eficiente en Laravel
- No se anticipan problemas de rendimiento

## Security Considerations

- El middleware de autenticación se aplica a nivel de grupo, reduciendo el riesgo de olvidar proteger rutas individuales
- La estructura clara hace más fácil auditar qué rutas están protegidas
- No se introducen nuevas vulnerabilidades de seguridad
