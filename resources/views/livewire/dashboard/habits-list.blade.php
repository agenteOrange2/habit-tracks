<div class="rounded-xl border border-neutral-200 bg-white p-4 sm:p-6 dark:border-neutral-700 dark:bg-gray-800">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
            Hábitos de Hoy
        </h2>
        <div wire:loading class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="hidden sm:inline">Actualizando...</span>
        </div>
    </div>
    
    @if($habits->count() > 0)
        <div class="space-y-2 sm:space-y-3">
            @foreach($habits as $habit)
                <div class="flex items-center gap-2 sm:gap-3 rounded-lg border border-gray-200 p-2.5 sm:p-3 dark:border-neutral-700 hover:border-gray-300 dark:hover:border-neutral-600 transition-colors">
                    <div class="relative flex-shrink-0">
                        <input 
                            type="checkbox" 
                            wire:click="toggleHabit({{ $habit->id }})"
                            {{ $habit->isCompletedToday() ? 'checked' : '' }}
                            class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                            wire:loading.attr="disabled"
                            wire:target="toggleHabit({{ $habit->id }})"
                        >
                        <div wire:loading wire:target="toggleHabit({{ $habit->id }})" class="absolute inset-0 flex items-center justify-center">
                            <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-sm sm:text-base text-gray-900 dark:text-white {{ $habit->isCompletedToday() ? 'line-through opacity-60' : '' }} truncate">
                            {{ $habit->name }}
                        </p>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full {{ $habit->category ? $habit->category->bgClass() : 'bg-gray-500' }} text-white">
                                {{ $habit->category ? $habit->category->icon() : '⭐' }}
                                <span class="hidden xs:inline">{{ $habit->category ? $habit->category->label() : 'Sin categoría' }}</span>
                            </span>
                            <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                {{ $habit->points_reward }} XP
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 sm:py-12">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4">
                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-2">
                No tienes hábitos programados para hoy
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Crea tu primer hábito para comenzar tu seguimiento y alcanzar tus metas
            </p>
            <a 
                href="/habits/create"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Crear mi primer hábito
            </a>
        </div>
    @endif
</div>
