<div>
    <div class="flex flex-wrap gap-1">
        @php
            $today = now();
            $startDate = $today->copy()->subDays(56); // 8 weeks
            
            for ($i = 0; $i < 56; $i++) {
                $date = $startDate->copy()->addDays($i);
                $dateString = $date->format('Y-m-d');
                $count = $heatmapData[$dateString] ?? 0;
                $isToday = $date->isToday();
                
                // Determine color based on activity
                if ($count === 0) {
                    $colorClass = 'bg-gray-200';
                } elseif ($count <= 2) {
                    $colorClass = 'bg-green-200';
                } elseif ($count <= 4) {
                    $colorClass = 'bg-green-400';
                } else {
                    $colorClass = 'bg-green-500';
                }
                
                echo '<div class="w-3 h-3 ' . $colorClass . ' rounded-sm' . ($isToday ? ' border border-gray-200' : '') . '" title="' . $date->format('M d, Y') . ': ' . $count . ' hábitos"></div>';
            }
        @endphp
    </div>
    <div class="text-[10px] text-gray-400 mt-2 flex items-center gap-1">
        Menos 
        <span class="w-2 h-2 bg-gray-200 rounded-sm"></span>
        <span class="w-2 h-2 bg-green-200 rounded-sm"></span>
        <span class="w-2 h-2 bg-green-500 rounded-sm"></span>
        Más
    </div>
</div>
