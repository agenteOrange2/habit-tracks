<div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-2xl transition-colors border border-transparent hover:border-gray-100 group cursor-pointer"
     wire:click="toggleComplete">
    <div class="flex items-center gap-4">
        <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white shadow-sm flex items-center justify-center bg-blue-100 text-blue-600">
            {{ $habit->getCategoryIcon() ?? '⭐' }}
        </div>
        <div>
            <h4 class="font-bold text-slate-800 text-sm {{ $isCompleted ? 'line-through opacity-60' : '' }}">
                {{ $habit->name }}
            </h4>
            @if($habit->description)
                <p class="text-xs text-slate-500 mt-0.5 max-w-md">
                    {{ Str::limit($habit->description, 60) }}
                </p>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-6">
        @if($habit->getDifficultyName())
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100">
                {{ $habit->getDifficultyIcon() }} {{ $habit->getDifficultyName() }}
            </span>
        @endif
        <button 
            type="button"
            wire:loading.attr="disabled"
            class="w-8 h-8 rounded-full border flex items-center justify-center transition-all
                {{ $isCompleted 
                    ? 'bg-brand-600 text-white border-transparent' 
                    : 'border-gray-200 text-gray-400 group-hover:bg-brand-600 group-hover:text-white group-hover:border-transparent' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </button>
    </div>
</div>
