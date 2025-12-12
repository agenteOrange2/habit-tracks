# Guía Paso a Paso: Vistas de Gestión de Hábitos

## Introducción

Esta guía te ayudará a construir las vistas para gestionar hábitos en tu aplicación HabitHero. Aprenderás a crear interfaces para **agregar**, **editar** y **eliminar** hábitos, siguiendo el diseño moderno que ya tienes en `design6-2.html`.

## Glosario

- **Hábito**: Una actividad que el usuario quiere realizar regularmente
- **Livewire**: Framework de Laravel para crear interfaces interactivas
- **Blade**: Motor de plantillas de Laravel
- **Alpine.js**: Framework JavaScript ligero para interactividad
- **Modal**: Ventana emergente que aparece sobre el contenido principal
- **Componente**: Pieza reutilizable de código

---

## 📋 Requisito 1: Vista para Crear Nuevo Hábito

**Historia de Usuario:** Como usuario, quiero poder crear un nuevo hábito desde un formulario intuitivo, para poder comenzar a rastrearlo.

### Criterios de Aceptación

1. CUANDO el usuario hace clic en el botón "Nuevo Hábito" ENTONCES el sistema DEBE mostrar un modal con un formulario
2. CUANDO el usuario completa el formulario y lo envía ENTONCES el sistema DEBE crear el hábito y actualizar la lista
3. CUANDO el usuario cancela la creación ENTONCES el sistema DEBE cerrar el modal sin guardar cambios
4. CUANDO el formulario tiene errores de validación ENTONCES el sistema DEBE mostrar mensajes de error claros

---

## 📋 Requisito 2: Vista para Editar Hábito Existente

**Historia de Usuario:** Como usuario, quiero poder editar los detalles de un hábito existente, para mantener mi información actualizada.

### Criterios de Aceptación

1. CUANDO el usuario hace clic en editar un hábito ENTONCES el sistema DEBE mostrar un modal con los datos actuales
2. CUANDO el usuario modifica y guarda los cambios ENTONCES el sistema DEBE actualizar el hábito
3. CUANDO el usuario cancela la edición ENTONCES el sistema DEBE cerrar el modal sin aplicar cambios
4. CUANDO hay errores de validación ENTONCES el sistema DEBE mostrar mensajes específicos

---

## 📋 Requisito 3: Funcionalidad para Eliminar Hábito

**Historia de Usuario:** Como usuario, quiero poder eliminar un hábito que ya no necesito, para mantener mi lista organizada.

### Criterios de Aceptación

1. CUANDO el usuario hace clic en eliminar ENTONCES el sistema DEBE mostrar una confirmación
2. CUANDO el usuario confirma la eliminación ENTONCES el sistema DEBE eliminar el hábito permanentemente
3. CUANDO el usuario cancela ENTONCES el sistema DEBE mantener el hábito sin cambios
4. CUANDO se elimina un hábito ENTONCES el sistema DEBE actualizar la lista automáticamente

---

## 📋 Requisito 4: Lista de Hábitos con Acciones

**Historia de Usuario:** Como usuario, quiero ver todos mis hábitos en una lista con opciones para editarlos o eliminarlos, para gestionar fácilmente mi colección.

### Criterios de Aceptación

1. CUANDO el usuario visita la página de hábitos ENTONCES el sistema DEBE mostrar todos sus hábitos
2. CUANDO un hábito se muestra ENTONCES el sistema DEBE incluir botones de editar y eliminar
3. CUANDO la lista está vacía ENTONCES el sistema DEBE mostrar un mensaje amigable
4. CUANDO se realiza una acción ENTONCES el sistema DEBE actualizar la lista sin recargar la página

---

## 🎨 Elementos de Diseño a Seguir

Basándote en `design6-2.html`, usa estos estilos:

- **Colores principales**: 
  - Azul brand: `bg-brand-600` (#2563eb)
  - Fondo: `bg-gray-50`
  - Tarjetas: `bg-white` con `border border-gray-100`
  
- **Bordes redondeados**: `rounded-3xl` para tarjetas, `rounded-xl` para botones

- **Sombras**: `shadow-sm` para tarjetas, `shadow-lg shadow-brand-500/30` para botones principales

- **Tipografía**:
  - Títulos: `font-bold text-slate-800`
  - Texto secundario: `text-slate-500`
  - Tamaños: `text-sm`, `text-base`, `text-lg`

---

## 📁 Estructura de Archivos

Crearás o modificarás estos archivos:

```
resources/views/livewire/habits/
├── habit-list.blade.php          (Ya existe - modificar)
├── create-habit.blade.php        (Ya existe - mejorar)
├── edit-habit.blade.php          (Ya existe - mejorar)
└── components/
    ├── habit-form-modal.blade.php    (Nuevo - componente reutilizable)
    └── delete-confirmation.blade.php  (Nuevo - modal de confirmación)

app/Livewire/Habits/
├── HabitList.php                 (Ya existe - revisar)
├── CreateHabit.php               (Ya existe - revisar)
└── EditHabit.php                 (Ya existe - revisar)
```

---

## 🚀 Próximos Pasos

En las siguientes secciones de esta guía, encontrarás:

1. **Paso 1**: Crear el componente modal reutilizable
2. **Paso 2**: Implementar el formulario de creación
3. **Paso 3**: Implementar el formulario de edición
4. **Paso 4**: Implementar la confirmación de eliminación
5. **Paso 5**: Integrar todo en la lista de hábitos
6. **Paso 6**: Agregar validaciones y mensajes de error
7. **Paso 7**: Probar la funcionalidad completa

Cada paso incluirá:
- ✅ Objetivo claro
- 📝 Código comentado
- 💡 Explicaciones de conceptos
- ⚠️ Puntos importantes a considerar
- 🎯 Resultado esperado

---

## 📚 Conceptos que Aprenderás

- Cómo funcionan los componentes Livewire
- Cómo crear modales con Alpine.js
- Cómo validar formularios en Laravel
- Cómo actualizar la UI sin recargar la página
- Cómo aplicar estilos consistentes con Tailwind CSS
- Cómo manejar eventos entre componentes

---

¿Listo para comenzar? Avísame cuando quieras que te proporcione el **Paso 1** con el código detallado y explicaciones.
