<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                🏆 Insignias Milestone
            </h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Nivel actual: {{ $this->userLevel }}</p>
        </div>
        <button wire:click="openCreateForm"
                class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition flex items-center gap-1">
            <span>+</span> Nueva Insignia
        </button>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300 text-sm">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300 text-sm">
            ✕ {{ session('error') }}
        </div>
    @endif

    {{-- Badges Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach($this->badges as $badge)
            @php
                $isAchieved = $badge->isAchievedBy(auth()->user());
            @endphp
            <div class="group relative flex flex-col items-center p-4 rounded-xl border transition-all
                        {{ $isAchieved 
                            ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-700' 
                            : 'bg-zinc-100 dark:bg-zinc-700/50 border-zinc-200 dark:border-zinc-600 opacity-60' }}">
                
                {{-- Actions (hover) --}}
                <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition flex gap-1">
                    <button wire:click="openEditForm({{ $badge->id }})"
                            class="p-1 bg-white dark:bg-zinc-800 rounded text-xs hover:bg-zinc-100 dark:hover:bg-zinc-700"
                            title="Editar">
                        ✏️
                    </button>
                    @if(!$badge->is_default)
                        <button wire:click="delete({{ $badge->id }})"
                                wire:confirm="¿Eliminar esta insignia?"
                                class="p-1 bg-white dark:bg-zinc-800 rounded text-xs hover:bg-red-50 dark:hover:bg-red-900/20"
                                title="Eliminar">
                            🗑️
                        </button>
                    @endif
                </div>
                
                {{-- Badge Icon --}}
                <span class="text-4xl mb-2 {{ $isAchieved ? '' : 'grayscale' }}">{{ $badge->icon }}</span>
                
                {{-- Badge Name --}}
                <span class="text-sm font-medium text-center {{ $isAchieved ? 'text-amber-700 dark:text-amber-300' : 'text-zinc-500 dark:text-zinc-400' }}">
                    {{ $badge->name }}
                </span>
                
                {{-- Level Required --}}
                <span class="text-xs {{ $isAchieved ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-400 dark:text-zinc-500' }}">
                    Nivel {{ $badge->level_required }}
                </span>
                
                {{-- Default Badge Indicator --}}
                @if($badge->is_default)
                    <span class="mt-1 text-[10px] px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-600 text-zinc-600 dark:text-zinc-300 rounded">
                        Predeterminada
                    </span>
                @endif
                
                {{-- Achievement Status --}}
                @if($isAchieved)
                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Create/Edit Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="closeForm">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">
                    {{ $isEditing ? 'Editar Insignia' : 'Nueva Insignia' }}
                </h3>
                
                <form wire:submit="save" class="space-y-4">
                    {{-- Icon Selector --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Icono</label>
                        <div class="flex flex-wrap gap-2 p-3 bg-zinc-50 dark:bg-zinc-700 rounded-lg max-h-32 overflow-y-auto">
                            @foreach($availableIcons as $iconOption)
                                <button type="button"
                                        wire:click="selectIcon('{{ $iconOption }}')"
                                        class="w-10 h-10 flex items-center justify-center text-2xl rounded-lg transition
                                               {{ $icon === $iconOption 
                                                   ? 'bg-amber-100 dark:bg-amber-900/30 ring-2 ring-amber-500' 
                                                   : 'hover:bg-zinc-200 dark:hover:bg-zinc-600' }}">
                                    {{ $iconOption }}
                                </button>
                            @endforeach
                        </div>
                        <div class="mt-2 text-center">
                            <span class="text-4xl">{{ $icon }}</span>
                        </div>
                    </div>
                    
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nombre</label>
                        <input type="text" wire:model="name" 
                               class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                               placeholder="Ej: Campeón">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Level Required --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nivel Requerido</label>
                        <input type="number" wire:model="levelRequired" min="1" max="1000"
                               class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                        @error('levelRequired') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Descripción (opcional)</label>
                        <input type="text" wire:model="description"
                               class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                               placeholder="Ej: Alcanza el nivel 150">
                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Actions --}}
                    <div class="flex gap-3 justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <button type="button" wire:click="closeForm"
                                class="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition">
                            {{ $isEditing ? 'Guardar Cambios' : 'Crear Insignia' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
