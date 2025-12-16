<div>
    @php
        $completedCount = $habits->filter->isCompletedToday()->count();
        $totalCount = $habits->count();
        $allCompleted = $totalCount > 0 && $completedCount === $totalCount;
    @endphp
    <p class="text-xs text-gray-500 dark:text-zinc-400 mb-3">
        {{ $completedCount }}/{{ $totalCount }} completados
        @if($allCompleted)
            <span class="text-green-600 dark:text-green-400 font-medium">🎉 ¡Todos completados! (+50 XP bonus)</span>
        @elseif($completedCount > 0)
            <span class="text-blue-600 dark:text-blue-400">· Sigue así, completa todos para +50 XP bonus</span>
        @else
            <span class="text-gray-400 dark:text-zinc-500">· Completa todos para ganar +50 XP bonus</span>
        @endif
    </p>

    @if($habits->count() > 0)
        <div class="border border-gray-200 dark:border-zinc-700 rounded-sm overflow-hidden bg-white dark:bg-zinc-800">
            {{-- Header --}}
            <div class="flex items-center bg-gray-50 dark:bg-zinc-700/50 border-b border-gray-200 dark:border-zinc-700 px-3 py-2 text-xs text-gray-500 dark:text-zinc-400">
                <div class="w-8"></div>
                <div class="flex-1 font-medium">Nombre</div>
                <div class="w-32 font-medium border-l border-gray-200 dark:border-zinc-700 pl-3">Categoría</div>
                <div class="w-24 font-medium border-l border-gray-200 dark:border-zinc-700 pl-3">Recompensa</div>
            </div>

            {{-- Habits List --}}
            @foreach($habits as $habit)
                <div wire:key="habit-{{ $habit->id }}-{{ $habit->isCompletedToday() ? 'completed' : 'pending' }}" 
                     class="flex items-center px-3 py-2 border-b border-gray-100 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 group transition {{ $habit->isCompletedToday() ? 'bg-gray-50 dark:bg-zinc-700/50' : '' }}">
                    <div class="w-8 flex items-center">
                        <input 
                            type="checkbox" 
                            wire:click="toggleHabit({{ $habit->id }})"
                            {{ $habit->isCompletedToday() ? 'checked' : '' }}
                            class="notion-checkbox cursor-pointer"
                            wire:loading.attr="disabled"
                            wire:target="toggleHabit({{ $habit->id }})"
                        >
                    </div>
                    <div class="flex-1 flex items-center gap-2 {{ $habit->isCompletedToday() ? 'text-gray-400 dark:text-zinc-500 line-through' : 'text-gray-800 dark:text-zinc-200' }}">
                        <span class="text-lg {{ $habit->isCompletedToday() ? 'opacity-50' : '' }}">{{ $habit->icon ?? '⭐' }}</span>
                        <span>{{ $habit->name }}</span>
                    </div>
                    <div class="w-32 border-l border-gray-100 dark:border-zinc-700 pl-3">
                        <span class="px-1.5 py-0.5 rounded text-xs {{ $habit->isCompletedToday() ? 'bg-gray-200 dark:bg-zinc-600 text-gray-500 dark:text-zinc-400' : 'bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300' }}">
                            {{ $habit->getCategoryIcon() }} {{ $habit->getCategoryName() }}
                        </span>
                    </div>
                    <div class="w-24 border-l border-gray-100 dark:border-zinc-700 pl-3">
                        <span class="px-1.5 py-0.5 rounded text-xs {{ $habit->isCompletedToday() ? 'bg-gray-200 dark:bg-zinc-600 text-gray-500 dark:text-zinc-400' : 'bg-notion-purple' }}">
                            {{ $habit->points_reward }} XP
                        </span>
                    </div>
                </div>
            @endforeach

            {{-- Add New --}}
            <a href="{{ route('admin.habits.create') }}" 
               wire:navigate
               class="flex items-center px-3 py-2 text-gray-400 dark:text-zinc-500 text-xs hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer">
                <span class="mr-2">+</span> Nuevo
            </a>
        </div>
    @else
        <div class="p-4 bg-gray-50 dark:bg-zinc-700/50 border border-gray-200 dark:border-zinc-700 rounded flex gap-3 items-center">
            <span class="text-xl">📋</span>
            <div class="flex-1">
                <p class="font-medium text-gray-800 dark:text-zinc-200">No tienes hábitos programados para hoy</p>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Crea tu primer hábito para comenzar tu seguimiento.</p>
            </div>
            <a href="{{ route('admin.habits.create') }}" 
               wire:navigate
               class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">
                Crear
            </a>
        </div>
    @endif
</div>
