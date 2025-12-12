<div class="min-h-screen bg-white">
    {{-- Header estilo Notion --}}
    <div class="h-12 flex items-center justify-between px-8 sticky top-0 bg-white/95 backdrop-blur z-20 border-b border-transparent hover:border-gray-100 transition">
        <a href="{{ route('admin.habits.index') }}" 
           wire:navigate
           class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 cursor-pointer transition">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            <span>Volver al Dashboard</span>
        </a>
        <div class="flex gap-3">
            <a href="{{ route('admin.habits.index') }}" 
               wire:navigate
               class="text-sm font-medium text-gray-500 hover:bg-gray-100 px-3 py-1.5 rounded transition">
                Cancelar
            </a>
            <button 
                type="submit"
                form="habit-form"
                class="text-sm font-medium text-white bg-[#2383E2] hover:bg-[#1B74C9] px-4 py-1.5 rounded shadow-sm transition"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="update">Guardar Cambios</span>
                <span wire:loading wire:target="update">Guardando...</span>
            </button>
        </div>
    </div>

    {{-- Form Content estilo Notion --}}
    <div class="max-w-3xl mx-auto px-8 py-10 pb-32">
        <form wire:submit.prevent="update" id="habit-form">
            
            {{-- Icono grande --}}
            <div class="group relative w-fit mb-6 -ml-1">
                <div class="text-7xl hover:bg-gray-100 p-2 rounded cursor-pointer transition select-none">
                    {{ $icon ?? '✨' }}
                </div>
            </div>

            {{-- Título del hábito --}}
            <div class="mb-10">
                <input 
                    type="text" 
                    wire:model="name"
                    placeholder="Nombre del hábito" 
                    class="w-full text-4xl font-bold placeholder-gray-300 border-none p-0 focus:ring-0 text-[#37352F] outline-none bg-transparent">
                @error('name') 
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                
                {{-- Estado activo --}}
                <div class="flex items-center gap-2 mt-3">
                    <span class="text-xs text-gray-400">Estado:</span>
                    <button 
                        type="button"
                        wire:click="$toggle('is_active')"
                        class="flex items-center gap-2 text-xs px-2 py-1 rounded transition
                            {{ $is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        {{ $is_active ? 'Activo' : 'Inactivo' }}
                    </button>
                </div>

                {{-- Botón de completar hábito --}}
                @if($habit->isScheduledForToday())
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <button 
                            type="button"
                            wire:click="toggleHabit"
                            class="w-full py-2.5 rounded text-sm font-medium text-center transition flex items-center justify-center gap-2
                                {{ $habit->isCompletedToday() 
                                    ? 'bg-[#EDF3EC] text-[#18443B] border border-transparent hover:bg-[#DBEDDB]' 
                                    : 'bg-[#2383E2] text-white hover:bg-[#1B74C9] shadow-sm' }}">
                            <span>{{ $habit->isCompletedToday() ? '✓' : '○' }}</span>
                            {{ $habit->isCompletedToday() ? 'Completado hoy' : 'Marcar como completado' }}
                        </button>
                    </div>
                @endif
            </div>

            <div class="space-y-8">

                {{-- Descripción --}}
                <div>
                    <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-1.5">Descripción</label>
                    <textarea 
                        wire:model="description"
                        rows="3"
                        class="w-full bg-transparent border border-[rgba(55,53,47,0.16)] rounded px-3 py-2 text-sm text-[#37352F] transition-all hover:border-[rgba(55,53,47,0.3)] focus:border-[#2383E2] focus:ring-2 focus:ring-[rgba(35,131,226,0.2)] outline-none resize-none"
                        placeholder="Describe tu rutina..."></textarea>
                    @error('description') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Grid de propiedades --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    
                    <div class="space-y-6">
                        {{-- Categoría --}}
                        <div>
                            <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-1.5">Categoría</label>
                            <div class="relative">
                                <select 
                                    wire:model="category_id"
                                    class="w-full bg-white border border-[rgba(55,53,47,0.16)] rounded px-3 py-2 text-sm text-[#37352F] transition-all hover:border-[rgba(55,53,47,0.3)] focus:border-[#2383E2] focus:ring-2 focus:ring-[rgba(35,131,226,0.2)] outline-none appearance-none cursor-pointer">
                                    <option value="">Selecciona una categoría</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ !$cat->is_active ? 'class=text-gray-400' : '' }}>
                                            {{ $cat->icon }} {{ $cat->name }}{{ !$cat->is_active ? ' (Obsoleta)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3 top-3 pointer-events-none text-gray-400">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                </div>
                            </div>
                            @error('category_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Frecuencia --}}
                        <div>
                            <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-1.5">Frecuencia</label>
                            <div class="relative">
                                <select 
                                    wire:model.live="frequency"
                                    class="w-full bg-white border border-[rgba(55,53,47,0.16)] rounded px-3 py-2 text-sm text-[#37352F] transition-all hover:border-[rgba(55,53,47,0.3)] focus:border-[#2383E2] focus:ring-2 focus:ring-[rgba(35,131,226,0.2)] outline-none appearance-none cursor-pointer">
                                    @foreach($frequencies as $freq)
                                        <option value="{{ $freq->value }}">{{ $freq->label() }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3 top-3 pointer-events-none text-gray-400">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                </div>
                            </div>
                            @error('frequency') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-6">
                        {{-- Dificultad --}}
                        <div>
                            <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-1.5">Dificultad (XP)</label>
                            <div class="relative">
                                <select 
                                    wire:model="difficulty_id"
                                    class="w-full bg-white border border-[rgba(55,53,47,0.16)] rounded px-3 py-2 text-sm text-[#37352F] transition-all hover:border-[rgba(55,53,47,0.3)] focus:border-[#2383E2] focus:ring-2 focus:ring-[rgba(35,131,226,0.2)] outline-none appearance-none cursor-pointer">
                                    <option value="">Selecciona una dificultad</option>
                                    @foreach($difficulties as $diff)
                                        <option value="{{ $diff->id }}" {{ !$diff->is_active ? 'class=text-gray-400' : '' }}>
                                            {{ $diff->icon }} {{ $diff->name }} ({{ $diff->points }} pts){{ !$diff->is_active ? ' (Obsoleta)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3 top-3 pointer-events-none text-gray-400">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                </div>
                            </div>
                            @error('difficulty_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Hora y Pomodoros --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-1.5">Hora</label>
                                <input 
                                    type="time" 
                                    wire:model="time"
                                    class="w-full bg-white border border-[rgba(55,53,47,0.16)] rounded px-3 py-2 text-sm text-[#37352F] transition-all hover:border-[rgba(55,53,47,0.3)] focus:border-[#2383E2] focus:ring-2 focus:ring-[rgba(35,131,226,0.2)] outline-none">
                                @error('time') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-1.5">Pomodoros</label>
                                <input 
                                    type="number" 
                                    wire:model="estimated_pomodoros"
                                    min="1"
                                    class="w-full bg-white border border-[rgba(55,53,47,0.16)] rounded px-3 py-2 text-sm text-[#37352F] transition-all hover:border-[rgba(55,53,47,0.3)] focus:border-[#2383E2] focus:ring-2 focus:ring-[rgba(35,131,226,0.2)] outline-none">
                                @error('estimated_pomodoros') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>

                <hr class="border-gray-100">

                {{-- Días de la semana (solo si frecuencia es weekly o custom) --}}
                @if($frequency === 'weekly' || $frequency === 'custom')
                    <div x-data="{ show: true }" x-show="show" x-transition>
                        <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-3">Días de la semana</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($daysOfWeek as $dayNum => $dayName)
                                <label class="flex items-center gap-2 p-3 rounded border cursor-pointer transition-all
                                    {{ in_array($dayNum, $selectedDays) ? 'border-[#2383E2] bg-[#E7F3F8]' : 'border-[rgba(55,53,47,0.16)] hover:bg-gray-50' }}">
                                    <input 
                                        type="checkbox" 
                                        wire:model="selectedDays" 
                                        value="{{ $dayNum }}"
                                        class="w-4 h-4 text-[#2383E2] border-gray-300 rounded focus:ring-[#2383E2]">
                                    <span class="text-sm font-medium text-[#37352F]">{{ $dayName }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedDays') 
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                {{-- Icono --}}
                <div>
                    <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-2">Seleccionar Icono</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['🎯', '💪', '📚', '💻', '🏃', '🧘', '🎨', '🎵', '✍️', '🍎', '💤', '🌟', '🔥', '⚡', '🚀', '💡', '🎮', '📝', '🧠', '💰', '🏠', '🌱', '⏰', '🎓'] as $emoji)
                            <button 
                                type="button"
                                wire:click="$set('icon', '{{ $emoji }}')"
                                class="w-10 h-10 rounded flex items-center justify-center text-xl transition-all hover:bg-[#EFEFED]
                                    {{ $icon === $emoji ? 'bg-[#E7F3F8] shadow-[inset_0_0_0_1px_#2383E2]' : '' }}">
                                {{ $emoji }}
                            </button>
                        @endforeach
                    </div>
                    @error('icon') 
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Color --}}
                <div>
                    <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-3">Color de Etiqueta</label>
                    <div class="flex gap-4 pl-1">
                        @foreach(['#EB5757', '#F2994A', '#F2C94C', '#27AE60', '#2383E2', '#9B51E0', '#808080'] as $colorOption)
                            <button 
                                type="button"
                                wire:click="$set('color', '{{ $colorOption }}')"
                                class="w-6 h-6 rounded-full transition-opacity hover:opacity-80 relative"
                                style="background-color: {{ $colorOption }}">
                                @if($color === $colorOption)
                                    <span class="absolute -top-0.5 -left-0.5 -right-0.5 -bottom-0.5 border-2 border-[#2383E2] rounded-full"></span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    @error('color') 
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Recordatorio --}}
                <div class="pt-4">
                    <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🔔</span>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Activar Recordatorio</div>
                                <div class="text-xs text-gray-500">Recibirás una notificación push.</div>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="reminder_enabled" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                        </label>
                    </div>
                    
                    @if($reminder_enabled)
                        <div x-data="{ show: true }" x-show="show" x-transition class="mt-3 ml-11">
                            <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-1.5">Hora del recordatorio</label>
                            <input 
                                type="time" 
                                wire:model="reminder_time"
                                class="w-full sm:w-48 bg-white border border-[rgba(55,53,47,0.16)] rounded px-3 py-2 text-sm text-[#37352F] transition-all hover:border-[rgba(55,53,47,0.3)] focus:border-[#2383E2] focus:ring-2 focus:ring-[rgba(35,131,226,0.2)] outline-none">
                            @error('reminder_time') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                {{-- Acciones peligrosas --}}
                <div class="pt-8 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button 
                            type="button"
                            wire:click="archive"
                            wire:confirm="¿Estás seguro de {{ $is_active ? 'archivar' : 'desarchivar' }} este hábito?"
                            class="px-4 py-2 rounded text-sm font-medium {{ $is_active ? 'text-orange-700 bg-orange-50 border-orange-200' : 'text-green-700 bg-green-50 border-green-200' }} border hover:{{ $is_active ? 'bg-orange-100' : 'bg-green-100' }} transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                            {{ $is_active ? 'Archivar' : 'Desarchivar' }}
                        </button>
                        <button 
                            type="button"
                            x-data
                            @click="$dispatch('open-modal', 'delete-habit')"
                            class="px-4 py-2 rounded text-sm font-medium text-red-700 bg-red-50 border border-red-200 hover:bg-red-100 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Eliminar
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- Modal de Confirmación de Eliminación --}}
    <x-modal name="delete-habit" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>

            <div class="text-center mb-6">
                <h3 class="text-lg font-bold text-slate-800 mb-2">
                    ¿Estás seguro de eliminar este hábito?
                </h3>
                <p class="text-slate-600 mb-4">
                    Estás a punto de eliminar el hábito <strong>"{{ $name }}"</strong>
                </p>
                <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                    <p class="text-sm text-red-800">
                        ⚠️ Esta acción no se puede deshacer. Se perderán todos los datos y el historial asociado a este hábito.
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button 
                    type="button"
                    x-on:click="$dispatch('close-modal')"
                    class="px-5 py-2.5 rounded text-sm font-medium text-slate-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                    Cancelar
                </button>
                <button 
                    type="button"
                    wire:click="delete"
                    class="px-5 py-2.5 rounded text-sm font-medium text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-500/30 transition-all active:scale-95 flex items-center gap-2"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="delete">
                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Sí, eliminar
                    </span>
                    <span wire:loading wire:target="delete" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Eliminando...
                    </span>
                </button>
            </div>
        </div>
    </x-modal>

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="update,delete,archive" class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 shadow-xl">
            <div class="flex items-center gap-3">
                <svg class="animate-spin h-5 w-5 text-[#2383E2]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-slate-700">Procesando...</span>
            </div>
        </div>
    </div>
</div>
