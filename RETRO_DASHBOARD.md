# 🎮 Dashboard Retro - HabitTracks

## 🌟 Características

Tu dashboard ahora tiene un **estilo retro inspirado en los años 80-90** con:

### 🎨 Diseño Visual
- **Tema CRT Monitor**: Efecto de monitor de tubo catódico con scanlines
- **Colores Neón**: Verde fosforescente (#00ff00), naranja (#ff6600), cyan (#00ffff)
- **Tipografía Retro**: Fuente monoespaciada estilo terminal
- **Efectos Glitch**: Animaciones sutiles de glitch en el título
- **Bordes Luminosos**: Box-shadows con efecto de neón

### 📊 Componentes del Dashboard

1. **Header Retro**
   - Saludo personalizado con efecto glitch
   - Timer Pomodoro integrado
   - Efecto de scanlines

2. **Tarjetas de Estadísticas**
   - **Nivel de Productividad**: Muestra tu nivel actual y barra de progreso XP
   - **Racha Actual**: Días consecutivos con visualización de barras
   - **Tasa de Completitud**: Porcentaje de hábitos completados hoy

3. **Lista de Hábitos**
   - Checkboxes personalizados estilo retro
   - Badges de categorías con colores neón
   - Puntos XP destacados
   - Estado de completado con tachado

### 🎯 Iconos Lucide

Se instaló **Lucide Icons** para tener iconos modernos y limpios:
- `zap` - Nivel de productividad
- `flame` - Racha de días
- `target` - Tasa de completitud
- `list-checks` - Lista de hábitos
- `play` - Botón de inicio del timer

## 🚀 Cómo Usar

1. **Accede al Dashboard**: Inicia sesión y serás redirigido automáticamente
2. **Visualiza tus Stats**: Las tarjetas muestran tu progreso en tiempo real
3. **Completa Hábitos**: Marca los checkboxes para completar tus hábitos del día
4. **Usa el Timer**: Inicia sesiones Pomodoro desde el header

## 🎨 Personalización

### Cambiar Colores
Edita las variables CSS en `resources/css/retro.css`:

```css
:root {
    --retro-green: #00ff00;      /* Verde principal */
    --retro-green-dark: #00aa00; /* Verde oscuro */
    --retro-orange: #ff6600;     /* Naranja para racha */
    --retro-cyan: #00ffff;       /* Cyan para porcentajes */
    --retro-yellow: #ffff00;     /* Amarillo para puntos */
    --retro-bg: #0a0a0a;         /* Fondo oscuro */
    --retro-bg-light: #1a1a1a;   /* Fondo claro */
}
```

### Desactivar Efectos
Para desactivar el efecto de parpadeo CRT, comenta esta línea en `retro.css`:

```css
/* body {
    animation: flicker 0.15s infinite;
} */
```

## 📱 Responsive

El dashboard es completamente responsive:
- **Desktop**: Grid de 3 columnas para las stats
- **Tablet**: Grid de 2 columnas
- **Mobile**: Stack vertical de todas las tarjetas

## 🔧 Archivos Modificados

- `resources/js/app.js` - Configuración de Lucide Icons
- `resources/css/retro.css` - Estilos retro globales
- `resources/css/app.css` - Import del CSS retro
- `resources/views/livewire/dashboard/index.blade.php` - Dashboard principal
- `resources/views/components/layouts/app/sidebar.blade.php` - Sidebar con tema retro

## 🎮 Próximas Funcionalidades

- [ ] Timer Pomodoro funcional
- [ ] Animaciones al completar hábitos
- [ ] Sonidos retro (opcional)
- [ ] Más efectos visuales (partículas, explosiones)
- [ ] Temas alternativos (Amber, Cyan, Purple)

## 💡 Tips

- El efecto de scanlines es sutil para no cansar la vista
- Los colores neón tienen buena legibilidad en fondos oscuros
- El diseño mantiene la funcionalidad mientras añade estilo

¡Disfruta tu dashboard retro! 🎮✨
