# Requirements Document

## Introduction

Este documento define los requisitos para el **Habit Tracking Dashboard** (Panel de Seguimiento de Hábitos), una interfaz visual que permite a los usuarios monitorear su progreso diario, visualizar rachas, completar hábitos y ver estadísticas en tiempo real. El dashboard combina elementos gamificados con una interfaz moderna y profesional basada en los diseños design2.html y design6-2.html.

## Glossary

- **Dashboard**: Panel principal que muestra el resumen de actividad del usuario
- **Habit**: Hábito o actividad que el usuario desea realizar regularmente
- **Streak**: Racha o secuencia consecutiva de días completando un hábito
- **Mission**: Misión o tarea diaria asociada a un hábito
- **XP (Experience Points)**: Puntos de experiencia que el usuario gana al completar hábitos
- **Level**: Nivel del usuario basado en XP acumulado
- **Completion Rate**: Tasa de completitud, porcentaje de hábitos completados
- **HabitLog**: Registro de completitud de un hábito en una fecha específica
- **Stats Card**: Tarjeta de estadística que muestra métricas clave
- **Timeline**: Cronología de eventos y actividades del día
- **Calendar Widget**: Componente de calendario que muestra días con actividad

## Requirements

### Requirement 1

**User Story:** Como usuario, quiero ver un dashboard con mis estadísticas principales (nivel, racha, misiones completadas), para tener una visión rápida de mi progreso.

#### Acceptance Criteria

1. WHEN el usuario accede al dashboard THEN el sistema SHALL mostrar tres tarjetas de estadísticas con nivel actual, racha de fuego y misiones completadas del día
2. WHEN se muestra el nivel actual THEN el sistema SHALL incluir una barra de progreso que indique el XP actual y el XP necesario para el siguiente nivel
3. WHEN se muestra la racha de fuego THEN el sistema SHALL incluir un indicador visual de los últimos 5 días de actividad
4. WHEN se muestra las misiones del día THEN el sistema SHALL mostrar el número de misiones completadas sobre el total de misiones programadas
5. WHERE el usuario ha ganado XP recientemente THEN el sistema SHALL mostrar un badge o indicador de cambio porcentual positivo

### Requirement 2

**User Story:** Como usuario, quiero ver una lista de mis hábitos/misiones del día con la capacidad de marcarlos como completados, para gestionar mi progreso diario.

#### Acceptance Criteria

1. WHEN el usuario visualiza la lista de misiones THEN el sistema SHALL mostrar todos los hábitos programados para el día actual
2. WHEN un hábito se muestra en la lista THEN el sistema SHALL incluir el nombre, descripción, categoría visual (emoji/icono), nivel de prioridad y botón de completar
3. WHEN el usuario hace clic en el botón de completar THEN el sistema SHALL marcar el hábito como completado, actualizar las estadísticas y mostrar los puntos ganados
4. WHEN un hábito ya está completado THEN el sistema SHALL mostrar un indicador visual de completitud y permitir desmarcarlo
5. WHEN se completa o desmarca un hábito THEN el sistema SHALL actualizar todas las métricas del dashboard sin recargar la página

### Requirement 3

**User Story:** Como usuario, quiero ver un calendario visual que muestre los días en que he completado hábitos, para visualizar mi consistencia a lo largo del tiempo.

#### Acceptance Criteria

1. WHEN el usuario visualiza el calendario THEN el sistema SHALL mostrar el mes actual con todos los días
2. WHEN un día tiene hábitos completados THEN el sistema SHALL resaltar ese día con un indicador visual distintivo
3. WHEN el día actual se muestra THEN el sistema SHALL destacarlo con un estilo diferente al resto de días
4. WHEN el usuario hace clic en un día del calendario THEN el sistema SHALL mostrar los hábitos completados en esa fecha
5. WHERE el usuario navega entre meses THEN el sistema SHALL actualizar el calendario mostrando los datos del mes seleccionado

### Requirement 4

**User Story:** Como usuario, quiero ver una cronología de mis actividades del día, para revisar qué he completado y qué tengo pendiente.

#### Acceptance Criteria

1. WHEN el usuario visualiza la cronología THEN el sistema SHALL mostrar eventos ordenados cronológicamente
2. WHEN un evento es un hábito completado THEN el sistema SHALL mostrar el nombre del hábito, hora de completitud y puntos ganados
3. WHEN un evento es un hábito pendiente THEN el sistema SHALL mostrar el nombre del hábito y hora programada
4. WHEN se completa un nuevo hábito THEN el sistema SHALL agregar el evento a la cronología automáticamente
5. WHERE hay eventos futuros programados THEN el sistema SHALL mostrarlos con un estilo visual diferente a los eventos pasados

### Requirement 5

**User Story:** Como usuario, quiero ver secciones de logros recientes y recompensas activas, para mantenerme motivado y ver mis beneficios actuales.

#### Acceptance Criteria

1. WHEN el usuario visualiza la sección de logros THEN el sistema SHALL mostrar los últimos 2-3 logros desbloqueados
2. WHEN se muestra un logro THEN el sistema SHALL incluir el nombre, descripción, icono y XP ganado
3. WHEN el usuario visualiza la sección de recompensas THEN el sistema SHALL mostrar las recompensas activas disponibles
4. WHEN se muestra una recompensa THEN el sistema SHALL incluir el nombre, descripción y icono representativo
5. WHERE el usuario desbloquea un nuevo logro THEN el sistema SHALL actualizar la sección de logros en tiempo real

### Requirement 6

**User Story:** Como usuario, quiero filtrar mis hábitos por estado (pendientes/todas), para enfocarme en lo que necesito completar.

#### Acceptance Criteria

1. WHEN el usuario visualiza la lista de misiones THEN el sistema SHALL mostrar botones de filtro para "Pendientes" y "Todas"
2. WHEN el usuario selecciona el filtro "Pendientes" THEN el sistema SHALL mostrar solo los hábitos no completados del día
3. WHEN el usuario selecciona el filtro "Todas" THEN el sistema SHALL mostrar todos los hábitos programados para el día
4. WHEN se aplica un filtro THEN el sistema SHALL actualizar el contador de misiones visible
5. WHERE no hay hábitos que coincidan con el filtro THEN el sistema SHALL mostrar un mensaje indicando que no hay misiones

### Requirement 7

**User Story:** Como usuario, quiero que el dashboard sea responsive y se adapte a diferentes tamaños de pantalla, para poder usarlo en móvil, tablet y desktop.

#### Acceptance Criteria

1. WHEN el usuario accede desde un dispositivo móvil THEN el sistema SHALL reorganizar las tarjetas en una sola columna
2. WHEN el usuario accede desde tablet THEN el sistema SHALL mostrar las tarjetas en una cuadrícula de 2 columnas
3. WHEN el usuario accede desde desktop THEN el sistema SHALL mostrar el layout completo con sidebar lateral
4. WHEN el tamaño de pantalla cambia THEN el sistema SHALL ajustar el layout sin perder datos o estado
5. WHERE elementos no caben en pantalla pequeña THEN el sistema SHALL ocultarlos o colapsarlos apropiadamente

### Requirement 8

**User Story:** Como usuario, quiero recibir feedback visual inmediato cuando completo un hábito, para sentir satisfacción y confirmación de mi acción.

#### Acceptance Criteria

1. WHEN el usuario completa un hábito THEN el sistema SHALL mostrar una notificación con los puntos ganados
2. WHEN se muestra la notificación THEN el sistema SHALL incluir un emoji o icono celebratorio
3. WHEN el hábito se marca como completado THEN el sistema SHALL aplicar una animación de transición al botón
4. WHEN las estadísticas se actualizan THEN el sistema SHALL animar los cambios en los números y barras de progreso
5. WHERE el usuario completa su primer hábito del día THEN el sistema SHALL mostrar un mensaje especial de bono

