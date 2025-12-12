# Diseño: Vistas de Gestión de Hábitos

## Introducción

Este documento describe el diseño técnico para completar y mejorar las vistas de gestión de hábitos en HabitHero. El sistema ya cuenta con componentes Livewire funcionales y una vista de lista implementada. Este diseño se enfoca en completar las vistas faltantes (crear y editar) y agregar funcionalidad de eliminación con confirmación.

## Glosario Técnico

- **Livewire Component**: Clase PHP que maneja la lógica del lado del servidor
- **Blade Template**: Vista que renderiza el HTML
- **Modal**: Componente de UI que se superpone al contenido principal
- **Alpine.js**: Framework JavaScript para interactividad del lado del cliente
- **Wire Model**: Directiva de Livewire para binding bidireccional de datos
- **Dispatch**: Mecanismo de eventos de Livewire para comunicación entre componentes

---

## Arquitectura

### Estado Actual del Sistema

**Componentes Livewire Existentes:**
- ✅ `HabitList.php` - Lista con filtros, búsqueda y paginación
- ✅ `CreateHabit.php` - Lógica completa de creación con validación
- ✅ `EditHabit.php` - Lógica completa de edición y eliminación

**Vistas Blade Existentes:**
- ✅ `habit-list.blade.php` - Vista completa con grid, filtros y estado vacío
- ❌ `create-habit.blade.php` - Vista vacía (solo comentario)
- ❌ `edit-habit.blade.php` - Vista vacía (solo comentario)
- ✅ `components/modal.blade.php` - Modal reutilizable con Alpine.js

### Arquitectura de Componentes

```
┌─────────────────────────────────────────────────────────────┐
│                     HabitList (Vista Principal)              │
│  - Muestra grid de hábitos                                   │
│  - Filtros (activo/archivado/todos)                         │
│  - Búsqueda                                                  │
│  - Botón "Nuevo Hábito" → Navega a CreateHabit             │
│  - Botón "Editar" → Navega a EditHabit                     │
└─────────────────────────────────────────────────────────────┘
                    ↓                           ↓
        ┌───────────────────┐       ┌───────────────────┐
        │   CreateHabit     │       │    EditHabit      │
        │  - Formulario     │       │  - Formulario     │
        │  - Validación     │       │  - Validación     │
        │  - Guarda y       │       │  - Actualiza      │
        │    redirige       │       │  - Elimina        │
        └───────────────────┘       │  - Archiva        │
                                    └───────────────────┘
                                            ↓
                                    ┌───────────────────┐
                                    │ Modal Confirmación│
                                    │  - Advertencia    │
                                    │  - Confirmar/     │
                                    │    Cancelar       │
                                    └───────────────────┘
```

---

## Componentes y Interfaces

### 1. Vista de Creación de Hábito

**Archivo:** `resources/views/livewire/habits/create-habit.blade.php`

**Responsabilidades:**
- Renderizar formulario completo de creación
- Mostrar campos según el tipo de frecuencia seleccionada
- Validar en tiempo real con Livewire
- Mostrar errores de validación
- Selector visual de iconos y colores
- Indicador de carga durante el guardado

**Campos del Formulario:**
1. Nombre (requerido, max 255)
2. Descripción (opcional, max 1000)
3. Categoría (select con opciones de enum)
4. Dificultad (select: easy, medium, hard, epic)
5. Frecuencia (select: daily, weekly, monthly)
6. Días seleccionados (solo si frecuencia = weekly)
7. Hora (time picker)
8. Color (selector de colores)
9. Icono (grid de emojis)
10. Pomodoros estimados (opcional, número)
11. Recordatorio habilitado (checkbox)
12. Hora de recordatorio (time picker, solo si recordatorio habilitado)

**Interacciones:**
- Al cambiar frecuencia a "weekly", mostrar selector de días
- Al habilitar recordatorio, mostrar campo de hora
- Al enviar formulario, mostrar spinner y deshabilitar botón
- Al guardar exitosamente, redirigir a lista con mensaje flash
- Botón cancelar que redirige a lista sin guardar

### 2. Vista de Edición de Hábito

**Archivo:** `resources/views/livewire/habits/edit-habit.blade.php`

**Responsabilidades:**
- Renderizar formulario pre-cargado con datos del hábito
- Mismos campos que creación
- Botón adicional para eliminar
- Botón adicional para archivar
- Confirmación antes de eliminar

**Campos del Formulario:**
- Mismos que creación, pero pre-cargados con valores actuales
- Campo adicional: `is_active` (toggle para activar/desactivar)

**Interacciones:**
- Al hacer clic en "Eliminar", abrir modal de confirmación
- Al confirmar eliminación, ejecutar método delete() y redirigir
- Al hacer clic en "Archivar", ejecutar método archive() y redirigir
- Al guardar cambios, ejecutar método update() y redirigir
- Botón cancelar que redirige a lista sin guardar

### 3. Modal de Confirmación de Eliminación

**Implementación:** Usar componente `<x-modal>` existente con Alpine.js

**Contenido:**
- Icono de advertencia (rojo)
- Título: "¿Estás seguro de eliminar este hábito?"
- Mensaje: Mostrar nombre del hábito
- Advertencia: "Esta acción no se puede deshacer"
- Botones:
  - Cancelar (gris, cierra modal)
  - Eliminar (rojo, ejecuta eliminación)

**Flujo:**
1. Usuario hace clic en botón "Eliminar" en EditHabit
2. Se abre modal con Alpine.js: `$dispatch('open-modal', 'delete-habit')`
3. Usuario confirma o cancela
4. Si confirma, se ejecuta `wire:click="delete"`
5. Modal se cierra y se redirige a lista

---

## Modelos de Datos

### Habit Model (Ya existe)

```php
class Habit extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'difficulty',
        'frequency',
        'schedule',
        'is_recurring',
        'points_reward',
        'color',
        'icon',
        'estimated_pomodoros',
        'reminder_enabled',
        'reminder_time',
        'is_active',
        'archived_at',
        'current_streak',
        'best_streak',
        'total_completions',
    ];

    protected $casts = [
        'schedule' => 'array',
        'difficulty' => HabitDifficulty::class,
        'frequency' => HabitFrequency::class,
        'category' => HabitCategory::class,
        'reminder_enabled' => 'boolean',
        'reminder_time' => 'datetime',
        'is_active' => 'boolean',
        'archived_at' => 'datetime',
    ];
}
```

### Enums Utilizados

**HabitCategory:**
- productivity, health, learning, social, creative, household, finance, personal

**HabitDifficulty:**
- easy (10 pts), medium (20 pts), hard (30 pts), epic (50 pts)

**HabitFrequency:**
- daily, weekly, monthly

---

## Propiedades de Corrección

*Una propiedad es una característica o comportamiento que debe cumplirse en todas las ejecuciones válidas del sistema.*

### Propiedad 1: Validación de campos requeridos
*Para cualquier* intento de crear o editar un hábito, si el nombre está vacío o la categoría no está seleccionada, el sistema debe rechazar el envío y mostrar mensajes de error específicos.
**Valida: Requisitos 1.4, 2.4**

### Propiedad 2: Persistencia de datos
*Para cualquier* hábito creado o editado exitosamente, los datos guardados en la base de datos deben coincidir exactamente con los valores ingresados en el formulario.
**Valida: Requisitos 1.2, 2.2**

### Propiedad 3: Actualización de UI sin recarga
*Para cualquier* acción de crear, editar o eliminar un hábito, la lista de hábitos debe actualizarse automáticamente sin recargar la página completa.
**Valida: Requisitos 1.2, 2.2, 3.4, 4.4**

### Propiedad 4: Cierre de modal al cancelar
*Para cualquier* modal abierto (crear, editar, eliminar), al hacer clic en "Cancelar" o fuera del modal, este debe cerrarse sin ejecutar ninguna acción de guardado o eliminación.
**Valida: Requisitos 1.3, 2.3, 3.3**

### Propiedad 5: Confirmación antes de eliminar
*Para cualquier* hábito, antes de ejecutar la eliminación, el sistema debe mostrar un modal de confirmación con el nombre del hábito y advertencia de irreversibilidad.
**Valida: Requisitos 3.1, 3.2**

### Propiedad 6: Visibilidad condicional de campos
*Para cualquier* formulario de hábito, cuando la frecuencia es "weekly", el selector de días debe ser visible; cuando no lo es, debe estar oculto.
**Valida: Requisitos 1.1, 2.1**

### Propiedad 7: Redirección después de guardar
*Para cualquier* operación exitosa de crear, editar o eliminar, el sistema debe redirigir al usuario a la lista de hábitos y mostrar un mensaje de confirmación.
**Valida: Requisitos 1.2, 2.2, 3.2**

---

## Manejo de Errores

### Errores de Validación

**Escenarios:**
1. Nombre vacío
2. Categoría no seleccionada
3. Frecuencia no seleccionada
4. Formato de hora inválido
5. Pomodoros negativos o cero

**Respuesta del Sistema:**
- Mostrar mensaje de error debajo del campo correspondiente
- Mensaje en rojo con icono de advertencia
- No cerrar el formulario
- Mantener los valores ingresados
- Enfocar el primer campo con error

### Errores de Servidor

**Escenarios:**
1. Error de conexión a base de datos
2. Usuario no autenticado
3. Hábito no encontrado (404)
4. Sin permisos para editar/eliminar

**Respuesta del Sistema:**
- Mostrar mensaje flash de error en la parte superior
- Redirigir a lista si el hábito no existe
- Mostrar página 403 si no tiene permisos

### Errores de Red

**Escenarios:**
1. Pérdida de conexión durante guardado
2. Timeout de servidor

**Respuesta del Sistema:**
- Mostrar mensaje de error genérico
- Mantener datos del formulario
- Permitir reintentar

---

## Estrategia de Testing

### Tests Unitarios

**Componente CreateHabit:**
1. Test: Validación rechaza nombre vacío
2. Test: Validación rechaza categoría inválida
3. Test: Guardado exitoso crea registro en BD
4. Test: Redirección después de guardar
5. Test: Mensaje flash después de guardar

**Componente EditHabit:**
1. Test: Carga correcta de datos del hábito
2. Test: Actualización exitosa modifica registro
3. Test: Eliminación exitosa borra registro
4. Test: Archivado marca is_active como false
5. Test: Autorización rechaza edición de hábito ajeno

### Tests de Integración

1. Test: Flujo completo de crear hábito desde lista
2. Test: Flujo completo de editar hábito desde lista
3. Test: Flujo completo de eliminar con confirmación
4. Test: Navegación entre vistas mantiene estado

### Tests de UI (Browser Tests)

1. Test: Modal se abre al hacer clic en "Nuevo Hábito"
2. Test: Selector de días aparece al elegir frecuencia weekly
3. Test: Modal de confirmación aparece al eliminar
4. Test: Formulario muestra errores de validación
5. Test: Lista se actualiza después de crear hábito

---

## Consideraciones de Diseño

### Consistencia Visual

- Usar clases de Tailwind CSS del sistema de diseño existente
- Colores: brand-600 para acciones primarias, red-600 para eliminación
- Bordes redondeados: rounded-xl para botones, rounded-3xl para tarjetas
- Sombras: shadow-lg para botones principales, shadow-sm para tarjetas
- Espaciado: p-6 para contenido de tarjetas, gap-4 para formularios

### Accesibilidad

- Labels asociados a inputs con atributo `for`
- Mensajes de error con `aria-describedby`
- Botones con texto descriptivo
- Modal con manejo de foco (ya implementado en componente modal)
- Navegación con teclado (Tab, Escape)

### Responsive Design

- Formularios en una columna en móvil
- Grid de iconos adaptable (4 columnas en móvil, 8 en desktop)
- Botones full-width en móvil
- Espaciado reducido en pantallas pequeñas

### Performance

- Usar `wire:model.live.debounce` para búsqueda (ya implementado)
- Lazy loading de modales (solo cargar cuando se abren)
- Paginación para listas grandes (ya implementado)
- Optimizar queries con `with()` para relaciones (ya implementado)

---

## Dependencias Técnicas

### Frontend
- Tailwind CSS (ya configurado)
- Alpine.js (ya configurado)
- Livewire 3.x (ya configurado)

### Backend
- Laravel 11.x
- PHP 8.2+
- MySQL/PostgreSQL

### Componentes Reutilizables
- `<x-modal>` - Modal con Alpine.js (ya existe)
- Componentes de formulario de Laravel (inputs, selects, etc.)

---

## Notas de Implementación

1. **No crear nuevos componentes Livewire** - Los componentes PHP ya existen y funcionan
2. **Enfocarse en las vistas Blade** - Completar create-habit.blade.php y edit-habit.blade.php
3. **Reutilizar el modal existente** - No crear nuevo componente de modal
4. **Mantener consistencia** - Seguir el estilo de habit-list.blade.php
5. **Usar wire:navigate** - Para navegación SPA entre vistas
6. **Aprovechar validación existente** - Los componentes ya tienen reglas de validación
7. **No modificar lógica de negocio** - Solo completar las vistas

