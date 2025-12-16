<a href="{{ route('admin.xp-history') }}" 
   wire:navigate
   class="group flex items-center gap-3 p-3 rounded-xl bg-zinc-50 hover:bg-amber-50/50 dark:bg-zinc-800 dark:hover:bg-amber-900/20 border border-transparent hover:border-amber-200 dark:hover:border-amber-700 transition-all duration-200">
    
    {{-- Avatar con nivel --}}
    <div class="relative flex-shrink-0">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-yellow-500 flex items-center justify-center border-2 border-white dark:border-zinc-700 shadow-sm group-hover:border-amber-200 dark:group-hover:border-amber-600 transition-colors">
            <span class="text-lg font-bold text-white">{{ $this->currentLevel }}</span>
        </div>
        {{-- Indicador de progreso --}}
        <span class="absolute -bottom-0.5 -right-0.5 block h-3.5 w-3.5 rounded-full ring-2 ring-white dark:ring-zinc-800 bg-gradient-to-r from-green-400 to-emerald-500 flex items-center justify-center">
            <span class="text-[8px] text-white font-bold">⚡</span>
        </span>
    </div>
    
    {{-- Info --}}
    <div class="flex-1 min-w-0">
        <p class="text-sm font-bold text-zinc-900 dark:text-white truncate group-hover:text-amber-700 dark:group-hover:text-amber-400 transition-colors">
            {{ $this->levelTitle }}
        </p>
        
        {{-- Barra de progreso XP --}}
        <div class="mt-1.5 flex items-center gap-2">
            <div class="flex-1 h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                <div 
                    class="h-full bg-gradient-to-r from-amber-400 to-yellow-500 rounded-full transition-all duration-500"
                    style="width: {{ $this->progressPercentage }}%"
                ></div>
            </div>
            <span class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                {{ $this->currentXP }}/{{ $this->requiredXP }}
            </span>
        </div>
    </div>
    
    {{-- Flecha --}}
    <div class="opacity-0 group-hover:opacity-100 text-amber-500 transition-opacity">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
        </svg>
    </div>
</a>
