<div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-6 h-full flex flex-col">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-gray-900 dark:text-zinc-50">Historial</h3>
        <button class="text-gray-400 dark:text-zinc-500 hover:text-gray-900 dark:hover:text-zinc-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
            </svg>
        </button>
    </div>

    {{-- Months Grid --}}
    <div class="flex gap-4 overflow-x-auto pb-1 no-scrollbar">
        @php
            $today = now();
            $months = [];
            
            // Get last 3 months
            for ($i = 2; $i >= 0; $i--) {
                $monthDate = $today->copy()->subMonths($i);
                $monthName = $monthDate->locale('es')->monthName;
                $daysInMonth = $monthDate->daysInMonth;
                $startOfMonth = $monthDate->copy()->startOfMonth();
                
                $days = [];
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = $startOfMonth->copy()->addDays($day - 1);
                    $dateString = $date->format('Y-m-d');
                    $count = $heatmapData[$dateString] ?? 0;
                    $isToday = $date->isToday();
                    
                    $days[] = [
                        'date' => $date,
                        'count' => $count,
                        'isToday' => $isToday,
                    ];
                }
                
                $months[] = [
                    'name' => ucfirst($monthName),
                    'days' => $days,
                ];
            }
            
            $totalActiveDays = collect($heatmapData)->filter(fn($count) => $count > 0)->count();
        @endphp

        @foreach($months as $index => $month)
            <div class="flex-1 min-w-[100px] {{ $index > 0 ? 'border-l border-gray-100 dark:border-zinc-700 pl-4' : '' }}">
                <div class="text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase mb-2">{{ $month['name'] }}</div>
                <div class="grid grid-cols-7 gap-1">
                    @foreach($month['days'] as $day)
                        @php
                            if ($day['isToday']) {
                                $colorClass = 'bg-red-500 dark:bg-red-600 ring-2 ring-red-100 dark:ring-red-900';
                            } elseif ($day['count'] > 0) {
                                $colorClass = 'bg-blue-500 dark:bg-blue-600';
                            } else {
                                $colorClass = 'bg-gray-100 dark:bg-zinc-700';
                            }
                        @endphp
                        <div class="w-2 h-2 rounded-full {{ $colorClass }} cursor-pointer hover:scale-125 transition-transform" 
                             title="{{ $day['date']->format('M d, Y') }}: {{ $day['count'] }} hábitos"></div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Footer --}}
    <div class="mt-3 pt-3 border-t border-gray-50 dark:border-zinc-700 flex justify-between items-center text-xs">
        <span class="text-gray-400 dark:text-zinc-500">Total días activos: {{ $totalActiveDays }}</span>
        <a href="/admin/analytics" class="text-blue-600 dark:text-blue-400 font-medium cursor-pointer hover:underline">Ver todo</a>
    </div>
</div>
