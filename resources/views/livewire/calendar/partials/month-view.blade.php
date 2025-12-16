<!-- Days of week header -->
<div class="grid grid-cols-7 mb-1 sm:mb-2">
    @foreach(['D', 'L', 'M', 'M', 'J', 'V', 'S'] as $index => $shortDay)
        @php
            $fullDays = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        @endphp
        <div class="text-center text-[10px] sm:text-xs font-medium text-zinc-500 dark:text-zinc-400 py-1 sm:py-2">
            <span class="sm:hidden">{{ $shortDay }}</span>
            <span class="hidden sm:inline">{{ $fullDays[$index] }}</span>
        </div>
    @endforeach
</div>

<!-- Calendar grid -->
<div class="grid grid-cols-7 gap-0.5 sm:gap-1">
    @foreach($this->calendarDays as $day)
        @php
            $dayEvents = $this->eventsByDate[$day['date']] ?? [];
            $hasEvents = count($dayEvents) > 0;
        @endphp
        <button
            wire:click="selectDay('{{ $day['date'] }}')"
            class="aspect-square p-0.5 sm:p-1 rounded sm:rounded-lg text-xs sm:text-sm transition-all relative
                {{ !$day['isCurrentMonth'] ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-900 dark:text-white' }}
                {{ $day['isToday'] ? 'bg-blue-100 dark:bg-blue-900/30 font-bold' : '' }}
                {{ $day['isSelected'] ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/50' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
        >
            <span class="block">{{ $day['day'] }}</span>
            @if($hasEvents)
                <div class="absolute bottom-0.5 sm:bottom-1 left-1/2 -translate-x-1/2 flex gap-0.5">
                    @foreach(array_slice($dayEvents, 0, 2) as $event)
                        <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full" 
                              style="background-color: {{ $event->color ?? '#3b82f6' }}"></span>
                    @endforeach
                    @if(count($dayEvents) > 2)
                        <span class="hidden sm:inline text-[8px] text-zinc-500">+{{ count($dayEvents) - 2 }}</span>
                    @endif
                </div>
            @endif
        </button>
    @endforeach
</div>
