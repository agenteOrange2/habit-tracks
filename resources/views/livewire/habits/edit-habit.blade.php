<div class="min-h-screen bg-[#FAFAFA] dark:bg-[#191919]">
    {{-- Header --}}
    <div class="h-14 flex items-center justify-between px-4 sm:px-8 bg-white dark:bg-[#252525] border-b border-[#E9E9E7] dark:border-[#3E3E3A]">
        <a href="{{ route('admin.habits.index') }}" wire:navigate
           class="flex items-center gap-2 text-sm text-[#787774] dark:text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED] transition">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            <span class="hidden sm:inline">Volver</span>
        </a>
        <div class="flex gap-2 sm:gap-3">
            <a href="{{ route('admin.habits.index') }}" wire:navigate
               class="text-sm font-medium text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] px-3 py-1.5 rounded transition hidden sm:block">
                Cancelar
            </a>
            <button type="submit" form="habit-form"
                class="text-sm cursor-pointer font-medium text-white bg-[#2383E2] hover:bg-[#1B74C9] px-4 py-1.5 rounded shadow-sm transition"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="update">Guardar</span>
                <span wire:loading wire:target="update">...</span>
            </button>
        </div>
    </div>

    {{-- Form --}}
    <div class="max-w-3xl mx-auto px-4 sm:px-8 py-6 sm:py-10 pb-32">
        <form wire:submit.prevent="update" id="habit-form">
            
            {{-- Icono --}}
            <div class="mb-4 sm:mb-6">
                <div class="text-5xl sm:text-7xl hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] p-2 rounded cursor-pointer transition select-none inline-block">
                    {{ $icon ?? '✨' }}
                </div>
            </div>

            {{-- Título --}}
            <div class="mb-6 sm:mb-8">
                <input type="text" wire:model="name" placeholder="Nombre del hábito"
                    class="w-full text-2xl sm:text-4xl font-bold placeholder-[#9B9A97] border-none p-0 focus:ring-0 text-[#37352F] dark:text-[#EFEFED] outline-none bg-transparent">
                @error('name') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                
                {{-- Estado --}}
                <div class="flex items-center gap-2 mt-3">
                    <span class="text-xs text-[#9B9A97]">Estado:</span>
                    <button type="button" wire:click="$toggle('is_active')"
                        class="flex items-center gap-2 text-xs px-2 py-1 rounded transition
                            {{ $is_active ? 'bg-[#DBEDDB] dark:bg-[#1B3B2D] text-[#18443B] dark:text-[#5BA572]' : 'bg-[#E3E2E0] dark:bg-[#3E3E3A] text-[#787774] dark:text-[#9B9A97]' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $is_active ? 'bg-[#27AE60]' : 'bg-[#9B9A97]' }}"></span>
                        {{ $is_active ? 'Activo' : 'Inactivo' }}
                    </button>
                </div>

                {{-- Botón completar --}}
                @if($habit->isScheduledForToday())
                    <div class="mt-4 pt-4 border-t border-[#E9E9E7] dark:border-[#3E3E3A]">
                        <button type="button" wire:click="toggleHabit"
                            class="w-full py-2.5 rounded text-sm font-medium text-center transition flex items-center justify-center gap-2
                                {{ $habit->isCompletedToday() 
                                    ? 'bg-[#DBEDDB] dark:bg-[#1B3B2D] text-[#18443B] dark:text-[#5BA572]' 
                                    : 'bg-[#2383E2] text-white hover:bg-[#1B74C9] shadow-sm' }}">
                            <span>{{ $habit->isCompletedToday() ? '✓' : '○' }}</span>
                            {{ $habit->isCompletedToday() ? 'Completado hoy' : 'Marcar como completado' }}
                        </button>
                    </div>
                @endif
            </div>

            <div class="space-y-6 sm:space-y-8">

                {{-- Descripción --}}
                <div>
                    <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-1.5">Descripción</label>
                    <textarea wire:model="description" rows="3" placeholder="Describe tu rutina..."
                        class="w-full bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] transition-all focus:border-[#2383E2] focus:ring-2 focus:ring-[#2383E2]/20 outline-none resize-none placeholder-[#9B9A97]"></textarea>
                    @error('description') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-x-8 sm:gap-y-6">
                    
                    {{-- Categoría --}}
                    <div>
                        <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-1.5">Categoría</label>
                        <div class="relative">
                            <select wire:model="category_id"
                                class="w-full bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] transition-all focus:border-[#2383E2] outline-none appearance-none cursor-pointer">
                                <option value="">Selecciona categoría</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <svg class="absolute right-3 top-3 pointer-events-none text-[#9B9A97]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                        </div>
                        @error('category_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Frecuencia --}}
                    <div>
                        <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-1.5">Frecuencia</label>
                        <div class="relative">
                            <select wire:model.live="frequency"
                                class="w-full bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] transition-all focus:border-[#2383E2] outline-none appearance-none cursor-pointer">
                                @foreach($frequencies as $freq)
                                    <option value="{{ $freq->value }}">{{ $freq->label() }}</option>
                                @endforeach
                            </select>
                            <svg class="absolute right-3 top-3 pointer-events-none text-[#9B9A97]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                        </div>
                        @error('frequency') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Dificultad --}}
                    <div>
                        <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-1.5">Dificultad (XP)</label>
                        <div class="relative">
                            <select wire:model="difficulty_id"
                                class="w-full bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] transition-all focus:border-[#2383E2] outline-none appearance-none cursor-pointer">
                                <option value="">Selecciona dificultad</option>
                                @foreach($difficulties as $diff)
                                    <option value="{{ $diff->id }}">{{ $diff->icon }} {{ $diff->name }} — {{ $diff->points }} XP</option>
                                @endforeach
                            </select>
                            <svg class="absolute right-3 top-3 pointer-events-none text-[#9B9A97]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                        </div>
                        @error('difficulty_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Hora --}}
                    <div>
                        <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-1.5">Hora</label>
                        <input type="time" wire:model="time"
                            class="w-full bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] transition-all focus:border-[#2383E2] outline-none">
                        @error('time') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Pomodoros --}}
                    <div>
                        <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-1.5">Pomodoros</label>
                        <input type="number" wire:model="estimated_pomodoros" min="1"
                            class="w-full bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] transition-all focus:border-[#2383E2] outline-none">
                        @error('estimated_pomodoros') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <hr class="border-[#E9E9E7] dark:border-[#3E3E3A]">

                {{-- Días de la semana --}}
                @if($frequency === 'weekly' || $frequency === 'custom')
                    <div>
                        <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-3">Días de la semana</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
                            @foreach($daysOfWeek as $dayNum => $dayName)
                                <label class="flex items-center gap-2 p-2.5 sm:p-3 rounded border cursor-pointer transition-all
                                    {{ in_array($dayNum, $selectedDays) ? 'border-[#2383E2] bg-[#E7F3F8] dark:bg-[#1B3A52]' : 'border-[#E9E9E7] dark:border-[#3E3E3A] hover:bg-[#F7F7F5] dark:hover:bg-[#2A2A2A]' }}">
                                    <input type="checkbox" wire:model="selectedDays" value="{{ $dayNum }}"
                                        class="w-4 h-4 text-[#2383E2] border-[#E9E9E7] dark:border-[#3E3E3A] rounded focus:ring-[#2383E2]">
                                    <span class="text-sm font-medium text-[#37352F] dark:text-[#EFEFED]">{{ $dayName }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedDays') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- Icono selector --}}
                <div>
                    <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-2">Seleccionar Icono</label>
                    <div class="flex flex-wrap gap-1.5 sm:gap-2">
                        @foreach(['🎯', '💪', '📚', '💻', '🏃', '🧘', '🎨', '🎵', '✍️', '🍎', '💤', '🌟', '🔥', '⚡', '🚀', '💡', '🎮', '📝', '🧠', '💰', '🏠', '🌱', '⏰', '🎓'] as $emoji)
                            <button type="button" wire:click="$set('icon', '{{ $emoji }}')"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded flex items-center justify-center text-lg sm:text-xl transition-all hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A]
                                    {{ $icon === $emoji ? 'bg-[#E7F3F8] dark:bg-[#1B3A52] ring-1 ring-[#2383E2]' : '' }}">
                                {{ $emoji }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Color --}}
                <div>
                    <label class="block text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wider mb-3">Color</label>
                    <div class="flex gap-3 sm:gap-4">
                        @foreach(['#EB5757', '#F2994A', '#F2C94C', '#27AE60', '#2383E2', '#9B51E0', '#808080'] as $colorOption)
                            <button type="button" wire:click="$set('color', '{{ $colorOption }}')"
                                class="w-6 h-6 sm:w-7 sm:h-7 rounded-full transition-transform hover:scale-110 relative"
                                style="background-color: {{ $colorOption }}">
                                @if($color === $colorOption)
                                    <span class="absolute -inset-1 border-2 border-[#2383E2] rounded-full"></span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Recordatorio --}}
                <div class="bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg p-3 sm:p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🔔</span>
                            <div>
                                <div class="text-sm font-medium text-[#37352F] dark:text-[#EFEFED]">Recordatorio</div>
                                <div class="text-xs text-[#9B9A97]">Notificación push</div>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="reminder_enabled" class="sr-only peer">
                            <div class="w-9 h-5 bg-[#E3E2E0] dark:bg-[#3E3E3A] rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#2383E2]"></div>
                        </label>
                    </div>
                    
                    @if($reminder_enabled)
                        <div class="mt-3 pt-3 border-t border-[#E9E9E7] dark:border-[#3E3E3A]">
                            <input type="time" wire:model="reminder_time"
                                class="w-full sm:w-48 bg-[#F7F7F5] dark:bg-[#1F1F1F] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] outline-none">
                        </div>
                    @endif
                </div>

                {{-- Acciones peligrosas --}}
                <div class="pt-6 border-t border-[#E9E9E7] dark:border-[#3E3E3A]">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" wire:click="archive"
                            wire:confirm="¿Estás seguro de {{ $is_active ? 'archivar' : 'desarchivar' }} este hábito?"
                            class="px-4 cursor-pointer py-2 rounded text-sm font-medium transition flex items-center justify-center gap-2
                                {{ $is_active 
                                    ? 'text-[#CB912F] bg-[#FBF3DB] dark:bg-[#3D3422] border border-[#F2C94C]/30' 
                                    : 'text-[#27AE60] bg-[#DBEDDB] dark:bg-[#1B3B2D] border border-[#27AE60]/30' }}">
                            📦 {{ $is_active ? 'Archivar' : 'Desarchivar' }}
                        </button>
                        <button type="button" x-data @click="$dispatch('open-modal', 'delete-habit')"
                            class="px-4 py-2 cursor-pointer rounded text-sm font-medium text-[#D44C47] bg-[#FFE2DD] dark:bg-[#3D2222] border border-[#EB5757]/30 transition flex items-center justify-center gap-2">
                            🗑️ Eliminar
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- Modal Eliminar --}}
    <x-modal name="delete-habit" maxWidth="md">
        <div class="p-6 bg-white dark:bg-[#252525]">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-[#FFE2DD] dark:bg-[#3D2222] flex items-center justify-center">
                    <span class="text-3xl">⚠️</span>
                </div>
            </div>

            <div class="text-center mb-6">
                <h3 class="text-lg font-bold text-[#37352F] dark:text-[#EFEFED] mb-2">
                    ¿Eliminar este hábito?
                </h3>
                <p class="text-[#787774] dark:text-[#9B9A97] mb-4">
                    Estás a punto de eliminar <strong class="text-[#37352F] dark:text-[#EFEFED]">"{{ $name }}"</strong>
                </p>
                <div class="bg-[#FFE2DD] dark:bg-[#3D2222] border border-[#EB5757]/20 rounded-lg p-4">
                    <p class="text-sm text-[#D44C47] dark:text-[#EB5757]">
                        Esta acción no se puede deshacer. Se perderá todo el historial.
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal')"
                    class="px-4 py-2 rounded text-sm font-medium text-[#787774] dark:text-[#9B9A97] bg-[#F7F7F5] dark:bg-[#1F1F1F] hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] transition">
                    Cancelar
                </button>
                <button type="button" wire:click="delete"
                    class="px-4 py-2 rounded text-sm font-medium text-white bg-[#EB5757] hover:bg-[#D44C47] transition flex items-center gap-2"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="delete">🗑️ Eliminar</span>
                    <span wire:loading wire:target="delete">Eliminando...</span>
                </button>
            </div>
        </div>
    </x-modal>

    {{-- Loading --}}
    <div wire:loading wire:target="update,delete,archive" class="fixed inset-0 bg-black/30 dark:bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-[#252525] rounded-lg p-5 shadow-xl border border-[#E9E9E7] dark:border-[#3E3E3A]">
            <div class="flex items-center gap-3">
                <svg class="animate-spin h-5 w-5 text-[#2383E2]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-[#37352F] dark:text-[#EFEFED]">Procesando...</span>
            </div>
        </div>
    </div>
</div>
