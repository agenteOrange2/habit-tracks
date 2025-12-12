<div class="bg-white p-4 sm:p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 dark:border-neutral-700 dark:bg-gray-800">
    {{-- Header with navigation --}}
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ \Carbon\Carbon::parse($currentMonth)->format('F Y') }}
        </h3>
        <div class="flex gap-2">
            <button 
                wire:click="previousMonth" 
                wire:loading.attr="disabled"
                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                aria-label="Previous month"
            >
                <svg class="w-5 h-5 text-slate-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button 
                wire:click="nextMonth" 
                wire:loading.attr="disabled"
                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                aria-label="Next month"
            >
                <svg class="w-5 h-5 text-slate-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Calendar grid --}}
    <div class="mb-4" wire:loading.class="opacity-50">
        {{-- Day headers --}}
        <div class="grid grid-cols-7 gap-1 mb-2">
            @foreach(['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $dayName)
                <div class="text-center text-xs font-medium text-slate-500 dark:text-gray-400 py-2">
                    {{ $dayName }}
                </div>
            @endforeach
        </div>

        {{-- Calendar days --}}
        @php
            $currentMonth = \Carbon\Carbon::parse($this->currentMonth);
            $startOfMonth = $currentMonth->copy()->startOfMonth();
            $endOfMonth = $currentMonth->copy()->endOfMonth();
            $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 = Sunday
            $daysInMonth = $endOfMonth->day;
            $today = now();
        @endphp

        <div class="grid grid-cols-7 gap-1">
            {{-- Empty cells before month starts --}}
            @for($i = 0; $i < $startDayOfWeek; $i++)
                <div class="aspect-square"></div>
            @endfor

            {{-- Days of the month --}}
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $date = $startOfMonth->copy()->addDays($day - 1);
                    $isToday = $date->isSameDay($today);
                    $hasActivity = in_array($day, $activityDays);
                    $isSelected = $selectedDay && $date->isSameDay($selectedDay);
                @endphp
                <button
                    wire:click="selectDay('{{ $date->format('Y-m-d') }}')"
                    class="aspect-square flex items-center justify-center rounded-lg text-sm font-medium transition-all
                        {{ $isToday ? 'ring-2 ring-primary-500 ring-offset-1 dark:ring-offset-gray-800' : '' }}
                        {{ $hasActivity ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800' : 'text-slate-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}
                        {{ $isSelected ? 'bg-primary-500 text-white hover:bg-primary-600' : '' }}
                        {{ $isToday && !$isSelected ? 'font-bold' : '' }}"
                    title="{{ $date->format('F j, Y') }}"
                >
                    {{ $day }}
                </button>
            @endfor
        </div>
    </div>

    {{-- Loading indicator --}}
    <div wire:loading class="text-center py-2">
        <span class="text-sm text-slate-500 dark:text-gray-400">Cargando...</span>
    </div>

    {{-- Selected day details --}}
    @if($selectedDay && count($selectedDayHabits) > 0)
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-3">
                {{ \Carbon\Carbon::parse($selectedDay)->format('F j, Y') }}
            </h4>
            <div class="space-y-2">
                @foreach($selectedDayHabits as $habit)
                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $habit['name'] }}</p>
                            <p class="text-xs text-slate-500 dark:text-gray-400">{{ $habit['time'] }}</p>
                        </div>
                        <span class="text-xs font-semibold text-green-600 dark:text-green-400">
                            +{{ $habit['points'] }} XP
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($selectedDay && count($selectedDayHabits) === 0)
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-slate-500 dark:text-gray-400 text-center">
                No hay hábitos completados en esta fecha
            </p>
        </div>
    @endif

    {{-- Legend --}}
    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-center gap-4 text-xs text-slate-500 dark:text-gray-400">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700"></div>
                <span>Con actividad</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded ring-2 ring-primary-500"></div>
                <span>Hoy</span>
            </div>
        </div>
    </div>
</div>
