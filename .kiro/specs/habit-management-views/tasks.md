# Plan de Implementación: Vistas de Gestión de Hábitos

## Contexto

Este plan se enfoca en **completar las vistas Blade** que están vacías. Los componentes Livewire ya existen y funcionan correctamente. NO necesitamos crear nuevos componentes PHP.

**Estado actual:**
- ✅ Componentes Livewire: HabitList.php, CreateHabit.php, EditHabit.php
- ✅ Vista: habit-list.blade.php (completa)
- ✅ Componente: modal.blade.php (reutilizable)
- ❌ Vista: create-habit.blade.php (vacía)
- ❌ Vista: edit-habit.blade.php (vacía)

---

## Tareas

- [x] 1. Implementar vista de creación de hábito


  - Completar `resources/views/livewire/habits/create-habit.blade.php`
  - Crear formulario completo con todos los campos
  - Implementar selector visual de iconos (grid de emojis)
  - Implementar selector de colores
  - Agregar visibilidad condicional para campos (días de semana si frecuencia=weekly)
  - Agregar validación visual con mensajes de error
  - Agregar indicador de carga durante guardado
  - _Requisitos: 1.1, 1.2, 1.3, 1.4_

- [x] 2. Implementar vista de edición de hábito


  - Completar `resources/views/livewire/habits/edit-habit.blade.php`
  - Crear formulario idéntico al de creación pero pre-cargado
  - Agregar botón de eliminar con confirmación
  - Agregar botón de archivar
  - Agregar toggle para activar/desactivar hábito
  - Reutilizar componentes visuales de creación (iconos, colores)
  - _Requisitos: 2.1, 2.2, 2.3, 2.4_

- [x] 3. Implementar modal de confirmación de eliminación

  - Agregar modal de confirmación en edit-habit.blade.php
  - Usar componente `<x-modal>` existente
  - Mostrar icono de advertencia
  - Mostrar nombre del hábito a eliminar
  - Agregar mensaje de irreversibilidad
  - Botones: Cancelar (cierra modal) y Eliminar (ejecuta delete)
  - _Requisitos: 3.1, 3.2, 3.3_

- [x] 4. Mejorar experiencia de usuario

  - Agregar transiciones suaves entre vistas con wire:navigate
  - Agregar animaciones de carga
  - Mejorar mensajes flash de éxito/error
  - Asegurar responsive design en formularios
  - _Requisitos: 4.4_

- [x] 5. Checkpoint - Verificar funcionalidad completa



  - Probar flujo completo: crear → listar → editar → eliminar
  - Verificar validaciones funcionan correctamente
  - Verificar modales se abren y cierran correctamente
  - Verificar responsive en móvil
  - Verificar mensajes de éxito/error se muestran
  - Asegurar que todos los tests pasan, preguntar al usuario si surgen dudas

---

## Notas Importantes

⚠️ **NO crear nuevos componentes Livewire** - Ya existen y funcionan
⚠️ **NO modificar lógica de negocio** - Solo completar vistas
⚠️ **Reutilizar componente modal existente** - No crear uno nuevo
⚠️ **Seguir estilo de habit-list.blade.php** - Mantener consistencia visual
⚠️ **Usar wire:model para binding** - Ya configurado en componentes PHP

## Campos del Formulario

Ambos formularios (crear y editar) deben incluir:

1. **Nombre** (text, requerido)
2. **Descripción** (textarea, opcional)
3. **Categoría** (select, requerido)
   - Opciones: productivity, health, learning, social, creative, household, finance, personal
4. **Dificultad** (select, requerido)
   - Opciones: easy, medium, hard, epic
5. **Frecuencia** (select, requerido)
   - Opciones: daily, weekly, monthly
6. **Días de la semana** (checkboxes, solo si frecuencia=weekly)
   - Lunes a Domingo
7. **Hora** (time, opcional)
8. **Color** (selector visual, requerido)
9. **Icono** (grid de emojis, requerido)
10. **Pomodoros estimados** (number, opcional)
11. **Recordatorio habilitado** (checkbox)
12. **Hora de recordatorio** (time, solo si recordatorio habilitado)

**Solo en edición:**
13. **Estado activo** (toggle)

## Estilos a Usar

- Inputs: `rounded-xl border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500`
- Botones primarios: `bg-brand-600 hover:bg-brand-700 text-white rounded-xl shadow-lg`
- Botones secundarios: `bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-xl`
- Botones peligro: `bg-red-600 hover:bg-red-700 text-white rounded-xl`
- Errores: `text-red-600 text-sm mt-1`
- Labels: `text-sm font-medium text-slate-700 mb-2`

