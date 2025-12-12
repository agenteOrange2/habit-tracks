# Requirements Document

## Introduction

Este documento define los requisitos para implementar un sistema de gestión dinámica de categorías y dificultades de hábitos. Actualmente, las categorías y dificultades están definidas como enums estáticos en el código. Este sistema permitirá a los administradores crear, editar y eliminar categorías y niveles de dificultad de forma dinámica a través de una interfaz de administración.

## Glossary

- **Category**: Una clasificación temática para agrupar hábitos (ej: Productividad, Salud, Aprendizaje)
- **Difficulty**: Un nivel de complejidad asignado a un hábito que determina los puntos otorgados al completarlo
- **CRUD**: Create, Read, Update, Delete - operaciones básicas de gestión de datos
- **Admin Panel**: Interfaz de administración para gestionar el sistema
- **System**: La aplicación de seguimiento de hábitos
- **User**: Usuario del sistema que crea y completa hábitos
- **Administrator**: Usuario con permisos para gestionar categorías y dificultades

## Requirements

### Requirement 1

**User Story:** Como administrador, quiero gestionar las categorías de hábitos de forma dinámica, para poder personalizar las opciones disponibles según las necesidades de los usuarios.

#### Acceptance Criteria

1. WHEN un administrador accede a la sección de categorías THEN el sistema SHALL mostrar una lista de todas las categorías existentes con su nombre, icono, color y estado
2. WHEN un administrador crea una nueva categoría THEN el sistema SHALL validar que el nombre sea único y guardar la categoría con nombre, icono, color y orden de visualización
3. WHEN un administrador edita una categoría existente THEN el sistema SHALL actualizar los datos y reflejar los cambios en todos los hábitos que usen esa categoría
4. WHEN un administrador intenta eliminar una categoría THEN el sistema SHALL verificar si hay hábitos asociados y prevenir la eliminación si existen hábitos activos
5. WHEN un administrador desactiva una categoría THEN el sistema SHALL ocultar la categoría de las opciones de selección pero mantener los hábitos existentes

### Requirement 2

**User Story:** Como administrador, quiero gestionar los niveles de dificultad de forma dinámica, para poder ajustar el sistema de puntos y recompensas según la estrategia de gamificación.

#### Acceptance Criteria

1. WHEN un administrador accede a la sección de dificultades THEN el sistema SHALL mostrar una lista de todos los niveles con su nombre, puntos asignados, icono y estado
2. WHEN un administrador crea un nuevo nivel de dificultad THEN el sistema SHALL validar que el nombre sea único y guardar el nivel con nombre, puntos, icono y orden
3. WHEN un administrador edita un nivel de dificultad THEN el sistema SHALL actualizar los datos y los hábitos existentes SHALL mantener sus puntos originales
4. WHEN un administrador intenta eliminar un nivel de dificultad THEN el sistema SHALL verificar si hay hábitos asociados y prevenir la eliminación si existen hábitos activos
5. WHEN un administrador desactiva un nivel de dificultad THEN el sistema SHALL ocultar el nivel de las opciones de selección pero mantener los hábitos existentes

### Requirement 3

**User Story:** Como usuario, quiero que las categorías y dificultades se carguen dinámicamente al crear o editar hábitos, para tener acceso a las opciones más actualizadas.

#### Acceptance Criteria

1. WHEN un usuario accede al formulario de crear hábito THEN el sistema SHALL cargar solo las categorías activas ordenadas por su orden de visualización
2. WHEN un usuario accede al formulario de crear hábito THEN el sistema SHALL cargar solo los niveles de dificultad activos ordenados por puntos ascendentes
3. WHEN un usuario selecciona una categoría THEN el sistema SHALL mostrar el icono y color asociado como vista previa
4. WHEN un usuario selecciona un nivel de dificultad THEN el sistema SHALL mostrar los puntos que ganará al completar el hábito
5. WHEN un usuario edita un hábito con una categoría o dificultad desactivada THEN el sistema SHALL mostrar la opción actual pero marcarla como obsoleta

### Requirement 4

**User Story:** Como desarrollador, quiero migrar los datos existentes de enums a tablas de base de datos, para mantener la compatibilidad con los hábitos existentes.

#### Acceptance Criteria

1. WHEN se ejecuta la migración inicial THEN el sistema SHALL crear las tablas categories y difficulties con todos los campos necesarios
2. WHEN se ejecuta el seeder THEN el sistema SHALL poblar las tablas con los valores actuales de los enums
3. WHEN se ejecuta la migración de datos THEN el sistema SHALL actualizar todos los hábitos existentes para usar las nuevas relaciones
4. WHEN se completa la migración THEN el sistema SHALL mantener todos los hábitos funcionando sin pérdida de datos
5. WHEN se verifica la integridad THEN el sistema SHALL confirmar que todos los hábitos tienen categorías y dificultades válidas

### Requirement 5

**User Story:** Como administrador, quiero una interfaz intuitiva estilo Notion para gestionar categorías y dificultades, para tener una experiencia de usuario consistente con el resto de la aplicación.

#### Acceptance Criteria

1. WHEN un administrador accede a la gestión de categorías THEN el sistema SHALL mostrar una vista de tabla con columnas para nombre, icono, color, orden y acciones
2. WHEN un administrador crea o edita una categoría THEN el sistema SHALL mostrar un formulario modal con selector de emoji para el icono y selector de color
3. WHEN un administrador reordena categorías THEN el sistema SHALL permitir arrastrar y soltar para cambiar el orden de visualización
4. WHEN un administrador accede a la gestión de dificultades THEN el sistema SHALL mostrar una vista similar con campos específicos de puntos
5. WHEN un administrador realiza cualquier acción THEN el sistema SHALL mostrar notificaciones de éxito o error en estilo Notion

### Requirement 6

**User Story:** Como sistema, quiero validar la integridad de los datos al gestionar categorías y dificultades, para prevenir inconsistencias y errores.

#### Acceptance Criteria

1. WHEN se crea una categoría THEN el sistema SHALL validar que el nombre tenga entre 3 y 50 caracteres
2. WHEN se crea una dificultad THEN el sistema SHALL validar que los puntos sean un número entero positivo entre 1 y 1000
3. WHEN se actualiza el orden de visualización THEN el sistema SHALL validar que no haya duplicados en los números de orden
4. WHEN se intenta eliminar una categoría con hábitos THEN el sistema SHALL mostrar un mensaje indicando cuántos hábitos están asociados
5. WHEN se guarda un icono THEN el sistema SHALL validar que sea un emoji válido de un solo carácter
