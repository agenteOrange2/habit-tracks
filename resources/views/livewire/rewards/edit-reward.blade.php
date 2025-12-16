<div class="min-h-screen bg-[#FAFAFA] dark:bg-[#191919]">
    {{-- Header (sin sticky) --}}
    <header class="h-12 flex items-center justify-between px-4 lg:px-12 border-b border-[#E9E9E7] dark:border-[#3E3E3A] bg-[#FAFAFA] dark:bg-[#191919]">
        <div class="flex items-center text-sm gap-1 text-[#37352F] dark:text-[#EFEFED]">
            <a href="{{ route('rewards.index') }}" wire:navigate class="opacity-50 hover:underline hover:opacity-100 cursor-pointer hidden sm:inline">Tienda de Recompensas</a>
            <a href="{{ route('rewards.index') }}" wire:navigate class="opacity-50 hover:underline hover:opacity-100 cursor-pointer sm:hidden">Tienda</a>
            <span class="opacity-30">/</span>
            <span class="font-medium">Editar</span>
        </div>
        
        <div class="flex items-center gap-2 sm:gap-3">
            <button wire:click="confirmDelete" class="text-sm text-[#EB5757] hover:bg-[#FFE2DD] dark:hover:bg-[#3D2222] px-2 sm:px-3 py-1.5 rounded transition">
                <span class="hidden sm:inline">Eliminar</span>
                <span class="sm:hidden">🗑️</span>
            </button>
            <a href="{{ route('rewards.index') }}" wire:navigate class="text-sm text-[#5F5E5B] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] px-3 py-1.5 rounded transition hidden sm:inline-block">
                Cancelar
            </a>
            <button wire:click="update" wire:loading.attr="disabled"
                    class="text-sm text-white bg-[#2383E2] hover:bg-[#1B74C9] disabled:opacity-50 px-4 py-1.5 rounded shadow-sm font-medium transition">
                <span wire:loading.remove wire:target="update">Guardar</span>
                <span wire:loading wire:target="update">...</span>
            </button>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="max-w-3xl mx-auto px-4 lg:px-12 mt-4">
            <div class="p-3 bg-[#DBEDDB] dark:bg-[#1B3D2F] text-[#1C3829] dark:text-[#27AE60] rounded text-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="max-w-3xl mx-auto px-4 lg:px-12 py-8 sm:py-12 pb-32">
        {{-- Icon Selector --}}
        <div class="group relative mb-6 sm:mb-8">
            <div class="text-5xl sm:text-[78px] leading-none mb-4 cursor-pointer hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] w-fit rounded px-2 transition select-none"
                 x-data="{ open: false }" @click="open = !open">
                {{ $icon }}
                
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute top-full left-0 mt-2 p-2 bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg shadow-lg z-20">
                    <div class="flex flex-wrap gap-1 w-64">
                        @foreach(['🎁', '🎮', '🍕', '🎬', '🛍️', '🎉', '☕', '🍦', '📱', '🎧', '🎯', '📺', '🍿', '🎪', '🎨', '🎭'] as $emoji)
                            <button type="button" wire:click="$set('icon', '{{ $emoji }}')" @click="open = false"
                                    class="w-10 h-10 flex items-center justify-center text-2xl hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] rounded transition {{ $icon === $emoji ? 'bg-[#E7F3F8] dark:bg-[#1B3A52] ring-1 ring-[#2383E2]' : '' }}">
                                {{ $emoji }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Title Input --}}
        <div class="mb-6 sm:mb-8">
            <input type="text" wire:model="name" placeholder="Nombre de la recompensa..." 
                   class="w-full text-2xl sm:text-4xl font-bold placeholder-[#D3D1CB] dark:placeholder-[#5A5A5A] border-none outline-none bg-transparent p-0 text-[#37352F] dark:text-[#EFEFED] focus:ring-0">
            @error('name') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Properties --}}
        <div class="space-y-1 mb-8 sm:mb-10">
            {{-- Category --}}
            <div class="flex flex-col sm:flex-row sm:items-center py-2 sm:py-1 min-h-[34px] group">
                <div class="w-full sm:w-40 text-[#787774] dark:text-[#9B9A97] text-sm flex items-center gap-1.5 flex-shrink-0 mb-1 sm:mb-0">
                    <span class="text-lg opacity-70">📂</span> 
                    <span>Categoría</span>
                </div>
                <div class="flex-1">
                    @php
                        $pillStyle = match($category) {
                            'entertainment' => 'background: #EAE4F2; color: #442A66;',
                            'food' => 'background: #FAEBDD; color: #8F4C09;',
                            'gaming' => 'background: #DDEBF1; color: #183347;',
                            'shopping' => 'background: #FBE4E4; color: #5D1715;',
                            'social' => 'background: #DDEDEA; color: #1C3829;',
                            'leisure' => 'background: #DDEBF1; color: #183347;',
                            default => 'background: #EFEFED; color: #37352F;',
                        };
                    @endphp
                    <select wire:model="category" 
                            class="appearance-none border-none outline-none text-sm cursor-pointer px-2 py-1 rounded transition hover:opacity-80 focus:ring-0"
                            style="{{ $pillStyle }}">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->value }}">{{ $cat->icon() }} {{ $cat->label() }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Cost --}}
            <div class="flex flex-col sm:flex-row sm:items-center py-2 sm:py-1 min-h-[34px] group">
                <div class="w-full sm:w-40 text-[#787774] dark:text-[#9B9A97] text-sm flex items-center gap-1.5 flex-shrink-0 mb-1 sm:mb-0">
                    <span class="text-lg opacity-70">🪙</span> 
                    <span>Costo (Puntos)</span>
                </div>
                <div class="flex-1">
                    <input type="number" wire:model="cost_points" min="1" placeholder="0"
                           class="w-full sm:w-24 bg-transparent border border-[#E9E9E7] dark:border-[#3E3E3A] px-2 py-1 rounded text-sm text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] focus:bg-white dark:focus:bg-[#252525] focus:border-[#2383E2] focus:ring-0 focus:shadow-sm transition">
                    @error('cost_points') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Icon Selection --}}
            <div class="flex flex-col sm:flex-row sm:items-start py-2 min-h-[34px] group">
                <div class="w-full sm:w-40 text-[#787774] dark:text-[#9B9A97] text-sm flex items-center gap-1.5 flex-shrink-0 mb-2 sm:mb-0 sm:pt-1">
                    <span class="text-lg opacity-70">😊</span> 
                    <span>Icono</span>
                </div>
                <div class="flex-1 flex flex-wrap gap-1">
                    @foreach(['🎁', '🎮', '🍕', '🎬', '🛍️', '🎉', '☕', '📱'] as $emoji)
                        <button type="button" wire:click="$set('icon', '{{ $emoji }}')"
                                class="w-9 h-9 flex items-center justify-center text-xl rounded transition
                                       {{ $icon === $emoji ? 'bg-[#E7F3F8] dark:bg-[#1B3A52] ring-1 ring-[#2383E2]' : 'hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F]' }}">
                            {{ $emoji }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Available Toggle --}}
            <div class="flex flex-col sm:flex-row sm:items-center py-2 sm:py-1 min-h-[34px] group">
                <div class="w-full sm:w-40 text-[#787774] dark:text-[#9B9A97] text-sm flex items-center gap-1.5 flex-shrink-0 mb-1 sm:mb-0">
                    <span class="text-lg opacity-70">✅</span> 
                    <span>Disponible</span>
                </div>
                <div class="flex-1">
                    <input type="checkbox" wire:model="is_available"
                           class="w-4 h-4 text-[#2383E2] rounded border-[#E9E9E7] dark:border-[#3E3E3A] focus:ring-[#2383E2] cursor-pointer bg-white dark:bg-[#252525]">
                </div>
            </div>

            {{-- Stats --}}
            <div class="flex flex-col sm:flex-row sm:items-center py-2 sm:py-1 min-h-[34px] group">
                <div class="w-full sm:w-40 text-[#787774] dark:text-[#9B9A97] text-sm flex items-center gap-1.5 flex-shrink-0 mb-1 sm:mb-0">
                    <span class="text-lg opacity-70">📊</span> 
                    <span>Estadísticas</span>
                </div>
                <div class="flex-1 text-sm text-[#37352F] dark:text-[#EFEFED]">
                    Canjeada <span class="font-semibold">{{ $reward->times_claimed }}</span> veces
                </div>
            </div>
        </div>

        <hr class="border-[#E9E9E7] dark:border-[#3E3E3A] mb-6 sm:mb-8">

        {{-- Description --}}
        <div class="min-h-[200px]">
            <h3 class="font-semibold text-lg text-[#37352F] dark:text-[#EFEFED] mb-2">Descripción</h3>
            <p class="text-sm text-[#9B9A97] mb-4 italic">Escribe detalles opcionales sobre esta recompensa...</p>
            
            <textarea wire:model="description" rows="6"
                      class="w-full resize-none outline-none text-[#37352F] dark:text-[#EFEFED] text-base leading-relaxed bg-transparent placeholder-[#D3D1CB] dark:placeholder-[#5A5A5A] border-none focus:ring-0"
                      placeholder="Presiona '/' para comandos o empieza a escribir..."></textarea>
            @error('description') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteConfirmation)
        <div class="fixed inset-0 bg-black/30 dark:bg-black/60 flex items-center justify-center z-50 p-4" wire:click.self="cancelDelete">
            <div class="bg-white dark:bg-[#252525] rounded-lg shadow-xl p-6 max-w-md w-full border border-[#E9E9E7] dark:border-[#3E3E3A]">
                <h3 class="text-lg font-bold text-[#37352F] dark:text-[#EFEFED] mb-2">¿Eliminar recompensa?</h3>
                <p class="text-[#787774] dark:text-[#9B9A97] text-sm mb-4">
                    Esta acción no se puede deshacer. El historial de canjes se mantendrá.
                </p>
                <div class="flex justify-end gap-3">
                    <button wire:click="cancelDelete"
                            class="px-4 py-2 text-sm text-[#5F5E5B] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] rounded transition">
                        Cancelar
                    </button>
                    <button wire:click="delete"
                            class="px-4 py-2 text-sm bg-[#EB5757] hover:bg-[#D44C47] text-white rounded transition">
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
