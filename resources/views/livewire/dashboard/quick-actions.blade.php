<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    @foreach ($actions as $action)
        @php
            $colorClasses = match ($action['color']) {
                'blue' => 'bg-notion-blue',
                'red' => 'bg-notion-red',
                'purple' => 'bg-notion-purple',
                'green' => 'bg-notion-green',
                default => 'bg-notion-gray',
            };
        @endphp
        <a href="{{ route($action['route']) }}"
            class="flex items-center gap-2 p-2 rounded {{ $colorClasses }} hover:opacity-80 transition text-left">
            <span class="text-lg">{{ $action['icon'] }}</span>
            <span class="text-sm font-medium">{{ $action['title'] }}</span>
        </a>
    @endforeach
</div>
