# ⌨️ Atajos de Teclado - Pomodoro Timer

## Atajos Principales

### Control del Timer

| Atajo | Acción | Descripción |
|-------|--------|-------------|
| `Espacio` | Play/Pause/Start | Inicia, pausa o reanuda el timer según el estado actual |
| `Escape` | Stop/Skip | Detiene el Pomodoro o salta el descanso actual |
| `N` | Nuevo Pomodoro | Inicia un nuevo Pomodoro (solo cuando está en idle) |
| `B` | Break | Inicia un descanso (corto o largo según el ciclo) |

---

## Detalles de Funcionamiento

### Tecla `Espacio` (Space)
**Comportamiento inteligente según el estado**:
- **Estado Idle**: Inicia un nuevo Pomodoro
- **Estado Running**: Pausa el timer actual
- **Estado Paused**: Reanuda el timer

**Feedback visual**:
- Muestra un toast con el estado actual
- Los toasts se muestran por 2 segundos
- Estilos: info (azul)

### Tecla `Escape` (Esc)
**Comportamiento según el contexto**:
- **Durante Pomodoro**: Detiene la sesión (la marca como interrumpida)
- **Durante Descanso**: Omite el descanso y vuelve a idle

**Feedback visual**:
- Toast confirmando la acción
- El timer se resetea inmediatamente

### Tecla `N` (New)
**Requisitos**:
- Solo funciona cuando el timer está en idle
- No funciona si ya hay un timer en progreso

**Acción**:
- Inicia un nuevo Pomodoro con la duración seleccionada
- Usa el hábito seleccionado (si hay uno)
- Consume energía del usuario

**Feedback**:
- Toast: "🍅 Nuevo Pomodoro"

### Tecla `B` (Break)
**Requisitos**:
- Solo funciona cuando el timer está en idle
- Determina automáticamente el tipo de descanso según el ciclo

**Lógica de descansos**:
- **Ciclo < 4**: Descanso corto (5 min por defecto)
- **Ciclo ≥ 4**: Descanso largo (15 min por defecto)

**Acción**:
- Inicia el descanso apropiado
- No consume energía
- El timer se muestra con colores específicos (verde/púrpura)

**Feedback**:
- Toast: "☕ Descanso iniciado"

---

## Toasts de Feedback

Cada atajo muestra un mensaje temporal con el resultado:

| Mensaje | Emoji | Contexto |
|---------|-------|----------|
| "⏸ Pausado" | ⏸ | Timer pausado |
| "▶ Reanudado" | ▶ | Timer reanudado |
| "▶ Iniciado" | ▶ | Nuevo timer iniciado |
| "⏹ Detenido" | ⏹ | Timer detenido |
| "⏭ Descanso omitido" | ⏭ | Descanso saltado |
| "☕ Descanso iniciado" | ☕ | Descanso comenzado |
| "🍅 Nuevo Pomodoro" | 🍅 | Pomodoro iniciado con N |

---

## Restricciones Importantes

### Deshabilitar en Inputs
Los atajos **NO funcionan** cuando el usuario está escribiendo en:
- Campos de texto (`<input>`)
- Áreas de texto (`<textarea>`)

**Razón**: Evitar interferencias con la escritura normal

### Validaciones de Energía
Algunos atajos verifican el nivel de energía:
- `Espacio` (para iniciar): Requiere ≥10 de energía
- `N`: Requiere ≥10 de energía
- `B`: No requiere energía (es un descanso)

**Si no hay energía suficiente**:
- El timer no se inicia
- Se muestra un mensaje de error
- Se sugiere descansar

---

## Implementación Técnica

### Ubicación del Código
```javascript
// Archivo: resources/views/livewire/pomodoro/pomodoro-timer.blade.php
// Sección: <script> dentro del Alpine component

setupKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        // Ignorar si está escribiendo
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return;
        }
        
        const timerState = @this.timerState;
        const breakType = @this.breakType;
        
        // Lógica de atajos...
    });
}
```

### Event Listeners
Los atajos se configuran en el método `init()` del componente Alpine:
1. Se registra un listener global de `keydown`
2. Se verifica el contexto (input/textarea)
3. Se obtiene el estado actual del timer
4. Se ejecuta la acción apropiada
5. Se muestra el feedback visual

---

## Tips de Uso

### Flujo de Trabajo Recomendado
1. **Preparación**: Selecciona hábito y duración
2. **Iniciar**: Presiona `Espacio` o `N`
3. **Concentración**: Trabaja sin interrupciones
4. **Pausar si necesario**: Presiona `Espacio` brevemente
5. **Descanso automático**: O forzar con `B`
6. **Repetir ciclo**: 4 Pomodoros → Descanso largo

### Modo Focus + Atajos
Los atajos funcionan perfectamente en el **Modo Fullscreen Focus**:
- Experiencia sin distracciones
- Control total sin mouse
- Perfecto para sesiones largas
- Los toasts se muestran sobre el fondo oscuro

### Productividad Máxima
**Combinación poderosa**:
```
Modo Focus activado + Atajos de teclado = Productividad x2
```

- No necesitas tocar el mouse
- Flujo de trabajo continuo
- Menos distracciones visuales
- Mayor concentración

---

## Compatibilidad

### Navegadores Soportados
- ✅ Chrome/Edge (recomendado)
- ✅ Firefox
- ✅ Safari
- ✅ Opera
- ⚠️ Navegadores móviles (limitado)

### Sistemas Operativos
- ✅ Windows
- ✅ macOS
- ✅ Linux
- ⚠️ iOS/Android (teclado virtual tiene limitaciones)

---

## Personalización Futura

### Atajos Planeados
| Atajo | Acción Propuesta |
|-------|------------------|
| `1-9` | Seleccionar duración rápida |
| `H` | Seleccionar hábito (popup) |
| `S` | Abrir configuración |
| `F` | Toggle modo focus |
| `R` | Ver reportes |
| `?` | Mostrar ayuda de atajos |

### Configuración Personalizada
**Próximamente**:
- Panel de configuración de atajos
- Mapeo personalizado
- Atajos según el usuario
- Import/Export de configuración

---

## Solución de Problemas

### Los atajos no funcionan
**Verifica**:
1. ✅ No estás escribiendo en un input
2. ✅ La página tiene el foco (no otra pestaña)
3. ✅ JavaScript está habilitado
4. ✅ No hay errores en la consola

### Conflictos con extensiones
Algunas extensiones del navegador pueden interceptar atajos:
- **Vimium**: Puede capturar todas las teclas
- **Surfingkeys**: Similar a Vimium
- **Shortkeys**: Mapeo de teclas personalizado

**Solución**: Desactiva temporalmente o excluye el dominio

### Atajos se ejecutan dos veces
**Causa**: Event bubbling
**Solución**: Ya implementado con `e.preventDefault()`

---

## Mejores Prácticas

### Do's ✅
- Aprende los 4 atajos principales
- Usa `Espacio` como atajo principal
- Combina con modo focus
- Mantén las manos en el teclado

### Don'ts ❌
- No uses atajos mientras escribes notas
- No presiones múltiples teclas rápidamente
- No dependas solo de atajos (mouse también válido)

---

## Feedback y Sugerencias

¿Tienes ideas para nuevos atajos?
- Abre un issue en el repositorio
- Contacta al equipo de desarrollo
- Propón en la comunidad

**Criterios para nuevos atajos**:
- Debe ser intuitivo
- No debe conflictuar con atajos del navegador
- Debe mejorar la productividad
- Debe ser fácil de recordar

---

**Última actualización**: Diciembre 2025
**Versión**: 2.0

