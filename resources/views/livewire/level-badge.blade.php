<div class="relative group" x-data="{ showTooltip: false }">
    {{-- Level Badge --}}
    <button 
        @mouseenter="showTooltip = true" 
        @mouseleave="showTooltip = false"
        class="flex items-center gap-1.5 px-2 py-1 rounded-lg bg-gradient-to-r from-amber-100 to-yellow-100 dark:from-amber-900/30 dark:to-yellow-900/30 border border-amber-200 dark:border-amber-700 hover:shadow-md transition-all duration-200"
    >
        <span class="text-sm">⭐</span>
        <span class="text-xs font-bold text-amber-700 dark:text-amber-300">{{ $this->currentLevel }}</span>
        
        {{-- Mini progress bar --}}
        <div class="w-8 h-1 bg-amber-200 dark:bg-amber-800 rounded-full overflow-hidden">
            <div 
                class="h-full bg-amber-500 dark:bg-amber-400 rounded-full transition-all duration-500"
                style="width: {{ $this->progressPercentage }}%"
            ></div>
        </div>
    </button>

    {{-- Tooltip --}}
    <div 
        x-show="showTooltip"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-48 p-3 bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 z-50"
    >
        {{-- Arrow --}}
        <div class="absolute -top-2 left-1/2 -translate-x-1/2 w-4 h-4 bg-white dark:bg-zinc-800 border-l border-t border-zinc-200 dark:border-zinc-700 rotate-45"></div>
        
        <div class="relative">
            <div class="text-center mb-2">
                <div class="text-lg font-bold text-zinc-900 dark:text-white">Nivel {{ $this->currentLevel }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $this->levelTitle }}</div>
            </div>
            
            {{-- Progress bar --}}
            <div class="w-full h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden mb-1">
                <div 
                    class="h-full bg-gradient-to-r from-amber-400 to-yellow-500 rounded-full transition-all duration-500"
                    style="width: {{ $this->progressPercentage }}%"
                ></div>
            </div>
            
            <div class="text-xs text-center text-zinc-500 dark:text-zinc-400">
                {{ $this->currentXP }} / {{ $this->requiredXP }} XP
            </div>
        </div>
    </div>
</div>
