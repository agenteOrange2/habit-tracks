<div class="flex items-center bg-white shadow-sm border border-gray-200 rounded-lg p-2 gap-2 sm:gap-4 dark:bg-gray-800 dark:border-gray-700" wire:poll.1s="tick">
    <div class="px-2 sm:px-4 border-r border-gray-200 dark:border-gray-700">
        <span class="block text-xs text-slate-400 dark:text-gray-500 uppercase font-semibold">Focus Timer</span>
        <span class="font-mono text-lg sm:text-xl font-medium text-slate-700 dark:text-gray-300">{{ $this->formatTime() }}</span>
    </div>
    <button 
        wire:click="toggleTimer" 
        style="background-color: #2563eb; color: white;"
        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
        aria-label="{{ $running ? 'Pausar timer' : 'Iniciar timer' }}"
        wire:loading.attr="disabled"
        wire:target="toggleTimer"
    >
        <span wire:loading.remove wire:target="toggleTimer" class="flex items-center justify-center">
            @if(!$running)
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="white">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="white">
                    <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
                </svg>
            @endif
        </span>
        <svg wire:loading wire:target="toggleTimer" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"></circle>
            <path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </button>
</div>
