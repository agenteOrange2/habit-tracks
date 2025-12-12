# Implementation Plan

- [x] 1. Crear modelos y migraciones de base de datos

  - Crear migración para tabla categories con todos los campos
  - Crear migración para tabla difficulties con todos los campos
  - Crear migración para agregar category_id y difficulty_id a habits
  - Crear modelo Category con relaciones y métodos
  - Crear modelo Difficulty con relaciones y métodos
  - _Requirements: 4.1, 4.2_

- [ ]* 1.1 Escribir tests de propiedad para modelos
  - **Property 1: Category name uniqueness**
  - **Validates: Requirements 1.2**

- [ ]* 1.2 Escribir tests de propiedad para validación de puntos
  - **Property 7: Points validation range**
  - **Validates: Requirements 6.2**




- [x] 2. Crear seeders para datos iniciales





  - Crear seeder para categories con datos del enum HabitCategory
  - Crear seeder para difficulties con datos del enum HabitDifficulty
  - Ejecutar seeders y verificar datos
  - _Requirements: 4.2_

- [x] 3. Implementar servicio de migración de datos





  - Crear HabitSettingsMigrationService
  - Implementar método para migrar categorías de habits
  - Implementar método para migrar dificultades de habits
  - Implementar verificación de integridad post-migración
  - _Requirements: 4.3, 4.4, 4.5_

- [ ]* 3.1 Escribir test de propiedad para migración
  - **Property 8: Migration data preservation**
  - **Validates: Requirements 4.4**

- [ ] 4. Crear políticas de autorización
  - Crear CategoryPolicy con métodos viewAny, create, update, delete
  - Crear DifficultyPolicy con métodos viewAny, create, update, delete
  - Registrar políticas en AuthServiceProvider
  - _Requirements: 1.1, 2.1_

- [x] 5. Implementar componente de lista de categorías





  - Crear CategoryList Livewire component
  - Basarse en el diseño de category-notion.html y el modal para dar de alta
  - Implementar método render con carga de categorías
  - Implementar método toggleActive para activar/desactivar
  - Implementar método delete con validación de hábitos asociados
  - Crear vista category-list.blade.php con diseño Notion
  - _Requirements: 1.1, 1.4, 1.5, 5.1_

- [ ]* 5.1 Escribir test de propiedad para protección de eliminación
  - **Property 3: Category deletion protection**
  - **Validates: Requirements 1.4**

- [x] 6. Implementar formulario de categorías



  - Crear CategoryForm Livewire component
  - Implementar validación de campos (nombre, icono, color)
  - Implementar métodos save para crear/actualizar
  - Crear vista category-form.blade.php con modal estilo Notion
  - Agregar emoji picker para selección de icono
  - Agregar color picker con paleta predefinida
  - _Requirements: 1.2, 1.3, 5.2, 6.1, 6.5_

- [ ]* 6.1 Escribir test de propiedad para validación de emoji
  - **Property 9: Icon emoji validation**
  - **Validates: Requirements 6.5**

- [x] 7. Implementar componente de lista de dificultades





  - Crear DifficultyList Livewire component
  - Implementar método render con carga de dificultades
  - Implementar método toggleActive para activar/desactivar
  - Implementar método delete con validación de hábitos asociados
  - Crear vista difficulty-list.blade.php con diseño Notion
  - _Requirements: 2.1, 2.4, 2.5, 5.4_

- [ ]* 7.1 Escribir test de propiedad para protección de eliminación de dificultades
  - **Property 4: Difficulty deletion protection**
  - **Validates: Requirements 2.4**

- [x] 8. Implementar formulario de dificultades



  - Crear DifficultyForm Livewire component
  - Implementar validación de campos (nombre, puntos, icono)
  - Implementar métodos save para crear/actualizar
  - Crear vista difficulty-form.blade.php con modal estilo Notion
  - Agregar emoji picker para selección de icono
  - _Requirements: 2.2, 2.3, 5.4, 6.2_

- [x] 9. Implementar funcionalidad de reordenamiento





  - Agregar método updateOrder en CategoryList
  - Agregar método updateOrder en DifficultyList
  - Implementar drag-and-drop en vistas con SortableJS
  - Validar unicidad de orden al actualizar
  - _Requirements: 5.3, 6.3_

- [ ]* 9.1 Escribir test de propiedad para unicidad de orden
  - **Property 6: Order uniqueness within type**
  - **Validates: Requirements 6.3**

- [x] 10. Actualizar modelo Habit para usar relaciones





  - Agregar relación belongsTo para category
  - Agregar relación belongsTo para difficulty
  - Actualizar métodos que usan enums para usar relaciones
  - Mantener compatibilidad con campos antiguos temporalmente
  - _Requirements: 4.3_

- [ ] 11. Actualizar formularios de hábitos para carga dinámica




