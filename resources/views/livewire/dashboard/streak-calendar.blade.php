<div class="bg-white p-4 sm:p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 dark:border-neutral-700 dark:bg-gray-800">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Calendario de Actividad</h3>
            <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">Últimos 365 días</p>
        </div>
        <div class="text-2xl">📅</div>
    </div>

    <div class="overflow-x-auto">
        <div class="inline-block min-w-full">
            @php
                $today = now();
                $startDate = $today->copy()->subDays(364);
                $weeks = [];
                $currentWeek = [];
                
                // Build the calendar grid (52-53 weeks)
                for ($i = 0; $i < 365; $i++) {
                    $date = $startDate->copy()->addDays($i);
                    $dateString = $date->format('Y-m-d');
                    $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday
                    
                    // Get activity count for this day
                    $count = $heatmapData[$dateString] ?? 0;
                    
                    // Determine color intensity based on activity count
                    if ($count === 0) {
                        $colorClass = 'bg-gray-100 dark:bg-neutral-700';
                    } elseif ($count <= 2) {
                        $colorClass = 'bg-green-200 dark:bg-green-900';
                    } elseif ($count <= 4) {
                        $colorClass = 'bg-green-400 dark:bg-green-700';
                    } elseif ($count <= 6) {
                        $colorClass = 'bg-green-600 dark:bg-green-600';
                    } else {
                        $colorClass = 'bg-green-700 dark:bg-green-500';
                    }
                    
                    $currentWeek[$dayOfWeek] = [
                        'date' => $date,
                        'count' => $count,
                        'color' => $colorClass,
                    ];
                    
                    // When we reach Saturday or the last day, complete the week
                    if ($dayOfWeek === 6 || $i === 364) {
                        $weeks[] = $currentWeek;
                        $currentWeek = [];
                    }
                }
            @endphp

            <div class="flex gap-1">
                @foreach($weeks as $week)
                    <div class="flex flex-col gap-1">
                        @for($day = 0; $day < 7; $day++)
                            @if(isset($week[$day]))
                                @php
                                    $dayData = $week[$day];
                                    $isToday = $dayData['date']->isToday();
                                @endphp
                                <div 
                                    class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-sm {{ $dayData['color'] }} {{ $isToday ? 'ring-2 ring-primary-500 ring-offset-1' : '' }} transition-all hover:scale-125 cursor-pointer"
                                    title="{{ $dayData['date']->format('M d, Y') }}: {{ $dayData['count'] }} {{ $dayData['count'] === 1 ? 'hábito' : 'hábitos' }} completado{{ $dayData['count'] === 1 ? '' : 's' }}"
                                    x-data
                                    x-tooltip.raw="{{ $dayData['date']->format('M d, Y') }}: {{ $dayData['count'] }} {{ $dayData['count'] === 1 ? 'hábito' : 'hábitos' }} completado{{ $dayData['count'] === 1 ? '' : 's' }}"
                                ></div>
                            @else
                                <div class="w-2.5 h-2.5 sm:w-3 sm:h-3"></div>
                            @endif
                        @endfor
                    </div>
                @endforeach
            </div>

            {{-- Legend --}}
            <div class="flex items-center gap-2 mt-4 text-xs text-slate-500 dark:text-gray-400">
                <span>Menos</span>
                <div class="flex gap-1">
                    <div class="w-3 h-3 rounded-sm bg-gray-100 dark:bg-neutral-700"></div>
                    <div class="w-3 h-3 rounded-sm bg-green-200 dark:bg-green-900"></div>
                    <div class="w-3 h-3 rounded-sm bg-green-400 dark:bg-green-700"></div>
                    <div class="w-3 h-3 rounded-sm bg-green-600 dark:bg-green-600"></div>
                    <div class="w-3 h-3 rounded-sm bg-green-700 dark:bg-green-500"></div>
                </div>
                <span>Más</span>
            </div>
        </div>
    </div>
</div>
