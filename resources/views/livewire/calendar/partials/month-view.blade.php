<!-- Days of week header -->
<div class="grid grid-cols-7 mb-2">
    @foreach(['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $day)
        <div class="text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 py-2">
            {{ $day }}
        </div>
    @endforeach
</div>

<!-- Calendar grid -->
<div class="grid grid-cols-7 gap-1">
    @foreach($this->calendarDays as $day)
        @php
            $dayEvents = $this->eventsByDate[$day['date']] ?? [];
            $hasEvents = count($dayEvents) > 0;
        @endphp
        <button
            wire:click="selectDay('{{ $day['date'] }}')"
            class="aspect-square p-1 rounded-lg text-sm transition-all relative
                {{ !$day['isCurrentMonth'] ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-900 dark:text-white' }}
                {{ $day['isToday'] ? 'bg-blue-100 dark:bg-blue-900/30 font-bold' : '' }}
                {{ $day['isSelected'] ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/50' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
        >
            <span class="block">{{ $day['day'] }}</span>
            @if($hasEvents)
                <div class="absolute bottom-1 left-1/2 -translate-x-1/2 flex gap-0.5">
                    @foreach(array_slice($dayEvents, 0, 3) as $event)
                        <a href="{{ route('admin.calendar.edit', $event) }}" wire:navigate 
                           class="w-1.5 h-1.5 rounded-full hover:scale-150 transition-transform" 
                           style="background-color: {{ $event->color ?? '#3b82f6' }}"
                           title="{{ $event->title }}"
                           onclick="event.stopPropagation()"></a>
                    @endforeach
                    @if(count($dayEvents) > 3)
                        <span class="text-[8px] text-zinc-500">+{{ count($dayEvents) - 3 }}</span>
                    @endif
                </div>
            @endif
        </button>
    @endforeach
</div>
