<div class="bg-white p-4 sm:p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 dark:border-neutral-700 dark:bg-gray-800">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Progreso Semanal</h3>
            <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">Últimos 7 días</p>
        </div>
        <div class="text-2xl">📊</div>
    </div>

    <div class="space-y-3">
        @foreach($weekDays as $day)
            @php
                $isToday = $day['isToday'];
                $percentage = $day['percentage'];
                
                // Determine styling based on completion percentage
                if ($percentage === 100) {
                    $bgClass = 'bg-green-50 dark:bg-green-900/20';
                    $borderClass = 'border-green-200 dark:border-green-800';
                    $textClass = 'text-green-700 dark:text-green-300';
                    $percentageClass = 'text-green-600 dark:text-green-400';
                } elseif ($percentage === 0) {
                    $bgClass = 'bg-gray-50 dark:bg-neutral-800/50';
                    $borderClass = 'border-gray-200 dark:border-neutral-700';
                    $textClass = 'text-gray-600 dark:text-gray-400';
                    $percentageClass = 'text-gray-500 dark:text-gray-500';
                } else {
                    $bgClass = 'bg-blue-50 dark:bg-blue-900/20';
                    $borderClass = 'border-blue-200 dark:border-blue-800';
                    $textClass = 'text-blue-700 dark:text-blue-300';
                    $percentageClass = 'text-blue-600 dark:text-blue-400';
                }
                
                // Add highlight for today
                if ($isToday) {
                    $borderClass .= ' ring-2 ring-primary-500 ring-offset-2 dark:ring-offset-gray-800';
                }
            @endphp
            
            <div class="flex items-center gap-3 p-3 rounded-lg border {{ $bgClass }} {{ $borderClass }} transition-all">
                <div class="flex-shrink-0 w-12 text-center">
                    <div class="text-xs font-medium {{ $textClass }} uppercase">
                        {{ $day['dayName'] }}
                    </div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white mt-0.5">
                        {{ $day['dayNumber'] }}
                    </div>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm font-medium text-slate-700 dark:text-gray-300">
                            {{ $day['completed'] }} / {{ $day['total'] }} hábitos
                        </span>
                        <span class="text-sm font-bold {{ $percentageClass }}">
                            {{ $percentage }}%
                        </span>
                    </div>
                    
                    <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2">
                        <div 
                            class="h-2 rounded-full transition-all duration-500 {{ $percentage === 100 ? 'bg-green-500 dark:bg-green-500' : ($percentage === 0 ? 'bg-gray-400 dark:bg-gray-600' : 'bg-blue-500 dark:bg-blue-500') }}" 
                            style="width: {{ $percentage }}%"
                        ></div>
                    </div>
                </div>
                
                @if($isToday)
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                            Hoy
                        </span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
