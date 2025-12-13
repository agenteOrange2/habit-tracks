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
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <flux:button size="sm" variant="ghost" wire:click="previous" icon="chevron-left" />
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white min-w-[180px] text-center">
                        @if($viewMode === 'month')
                            {{ $this->currentMonth->translatedFormat('F Y') }}
                        @elseif($viewMode === 'week')
                            {{ Carbon\Carbon::parse($currentDate)->startOfWeek(Carbon\Carbon::SUNDAY)->translatedFormat('d M') }} - 
                            {{ Carbon\Carbon::parse($currentDate)->endOfWeek(Carbon\Carbon::SATURDAY)->translatedFormat('d M Y') }}
                        @else
                            {{ Carbon\Carbon::parse($currentDate)->translatedFormat('l, d F Y') }}
                        @endif
                    </h2>
                    <flux:button size="sm" variant="ghost" wire:click="next" icon="chevron-right" />
                </div>
                <div class="flex items-center gap-2">
                    <!-- View Mode Buttons -->
                    <div class="flex rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                        <button wire:click="changeView('month')" 
                            class="px-3 py-1.5 text-xs font-medium transition-colors {{ $viewMode === 'month' ? 'bg-blue-500 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                            Mes
                        </button>
                        <button wire:click="changeView('week')" 
                            class="px-3 py-1.5 text-xs font-medium transition-colors border-x border-zinc-200 dark:border-zinc-700 {{ $viewMode === 'week' ? 'bg-blue-500 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                            Semana
                        </button>
                        <button wire:click="changeView('day')" 
                            class="px-3 py-1.5 text-xs font-medium transition-colors {{ $viewMode === 'day' ? 'bg-blue-500 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                            Día
                        </button>
                    </div>
                    <flux:button size="sm" variant="subtle" wire:click="goToToday">Hoy</flux:button>
                    <a href="{{ route('admin.calendar.create') }}" wire:navigate>
                        <flux:button size="sm" variant="primary" icon="plus">Nuevo Evento</flux:button>
                    </a>
                    <a href="{{ route('admin.calendar.settings') }}" wire:navigate>
                        <flux:button size="sm" variant="ghost" icon="cog-6-tooth" />
                    </a>
                </div>
            </div>

            @if($viewMode === 'month')
                @include('livewire.calendar.partials.month-view')
            @elseif($viewMode === 'week')
                @include('livewire.calendar.partials.week-view')
            @else
                @include('livewire.calendar.partials.day-view')
            @endif
        </div>

        <!-- Selected Day Events (only for month view) -->
        @if($viewMode === 'month')
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
        @endif
    </div>
</div>
