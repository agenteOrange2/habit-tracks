# 🚀 Guía de Implementación con Código Completo

## Tabla de Contenidos
1. [Paso 1: Modal Reutilizable](#paso-1-modal-reutilizable)
2. [Paso 2: Vista de Lista de Hábitos](#paso-2-vista-de-lista-de-hábitos)
3. [Paso 3: Formulario de Crear Hábito](#paso-3-formulario-de-crear-hábito)
4. [Paso 4: Formulario de Editar Hábito](#paso-4-formulario-de-editar-hábito)
5. [Paso 5: Modal de Confirmación de Eliminación](#paso-5-modal-de-confirmación-de-eliminación)
6. [Paso 6: Componente Livewire del Controlador](#paso-6-componente-livewire-del-controlador)

---

## Paso 1: Modal Reutilizable

### 📁 Archivo: `resources/views/components/modal.blade.php`

```blade
{{-- 
    Componente Modal Reutilizable
    
    Uso:
    <x-modal name="create-habit" title="Nuevo Hábito">
        <!-- Contenido del modal aquí -->
    </x-modal>
    
    Para abrir: $dispatch('open-modal', 'create-habit')
    Para cerrar: $dispatch('close-modal')
--}}

@props(['name', 'title' => '', 'maxWidth' => '2xl'])

@php
$maxWidthClass = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{
        show: false,
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select'
            return [...$el.querySelectorAll(selector)]
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-hidden');
            setTimeout(() => firstFocusable().focus(), 100);
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: none;"
>
    <!-- Overlay oscuro -->
    <div 
        x-show="show" 
        class="fixed inset-0 transform transition-all" 
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
    </div>

    <!-- Contenedor del Modal -->
    <div 
        x-show="show" 
        class="mb-6 bg-white rounded-3xl overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto {{ $maxWidthClass }}"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        <!-- Header del Modal -->
        @if($title)
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800">{{ $title }}</h3>
            <button 
                type="button"
                x-on:click="show = false" 
                class="text-slate-400 hover:text-slate-600 transition-colors"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        @endif

        <!-- Contenido del Modal -->
        <div class="px-6 py-6">
            {{ $slot }}
        </div>
    </div>
</div>
```

**💡 Explicación:**
- Este modal usa **Alpine.js** para manejar la apertura/cierre
- Se abre con el evento `$dispatch('open-modal', 'nombre-del-modal')`
- Tiene animaciones suaves de entrada/salida
- Sigue el diseño de `design6-2.html` con bordes redondeados (`rounded-3xl`)

---

## Paso 2: Vista de Lista de Hábitos

### 📁 Archivo: `resources/views/livewire/habits/index.blade.php`

```blade
<div>
    {{-- Header con botón de crear --}}
    <div class="px-8 py-6 bg-white flex justify-between items-center border-b border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Mis Hábitos</h1>
            <p class="text-sm text-slate-500 mt-1">Gestiona tus hábitos y alcanza tus metas.</p>
        </div>
        <button 
            wire:click="$dispatch('open-modal', 'create-habit')"
            class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium shadow-lg shadow-brand-500/30 flex items-center gap-2 transition-all active:scale-95"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo Hábito
        </button>
    </div>

    {{-- Contenido principal --}}
    <div class="p-8">
        @if($habits->count() > 0)
            {{-- Grid de hábitos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($habits as $habit)
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        {{-- Icono y categoría --}}
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl
                                {{ $habit->category === 'Salud' ? 'bg-green-50' : '' }}
                                {{ $habit->category === 'Trabajo' ? 'bg-blue-50' : '' }}
                                {{ $habit->category === 'Desarrollo' ? 'bg-purple-50' : '' }}
                                {{ $habit->category === 'Personal' ? 'bg-orange-50' : '' }}
                            ">
                                {{ $habit->icon ?? '🎯' }}
                            </div>
                            
                            {{-- Menú de acciones --}}
                            <div class="relative" x-data="{ open: false }">
                                <button 
                                    @click="open = !open"
                                    class="text-slate-400 hover:text-slate-600 p-1"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                    </svg>
                                </button>
                                
                                {{-- Dropdown --}}
                                <div 
                                    x-show="open" 
                                    @click.away="open = false"
                                    x-transition
                                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-10"
                                    style="display: none;"
                                >
                                    <button 
                                        wire:click="editHabit({{ $habit->id }})"
                                        class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-gray-50 flex items-center gap-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Editar
                                    </button>
                                    <button 
                                        wire:click="confirmDelete({{ $habit->id }})"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Información del hábito --}}
                        <h3 class="font-bold text-slate-800 text-lg mb-2">{{ $habit->name }}</h3>
                        <p class="text-sm text-slate-500 mb-4">{{ $habit->description }}</p>

                        {{-- Categoría y frecuencia --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $habit->category === 'Salud' ? 'bg-green-50 text-green-700' : '' }}
                                {{ $habit->category === 'Trabajo' ? 'bg-blue-50 text-blue-700' : '' }}
                                {{ $habit->category === 'Desarrollo' ? 'bg-purple-50 text-purple-700' : '' }}
                                {{ $habit->category === 'Personal' ? 'bg-orange-50 text-orange-700' : '' }}
                            ">
                                {{ $habit->category }}
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                {{ $habit->frequency }}
                            </span>
                        </div>

                        {{-- Estadísticas --}}
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Racha actual</span>
                                <span class="font-bold text-slate-800">{{ $habit->current_streak ?? 0 }} días</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $habits->links() }}
            </div>
        @else
            {{-- Estado vacío --}}
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">No tienes hábitos aún</h3>
                <p class="text-slate-500 mb-6">Comienza creando tu primer hábito para alcanzar tus metas.</p>
                <button 
                    wire:click="$dispatch('open-modal', 'create-habit')"
                    class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-3 rounded-xl text-sm font-medium shadow-lg shadow-brand-500/30 inline-flex items-center gap-2 transition-all"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear mi primer hábito
                </button>
            </div>
        @endif
    </div>

    {{-- Modales --}}
    @include('livewire.habits.create-modal')
    @include('livewire.habits.edit-modal')
    @include('livewire.habits.delete-modal')
</div>
```

**💡 Explicación:**
- Muestra los hábitos en un **grid responsive** (1 columna en móvil, 3 en desktop)
- Cada tarjeta tiene un **menú dropdown** con opciones de editar/eliminar
- Usa **colores dinámicos** según la categoría del hábito
- Incluye un **estado vacío** amigable cuando no hay hábitos
- Todo el diseño sigue el estilo de `design6-2.html`

---

## Paso 3: Formulario de Crear Hábito

### 📁 Archivo: `resources/views/livewire/habits/create-modal.blade.php`

```blade
<x-modal name="create-habit" title="Crear Nuevo Hábito" max-width="lg">
    <form wire:submit.prevent="save">
        <div class="space-y-5">
            
            {{-- Nombre del hábito --}}
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                    Nombre del hábito <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="name"
                    wire:model="name"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:ring-opacity-20 transition-colors"
                    placeholder="Ej: Hacer ejercicio"
                >
                @error('name') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descripción --}}
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                    Descripción
                </label>
                <textarea 
                    id="description"
                    wire:model="description"
                    rows="3"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:ring-opacity-20 transition-colors resize-none"
                    placeholder="Describe tu hábito..."
                ></textarea>
                @error('description') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Categoría --}}
            <div>
                <label for="category" class="block text-sm font-medium text-slate-700 mb-2">
                    Categoría <span class="text-red-500">*</span>
                </label>
                <select 
                    id="category"
                    wire:model="category"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:ring-opacity-20 transition-colors"
                >
                    <option value="">Selecciona una categoría</option>
                    <option value="Salud">🏃 Salud</option>
                    <option value="Trabajo">💼 Trabajo</option>
                    <option value="Desarrollo">💻 Desarrollo</option>
                    <option value="Personal">🎯 Personal</option>
                </select>
                @error('category') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Icono --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Icono
                </label>
                <div class="grid grid-cols-8 gap-2">
                    @foreach(['🎯', '💪', '📚', '💻', '🏃', '🧘', '🎨', '🎵', '✍️', '🍎', '💤', '🌟', '🔥', '⚡', '🚀', '💡'] as $emoji)
                        <button 
                            type="button"
                            wire:click="$set('icon', '{{ $emoji }}')"
                            class="w-12 h-12 rounded-xl border-2 flex items-center justify-center text-2xl transition-all hover:scale-110
                                {{ $icon === $emoji ? 'border-brand-500 bg-brand-50' : 'border-gray-200 hover:border-brand-300' }}"
                        >
                            {{ $emoji }}
                        </button>
                    @endforeach
                </div>
                @error('icon') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Frecuencia --}}
            <div>
                <label for="frequency" class="block text-sm font-medium text-slate-700 mb-2">
                    Frecuencia <span class="text-red-500">*</span>
                </label>
                <select 
                    id="frequency"
                    wire:model="frequency"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:ring-opacity-20 transition-colors"
                >
                    <option value="">Selecciona la frecuencia</option>
                    <option value="Diario">Diario</option>
                    <option value="Semanal">Semanal</option>
                    <option value="Mensual">Mensual</option>
                </select>
                @error('frequency') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Botones de acción --}}
        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
            <button 
                type="button"
                x-on:click="$dispatch('close-modal')"
                class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-700 bg-gray-100 hover:bg-gray-200 transition-colors"
            >
                Cancelar
            </button>
            <button 
                type="submit"
                class="px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition-all active:scale-95 flex items-center gap-2"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Crear Hábito</span>
                <span wire:loading>
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creando...
                </span>
            </button>
        </div>
    </form>
</x-modal>
```

**💡 Explicación:**
- Formulario completo con todos los campos necesarios
- **Selector de iconos visual** con emojis
- **Validación en tiempo real** con Livewire
- **Indicador de carga** cuando se envía el formulario
- Diseño consistente con el resto de la aplicación

---

Continúo en el siguiente mensaje con los pasos 4, 5 y 6...


## Paso 4: Formulario de Editar Hábito

### 📁 Archivo: `resources/views/livewire/habits/edit-modal.blade.php`

```blade
<x-modal name="edit-habit" title="Editar Hábito" max-width="lg">
    @if($editingHabit)
    <form wire:submit.prevent="update">
        <div class="space-y-5">
            
            {{-- Nombre del hábito --}}
            <div>
                <label for="edit_name" class="block text-sm font-medium text-slate-700 mb-2">
                    Nombre del hábito <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="edit_name"
                    wire:model="editingHabit.name"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:ring-opacity-20 transition-colors"
                    placeholder="Ej: Hacer ejercicio"
                >
                @error('editingHabit.name') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descripción --}}
            <div>
                <label for="edit_description" class="block text-sm font-medium text-slate-700 mb-2">
                    Descripción
                </label>
                <textarea 
                    id="edit_description"
                    wire:model="editingHabit.description"
                    rows="3"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:ring-opacity-20 transition-colors resize-none"
                    placeholder="Describe tu hábito..."
                ></textarea>
                @error('editingHabit.description') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Categoría --}}
            <div>
                <label for="edit_category" class="block text-sm font-medium text-slate-700 mb-2">
                    Categoría <span class="text-red-500">*</span>
                </label>
                <select 
                    id="edit_category"
                    wire:model="editingHabit.category"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:ring-opacity-20 transition-colors"
                >
                    <option value="">Selecciona una categoría</option>
                    <option value="Salud">🏃 Salud</option>
                    <option value="Trabajo">💼 Trabajo</option>
                    <option value="Desarrollo">💻 Desarrollo</option>
                    <option value="Personal">🎯 Personal</option>
                </select>
                @error('editingHabit.category') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Icono --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Icono
                </label>
                <div class="grid grid-cols-8 gap-2">
                    @foreach(['🎯', '💪', '📚', '💻', '🏃', '🧘', '🎨', '🎵', '✍️', '🍎', '💤', '🌟', '🔥', '⚡', '🚀', '💡'] as $emoji)
                        <button 
                            type="button"
                            wire:click="$set('editingHabit.icon', '{{ $emoji }}')"
                            class="w-12 h-12 rounded-xl border-2 flex items-center justify-center text-2xl transition-all hover:scale-110
                                {{ $editingHabit['icon'] === $emoji ? 'border-brand-500 bg-brand-50' : 'border-gray-200 hover:border-brand-300' }}"
                        >
                            {{ $emoji }}
                        </button>
                    @endforeach
                </div>
                @error('editingHabit.icon') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Frecuencia --}}
            <div>
                <label for="edit_frequency" class="block text-sm font-medium text-slate-700 mb-2">
                    Frecuencia <span class="text-red-500">*</span>
                </label>
                <select 
                    id="edit_frequency"
                    wire:model="editingHabit.frequency"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:ring-opacity-20 transition-colors"
                >
                    <option value="">Selecciona la frecuencia</option>
                    <option value="Diario">Diario</option>
                    <option value="Semanal">Semanal</option>
                    <option value="Mensual">Mensual</option>
                </select>
                @error('editingHabit.frequency') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Botones de acción --}}
        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
            <button 
                type="button"
                x-on:click="$dispatch('close-modal')"
                class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-700 bg-gray-100 hover:bg-gray-200 transition-colors"
            >
                Cancelar
            </button>
            <button 
                type="submit"
                class="px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition-all active:scale-95 flex items-center gap-2"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Guardar Cambios</span>
                <span wire:loading>
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Guardando...
                </span>
            </button>
        </div>
    </form>
    @endif
</x-modal>
```

**💡 Explicación:**
- Similar al formulario de crear, pero **pre-carga los datos** del hábito
- Usa `editingHabit` para mantener los datos temporales
- Valida que exista un hábito antes de mostrar el formulario

---

## Paso 5: Modal de Confirmación de Eliminación

### 📁 Archivo: `resources/views/livewire/habits/delete-modal.blade.php`

```blade
<x-modal name="delete-habit" title="Confirmar Eliminación" max-width="md">
    @if($deletingHabit)
    <div>
        {{-- Icono de advertencia --}}
        <div class="flex items-center justify-center mb-4">
            <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>

        {{-- Mensaje --}}
        <div class="text-center mb-6">
            <h3 class="text-lg font-bold text-slate-800 mb-2">
                ¿Estás seguro de eliminar este hábito?
            </h3>
            <p class="text-slate-600 mb-4">
                Estás a punto de eliminar el hábito <strong>"{{ $deletingHabit->name }}"</strong>
            </p>
            <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                <p class="text-sm text-red-800">
                    ⚠️ Esta acción no se puede deshacer. Se perderán todos los datos y el historial asociado a este hábito.
                </p>
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="flex justify-end gap-3">
            <button 
                type="button"
                x-on:click="$dispatch('close-modal')"
                class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-700 bg-gray-100 hover:bg-gray-200 transition-colors"
            >
                Cancelar
            </button>
            <button 
                type="button"
                wire:click="delete"
                class="px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-500/30 transition-all active:scale-95 flex items-center gap-2"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Sí, eliminar
                </span>
                <span wire:loading>
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Eliminando...
                </span>
            </button>
        </div>
    </div>
    @endif
</x-modal>
```

**💡 Explicación:**
- Modal de confirmación con **advertencia visual** (icono rojo)
- Muestra el **nombre del hábito** que se va a eliminar
- Mensaje claro sobre la **irreversibilidad** de la acción
- Botón rojo para indicar peligro

---

## Paso 6: Componente Livewire del Controlador

### 📁 Archivo: `app/Livewire/Habits/HabitIndex.php`

```php
<?php

namespace App\Livewire\Habits;

use App\Models\Habit;
use Livewire\Component;
use Livewire\WithPagination;

class HabitIndex extends Component
{
    use WithPagination;

    // Propiedades para crear hábito
    public $name = '';
    public $description = '';
    public $category = '';
    public $icon = '🎯';
    public $frequency = '';

    // Propiedades para editar hábito
    public $editingHabit = null;

    // Propiedad para eliminar hábito
    public $deletingHabit = null;

    /**
     * Reglas de validación
     */
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category' => 'required|in:Salud,Trabajo,Desarrollo,Personal',
            'icon' => 'required|string|max:10',
            'frequency' => 'required|in:Diario,Semanal,Mensual',
        ];
    }

    /**
     * Mensajes de validación personalizados
     */
    protected $messages = [
        'name.required' => 'El nombre del hábito es obligatorio.',
        'name.max' => 'El nombre no puede tener más de 255 caracteres.',
        'category.required' => 'Debes seleccionar una categoría.',
        'category.in' => 'La categoría seleccionada no es válida.',
        'frequency.required' => 'Debes seleccionar una frecuencia.',
        'frequency.in' => 'La frecuencia seleccionada no es válida.',
        'icon.required' => 'Debes seleccionar un icono.',
    ];

    /**
     * Guardar nuevo hábito
     */
    public function save()
    {
        // Validar los datos
        $validated = $this->validate();

        // Crear el hábito asociado al usuario autenticado
        auth()->user()->habits()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'icon' => $validated['icon'],
            'frequency' => $validated['frequency'],
            'current_streak' => 0,
        ]);

        // Limpiar el formulario
        $this->reset(['name', 'description', 'category', 'icon', 'frequency']);
        $this->icon = '🎯'; // Resetear al icono por defecto

        // Cerrar el modal
        $this->dispatch('close-modal');

        // Mostrar mensaje de éxito
        session()->flash('success', '¡Hábito creado exitosamente! 🎉');

        // Resetear la paginación para mostrar el nuevo hábito
        $this->resetPage();
    }

    /**
     * Preparar hábito para editar
     */
    public function editHabit($habitId)
    {
        // Buscar el hábito del usuario
        $habit = auth()->user()->habits()->findOrFail($habitId);

        // Cargar los datos en el array de edición
        $this->editingHabit = $habit->toArray();

        // Abrir el modal de edición
        $this->dispatch('open-modal', 'edit-habit');
    }

    /**
     * Actualizar hábito existente
     */
    public function update()
    {
        // Validar los datos de edición
        $this->validate([
            'editingHabit.name' => 'required|string|max:255',
            'editingHabit.description' => 'nullable|string|max:500',
            'editingHabit.category' => 'required|in:Salud,Trabajo,Desarrollo,Personal',
            'editingHabit.icon' => 'required|string|max:10',
            'editingHabit.frequency' => 'required|in:Diario,Semanal,Mensual',
        ]);

        // Buscar y actualizar el hábito
        $habit = auth()->user()->habits()->findOrFail($this->editingHabit['id']);
        $habit->update([
            'name' => $this->editingHabit['name'],
            'description' => $this->editingHabit['description'],
            'category' => $this->editingHabit['category'],
            'icon' => $this->editingHabit['icon'],
            'frequency' => $this->editingHabit['frequency'],
        ]);

        // Limpiar el hábito en edición
        $this->editingHabit = null;

        // Cerrar el modal
        $this->dispatch('close-modal');

        // Mostrar mensaje de éxito
        session()->flash('success', '¡Hábito actualizado exitosamente! ✨');
    }

    /**
     * Confirmar eliminación de hábito
     */
    public function confirmDelete($habitId)
    {
        // Buscar el hábito
        $habit = auth()->user()->habits()->findOrFail($habitId);

        // Guardar el hábito a eliminar
        $this->deletingHabit = $habit;

        // Abrir el modal de confirmación
        $this->dispatch('open-modal', 'delete-habit');
    }

    /**
     * Eliminar hábito
     */
    public function delete()
    {
        if ($this->deletingHabit) {
            // Eliminar el hábito
            $this->deletingHabit->delete();

            // Limpiar la propiedad
            $this->deletingHabit = null;

            // Cerrar el modal
            $this->dispatch('close-modal');

            // Mostrar mensaje de éxito
            session()->flash('success', 'Hábito eliminado correctamente.');

            // Resetear la paginación
            $this->resetPage();
        }
    }

    /**
     * Renderizar el componente
     */
    public function render()
    {
        return view('livewire.habits.index', [
            'habits' => auth()->user()->habits()->latest()->paginate(9)
        ]);
    }
}
```

**💡 Explicación:**
- **Maneja todas las operaciones CRUD** (Crear, Leer, Actualizar, Eliminar)
- **Validación completa** con mensajes personalizados
- **Seguridad**: Solo permite manipular hábitos del usuario autenticado
- **Feedback al usuario** con mensajes flash
- **Paginación** para manejar muchos hábitos

---

## Paso 7: Modelo Habit

### 📁 Archivo: `app/Models/Habit.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    use HasFactory;

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'icon',
        'frequency',
        'current_streak',
    ];

    /**
     * Relación: Un hábito pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el color de la categoría
     */
    public function getCategoryColorAttribute()
    {
        return match($this->category) {
            'Salud' => 'green',
            'Trabajo' => 'blue',
            'Desarrollo' => 'purple',
            'Personal' => 'orange',
            default => 'gray',
        };
    }
}
```

---

## Paso 8: Migración de Base de Datos

### 📁 Archivo: `database/migrations/xxxx_xx_xx_create_habits_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración
     */
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category'); // Salud, Trabajo, Desarrollo, Personal
            $table->string('icon')->default('🎯');
            $table->string('frequency'); // Diario, Semanal, Mensual
            $table->integer('current_streak')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Revertir la migración
     */
    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
```

---

## Paso 9: Actualizar el Modelo User

### 📁 Archivo: `app/Models/User.php`

Agrega esta relación dentro de la clase User:

```php
/**
 * Relación: Un usuario tiene muchos hábitos
 */
public function habits()
{
    return $this->hasMany(Habit::class);
}
```

---

## Paso 10: Ruta

### 📁 Archivo: `routes/web.php`

```php
use App\Livewire\Habits\HabitIndex;

Route::middleware(['auth'])->group(function () {
    // ... otras rutas
    
    Route::get('/habits', HabitIndex::class)->name('habits.index');
});
```

---

## 🎯 Resumen de Archivos Creados

✅ **Vistas Blade:**
1. `resources/views/components/modal.blade.php` - Modal reutilizable
2. `resources/views/livewire/habits/index.blade.php` - Lista de hábitos
3. `resources/views/livewire/habits/create-modal.blade.php` - Formulario crear
4. `resources/views/livewire/habits/edit-modal.blade.php` - Formulario editar
5. `resources/views/livewire/habits/delete-modal.blade.php` - Confirmación eliminar

✅ **Componentes Livewire:**
6. `app/Livewire/Habits/HabitIndex.php` - Controlador principal

✅ **Modelos:**
7. `app/Models/Habit.php` - Modelo de hábito

✅ **Migraciones:**
8. `database/migrations/xxxx_create_habits_table.php` - Tabla de hábitos

✅ **Rutas:**
9. Ruta en `routes/web.php`

---

## 🚀 Comandos para Ejecutar

```bash
# 1. Crear la migración (si no existe)
php artisan make:migration create_habits_table

# 2. Ejecutar las migraciones
php artisan migrate

# 3. Crear el componente Livewire (si no existe)
php artisan make:livewire Habits/HabitIndex

# 4. Limpiar caché
php artisan optimize:clear
```

---

## 💡 Próximos Pasos de Aprendizaje

Ahora que tienes el código completo, te recomiendo:

1. **Copia cada archivo** en su ubicación correspondiente
2. **Lee los comentarios** en el código para entender qué hace cada parte
3. **Prueba la funcionalidad** creando, editando y eliminando hábitos
4. **Experimenta** cambiando colores, textos o agregando campos
5. **Pregunta** si algo no te queda claro

¿Quieres que te explique alguna parte específica del código con más detalle? 😊
