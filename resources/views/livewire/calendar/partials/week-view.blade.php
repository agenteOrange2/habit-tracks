<!-- Week header -->
<div class="grid grid-cols-7 gap-1 mb-2">
    @foreach($this->weekDays as $day)
        <div class="text-center p-2 rounded-lg {{ $day['isToday'] ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
            <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $day['dayName'] }}</div>
            <div class="text-lg font-semibold {{ $day['isToday'] ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-900 dark:text-white' }}">
                {{ $day['day'] }}
            </div>
        </div>
    @endforeach
</div>

<!-- Week events grid -->
<div class="grid grid-cols-7 gap-1 min-h-[400px]">
    @foreach($this->weekDays as $day)
        @php
            $dayEvents = $this->weekEvents->filter(fn($e) => $e->start_time->format('Y-m-d') === $day['date']);
        @endphp
        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-1 {{ $day['isToday'] ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
            @if($dayEvents->isEmpty())
                <a href="{{ route('admin.calendar.create', ['date' => $day['date']]) }}" wire:navigate
                   class="block h-full min-h-[100px] flex items-center justify-center text-zinc-300 dark:text-zinc-600 hover:text-zinc-400 dark:hover:text-zinc-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>
            @else
                <div class="space-y-1">
                    @foreach($dayEvents->sortBy('start_time') as $event)
                        <a href="{{ route('admin.calendar.edit', $event) }}" wire:navigate
                           class="block p-1.5 rounded text-xs transition-colors hover:opacity-80"
                           style="background-color: {{ $event->color ?? '#3b82f6' }}20; border-left: 3px solid {{ $event->color ?? '#3b82f6' }}">
                            <div class="font-medium text-zinc-900 dark:text-white truncate">{{ $event->title }}</div>
                            <div class="text-zinc-500 dark:text-zinc-400">{{ $event->start_time->format('H:i') }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
