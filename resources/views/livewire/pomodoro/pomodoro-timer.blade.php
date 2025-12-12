<div class="min-h-screen bg-white pb-20" 
     x-data="pomodoroTimer()" 
     x-init="init()"
     wire:ignore.self>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .notion-border { border: 1px solid #E9E9E7; }
        .notion-bg-hover:hover { background-color: #F7F7F5; }
        .text-gray-notion { color: #9B9A97; }
        
        .prop-input {
            background: transparent;
            border: 1px solid transparent;
            border-radius: 4px;
            padding: 2px 6px;
            color: #37352F;
            font-size: 14px;
            text-align: right;
            transition: all 0.2s;
            width: 60px;
        }
        .prop-input:hover { background-color: #EFEFED; }
        .prop-input:focus { 
            background-color: white; 
            border-color: #2383E2; 
            outline: none; 
            box-shadow: 0 0 0 2px rgba(35, 131, 226, 0.1);
        }

        .notion-toggle {
            position: relative; display: inline-block; width: 32px; height: 18px;
        }
        .notion-toggle input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
            background-color: #E3E2E0; transition: .4s; border-radius: 34px;
        }
        .slider:before {
            position: absolute; content: ""; height: 14px; width: 14px; left: 2px; bottom: 2px;
            background-color: white; transition: .4s; border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        input:checked + .slider { background-color: #2383E2; }
        input:checked + .slider:before { transform: translateX(14px); }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #E3E2E0; border-radius: 3px; }
        
        .progress-circle { transition: stroke-dashoffset 0.3s ease; }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(235, 87, 87, 0.4); }
            50% { box-shadow: 0 0 0 20px rgba(235, 87, 87, 0); }
        }
        .timer-running { animation: pulse-glow 2s infinite; }
    </style>

    {{-- Focus Mode Overlay - Notion Style --}}
    <div x-show="focusMode" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         class="fixed inset-0 bg-white z-50 flex flex-col"
         @keydown.escape.window="focusMode && toggleFocusMode()">
        
        {{-- Focus Mode Header --}}
        <header class="border-b border-[#E9E9E7] px-6 py-3">
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🍅</span>
                    <span class="font-semibold text-[#37352F]">Focus Mode</span>
                </div>
                <button @click="toggleFocusMode()"
                        class="flex items-center gap-2 px-3 py-1.5 text-[#9B9A97] hover:text-[#37352F] hover:bg-[#F7F7F5] rounded transition">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
                    <span class="text-sm">Salir</span>
                </button>
        </div>
        </header>

        {{-- Focus Mode Content --}}
        <div class="flex-1 flex items-center justify-center p-8">
            <div class="text-center max-w-md w-full">
                
                {{-- Timer Circle --}}
                <div class="relative mb-8 inline-block">
                    <svg class="w-80 h-80" viewBox="0 0 320 320" style="transform: rotate(-90deg);">
                        <circle cx="160" cy="160" r="150" stroke="#F1F1EF" stroke-width="6" fill="none" />
                        <circle cx="160" cy="160" r="150" 
                                :stroke="isBreak ? '#27AE60' : '#EB5757'"
                                stroke-width="6" 
                                fill="none"
                                stroke-dasharray="942"
                                :stroke-dashoffset="942 * (1 - getProgress())"
                                stroke-linecap="round"
                                class="progress-circle" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-8xl font-bold text-[#37352F] tracking-tighter tabular-nums mb-3"
                             x-text="formatTime(remainingSeconds)">
                        </div>
                        <span class="px-3 py-1 rounded text-sm font-medium"
                              :class="{
                                  'bg-[#FCE8E6] text-[#EB5757]': isRunning && !isBreak,
                                  'bg-[#DDEDEA] text-[#27AE60]': isRunning && isBreak,
                                  'bg-[#FFF4E0] text-[#9A6D02]': isPaused,
                                  'bg-[#E7F3F8] text-[#154664]': isIdle
                              }">
                            <span x-show="isIdle">Listo para comenzar</span>
                            <span x-show="isRunning && !isBreak">🎯 Enfocado</span>
                            <span x-show="isRunning && isBreak">☕ Descanso</span>
                            <span x-show="isPaused">⏸ Pausado</span>
                    </span>
            </div>
        </div>

                {{-- Focus Mode Controls --}}
                <div class="flex justify-center gap-3 mb-8">
                    <template x-if="isIdle">
                        <button @click="$wire.startTimer()"
                                class="bg-[#2383E2] hover:bg-[#1B74C9] text-white text-base font-medium px-10 py-3 rounded shadow-sm transition flex items-center gap-2">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            Iniciar
                        </button>
                    </template>
                    
                    <template x-if="isRunning && !isBreak">
                        <div class="flex gap-3">
                            <button @click="$wire.pauseTimer()"
                                    class="bg-[#F2C94C] hover:bg-[#E5BC3B] text-[#37352F] font-medium px-8 py-3 rounded shadow-sm transition flex items-center gap-2">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h4v16H6zM14 4h4v16h-4z"/></svg>
                                Pausar
                            </button>
                            <button @click="$wire.stopTimer()"
                                    class="bg-[#F7F7F5] hover:bg-[#EFEFED] text-[#37352F] font-medium px-8 py-3 rounded border border-[#E9E9E7] transition">
                                ⏹ Detener
                            </button>
                        </div>
                    </template>
                    
                    <template x-if="isRunning && isBreak">
                        <button @click="$wire.skipBreak()"
                                class="bg-[#F7F7F5] hover:bg-[#EFEFED] text-[#37352F] font-medium px-8 py-3 rounded border border-[#E9E9E7] transition">
                            ⏭ Omitir Descanso
                        </button>
                    </template>
                    
                    <template x-if="isPaused">
                        <div class="flex gap-3">
                            <button @click="$wire.resumeTimer()"
                                    class="bg-[#27AE60] hover:bg-[#219653] text-white font-medium px-8 py-3 rounded shadow-sm transition flex items-center gap-2">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                Reanudar
                            </button>
                            <button @click="$wire.stopTimer()"
                                    class="bg-[#F7F7F5] hover:bg-[#EFEFED] text-[#37352F] font-medium px-8 py-3 rounded border border-[#E9E9E7] transition">
                                ⏹ Detener
                            </button>
                        </div>
                    </template>
                        </div>

                {{-- Stats Row --}}
                <div class="flex justify-center gap-6 text-sm text-[#9B9A97]">
                    <div class="flex items-center gap-2">
                        <span>Ciclo:</span>
                        <span class="font-semibold text-[#37352F]">{{ $userSettings['cycle_count'] ?? 0 }}/{{ $maxCycles }}</span>
                        </div>
                    <div class="w-px h-4 bg-[#E9E9E7]"></div>
                    <div class="flex items-center gap-2">
                        <span>Meta:</span>
                        <span class="font-semibold text-[#37352F]">{{ $todayStats['pomodoros'] }}/{{ $dailyGoal }}</span>
                                </div>
                    <div class="w-px h-4 bg-[#E9E9E7]"></div>
                    <div class="flex items-center gap-2">
                        <span>Hoy:</span>
                        <span class="font-semibold text-[#37352F]">{{ $todayStats['focus_time'] }} min</span>
                            </div>
                        </div>

                {{-- Keyboard Hints --}}
                <div class="mt-8 text-xs text-[#9B9A97]">
                    <span class="px-2 py-1 bg-[#F7F7F5] rounded mr-2">Espacio</span> Pausar/Reanudar
                    <span class="px-2 py-1 bg-[#F7F7F5] rounded mx-2 ml-4">Esc</span> Salir
                </div>
            </div>
                        </div>
                    </div>

    {{-- Custom Duration Modal --}}
    <div x-show="showCustomModal" 
         x-transition
         class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center"
         @click.self="showCustomModal = false"
         @keydown.escape.window="showCustomModal = false">
        <div class="bg-white rounded-lg p-6 shadow-xl max-w-sm w-full mx-4 border border-[#E9E9E7]">
            <h3 class="text-lg font-semibold text-[#37352F] mb-4">⏱ Duración Personalizada</h3>
            
                        <div class="mb-4">
                <label class="block text-sm text-[#9B9A97] mb-2">Minutos (1-120)</label>
                <input type="number" 
                       x-model="customMinutes"
                       min="1" max="120"
                       class="w-full px-3 py-2 border border-[#E9E9E7] rounded focus:border-[#2383E2] focus:ring-1 focus:ring-[#2383E2] outline-none"
                       @keydown.enter="applyCustomDuration()">
                        </div>

            @if(count($recentCustomDurations) > 0)
                <div class="mb-4">
                    <label class="block text-sm text-[#9B9A97] mb-2">Recientes</label>
                    <div class="flex gap-2">
                        @foreach($recentCustomDurations as $dur)
                            <button wire:click="setCustomDuration({{ $dur }})"
                                    @click="showCustomModal = false"
                                    class="px-3 py-1 text-sm bg-[#F7F7F5] hover:bg-[#EFEFED] rounded border border-[#E9E9E7]">
                                {{ $dur }}m
                                    </button>
                                @endforeach
                            </div>
                </div>
            @endif

            <div class="flex gap-2">
                <button @click="showCustomModal = false" 
                        class="flex-1 px-4 py-2 bg-[#F7F7F5] hover:bg-[#EFEFED] rounded text-[#37352F] text-sm border border-[#E9E9E7]">
                    Cancelar
                                </button>
                <button @click="applyCustomDuration()"
                        class="flex-1 px-4 py-2 bg-[#2383E2] hover:bg-[#1B74C9] text-white rounded text-sm">
                                        Aplicar
                                    </button>
                                </div>
                                </div>
                            </div>
                            
    {{-- Header --}}
    <header class="sticky top-0 bg-white/95 backdrop-blur z-20 border-b border-[#E9E9E7] px-6 py-3"
            x-show="!focusMode">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-gray-400 hover:text-[#37352F] transition mr-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                </a>
                <span class="text-xl">🍅</span>
                <span class="font-semibold text-[#37352F]">Focus Room</span>
                
                {{-- Timer badge in header when running --}}
                <template x-if="!isIdle">
                    <span class="ml-3 px-2 py-0.5 rounded text-xs font-mono font-medium"
                          :class="isBreak ? 'bg-[#DDEDEA] text-[#27AE60]' : 'bg-[#FCE8E6] text-[#EB5757]'"
                          x-text="formatTime(remainingSeconds)">
                    </span>
                </template>
            </div>
            
            <div class="flex items-center gap-3">
                {{-- Focus Mode Button --}}
                <button @click="toggleFocusMode()"
                        class="flex items-center gap-2 px-3 py-1.5 bg-[#2383E2] hover:bg-[#1B74C9] text-white text-xs font-medium rounded shadow-sm transition-all">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <path d="M9 9h6v6H9z"/>
                    </svg>
                    <span>Fullscreen</span>
                                            </button>

                {{-- Energy Bar --}}
                <div class="flex items-center gap-3 bg-[#F7F7F5] px-3 py-1.5 rounded border border-[#E9E9E7]">
                    <span class="text-xs font-bold text-[#9B9A97] uppercase tracking-wide">Energía</span>
                    <div class="w-32 h-2 bg-[#E3E2E0] rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500
                            {{ $energyStatus['current'] < 30 ? 'bg-[#EB5757]' : ($energyStatus['current'] < 50 ? 'bg-[#F2C94C]' : 'bg-[#27AE60]') }}" 
                            style="width: {{ $energyStatus['percentage'] }}%"></div>
                                    </div>
                    <span class="text-xs font-mono text-[#37352F]">{{ $energyStatus['current'] }}/{{ $energyStatus['max'] }}</span>
                                </div>
                        </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="max-w-6xl mx-auto px-6 py-8 grid grid-cols-1 lg:grid-cols-12 gap-8" x-show="!focusMode">

        {{-- Left Column (8 cols) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Timer Card --}}
            <div class="border border-[#E9E9E7] rounded-md p-8 flex flex-col items-center justify-center relative bg-white shadow-[0_1px_3px_rgba(0,0,0,0.02)]"
                 :class="{ 'timer-running': isRunning && !isBreak }">
                
                {{-- Habit Selector --}}
                <div class="absolute top-4 left-4 right-4 flex justify-center" x-data="{ open: false }" @click.away="open = false">
                    <template x-if="isIdle">
                        <div class="relative">
                            <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 hover:bg-[#F7F7F5] rounded cursor-pointer transition text-[#37352F] group border border-transparent hover:border-[#E9E9E7]">
                                <span class="text-gray-400 text-xs mr-1">Hábito:</span>
                                @if($selectedHabit)
                                    @php $habit = $habits->find($selectedHabit); @endphp
                                    <span class="text-lg">{{ $habit->icon ?? '💻' }}</span>
                                    <span class="font-medium text-sm border-b border-gray-300 border-dashed pb-0.5">{{ $habit->name }}</span>
                                @else
                                    <span class="text-lg">💻</span>
                                    <span class="font-medium text-sm border-b border-gray-300 border-dashed pb-0.5">Sin hábito específico</span>
                    @endif
                                <svg class="w-3 h-3 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            
                            <div x-show="open" x-transition class="absolute top-full left-1/2 -translate-x-1/2 mt-1 bg-white border border-[#E9E9E7] rounded-md shadow-lg z-50 min-w-[220px]">
                                <div class="py-1 max-h-60 overflow-y-auto">
                                    <button wire:click="selectHabit(null)" @click="open = false" class="w-full text-left px-4 py-2 text-sm hover:bg-[#F7F7F5] flex items-center gap-2 {{ !$selectedHabit ? 'bg-[#F7F7F5]' : '' }}">
                                        <span class="text-lg">💻</span>
                                        <span class="text-[#37352F]">Sin hábito específico</span>
                                </button>
                                    @foreach($habits as $h)
                                        <button wire:click="selectHabit({{ $h->id }})" @click="open = false" class="w-full text-left px-4 py-2 text-sm hover:bg-[#F7F7F5] flex items-center gap-2 {{ $selectedHabit === $h->id ? 'bg-[#F7F7F5]' : '' }}">
                                            <span class="text-lg">{{ $h->icon }}</span>
                                            <span class="text-[#37352F]">{{ $h->name }}</span>
                                </button>
                                    @endforeach
                            </div>
                        </div>
                        </div>
                    </template>
                </div>

                {{-- Timer Circle --}}
                <div class="relative mt-12 mb-8">
                    <svg class="w-72 h-72" viewBox="0 0 288 288" style="transform: rotate(-90deg);">
                        <circle cx="144" cy="144" r="136" stroke="#F1F1EF" stroke-width="4" fill="none" />
                        <circle cx="144" cy="144" r="136" 
                                :stroke="isBreak ? '#27AE60' : '#EB5757'"
                                stroke-width="4" 
                                fill="none"
                                stroke-dasharray="854"
                                :stroke-dashoffset="854 * (1 - getProgress())"
                                stroke-linecap="round"
                                class="progress-circle" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-7xl font-bold text-[#37352F] tracking-tighter tabular-nums mb-2"
                             x-text="formatTime(remainingSeconds)">
                        </div>
                        <span class="px-2 py-0.5 rounded text-xs font-medium"
                              :class="{
                                  'bg-[#FCE8E6] text-[#EB5757]': isRunning && !isBreak,
                                  'bg-[#DDEDEA] text-[#27AE60]': isRunning && isBreak,
                                  'bg-[#FFF4E0] text-[#9A6D02]': isPaused,
                                  'bg-[#E7F3F8] text-[#154664]': isIdle
                              }">
                            <span x-show="isIdle">Listo para comenzar</span>
                            <span x-show="isRunning && !isBreak">🎯 Enfocado</span>
                            <span x-show="isRunning && isBreak">☕ Descanso</span>
                            <span x-show="isPaused">⏸ Pausado</span>
                        </span>
                    </div>
                </div>

                {{-- Duration Buttons --}}
                <template x-if="isIdle">
                    <div class="flex gap-2 mb-8 flex-wrap justify-center">
                        @foreach($allowedDurations as $dur)
                            <button wire:click="setDuration({{ $dur }})"
                                    class="px-3 py-1 text-xs font-medium rounded border transition
                                        {{ $duration === $dur 
                                            ? 'text-white bg-[#2383E2] shadow-sm border-transparent' 
                                            : 'text-[#37352F] bg-[#F7F7F5] hover:bg-[#EFEFED] border-[#E9E9E7]' }}">
                                {{ $dur }} min
                            </button>
                        @endforeach
                        <button @click="showCustomModal = true; customMinutes = 45"
                                class="px-3 py-1 text-xs font-medium text-gray-400 hover:text-[#37352F] hover:bg-[#F7F7F5] rounded transition border border-transparent hover:border-[#E9E9E7]">
                            + Custom
                        </button>
                    </div>
                </template>

                {{-- Action Buttons --}}
                <template x-if="isIdle">
                            <button wire:click="startTimer"
                                    @if($energyStatus['current'] < 10) disabled @endif
                            class="bg-[#2383E2] hover:bg-[#1B74C9] disabled:bg-[#E3E2E0] disabled:cursor-not-allowed text-white text-base font-medium px-10 py-2.5 rounded shadow-sm transition active:scale-95 flex items-center gap-2">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                <span wire:loading.remove wire:target="startTimer">Iniciar Pomodoro</span>
                                <span wire:loading wire:target="startTimer">Iniciando...</span>
                            </button>
                </template>

                <template x-if="isRunning && isBreak">
                    <button wire:click="skipBreak" class="bg-[#F7F7F5] hover:bg-[#EFEFED] text-[#37352F] text-sm font-medium px-8 py-2 rounded border border-[#E9E9E7] transition">
                                    ⏭ Omitir Descanso
                                </button>
                </template>

                <template x-if="isRunning && !isBreak">
                    <div class="flex gap-2">
                        <button wire:click="pauseTimer" class="bg-[#F2C94C] hover:bg-[#E5BC3B] text-[#37352F] text-sm font-medium px-6 py-2 rounded shadow-sm transition flex items-center gap-2">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h4v16H6zM14 4h4v16h-4z"/></svg>
                            Pausar
                                </button>
                        <button wire:click="stopTimer" class="bg-[#F7F7F5] hover:bg-[#EFEFED] text-[#37352F] text-sm font-medium px-6 py-2 rounded border border-[#E9E9E7] transition">
                                    ⏹ Detener
                                </button>
                    </div>
                </template>

                <template x-if="isPaused">
                    <div class="flex gap-2">
                        <button wire:click="resumeTimer" class="bg-[#27AE60] hover:bg-[#219653] text-white text-sm font-medium px-6 py-2 rounded shadow-sm transition flex items-center gap-2">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            Reanudar
                            </button>
                        <button wire:click="stopTimer" class="bg-[#F7F7F5] hover:bg-[#EFEFED] text-[#37352F] text-sm font-medium px-6 py-2 rounded border border-[#E9E9E7] transition">
                                ⏹ Detener
                            </button>
                    </div>
                </template>
                </div>

            {{-- Cycle & Goal Row --}}
            <div class="grid grid-cols-2 gap-6">
                <div class="border border-[#E9E9E7] rounded-md p-4 bg-white">
                    <div class="text-[11px] font-bold text-[#9B9A97] uppercase tracking-wide mb-3">Ciclo Actual</div>
                    <div class="flex flex-col items-center justify-center gap-2">
                        <div class="flex gap-2 flex-wrap justify-center">
                        @for($i = 1; $i <= $maxCycles; $i++)
                                <div class="w-4 h-4 rounded-full border transition-all
                                {{ $i <= ($userSettings['cycle_count'] ?? 0) 
                                        ? 'border-[#EB5757] bg-[#EB5757]' 
                                        : 'border-[#E9E9E7] bg-gray-50' }}">
                            </div>
                        @endfor
                    </div>
                        <div class="text-sm text-[#37352F]">
                            <span class="font-bold">{{ $userSettings['cycle_count'] ?? 0 }}</span> / {{ $maxCycles }}
                        </div>
                        <div class="text-[10px] px-1.5 py-0.5 rounded
                            {{ ($userSettings['cycle_count'] ?? 0) >= $maxCycles ? 'text-[#9333EA] bg-[#F3E8FF]' : 'text-[#27AE60] bg-[#DDEDEA]' }}">
                            Próximo: {{ ($userSettings['cycle_count'] ?? 0) >= $maxCycles ? 'Descanso Largo' : 'Descanso Corto' }}
                        </div>
                    </div>
                </div>

                <div class="border border-[#E9E9E7] rounded-md p-4 bg-[#FFFBF0] relative overflow-hidden" x-data="{ editingGoal: false, newGoal: {{ $dailyGoal }} }">
                    @php $goalProgress = $dailyGoal > 0 ? min(($todayStats['pomodoros'] / $dailyGoal) * 100, 100) : 0; @endphp
                    <div class="flex justify-between items-center mb-3 relative z-10">
                        <div class="text-[11px] font-bold text-[#9A6D02] uppercase tracking-wide">Meta Diaria</div>
                        <button @click="editingGoal = true" x-show="!editingGoal" class="text-xs text-[#9A6D02] hover:underline">✏️ Editar</button>
                        <div x-show="editingGoal" class="flex items-center gap-1" @click.away="editingGoal = false">
                            <input type="number" x-model="newGoal" min="1" max="50" 
                                   class="w-12 px-1 py-0.5 text-xs border border-[#E9E9E7] rounded text-center"
                                   @keydown.enter="$wire.setDailyGoal(parseInt(newGoal)); editingGoal = false"
                                   @keydown.escape="editingGoal = false">
                            <button @click="$wire.setDailyGoal(parseInt(newGoal)); editingGoal = false" class="text-xs text-[#27AE60]">✓</button>
                    </div>
                    </div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="text-3xl font-bold text-[#37352F]">{{ $todayStats['pomodoros'] }}<span class="text-lg text-[#9B9A97] font-normal">/{{ $dailyGoal }}</span></div>
                        <span class="text-2xl">{{ $goalProgress >= 100 ? '🎉' : '💪' }}</span>
                </div>
                    <div class="absolute bottom-0 left-0 h-1.5 bg-[#FBF3DB] w-full">
                        <div class="h-full bg-[#F2C94C] transition-all duration-500" style="width: {{ $goalProgress }}%"></div>
            </div>
                        </div>
                        </div>
                        
            {{-- History Table --}}
            <div class="border border-[#E9E9E7] rounded-md bg-white overflow-hidden">
                <div class="px-4 py-2 border-b border-[#E9E9E7] bg-[#FBFBFA] flex items-center gap-4">
                    <span class="text-xs font-bold text-[#37352F]">📋 Historial</span>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <button wire:click="setFilter('all')" class="text-xs px-2 py-1 rounded transition {{ $sessionFilter === 'all' ? 'bg-white border border-[#E9E9E7] text-[#37352F]' : 'text-[#9B9A97] hover:text-[#37352F]' }}">Todas</button>
                    <button wire:click="setFilter('completed')" class="text-xs px-2 py-1 rounded transition {{ $sessionFilter === 'completed' ? 'bg-white border border-[#E9E9E7] text-[#37352F]' : 'text-[#9B9A97] hover:text-[#37352F]' }}">Completadas</button>
                    <button wire:click="setFilter('interrupted')" class="text-xs px-2 py-1 rounded transition {{ $sessionFilter === 'interrupted' ? 'bg-white border border-[#E9E9E7] text-[#37352F]' : 'text-[#9B9A97] hover:text-[#37352F]' }}">Interrumpidas</button>
                        </div>
                        
                <table class="w-full text-left">
                    <thead class="text-[10px] text-[#9B9A97] uppercase bg-white border-b border-[#E9E9E7]">
                        <tr>
                            <th class="px-4 py-2 font-medium">Hábito</th>
                            <th class="px-4 py-2 font-medium w-24">Estado</th>
                            <th class="px-4 py-2 font-medium w-24 text-right">Duración</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-[#F1F1EF]">
                        @forelse($recentSessions as $session)
                            <tr class="hover:bg-[#F7F7F5] transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">{{ $session['habit_icon'] }}</span>
                                        <span class="text-[#37352F]">{{ $session['habit_name'] }}</span>
                            </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($session['was_interrupted'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-[#FFF4E0] text-[#9A6D02]">⚠️ Interrumpido</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-[#DDEDEA] text-[#27AE60]">✓ Completado</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-[#9B9A97]">{{ $session['duration'] }} min</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-400 text-xs">
                                    <div class="text-xl mb-2">🍅</div>
                                    No hay sesiones hoy. ¡Empieza una!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                        </div>
                    </div>

        {{-- Right Column (4 cols) --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Settings Card --}}
            <div class="border border-[#E9E9E7] rounded-md bg-white p-5">
                <div class="flex items-center gap-2 mb-4 pb-2 border-b border-[#E9E9E7]">
                    <span class="text-lg">⚙️</span>
                    <h3 class="text-xs font-bold text-[#9B9A97] uppercase tracking-wide">Configuración</h3>
                </div>

                <div class="space-y-0.5">
                    <div class="flex items-center justify-between py-2 hover:bg-[#F7F7F5] rounded px-2 -mx-2 transition">
                        <div class="flex items-center gap-2 text-sm text-[#37352F]">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Descanso Corto
                        </div>
                        <input type="number" value="{{ $userSettings['short_break_duration'] ?? 5 }}"
                               wire:change="updateSettings({ 'short_break_duration': $event.target.value })"
                               min="1" max="30" class="prop-input">
                    </div>
                    
                    <div class="flex items-center justify-between py-2 hover:bg-[#F7F7F5] rounded px-2 -mx-2 transition">
                        <div class="flex items-center gap-2 text-sm text-[#37352F]">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            Descanso Largo
                                    </div>
                        <input type="number" value="{{ $userSettings['long_break_duration'] ?? 15 }}"
                               wire:change="updateSettings({ 'long_break_duration': $event.target.value })"
                               min="5" max="60" class="prop-input">
                    </div>

                    <div class="h-2"></div>

                    <div class="flex items-center justify-between py-2 hover:bg-[#F7F7F5] rounded px-2 -mx-2 transition">
                        <div class="text-sm text-[#37352F]">Auto-iniciar descansos</div>
                        <label class="notion-toggle">
                            <input type="checkbox" wire:change="updateSettings({ 'auto_start_breaks': $event.target.checked })"
                                   {{ ($userSettings['auto_start_breaks'] ?? true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                                        </div>
                                        
                    <div class="flex items-center justify-between py-2 hover:bg-[#F7F7F5] rounded px-2 -mx-2 transition">
                        <div class="text-sm text-[#37352F]">Sonidos habilitados</div>
                        <label class="notion-toggle">
                            <input type="checkbox" wire:change="updateSettings({ 'sound_enabled': $event.target.checked })"
                                   {{ ($userSettings['sound_enabled'] ?? true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                                        </div>
                                        </div>
                                        </div>
                                        
            {{-- Stats Card --}}
            <div class="border border-[#E9E9E7] rounded-md bg-white p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xs font-bold text-[#9B9A97] uppercase tracking-wide">Estadísticas</h3>
                                    </div>

                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="bg-[#F7F7F5] rounded p-3">
                        <div class="text-2xl font-bold text-[#37352F]">{{ $todayStats['pomodoros'] }}</div>
                        <div class="text-[10px] text-gray-500 uppercase">Pomodoros Hoy</div>
                                </div>
                    <div class="bg-[#F7F7F5] rounded p-3">
                        <div class="text-2xl font-bold text-[#37352F]">{{ $todayStats['focus_time'] }}</div>
                        <div class="text-[10px] text-gray-500 uppercase">Minutos</div>
                        </div>
                        </div>

                <div class="mt-4 flex gap-3 p-3 bg-white border border-[#E9E9E7] rounded shadow-sm">
                    <span class="text-xl">💡</span>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        @if($todayStats['pomodoros'] > 0)
                            ¡Excelente trabajo! Has completado {{ $todayStats['pomodoros'] }} Pomodoro{{ $todayStats['pomodoros'] > 1 ? 's' : '' }} hoy.
                        @else
                            Aún no has completado ningún Pomodoro hoy. ¡Es momento de comenzar!
                    @endif
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.dashboard') }}" wire:navigate class="block w-full text-center text-xs text-gray-400 hover:text-[#37352F] hover:bg-[#F7F7F5] py-2 rounded transition">
                ← Volver al Dashboard
            </a>
        </div>
    </main>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="fixed bottom-4 right-4 bg-[#27AE60] text-white px-4 py-2 rounded shadow-lg text-sm z-30">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="fixed bottom-4 right-4 bg-[#EB5757] text-white px-4 py-2 rounded shadow-lg text-sm z-30">
            {{ session('error') }}
        </div>
    @endif

    {{-- Alpine.js Timer Logic - SIMPLE VERSION --}}
    <script>
        function pomodoroTimer() {
            return {
                remainingSeconds: {{ $remainingSeconds }},
                totalSeconds: {{ $duration * 60 }},
                showCustomModal: false,
                customMinutes: 45,
                focusMode: @entangle('focusMode'),
                originalTitle: '🍅 Pomodoro - Habit Tracks',
                interval: null,
                oneMinuteNotified: false,

                get isIdle() { return this.$wire.timerState === 'idle'; },
                get isRunning() { return this.$wire.timerState === 'running'; },
                get isPaused() { return this.$wire.timerState === 'paused'; },
                get isBreak() { return this.$wire.breakType !== ''; },

                init() {
                    this.remainingSeconds = {{ $remainingSeconds }};
                    this.totalSeconds = {{ $duration * 60 }};
                    
                    // Si está corriendo, iniciar countdown
                    if (this.isRunning) {
                        this.startCountdown();
                    }
                    
                    // Actualizar título
                    this.updateTitle();

                    // Eventos de Livewire
                    this.$wire.$on('timerStarted', (data) => {
                        this.totalSeconds = this.$wire.duration * 60;
                        this.remainingSeconds = data[0]?.remainingSeconds || this.totalSeconds;
                        this.oneMinuteNotified = false;
                        this.startCountdown();
                    });

                    this.$wire.$on('breakStarted', (data) => {
                        const breakDuration = data[0]?.duration || 5;
                        this.totalSeconds = breakDuration * 60;
                        this.remainingSeconds = this.totalSeconds;
                        this.oneMinuteNotified = false;
                        this.startCountdown();
                    });

                    this.$wire.$on('timerPaused', () => {
                        this.stopCountdown();
                        this.updateTitle();
                    });

                    this.$wire.$on('timerResumed', () => {
                        this.startCountdown();
                    });

                    this.$wire.$on('timerStopped', () => {
                        this.stopCountdown();
                        this.remainingSeconds = this.$wire.duration * 60;
                        this.totalSeconds = this.remainingSeconds;
                        this.oneMinuteNotified = false;
                        document.title = this.originalTitle;
                    });

                    // Atajos de teclado
                    document.addEventListener('keydown', (e) => {
                        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
                        if (this.showCustomModal) return;
                        
                        if (e.code === 'Space') {
                            e.preventDefault();
                            if (this.isIdle) this.$wire.startTimer();
                            else if (this.isRunning && !this.isBreak) this.$wire.pauseTimer();
                            else if (this.isPaused) this.$wire.resumeTimer();
                        }
                        
                        if (e.code === 'Escape') {
                            if (this.focusMode) {
                                this.toggleFocusMode();
                            } else if (!this.isIdle) {
                                if (this.isBreak) this.$wire.skipBreak();
                                else this.$wire.stopTimer();
                            }
                        }

                        if (e.code === 'KeyF' && !e.ctrlKey && !e.metaKey && !e.altKey) {
                            e.preventDefault();
                            this.toggleFocusMode();
                        }
                    });

                    // Pedir permiso para notificaciones
                    if ('Notification' in window && Notification.permission === 'default') {
                        Notification.requestPermission();
                    }
                },

                startCountdown() {
                    this.stopCountdown();
                    
                    this.interval = setInterval(() => {
                        if (this.remainingSeconds > 0) {
                            this.remainingSeconds--;
                            this.updateTitle();
                            
                            // Notificación a 1 minuto
                            if (this.remainingSeconds === 60 && !this.oneMinuteNotified) {
                                this.showOneMinuteWarning();
                                this.oneMinuteNotified = true;
                            }
                            
                            // Beep últimos 10 segundos
                            if (this.remainingSeconds <= 10 && this.remainingSeconds > 0) {
                                this.playCountdownBeep();
                            }
                        } else {
                            this.stopCountdown();
                            this.playCompletionSound();
                            this.showCompletionNotification();
                            this.$wire.completeTimer();
                        }
                    }, 1000);
                },
                
                stopCountdown() {
                    if (this.interval) {
                        clearInterval(this.interval);
                        this.interval = null;
                    }
                },

                formatTime(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                },

                getProgress() {
                    if (this.totalSeconds === 0) return 1;
                    return this.remainingSeconds / this.totalSeconds;
                },

                updateTitle() {
                    if (this.isIdle) {
                        document.title = this.originalTitle;
                    } else {
                        const emoji = this.isBreak ? '☕' : (this.isPaused ? '⏸' : '🍅');
                        document.title = `${emoji} ${this.formatTime(this.remainingSeconds)} - Pomodoro`;
                    }
                },

                toggleFocusMode() {
                    this.$wire.toggleFocusMode();
                },

                playCountdownBeep() {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.frequency.value = this.remainingSeconds <= 3 ? 1000 : 600;
                        osc.type = 'sine';
                        gain.gain.value = 0.15;
                        osc.start();
                        setTimeout(() => { osc.stop(); ctx.close(); }, 100);
                    } catch(e) {}
                },

                playCompletionSound() {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.frequency.value = 800;
                        osc.type = 'sine';
                        gain.gain.value = 0.3;
                        osc.start();
                        setTimeout(() => { osc.stop(); ctx.close(); }, 500);
                    } catch(e) {}
                },

                showOneMinuteWarning() {
                    if ('Notification' in window && Notification.permission === 'granted') {
                        const title = this.isBreak ? '☕ 1 minuto de descanso!' : '🍅 1 minuto restante!';
                        const body = this.isBreak ? 'Prepárate para volver al trabajo' : '¡Último minuto! Mantén el enfoque';
                        new Notification(title, { body, icon: '/favicon.svg' });
                    }
                },

                showCompletionNotification() {
                    if ('Notification' in window && Notification.permission === 'granted') {
                        const title = this.isBreak ? '☕ Descanso terminado!' : '🍅 Pomodoro Completado!';
                        const body = this.isBreak ? '¡Es hora de volver al trabajo!' : '¡Gran trabajo! Has completado tu sesión.';
                        new Notification(title, { body, icon: '/favicon.svg' });
                    }
                },

                applyCustomDuration() {
                    const mins = parseInt(this.customMinutes);
                    if (mins >= 1 && mins <= 120) {
                        this.$wire.setCustomDuration(mins);
                        this.showCustomModal = false;
                    }
                }
            }
        }
    </script>
</div>
