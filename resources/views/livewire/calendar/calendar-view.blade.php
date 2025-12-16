<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:heading size="xl" level="1" class="text-lg sm:text-xl">📅 Calendario de Estudios</flux:heading>

    @if (session('message'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">
            {{ session('message') }}
        </flux:callout>
    @endif

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <!-- Calendar Grid -->
        <div class="flex-1 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-3 sm:p-4">
            <!-- Header - Mobile -->
            <div class="flex flex-col gap-3 mb-4">
                <!-- Navigation Row -->
                <div class="flex items-center justify-between">
                    <flux:button size="sm" variant="ghost" wire:click="previous" icon="chevron-left" />
                    <h2 class="text-sm sm:text-lg font-semibold text-zinc-900 dark:text-white text-center flex-1">
                        @if($viewMode === 'month')
                            {{ $this->currentMonth->translatedFormat('F Y') }}
                        @elseif($viewMode === 'week')
                            <span class="hidden sm:inline">{{ Carbon\Carbon::parse($currentDate)->startOfWeek(Carbon\Carbon::SUNDAY)->translatedFormat('d M') }} - {{ Carbon\Carbon::parse($currentDate)->endOfWeek(Carbon\Carbon::SATURDAY)->translatedFormat('d M Y') }}</span>
                            <span class="sm:hidden">{{ Carbon\Carbon::parse($currentDate)->startOfWeek(Carbon\Carbon::SUNDAY)->translatedFormat('d M') }} - {{ Carbon\Carbon::parse($currentDate)->endOfWeek(Carbon\Carbon::SATURDAY)->translatedFormat('d M') }}</span>
                        @else
                            <span class="hidden sm:inline">{{ Carbon\Carbon::parse($currentDate)->translatedFormat('l, d F Y') }}</span>
                            <span class="sm:hidden">{{ Carbon\Carbon::parse($currentDate)->translatedFormat('d M Y') }}</span>
                        @endif
                    </h2>
                    <flux:button size="sm" variant="ghost" wire:click="next" icon="chevron-right" />
                </div>
                
                <!-- Controls Row -->
                <div class="flex items-center justify-between gap-2">
                    <!-- View Mode Buttons -->
                    <div class="flex rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                        <button wire:click="changeView('month')" 
                            class="px-2 sm:px-3 py-1.5 text-xs font-medium transition-colors {{ $viewMode === 'month' ? 'bg-blue-500 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                            Mes
                        </button>
                        <button wire:click="changeView('week')" 
                            class="px-2 sm:px-3 py-1.5 text-xs font-medium transition-colors border-x border-zinc-200 dark:border-zinc-700 {{ $viewMode === 'week' ? 'bg-blue-500 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                            <span class="hidden sm:inline">Semana</span>
                            <span class="sm:hidden">Sem</span>
                        </button>
                        <button wire:click="changeView('day')" 
                            class="px-2 sm:px-3 py-1.5 text-xs font-medium transition-colors {{ $viewMode === 'day' ? 'bg-blue-500 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                            Día
                        </button>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center gap-1 sm:gap-2">
                        <flux:button size="sm" variant="subtle" wire:click="goToToday" class="px-2 sm:px-3">
                            <span class="hidden sm:inline">Hoy</span>
                            <span class="sm:hidden text-xs">Hoy</span>
                        </flux:button>
                        <a href="{{ route('admin.calendar.create') }}" wire:navigate>
                            <flux:button size="sm" variant="primary" icon="plus">
                                <span class="hidden sm:inline">Nuevo</span>
                            </flux:button>
                        </a>
                        <a href="{{ route('admin.calendar.settings') }}" wire:navigate>
                            <flux:button size="sm" variant="ghost" icon="cog-6-tooth" />
                        </a>
                    </div>
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
        <div class="w-full lg:w-80 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-3 sm:p-4">
            @if($selectedDate)
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="font-semibold text-zinc-900 dark:text-white text-sm sm:text-base">
                        <span class="hidden sm:inline">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d M') }}</span>
                        <span class="sm:hidden">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('D, d M') }}</span>
                    </h3>
                    <a href="{{ route('admin.calendar.create', ['date' => $selectedDate]) }}" wire:navigate>
                        <flux:button size="xs" variant="subtle" icon="plus">
                            <span class="hidden sm:inline">Agregar</span>
                        </flux:button>
                    </a>
                </div>

                @if($this->selectedDayEvents->isEmpty())
                    <div class="text-center py-6 sm:py-8 text-zinc-500 dark:text-zinc-400">
                        <div class="text-2xl sm:text-3xl mb-2">📭</div>
                        <p class="text-xs sm:text-sm">No hay eventos para este día</p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($this->selectedDayEvents as $event)
                            <a href="{{ route('admin.calendar.edit', $event) }}" wire:navigate
                               class="block p-2 sm:p-3 rounded-lg border-l-4 bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                               style="border-left-color: {{ $event->color ?? '#3b82f6' }}">
                                <div class="font-medium text-zinc-900 dark:text-white text-xs sm:text-sm">
                                    {{ $event->title }}
                                </div>
                                <div class="text-[10px] sm:text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    {{ $event->start_time->format('H:i') }} - {{ $event->end_time->format('H:i') }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="text-center py-6 sm:py-8 text-zinc-500 dark:text-zinc-400">
                    <div class="text-2xl sm:text-3xl mb-2">👆</div>
                    <p class="text-xs sm:text-sm">Selecciona un día para ver los eventos</p>
                </div>
            @endif
        </div>
        @endif
    </div>
</div>
