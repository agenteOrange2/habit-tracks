<div>

    @if($achievements->count() > 0)
        <div class="space-y-2">
            @foreach($achievements as $achievement)
                <div class="p-3 bg-notion-gray rounded flex gap-3 items-center text-gray-600 hover:bg-gray-200 transition">
                    <span class="text-xl">{{ $achievement->icon ?? '🏆' }}</span>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800 text-sm">{{ $achievement->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">+{{ $achievement->points_reward }} XP</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-4 bg-notion-gray rounded flex gap-3 items-center text-gray-600">
            <span class="text-xl">🏆</span>
            <div class="flex-1">
                <p class="font-medium text-gray-800">Aún no has desbloqueado logros</p>
                <p class="text-xs text-gray-500 mt-0.5">Completa hábitos para verlos aquí.</p>
            </div>
        </div>
    @endif
</div>
