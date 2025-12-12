<div>
    <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
        Hábitos de Hoy 
        <span class="text-xs text-gray-400 font-normal border rounded px-1">{{ $habits->count() }}</span>
    </h3>
    
    @if($habits->count() > 0)
        <div class="border notion-border rounded-sm overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center bg-gray-50 border-b notion-border px-3 py-2 text-xs text-gray-500">
                <div class="w-8"></div>
                <div class="flex-1 font-medium">Nombre</div>
                <div class="w-32 font-medium border-l border-gray-200 pl-3">Categoría</div>
                <div class="w-24 font-medium border-l border-gray-200 pl-3">Recompensa</div>
            </div>

            {{-- Habits List --}}
            @foreach($habits as $habit)
                <div wire:key="habit-{{ $habit->id }}-{{ $habit->isCompletedToday() ? 'completed' : 'pending' }}" 
                     class="flex items-center px-3 py-2 border-b border-gray-100 hover:bg-gray-50 group transition {{ $habit->isCompletedToday() ? 'bg-gray-50' : '' }}">
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
                    <div class="flex-1 flex items-center gap-2 {{ $habit->isCompletedToday() ? 'text-gray-400 line-through' : 'text-gray-800' }}">
                        <span class="text-lg {{ $habit->isCompletedToday() ? 'opacity-50' : '' }}">{{ $habit->icon ?? '⭐' }}</span>
                        <span>{{ $habit->name }}</span>
                    </div>
                    <div class="w-32 border-l border-gray-100 pl-3">
                        <span class="px-1.5 py-0.5 rounded text-xs {{ $habit->isCompletedToday() ? 'bg-gray-200 text-gray-500' : 'bg-gray-100 text-gray-700' }}">
                            {{ $habit->getCategoryIcon() }} {{ $habit->getCategoryName() }}
                        </span>
                    </div>
                    <div class="w-24 border-l border-gray-100 pl-3">
                        <span class="px-1.5 py-0.5 rounded text-xs {{ $habit->isCompletedToday() ? 'bg-gray-200 text-gray-500' : 'bg-notion-purple' }}">
                            {{ $habit->points_reward }} XP
                        </span>
                    </div>
                </div>
            @endforeach

            {{-- Add New --}}
            <a href="{{ route('admin.habits.create') }}" 
               wire:navigate
               class="flex items-center px-3 py-2 text-gray-400 text-xs hover:bg-gray-50 cursor-pointer">
                <span class="mr-2">+</span> Nuevo
            </a>
        </div>
    @else
        <div class="p-4 bg-notion-gray rounded flex gap-3 items-center text-gray-600">
            <span class="text-xl">📋</span>
            <div class="flex-1">
                <p class="font-medium text-gray-800">No tienes hábitos programados para hoy</p>
                <p class="text-xs text-gray-500 mt-0.5">Crea tu primer hábito para comenzar tu seguimiento.</p>
            </div>
            <a href="{{ route('admin.habits.create') }}" 
               wire:navigate
               class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Crear
            </a>
        </div>
    @endif
</div>
