<div class="space-y-2">
    @foreach($weekDays as $day)
        @php
            $isToday = $day['isToday'];
            $percentage = $day['percentage'];
        @endphp
        
        <div class="flex items-center gap-2 text-xs">
            <span class="w-6 {{ $isToday ? 'text-gray-900 dark:text-zinc-50 font-bold' : 'text-gray-400 dark:text-zinc-500' }}">
                {{ $day['dayName'] }}
            </span>
            <div class="flex-1 h-6 {{ $isToday ? 'bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700' : 'bg-gray-100 dark:bg-zinc-700' }} rounded relative overflow-hidden">
                @if($percentage > 0)
                    <div class="absolute inset-0 {{ $percentage === 100 ? 'bg-green-200 dark:bg-green-700' : 'bg-blue-100 dark:bg-blue-800' }}" style="width: {{ $percentage }}%"></div>
                @endif
                <span class="absolute {{ $isToday ? 'left-2' : 'right-2' }} top-1 text-[10px] {{ $isToday ? 'text-blue-800 dark:text-blue-300 z-10' : 'text-gray-400 dark:text-zinc-500' }}">
                    @if($isToday)
                        {{ $day['completed'] }} / {{ $day['total'] }} hábitos
                    @else
                        {{ $percentage }}%
                    @endif
                </span>
            </div>
        </div>
    @endforeach
</div>
