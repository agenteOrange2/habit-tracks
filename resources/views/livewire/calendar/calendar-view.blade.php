<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:heading size="xl" level="1">📅 Calendario de Estudios</flux:heading>

    @if (session('message'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">
            {{ session('message') }}
        </flux:callout>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Calendar Grid -->
        <div class="flex-1 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <flux:button size="sm" variant="ghost" wire:click="previousMonth" icon="chevron-left" />
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white min-w-[180px] text-center">
                        {{ $this->currentMonth->translatedFormat('F Y') }}
                    </h2>
                    <flux:button size="sm" variant="ghost" wire:click="nextMonth" icon="chevron-right" />
                </div>
                <div class="flex items-center gap-2">
                    <flux:button size="sm" variant="subtle" wire:click="goToToday">Hoy</flux:button>
                    <a href="{{ route('admin.calendar.create') }}" wire:navigate>
                        <flux:button size="sm" variant="primary" icon="plus">Nuevo Evento</flux:button>
                    </a>
                    <a href="{{ route('admin.calendar.settings') }}" wire:navigate>
                        <flux:button size="sm" variant="ghost" icon="cog-6-tooth" />
                    </a>
                </div>
            </div>

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
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $event->color ?? '#3b82f6' }}"></span>
                                @endforeach
                                @if(count($dayEvents) > 3)
                                    <span class="text-[8px] text-zinc-500">+{{ count($dayEvents) - 3 }}</span>
                                @endif
                            </div>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Selected Day Events -->
        <div class="w-full lg:w-80 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
            @if($selectedDate)
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-zinc-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d M') }}
                    </h3>
                    <a href="{{ route('admin.calendar.create', ['date' => $selectedDate]) }}" wire:navigate>
                        <flux:button size="xs" variant="subtle" icon="plus">Agregar</flux:button>
                    </a>
                </div>

                @if($this->selectedDayEvents->isEmpty())
                    <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                        <div class="text-3xl mb-2">📭</div>
                        <p class="text-sm">No hay eventos para este día</p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($this->selectedDayEvents as $event)
                            <a href="{{ route('admin.calendar.edit', $event) }}" wire:navigate
                               class="block p-3 rounded-lg border-l-4 bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                               style="border-left-color: {{ $event->color ?? '#3b82f6' }}">
                                <div class="font-medium text-zinc-900 dark:text-white text-sm">
                                    {{ $event->title }}
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    {{ $event->start_time->format('H:i') }} - {{ $event->end_time->format('H:i') }}
                                </div>
                                @if($event->habit)
                                    <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                        🎯 {{ $event->habit->name }}
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                    <div class="text-3xl mb-2">👆</div>
                    <p class="text-sm">Selecciona un día para ver los eventos</p>
                </div>
            @endif
        </div>
    </div>
</div>
