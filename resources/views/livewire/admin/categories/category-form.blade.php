<div>
    @if($showModal)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center" wire:click="closeModal">
            <div class="bg-white w-full max-w-sm rounded-lg shadow-2xl border border-[#E9E9E7] overflow-hidden transform scale-100 transition-all" 
                 wire:click.stop>
                
                {{-- Modal Header --}}
                <div class="bg-[#FBFBFA] px-4 py-3 border-b border-[#E9E9E7] flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-[#37352F]">
                        {{ $isEditing ? 'Editar Categoría' : 'Nueva Categoría' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-700 transition">✕</button>
                </div>

                {{-- Modal Body --}}
                <div class="p-5 space-y-5">
                    
                    {{-- Name Field --}}
                    <div>
                        <label class="text-[11px] font-bold text-[#9B9A97] uppercase tracking-wide block mb-1.5">
                            Nombre
                        </label>
                        <input type="text" 
                               wire:model.live="name"
                               class="w-full text-sm text-[#37352F] border border-[rgba(55,53,47,0.16)] rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#2383E2]/20 focus:border-[#2383E2] transition shadow-sm"
                               placeholder="Escribe un nombre...">
                        @error('name')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Icon Field --}}
                    <div>
                        <label class="text-[11px] font-bold text-[#9B9A97] uppercase tracking-wide block mb-2.5">
                            Icono
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="text-3xl bg-[#F7F7F5] rounded p-2 border border-[#E9E9E7]">
                                {{ $icon }}
                            </div>
                            <div class="flex-1">
                                <input type="text" 
                                       wire:model.live="icon"
                                       class="w-full text-sm text-[#37352F] border border-[rgba(55,53,47,0.16)] rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#2383E2]/20 focus:border-[#2383E2] transition shadow-sm"
                                       placeholder="Emoji...">
                            </div>
                        </div>
                        
                        {{-- Emoji Picker --}}
                        <div class="mt-3 grid grid-cols-8 gap-2">
                            @foreach(['📁', '💼', '🧘', '📚', '👥', '🎨', '🏠', '💰', '⭐', '🔥', '💪', '🎯', '🌱', '🎓', '🏃', '🍎', '💡', '🎵', '✈️', '🎮', '📝', '🏆', '💻', '📱'] as $emoji)
                                <button type="button"
                                        wire:click="$set('icon', '{{ $emoji }}')"
                                        class="text-2xl p-2 rounded hover:bg-[#F7F7F5] transition {{ $icon === $emoji ? 'bg-[#DDEBF1] ring-2 ring-[#2383E2]' : '' }}">
                                    {{ $emoji }}
                                </button>
                            @endforeach
                        </div>
                        @error('icon')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Color Field --}}
                    <div>
                        <label class="text-[11px] font-bold text-[#9B9A97] uppercase tracking-wide block mb-2.5">
                            Color
                        </label>
                        
                        {{-- Color Preview --}}
                        <div class="mb-3 p-3 rounded" style="background-color: {{ $color }}20;">
                            <span class="text-sm font-medium" style="color: {{ $color }};">
                                {{ $name ?: 'Vista previa' }}
                            </span>
                        </div>
                        
                        {{-- Color Picker --}}
                        <div class="flex flex-wrap gap-3 px-1">
                            @foreach($this->availableColors as $availableColor)
                                <button type="button"
                                        wire:click="$set('color', '{{ $availableColor }}')"
                                        class="relative w-6 h-6 rounded-full cursor-pointer transition-transform hover:scale-110 {{ $color === $availableColor ? 'ring-2 ring-offset-2 ring-[#2383E2]' : '' }}"
                                        style="background-color: {{ $availableColor }};"
                                        title="{{ $availableColor }}">
                                </button>
                            @endforeach
                        </div>
                        @error('color')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Order Field (Hidden for now, auto-calculated) --}}
                    <input type="hidden" wire:model="order">

                </div>

                {{-- Modal Footer --}}
                <div class="p-4 border-t border-[#E9E9E7] flex justify-end gap-2 bg-[#FBFBFA]">
                    <button wire:click="closeModal" 
                            class="px-3 py-1.5 text-sm font-medium text-[#555555] hover:bg-[#EFEFED] rounded transition">
                        Cancelar
                    </button>
                    <button wire:click="save" 
                            class="px-3 py-1.5 text-sm font-medium text-white bg-[#2383E2] hover:bg-[#1B74C9] rounded shadow-sm transition">
                        {{ $isEditing ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
