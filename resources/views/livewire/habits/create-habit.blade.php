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
                <span wire:loading.remove wire:target="save">Guardar Hábito</span>
                <span wire:loading wire:target="save">Guardando...</span>
            </button>
        </div>
    </div>

    {{-- Form Content estilo Notion --}}
    <div class="max-w-3xl mx-auto px-8 py-10 pb-32">
        <form wire:submit.prevent="save" id="habit-form">
            
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
                    class="w-full text-4xl font-bold placeholder-gray-300 border-none p-0 focus:ring-0 text-[#37352F] outline-none bg-transparent"
                    autofocus>
                @error('name') 
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                                        <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
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
                                        <option value="{{ $diff->id }}">{{ $diff->icon }} {{ $diff->name }} ({{ $diff->points }} pts)</option>
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

            </div>
        </form>
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="save" class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 shadow-xl">
            <div class="flex items-center gap-3">
                <svg class="animate-spin h-5 w-5 text-[#2383E2]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-slate-700">Guardando hábito...</span>
            </div>
        </div>
    </div>
</div>
