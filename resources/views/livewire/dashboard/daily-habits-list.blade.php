<div class="bg-white dark:bg-zinc-800 p-6 rounded-3xl border border-gray-100 dark:border-zinc-700 shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="font-bold text-slate-800 dark:text-zinc-50 text-lg">Misiones de Hoy</h3>
            <div class="flex bg-gray-100 dark:bg-zinc-700 p-1 rounded-lg">
                <button 
                    wire:click="setFilter('pending')"
                    wire:loading.attr="disabled"
                    class="px-3 py-1 text-xs font-medium transition-all {{ $filter === 'pending' ? 'bg-white dark:bg-zinc-600 shadow-sm rounded-md font-bold text-slate-800 dark:text-zinc-50' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-200' }} disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Pendientes ({{ $totalCount - $completedCount }})
                </button>
                <button 
                    wire:click="setFilter('all')"
                    wire:loading.attr="disabled"
                    class="px-3 py-1 text-xs font-medium transition-all {{ $filter === 'all' ? 'bg-white dark:bg-zinc-600 shadow-sm rounded-md font-bold text-slate-800 dark:text-zinc-50' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-200' }} disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Todas
                </button>
            </div>
        </div>
        <button class="text-slate-400 dark:text-zinc-500 hover:text-slate-600 dark:hover:text-zinc-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
            </svg>
        </button>
    </div>

    <div wire:loading class="flex items-center justify-center py-8">
        <svg class="animate-spin h-8 w-8 text-brand-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <div wire:loading.remove>
        @if($habits->count() > 0)
            <div class="space-y-4">
                @foreach($habits as $habit)
                    <div class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-zinc-700 rounded-2xl transition-colors border border-transparent hover:border-gray-100 dark:hover:border-zinc-600 group cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white dark:border-zinc-700 shadow-sm flex items-center justify-center bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                                {{ $habit->getCategoryIcon() ?? '⭐' }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-zinc-200 text-sm {{ $habit->isCompletedToday() ? 'line-through opacity-60' : '' }}">
                                    {{ $habit->name }}
                                </h4>
                                @if($habit->description)
                                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5 max-w-md">
                                        {{ Str::limit($habit->description, 60) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            @if($habit->difficulty)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold 
                                    {{ $habit->difficulty->value === 'easy' ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-300 border border-green-100 dark:border-green-800' : '' }}
                                    {{ $habit->difficulty->value === 'medium' ? 'bg-orange-50 dark:bg-orange-900 text-orange-600 dark:text-orange-300 border border-orange-100 dark:border-orange-800' : '' }}
                                    {{ $habit->difficulty->value === 'hard' || $habit->difficulty->value === 'epic' ? 'bg-red-50 dark:bg-red-900 text-red-600 dark:text-red-300 border border-red-100 dark:border-red-800' : '' }}">
                                    {{ $habit->difficulty->value === 'easy' ? 'Baja' : '' }}
                                    {{ $habit->difficulty->value === 'medium' ? 'Media' : '' }}
                                    {{ $habit->difficulty->value === 'hard' ? 'Prioridad Alta' : '' }}
                                    {{ $habit->difficulty->value === 'epic' ? 'Épica' : '' }}
                                </span>
                            @endif
                            <span class="text-xs text-slate-500 dark:text-zinc-400">{{ $habit->points_reward }} XP</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-zinc-700 mb-4">
                    <svg class="h-8 w-8 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-700 dark:text-zinc-200 mb-2">
                    @if($totalCount === 0)
                        No tienes hábitos programados para hoy
                    @elseif($filter === 'pending' && $completedCount === $totalCount)
                        ¡Todas las misiones completadas! 🎉
                    @else
                        No hay misiones que mostrar
                    @endif
                </h3>
                <p class="text-sm text-slate-500 dark:text-zinc-400 mb-6">
                    @if($totalCount === 0)
                        Crea tu primer hábito para comenzar tu seguimiento
                    @elseif($filter === 'pending' && $completedCount === $totalCount)
                        Has completado todas tus misiones del día. ¡Excelente trabajo!
                    @else
                        Ajusta los filtros para ver tus hábitos
                    @endif
                </p>
                @if($totalCount === 0)
                    <a 
                        href="{{ route('admin.habits.create') }}"
                        wire:navigate
                        class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Crear mi primer hábito
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
