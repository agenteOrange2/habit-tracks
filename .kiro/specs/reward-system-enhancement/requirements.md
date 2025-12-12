# Requirements Document

## Introduction

El sistema de recompensas es un componente fundamental de la aplicación de seguimiento de hábitos que permite a los usuarios canjear puntos ganados por completar hábitos y sesiones de Pomodoro. Este sistema motiva a los usuarios a mantener sus hábitos mediante un ciclo de recompensa tangible. La especificación actual busca mejorar y completar el sistema de recompensas existente, agregando funcionalidades clave como gestión completa de recompensas, historial detallado, estadísticas, y una experiencia de usuario mejorada.

## Glossary

- **Reward System**: El sistema completo de recompensas que permite crear, gestionar y canjear recompensas
- **Reward**: Una recompensa individual que puede ser canjeada por puntos
- **Reward Claim**: El registro de cuando un usuario canjea una recompensa
- **Points**: Moneda virtual ganada al completar hábitos y sesiones de Pomodoro
- **Available Points**: Puntos disponibles que el usuario puede gastar en recompensas
- **Reward Shop**: La interfaz donde los usuarios pueden ver y canjear recompensas
- **Reward Category**: Categoría de clasificación de recompensas (entretenimiento, comida, ocio, etc.)
- **Focus Mode**: Modo especial que puede bloquear ciertas categorías de recompensas
- **User Stats**: Estadísticas del usuario que incluyen puntos disponibles y totales

## Requirements

### Requirement 1

**User Story:** Como usuario, quiero crear recompensas personalizadas, para poder definir incentivos que me motiven a completar mis hábitos.

#### Acceptance Criteria

1. WHEN un usuario accede al formulario de creación de recompensas THEN el sistema SHALL mostrar campos para nombre, descripción, categoría, costo en puntos e icono
2. WHEN un usuario envía el formulario con datos válidos THEN el sistema SHALL crear la recompensa y asociarla al usuario
3. WHEN un usuario intenta crear una recompensa con nombre vacío THEN el sistema SHALL rechazar la creación y mostrar un mensaje de error
4. WHEN un usuario intenta crear una recompensa con costo de puntos menor a 1 THEN el sistema SHALL rechazar la creación y mostrar un mensaje de error
5. WHEN una recompensa es creada exitosamente THEN el sistema SHALL redirigir al usuario a la tienda de recompensas con un mensaje de confirmación

### Requirement 2

**User Story:** Como usuario, quiero editar mis recompensas existentes, para poder ajustar los detalles cuando mis preferencias cambien.

#### Acceptance Criteria

1. WHEN un usuario accede a la página de edición de una recompensa propia THEN el sistema SHALL mostrar el formulario prellenado con los datos actuales
2. WHEN un usuario modifica los campos y envía el formulario THEN el sistema SHALL actualizar la recompensa con los nuevos valores
3. WHEN un usuario intenta editar una recompensa que no le pertenece THEN el sistema SHALL denegar el acceso y mostrar un error 403
4. WHEN un usuario marca una recompensa como no disponible THEN el sistema SHALL ocultar la recompensa de la tienda pero mantener el historial de canjes
5. WHEN una recompensa es actualizada exitosamente THEN el sistema SHALL mostrar un mensaje de confirmación

### Requirement 3

**User Story:** Como usuario, quiero eliminar recompensas que ya no me interesan, para mantener mi lista de recompensas organizada y relevante.

#### Acceptance Criteria

1. WHEN un usuario solicita eliminar una recompensa propia THEN el sistema SHALL mostrar una confirmación antes de proceder
2. WHEN un usuario confirma la eliminación THEN el sistema SHALL eliminar la recompensa de la base de datos
3. WHEN un usuario intenta eliminar una recompensa que no le pertenece THEN el sistema SHALL denegar la acción y mostrar un error 403
4. WHEN una recompensa con canjes previos es eliminada THEN el sistema SHALL mantener los registros de canjes históricos
5. WHEN una recompensa es eliminada exitosamente THEN el sistema SHALL mostrar un mensaje de confirmación

### Requirement 4

**User Story:** Como usuario, quiero ver todas mis recompensas disponibles en una tienda organizada, para poder elegir fácilmente qué canjear con mis puntos.

#### Acceptance Criteria

1. WHEN un usuario accede a la tienda de recompensas THEN el sistema SHALL mostrar todas las recompensas disponibles del usuario
2. WHEN un usuario filtra por categoría THEN el sistema SHALL mostrar solo las recompensas de esa categoría
3. WHEN un usuario tiene Focus Mode activo THEN el sistema SHALL ocultar o marcar como bloqueadas las recompensas de categorías bloqueadas
4. WHEN se muestra una recompensa THEN el sistema SHALL indicar visualmente si el usuario tiene suficientes puntos para canjearla
5. WHEN la tienda tiene más de 12 recompensas THEN el sistema SHALL paginar los resultados

### Requirement 5

**User Story:** Como usuario, quiero canjear recompensas con mis puntos ganados, para disfrutar de los incentivos que he definido.

#### Acceptance Criteria

1. WHEN un usuario canjea una recompensa con suficientes puntos THEN el sistema SHALL deducir los puntos del balance del usuario
2. WHEN un usuario canjea una recompensa THEN el sistema SHALL crear un registro de canje con la fecha y puntos gastados
3. WHEN un usuario intenta canjear una recompensa sin suficientes puntos THEN el sistema SHALL rechazar el canje y mostrar un mensaje de error
4. WHEN un usuario intenta canjear una recompensa bloqueada por Focus Mode THEN el sistema SHALL rechazar el canje y mostrar un mensaje explicativo
5. WHEN un canje es exitoso THEN el sistema SHALL actualizar el balance de puntos en tiempo real y mostrar un mensaje de celebración

### Requirement 6

**User Story:** Como usuario, quiero ver el historial completo de mis canjes de recompensas, para revisar qué recompensas he disfrutado y cuándo.

#### Acceptance Criteria

1. WHEN un usuario accede al historial de recompensas THEN el sistema SHALL mostrar todos los canjes ordenados por fecha descendente
2. WHEN se muestra un canje THEN el sistema SHALL incluir el nombre de la recompensa, fecha de canje, puntos gastados y notas opcionales
3. WHEN un usuario marca un canje como disfrutado THEN el sistema SHALL actualizar el registro con esta información
4. WHEN un usuario agrega notas a un canje THEN el sistema SHALL guardar las notas asociadas al registro
5. WHEN el historial tiene más de 20 registros THEN el sistema SHALL paginar los resultados

### Requirement 7

**User Story:** Como usuario, quiero ver estadísticas sobre mis recompensas, para entender mis patrones de gasto de puntos y recompensas favoritas.

#### Acceptance Criteria

1. WHEN un usuario accede a las estadísticas de recompensas THEN el sistema SHALL mostrar el total de puntos gastados
2. WHEN se muestran estadísticas THEN el sistema SHALL incluir el número total de recompensas canjeadas
3. WHEN se muestran estadísticas THEN el sistema SHALL mostrar las categorías de recompensas más canjeadas
4. WHEN se muestran estadísticas THEN el sistema SHALL listar las recompensas individuales más canjeadas
5. WHEN se muestran estadísticas THEN el sistema SHALL calcular el promedio de puntos gastados por canje

### Requirement 8

**User Story:** Como usuario, quiero ver mis recompensas activas en el dashboard, para tener un recordatorio visual de los incentivos disponibles.

#### Acceptance Criteria

1. WHEN un usuario accede al dashboard THEN el sistema SHALL mostrar hasta 3 recompensas disponibles que el usuario puede canjear
2. WHEN se muestran recompensas en el dashboard THEN el sistema SHALL priorizar recompensas que el usuario puede canjear con sus puntos actuales
3. WHEN un usuario no tiene recompensas disponibles THEN el sistema SHALL mostrar un mensaje invitando a crear recompensas
4. WHEN un usuario canjea una recompensa desde el dashboard THEN el sistema SHALL actualizar la vista en tiempo real
5. WHEN se muestra una recompensa THEN el sistema SHALL incluir un indicador visual del progreso hacia poder canjearla

### Requirement 9

**User Story:** Como usuario, quiero que el sistema valide mis datos al crear o editar recompensas, para evitar errores y mantener la integridad de los datos.

#### Acceptance Criteria

1. WHEN un usuario envía un formulario de recompensa THEN el sistema SHALL validar que el nombre tenga entre 3 y 100 caracteres
2. WHEN un usuario envía un formulario de recompensa THEN el sistema SHALL validar que la descripción no exceda 500 caracteres
3. WHEN un usuario envía un formulario de recompensa THEN el sistema SHALL validar que el costo de puntos sea un número entero positivo
4. WHEN un usuario envía un formulario de recompensa THEN el sistema SHALL validar que la categoría sea una de las categorías válidas del sistema
5. WHEN la validación falla THEN el sistema SHALL mostrar mensajes de error específicos para cada campo inválido

### Requirement 10

**User Story:** Como usuario, quiero que el sistema de recompensas se integre con Focus Mode, para que mis recompensas distractoras estén bloqueadas cuando necesito concentrarme.

#### Acceptance Criteria

1. WHEN un usuario activa Focus Mode con categorías bloqueadas THEN el sistema SHALL ocultar o deshabilitar recompensas de esas categorías en la tienda
2. WHEN un usuario intenta canjear una recompensa bloqueada THEN el sistema SHALL rechazar el canje con un mensaje explicativo
3. WHEN un usuario desactiva Focus Mode THEN el sistema SHALL restaurar el acceso a todas las recompensas
4. WHEN se muestra una recompensa bloqueada THEN el sistema SHALL indicar visualmente que está bloqueada por Focus Mode
5. WHEN un usuario crea una recompensa durante Focus Mode THEN el sistema SHALL permitir la creación pero aplicar las restricciones de canje
