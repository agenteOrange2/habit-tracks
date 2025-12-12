# Requirements Document

## Introduction

Este documento define los requisitos para reestructurar el sistema de rutas de la aplicación, moviendo todas las rutas del área administrativa bajo el prefijo `/admin` y organizando el archivo `routes/web.php` de manera más clara y mantenible.

## Glossary

- **Sistema de Rutas**: El conjunto de definiciones de URL y sus controladores correspondientes en Laravel
- **Área Administrativa**: Todas las rutas que requieren autenticación y están relacionadas con la gestión de hábitos, dashboard, configuraciones, etc.
- **Prefijo de Ruta**: Un segmento de URL que precede a todas las rutas dentro de un grupo (ej: `/admin`)
- **Middleware**: Filtros que se ejecutan antes de procesar una solicitud HTTP
- **Ruta Raíz**: La URL principal de la aplicación (`/`)

## Requirements

### Requirement 1

**User Story:** Como usuario autenticado, quiero acceder al dashboard a través de `/admin/dashboard`, para que la estructura de URLs refleje claramente que estoy en el área administrativa.

#### Acceptance Criteria

1. WHEN un usuario autenticado accede a `/admin/dashboard` THEN el sistema SHALL mostrar el dashboard principal
2. WHEN un usuario autenticado accede a `/` THEN el sistema SHALL redirigir a `/admin/dashboard`
3. WHEN un usuario no autenticado accede a `/admin/dashboard` THEN el sistema SHALL redirigir a la página de login
4. WHEN un usuario accede a `/dashboard` THEN el sistema SHALL redirigir a `/admin/dashboard`

### Requirement 2

**User Story:** Como usuario autenticado, quiero que todas las rutas de gestión de hábitos estén bajo `/admin/habits`, para mantener una estructura de URLs consistente.

#### Acceptance Criteria

1. WHEN un usuario autenticado accede a `/admin/habits` THEN el sistema SHALL mostrar la lista de hábitos
2. WHEN un usuario autenticado accede a `/admin/habits/create` THEN el sistema SHALL mostrar el formulario de creación de hábitos
3. WHEN un usuario autenticado accede a `/admin/habits/{id}/edit` THEN el sistema SHALL mostrar el formulario de edición del hábito especificado
4. WHEN un usuario autenticado accede a `/admin/habits/{id}/stats` THEN el sistema SHALL mostrar las estadísticas del hábito especificado
5. WHEN un usuario accede a las rutas antiguas sin `/admin` THEN el sistema SHALL redirigir a las nuevas rutas con `/admin`

### Requirement 3

**User Story:** Como usuario autenticado, quiero que todas las rutas de configuración estén bajo `/admin/settings`, para acceder fácilmente a las opciones de configuración.

#### Acceptance Criteria

1. WHEN un usuario autenticado accede a `/admin/settings` THEN el sistema SHALL redirigir a `/admin/settings/profile`
2. WHEN un usuario autenticado accede a `/admin/settings/profile` THEN el sistema SHALL mostrar la página de perfil
3. WHEN un usuario autenticado accede a `/admin/settings/password` THEN el sistema SHALL mostrar la página de cambio de contraseña
4. WHEN un usuario autenticado accede a `/admin/settings/appearance` THEN el sistema SHALL mostrar la página de apariencia
5. WHEN un usuario autenticado accede a `/admin/settings/two-factor` THEN el sistema SHALL mostrar la página de autenticación de dos factores

### Requirement 4

**User Story:** Como usuario autenticado, quiero que las rutas de otras funcionalidades (pomodoro, rewards, journal) estén bajo `/admin`, para mantener la consistencia en toda la aplicación.

#### Acceptance Criteria

1. WHEN un usuario autenticado accede a `/admin/pomodoro` THEN el sistema SHALL mostrar la funcionalidad de pomodoro o un mensaje apropiado
2. WHEN un usuario autenticado accede a `/admin/rewards` THEN el sistema SHALL mostrar la funcionalidad de recompensas o un mensaje apropiado
3. WHEN un usuario autenticado accede a `/admin/rewards/create` THEN el sistema SHALL mostrar el formulario de creación de recompensas o un mensaje apropiado
4. WHEN un usuario autenticado accede a `/admin/journal/create` THEN el sistema SHALL mostrar el formulario de creación de entradas de diario o un mensaje apropiado

### Requirement 5

**User Story:** Como usuario no autenticado, quiero acceder a la página de bienvenida en la ruta raíz `/`, para ver información sobre la aplicación antes de iniciar sesión.

#### Acceptance Criteria

1. WHEN un usuario no autenticado accede a `/` THEN el sistema SHALL mostrar la página de bienvenida
2. WHEN un usuario no autenticado accede a `/welcome` THEN el sistema SHALL mostrar la página de bienvenida
3. WHEN un usuario autenticado accede a `/welcome` THEN el sistema SHALL permitir el acceso a la página de bienvenida

### Requirement 6

**User Story:** Como desarrollador, quiero que el archivo `routes/web.php` esté organizado de manera clara y lógica, para facilitar el mantenimiento y la comprensión del sistema de rutas.

#### Acceptance Criteria

1. WHEN se revisa el archivo de rutas THEN el sistema SHALL agrupar todas las rutas administrativas bajo un grupo con prefijo `/admin`
2. WHEN se revisa el archivo de rutas THEN el sistema SHALL aplicar middleware de autenticación a todas las rutas administrativas
3. WHEN se revisa el archivo de rutas THEN el sistema SHALL mantener las rutas públicas separadas de las rutas administrativas
4. WHEN se revisa el archivo de rutas THEN el sistema SHALL incluir comentarios descriptivos para cada sección de rutas
5. WHEN se revisa el archivo de rutas THEN el sistema SHALL mantener nombres de ruta consistentes que reflejen la nueva estructura
