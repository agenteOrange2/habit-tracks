# 📝 Sistema de Notas - Guía de Funcionalidades

## Características Principales

### 🔍 Búsqueda Mejorada
- **Búsqueda en tiempo real**: Escribe en el campo de búsqueda para filtrar notas instantáneamente
- **Busca en**: Títulos y contenido completo de las notas
- **Busca por**: Palabras clave, frases, cualquier texto dentro del contenido HTML

### 📁 Organización
- **Carpetas**: Organiza tus notas en carpetas personalizadas
- **Etiquetas**: Añade múltiples etiquetas con colores para categorizar
- **Notas fijadas**: Mantén notas importantes al principio con el pin
- **Papelera**: Las notas eliminadas van a la papelera (recuperables)

### ✨ Editor Rico de Contenido

#### Formato de Texto
- **Negrita** (Ctrl+B): Para destacar texto importante
- **Cursiva** (Ctrl+I): Para énfasis o citas
- **Subrayado** (Ctrl+U): Para marcar texto
- **Tachado**: Para texto que ya no aplica
- **Títulos**: H1, H2, H3 para estructurar contenido
- **Limpiar formato**: Elimina todo el formato del texto seleccionado

#### Listas y Estructura
- **Listas con viñetas**: Para ítems no ordenados
- **Listas numeradas**: Para pasos o secuencias
- **Citas**: Para destacar referencias o citas textuales
- **Sangría**: Aumentar/disminuir indentación
- **Separadores**: Líneas horizontales para dividir secciones

#### Código
- **Código en línea**: Para comandos o variables cortas
- **Bloques de código**: Para fragmentos de código más largos
- Formato monoespaciado con resaltado

#### Contenido Multimedia

##### 🔗 Enlaces (Ctrl+K)
- Inserta enlaces a sitios web
- Se abren en nueva pestaña automáticamente
- Puedes usar texto personalizado para el enlace

##### 🖼️ Imágenes
- Inserta imágenes desde URL
- Texto alternativo para accesibilidad
- Carga lazy para mejor rendimiento
- Responsive: se ajustan al ancho del contenedor
- Bordes redondeados con sombra suave

##### 📺 Iframes (Contenido Embebido)
Soporta inserción automática de:
- **YouTube**: Copia el link del video (convierte automáticamente)
  - Ejemplo: `https://youtube.com/watch?v=VIDEO_ID`
  - Ejemplo: `https://youtu.be/VIDEO_ID`
- **Vimeo**: Videos de Vimeo
  - Ejemplo: `https://vimeo.com/VIDEO_ID`
- **Google Maps**: Mapas embebidos
- **Otros servicios**: Cualquier URL con iframe
- Aspect ratio 16:9 automático para videos
- Fullscreen habilitado

##### 🎥 Videos
- Inserta archivos de video directos (MP4, WebM)
- Controles nativos del navegador
- Responsive y con controles de reproducción

##### ⊞ Tablas
- Crea tablas personalizadas
- Define número de filas y columnas
- Bordes y estilos automáticos
- Primera fila como encabezado

### ⌨️ Atajos de Teclado

| Atajo | Función |
|-------|---------|
| Ctrl+B | Negrita |
| Ctrl+I | Cursiva |
| Ctrl+U | Subrayado |
| Ctrl+K | Insertar enlace |
| Ctrl+S | Guardar manualmente |

### 💾 Auto-guardado
- **Guardado automático**: Cada cambio se guarda después de 1 segundo de inactividad
- **Indicador visual**: "Guardando..." cuando se está guardando
- **Confirmación**: ✓ verde cuando se guardó exitosamente

### 🎨 Interfaz Notion-style
- Diseño limpio y minimalista
- Íconos emoji para personalizar cada nota
- Vista de grilla para notas
- Previsualización del contenido en tarjetas
- Timestamps con formato amigable ("hace 2 horas")

## Ejemplos de Uso

### Insertar Video de YouTube
1. Clic en el botón 📺 (iframe)
2. Pega la URL del video: `https://youtube.com/watch?v=dQw4w9WgXcQ`
3. Se convierte automáticamente al formato embed
4. El video aparece con controles y fullscreen

### Insertar Imagen
1. Clic en el botón 🖼️
2. Pega la URL de la imagen: `https://example.com/imagen.jpg`
3. (Opcional) Añade texto alternativo
4. La imagen se inserta responsive

### Crear Tabla
1. Clic en el botón ⊞
2. Define filas (ejemplo: 3) y columnas (ejemplo: 4)
3. Se crea una tabla editable
4. Primera fila tiene estilo de encabezado

### Insertar Código
1. Selecciona texto o clic en botón &lt;/&gt;
2. Para código inline: texto simple
3. Para bloque: texto con saltos de línea
4. Formato monoespaciado automático

## Tips y Trucos

1. **Organización**: Usa carpetas para proyectos y etiquetas para temas transversales
2. **Búsqueda rápida**: El buscador funciona en tiempo real, perfecto para encontrar notas rápidamente
3. **Multimedia**: Aprovecha iframes para documentación visual (videos tutoriales, mapas, etc.)
4. **Enlaces internos**: Copia URLs de otras notas para crear referencias cruzadas
5. **Tablas para listas**: Usa tablas para comparaciones o listas estructuradas
6. **Atajos**: Memoriza Ctrl+K para enlaces rápidos y Ctrl+B/I para formato

## Compatibilidad

- ✅ Chrome, Firefox, Safari, Edge
- ✅ Responsive: funciona en móviles y tablets
- ✅ Pegado de texto limpio (sin formato no deseado)
- ✅ Imágenes con lazy loading
- ✅ Videos embebidos responsive

## Próximas Funcionalidades (Roadmap)

- [ ] Subida de imágenes directa
- [ ] Colaboración en tiempo real
- [ ] Historial de versiones
- [ ] Exportar a PDF/Markdown
- [ ] Plantillas de notas
- [ ] Atajos personalizables

---

**Última actualización**: Diciembre 2025

