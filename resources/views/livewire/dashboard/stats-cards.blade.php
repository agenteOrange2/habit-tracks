<div class="grid gap-4 sm:gap-6 grid-cols-1 md:grid-cols-3">
    {{-- Level/XP Card --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 dark:border-neutral-700 dark:bg-gray-800" wire:loading.class="animate-pulse">
        <div class="flex justify-between items-start mb-4">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-500 dark:text-gray-400">Nivel</p>
                <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    Nivel {{ $this->userLevel->current_level ?? 1 }}
                </h3>
                <p class="text-xs text-slate-400 dark:text-gray-500 mt-1 truncate">
                    {{ $this->userLevel->level_title ?? 'Principiante 🌱' }}
                </p>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 flex-shrink-0 ml-2">
                ⭐
            </span>
        </div>
        <div class="w-full bg-gray-100 dark:bg-neutral-700 rounded-full h-1.5 mb-2">
            <div 
                class="bg-primary-600 h-1.5 rounded-full transition-all duration-500" 
                style="width: {{ min($this->userLevel->progress_percentage ?? 0, 100) }}%"
            ></div>
        </div>
        <p class="text-xs text-slate-400 dark:text-gray-500">
            {{ $this->userLevel->current_xp ?? 0 }} / {{ $this->userLevel->required_xp ?? 100 }} XP para el siguiente nivel
        </p>
    </div>

    {{-- Streak Card --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 dark:border-neutral-700 dark:bg-gray-800" wire:loading.class="animate-pulse">
        <div class="flex justify-between items-start mb-4">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-500 dark:text-gray-400">Racha de Actividad</p>
                <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    {{ $this->currentStreak }} Días
                </h3>
                <p class="text-xs text-slate-400 dark:text-gray-500 mt-1">
                    ¡Sigue así!
                </p>
            </div>
            <div class="p-2 bg-orange-50 text-orange-600 rounded-lg dark:bg-orange-900 dark:text-orange-200 flex-shrink-0 ml-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                </svg>
            </div>
        </div>
        <div class="mt-4 flex gap-1">
            @foreach($this->last7Days as $day)
                <div 
                    class="h-2 flex-1 rounded-sm transition-colors {{ $day['hasActivity'] ? 'bg-primary-600' : 'bg-gray-200 dark:bg-neutral-700' }}"
                    title="{{ $day['date']->format('d/m/Y') }}"
                ></div>
            @endforeach
        </div>
    </div>

    {{-- Completion Rate Card --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 dark:border-neutral-700 dark:bg-gray-800" wire:loading.class="animate-pulse">
        <div class="flex justify-between items-start">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-500 dark:text-gray-400">Tasa de Completitud</p>
                <div class="flex items-end gap-2 mt-1">
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">
                        {{ number_format($this->completionRate, 0) }}%
                    </h3>
                    <span class="text-sm text-slate-400 dark:text-gray-500 mb-1">hoy</span>
                </div>
                <p class="text-xs text-slate-400 dark:text-gray-500 mt-1">
                    Hábitos completados
                </p>
            </div>
            <div class="text-3xl sm:text-4xl flex-shrink-0 ml-2">
                ✅
            </div>
        </div>
    </div>
</div>
