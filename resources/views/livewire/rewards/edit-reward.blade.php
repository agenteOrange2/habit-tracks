<div class="min-h-screen bg-white">
    {{-- Header --}}
    <header class="h-12 flex items-center justify-between px-4 lg:px-12 border-b border-transparent hover:border-[#E9E9E7] transition sticky top-0 z-10 bg-white/95 backdrop-blur">
        <div class="flex items-center text-sm gap-1 text-[#37352F]">
            <a href="{{ route('rewards.index') }}" wire:navigate class="opacity-50 hover:underline cursor-pointer">Tienda de Recompensas</a>
            <span class="opacity-30">/</span>
            <span class="font-medium">Editar Recompensa</span>
        </div>
        
        <div class="flex items-center gap-3">
            <button wire:click="confirmDelete" class="text-sm text-red-500 hover:bg-red-50 px-3 py-1.5 rounded transition">
                Eliminar
            </button>
            <a href="{{ route('rewards.index') }}" wire:navigate class="text-sm text-[#5F5E5B] hover:bg-[#EFEFED] px-3 py-1.5 rounded transition">
                Cancelar
            </a>
            <button wire:click="update" wire:loading.attr="disabled"
                    class="text-sm text-white bg-[#2383E2] hover:bg-[#1B74C9] disabled:opacity-50 px-4 py-1.5 rounded shadow-sm font-medium transition">
                <span wire:loading.remove wire:target="update">Guardar</span>
                <span wire:loading wire:target="update">Guardando...</span>
            </button>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="max-w-3xl mx-auto px-4 lg:px-12 mt-4">
            <div class="p-3 bg-[#DBEDDB] text-[#1C3829] rounded text-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="max-w-3xl mx-auto px-4 lg:px-12 py-12 pb-32">
        {{-- Icon Selector --}}
        <div class="group relative mb-8 -mt-6">
            <div class="text-[78px] leading-none mb-4 -ml-1 cursor-pointer hover:bg-[#EFEFED] w-fit rounded px-2 transition select-none"
                 x-data="{ open: false }" @click="open = !open">
                {{ $icon }}
                
                {{-- Icon Picker Dropdown --}}
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute top-full left-0 mt-2 p-2 bg-white border border-[#E9E9E7] rounded-lg shadow-lg z-20">
                    <div class="flex flex-wrap gap-1 w-64">
                        @foreach(['🎁', '🎮', '🍕', '🎬', '🛍️', '🎉', '☕', '🍦', '📱', '🎧', '🎯', '📺', '🍿', '🎪', '🎨', '🎭'] as $emoji)
                            <button type="button" wire:click="$set('icon', '{{ $emoji }}')" @click="open = false"
                                    class="w-10 h-10 flex items-center justify-center text-2xl hover:bg-[#EFEFED] rounded transition {{ $icon === $emoji ? 'bg-[#E7F3F8] ring-1 ring-[#2383E2]' : '' }}">
                                {{ $emoji }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Title Input --}}
        <div class="mb-8">
            <input type="text" wire:model="name" placeholder="Nombre de la recompensa..." 
                   class="w-full text-4xl font-bold placeholder-[#D3D1CB] border-none outline-none bg-transparent p-0 text-[#37352F] focus:ring-0">
            @error('name')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Properties --}}
        <div class="space-y-0.5 mb-10">
            {{-- Category --}}
            <div class="flex items-center py-1 min-h-[34px] group">
                <div class="w-40 text-[#787774] text-sm flex items-center gap-1.5 flex-shrink-0">
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
                            class="appearance-none border-none outline-none text-sm cursor-pointer px-2 py-0.5 rounded transition hover:opacity-80 focus:ring-0"
                            style="{{ $pillStyle }}">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->value }}">{{ $cat->icon() }} {{ $cat->label() }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Cost --}}
            <div class="flex items-center py-1 min-h-[34px] group">
                <div class="w-40 text-[#787774] text-sm flex items-center gap-1.5 flex-shrink-0">
                    <span class="text-lg opacity-70">🪙</span> 
                    <span>Costo (Puntos)</span>
                </div>
                <div class="flex-1">
                    <input type="number" wire:model="cost_points" min="1" placeholder="0"
                           class="w-24 bg-transparent border border-transparent px-2 py-1 rounded text-sm text-[#37352F] hover:bg-[#EFEFED] focus:bg-white focus:border-[#2383E2] focus:ring-0 focus:shadow-sm transition">
                    @error('cost_points')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Icon Selection --}}
            <div class="flex items-start py-2 min-h-[34px] group">
                <div class="w-40 text-[#787774] text-sm flex items-center gap-1.5 flex-shrink-0 pt-1">
                    <span class="text-lg opacity-70">😊</span> 
                    <span>Icono</span>
                </div>
                <div class="flex-1 flex flex-wrap gap-1">
                    @foreach(['🎁', '🎮', '🍕', '🎬', '🛍️', '🎉', '☕', '📱'] as $emoji)
                        <button type="button" wire:click="$set('icon', '{{ $emoji }}')"
                                class="w-9 h-9 flex items-center justify-center text-xl rounded transition
                                       {{ $icon === $emoji ? 'bg-[#E7F3F8] ring-1 ring-[#2383E2]' : 'hover:bg-[#EFEFED]' }}">
                            {{ $emoji }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Available Toggle --}}
            <div class="flex items-center py-1 min-h-[34px] group">
                <div class="w-40 text-[#787774] text-sm flex items-center gap-1.5 flex-shrink-0">
                    <span class="text-lg opacity-70">✅</span> 
                    <span>Disponible</span>
                </div>
                <div class="flex-1">
                    <input type="checkbox" wire:model="is_available"
                           class="w-4 h-4 text-[#2383E2] rounded border-gray-300 focus:ring-[#2383E2] cursor-pointer">
                </div>
            </div>

            {{-- Stats --}}
            <div class="flex items-center py-1 min-h-[34px] group">
                <div class="w-40 text-[#787774] text-sm flex items-center gap-1.5 flex-shrink-0">
                    <span class="text-lg opacity-70">📊</span> 
                    <span>Estadísticas</span>
                </div>
                <div class="flex-1 text-sm text-[#37352F]">
                    Canjeada <span class="font-semibold">{{ $reward->times_claimed }}</span> veces
                </div>
            </div>
        </div>

        <hr class="border-[#E9E9E7] mb-8">

        {{-- Description --}}
        <div class="min-h-[200px]">
            <h3 class="font-semibold text-lg text-[#37352F] mb-2">Descripción</h3>
            <p class="text-sm text-[#9B9A97] mb-4 italic">Escribe detalles opcionales sobre esta recompensa...</p>
            
            <textarea wire:model="description" rows="6"
                      class="w-full resize-none outline-none text-[#37352F] text-base leading-relaxed bg-transparent placeholder-gray-300 border-none focus:ring-0"
                      placeholder="Presiona '/' para comandos o empieza a escribir..."></textarea>
            @error('description')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteConfirmation)
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50" wire:click.self="cancelDelete">
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-md mx-4 border border-[#E9E9E7]">
                <h3 class="text-lg font-bold text-[#37352F] mb-2">¿Eliminar recompensa?</h3>
                <p class="text-[#787774] text-sm mb-4">
                    Esta acción no se puede deshacer. El historial de canjes se mantendrá.
                </p>
                <div class="flex justify-end gap-3">
                    <button wire:click="cancelDelete"
                            class="px-4 py-2 text-sm text-[#5F5E5B] hover:bg-[#EFEFED] rounded transition">
                        Cancelar
                    </button>
                    <button wire:click="delete"
                            class="px-4 py-2 text-sm bg-red-500 hover:bg-red-600 text-white rounded transition">
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
