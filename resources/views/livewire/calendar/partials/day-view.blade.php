@php
    $currentDayDate = Carbon\Carbon::parse($currentDate);
@endphp

<!-- Day header -->
<div class="text-center mb-4 pb-4 border-b border-zinc-200 dark:border-zinc-700">
    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $currentDayDate->translatedFormat('l') }}</div>
    <div class="text-4xl font-bold {{ $currentDayDate->isToday() ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-900 dark:text-white' }}">
        {{ $currentDayDate->day }}
    </div>
    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $currentDayDate->translatedFormat('F Y') }}</div>
</div>

<!-- Day events -->
<div class="space-y-2">
    @if($this->dayEvents->isEmpty())
        <div class="text-center py-12 text-zinc-500 dark:text-zinc-400">
            <div class="text-4xl mb-3">📭</div>
            <p class="text-sm mb-4">No hay eventos para este día</p>
            <a href="{{ route('admin.calendar.create', ['date' => $currentDate]) }}" wire:navigate>
                <flux:button size="sm" variant="primary" icon="plus">Crear evento</flux:button>
            </a>
        </div>
    @else
        <!-- Timeline view -->
        <div class="relative">
            @foreach($this->dayEvents->sortBy('start_time') as $event)
                <a href="{{ route('admin.calendar.edit', $event) }}" wire:navigate
                   class="block mb-3 p-4 rounded-lg border-l-4 bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                   style="border-left-color: {{ $event->color ?? '#3b82f6' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="font-semibold text-zinc-900 dark:text-white">
                                {{ $event->title }}
                            </div>
                            @if($event->description)
                                <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                                    {{ Str::limit($event->description, 100) }}
                                </div>
                            @endif
                            @if($event->habit)
                                <div class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                                    🎯 {{ $event->habit->name }}
                                </div>
                            @endif
                        </div>
                        <div class="text-right ml-4">
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $event->start_time->format('H:i') }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $event->end_time->format('H:i') }}
                            </div>
                            <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">
                                {{ $event->getDurationInMinutes() }} min
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        
        <div class="text-center pt-4">
            <a href="{{ route('admin.calendar.create', ['date' => $currentDate]) }}" wire:navigate>
                <flux:button size="sm" variant="subtle" icon="plus">Agregar evento</flux:button>
            </a>
        </div>
    @endif
</div>
