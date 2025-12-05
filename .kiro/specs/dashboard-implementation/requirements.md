# Requirements Document

## Introduction

Este documento define los requisitos para implementar el dashboard principal de la aplicación de seguimiento de hábitos gamificada. El dashboard será la página principal donde los usuarios verán su progreso, estadísticas y hábitos del día. La implementación se basará en el diseño visual proporcionado en design2.html y utilizará los modelos, servicios y enums ya existentes en la aplicación.

## Glossary

- **Dashboard**: Página principal de la aplicación que muestra el resumen de actividad del usuario
- **Sistema**: La aplicación web de seguimiento de hábitos
- **Usuario**: Persona autenticada que utiliza la aplicación
- **Hábito**: Tarea recurrente que el usuario desea completar
- **Racha (Streak)**: Número de días consecutivos completando hábitos
- **XP (Experience Points)**: Puntos de experiencia que el usuario gana al completar hábitos
- **Nivel**: Clasificación del usuario basada en XP acumulado
- **Pomodoro Timer**: Temporizador de 25 minutos para sesiones de trabajo enfocado
- **Sidebar**: Barra lateral de navegación
- **Stats Card**: Tarjeta que muestra una estadística específica del usuario

## Requirements

### Requirement 1: Visualización del Dashboard

**User Story:** Como usuario autenticado, quiero ver mi dashboard principal al iniciar sesión, para tener una visión general de mi progreso y hábitos del día.

#### Acceptance Criteria

1. WHEN un usuario autenticado accede a la ruta raíz ("/") THEN el Sistema SHALL mostrar el dashboard con todos sus componentes
2. WHEN el dashboard se carga THEN el Sistema SHALL mostrar el nombre del usuario en el saludo personalizado
3. WHEN el dashboard se renderiza THEN el Sistema SHALL utilizar el componente Livewire Volt para la interactividad
4. WHEN el dashboard se muestra THEN el Sistema SHALL aplicar el diseño visual basado en design2.html usando Tailwind CSS y Flux UI
5. WHEN el usuario no está autenticado THEN el Sistema SHALL redirigir a la página de login

### Requirement 2: Sidebar de Navegación

**User Story:** Como usuario, quiero tener una barra lateral de navegación, para poder acceder fácilmente a diferentes secciones de la aplicación.

#### Acceptance Criteria

1. WHEN el dashboard se muestra THEN el Sistema SHALL renderizar un sidebar con el logo "FocusFlow"
2. WHEN el sidebar se renderiza THEN el Sistema SHALL mostrar enlaces de navegación para "Resumen General", "Estadísticas" y "Configuración"
3. WHEN el usuario está en el dashboard THEN el Sistema SHALL resaltar visualmente el enlace "Resumen General" como activo
4. WHEN el sidebar se muestra THEN el Sistema SHALL mostrar el avatar del usuario, nombre y plan en la parte inferior
5. WHEN el usuario hace clic en un enlace de navegación THEN el Sistema SHALL navegar a la ruta correspondiente

### Requirement 3: Estadísticas de Usuario

**User Story:** Como usuario, quiero ver mis estadísticas principales en tarjetas visuales, para entender rápidamente mi progreso.

#### Acceptance Criteria

1. WHEN el dashboard se carga THEN el Sistema SHALL mostrar una tarjeta con el nivel actual del usuario y XP
2. WHEN la tarjeta de nivel se muestra THEN el Sistema SHALL calcular y mostrar el progreso hacia el siguiente nivel con una barra de progreso
3. WHEN el dashboard se carga THEN el Sistema SHALL mostrar una tarjeta con la racha actual de días consecutivos
4. WHEN la tarjeta de racha se muestra THEN el Sistema SHALL mostrar una visualización de los últimos 7 días
5. WHEN el dashboard se carga THEN el Sistema SHALL mostrar una tarjeta con la tasa de completitud de hábitos del día actual
6. WHEN se calcula la tasa de completitud THEN el Sistema SHALL dividir hábitos completados entre hábitos programados para hoy

### Requirement 4: Timer de Pomodoro

**User Story:** Como usuario, quiero tener un temporizador Pomodoro en el header, para poder iniciar sesiones de trabajo enfocado directamente desde el dashboard.

#### Acceptance Criteria

1. WHEN el dashboard se muestra THEN el Sistema SHALL renderizar un timer de Pomodoro en el header con tiempo inicial de 25:00
2. WHEN el usuario hace clic en el botón de play THEN el Sistema SHALL iniciar el countdown del timer
3. WHEN el timer está corriendo THEN el Sistema SHALL actualizar el display cada segundo
4. WHEN el usuario hace clic en el botón de pausa THEN el Sistema SHALL pausar el timer
5. WHEN el timer llega a 00:00 THEN el Sistema SHALL detener el countdown automáticamente

### Requirement 5: Lista de Hábitos del Día

**User Story:** Como usuario, quiero ver una lista de mis hábitos programados para hoy, para saber qué necesito completar.

#### Acceptance Criteria

1. WHEN el dashboard se carga THEN el Sistema SHALL mostrar una lista de hábitos programados para el día actual
2. WHEN un hábito se muestra THEN el Sistema SHALL incluir checkbox, nombre, categoría y puntos XP
3. WHEN el usuario hace clic en el checkbox de un hábito THEN el Sistema SHALL marcar el hábito como completado
4. WHEN un hábito se marca como completado THEN el Sistema SHALL aplicar estilo de tachado al nombre
5. WHEN un hábito se completa THEN el Sistema SHALL invocar los servicios correspondientes para actualizar puntos, rachas y logros
6. WHEN no hay hábitos programados para hoy THEN el Sistema SHALL mostrar un mensaje indicando que no hay hábitos pendientes

### Requirement 6: Saludo Personalizado

**User Story:** Como usuario, quiero ver un saludo personalizado basado en la hora del día, para tener una experiencia más amigable.

#### Acceptance Criteria

1. WHEN la hora actual es entre 00:00 y 11:59 THEN el Sistema SHALL mostrar "Buenos días, [Nombre]"
2. WHEN la hora actual es entre 12:00 y 18:59 THEN el Sistema SHALL mostrar "Buenas tardes, [Nombre]"
3. WHEN la hora actual es entre 19:00 y 23:59 THEN el Sistema SHALL mostrar "Buenas noches, [Nombre]"
4. WHEN el saludo se muestra THEN el Sistema SHALL incluir el nombre del usuario autenticado

### Requirement 7: Responsividad

**User Story:** Como usuario móvil, quiero que el dashboard se adapte a mi pantalla, para poder usarlo cómodamente en cualquier dispositivo.

#### Acceptance Criteria

1. WHEN el dashboard se visualiza en pantallas menores a 768px THEN el Sistema SHALL colapsar el sidebar en un menú hamburguesa
2. WHEN el dashboard se visualiza en móvil THEN el Sistema SHALL apilar las tarjetas de estadísticas verticalmente
3. WHEN el dashboard se visualiza en tablet o desktop THEN el Sistema SHALL mostrar las tarjetas en una grilla de 3 columnas
4. WHEN el usuario interactúa con elementos THEN el Sistema SHALL mantener la funcionalidad en todos los tamaños de pantalla

### Requirement 8: Integración con Servicios Existentes

**User Story:** Como desarrollador, quiero que el dashboard utilice los servicios existentes, para mantener la consistencia y reutilizar la lógica de negocio.

#### Acceptance Criteria

1. WHEN se completa un hábito THEN el Sistema SHALL invocar PointsService para otorgar puntos al usuario
2. WHEN se completa un hábito THEN el Sistema SHALL invocar StreakService para actualizar las rachas
3. WHEN se completa un hábito THEN el Sistema SHALL invocar AchievementService para verificar logros desbloqueados
4. WHEN se carga el dashboard THEN el Sistema SHALL utilizar las relaciones Eloquent definidas en los modelos
5. WHEN se muestran categorías THEN el Sistema SHALL utilizar el enum HabitCategory para colores e iconos
