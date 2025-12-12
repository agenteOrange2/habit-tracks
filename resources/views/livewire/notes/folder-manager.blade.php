<div class="min-h-screen bg-white p-6">
    <div class="max-w-4xl mx-auto">
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('notes.index') }}" wire:navigate 
               class="inline-flex items-center gap-2 text-sm text-[#787774] hover:text-[#37352F] transition">
                ← Volver a Notas
            </a>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-4 p-3 bg-[#DBEDDB] text-[#1C3829] rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-[#37352F]">Gestión de Carpetas</h2>
            <button wire:click="openModal"
                    class="flex items-center gap-2 px-4 py-2 bg-[#2383E2] text-white text-sm rounded hover:bg-[#1B74C9] transition">
                <span>+</span> Nueva carpeta
            </button>
        </div>

    {{-- Folders List --}}
    <div class="space-y-2">
        @forelse($folders as $folder)
            <div class="flex items-center justify-between p-4 bg-white border border-[#E9E9E7] rounded-lg hover:shadow-sm transition">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">{{ $folder->icon }}</span>
                    <div>
                        <h3 class="font-medium text-[#37352F]">{{ $folder->name }}</h3>
                        <p class="text-xs text-[#9B9A97]">{{ $folder->notes_count }} {{ Str::plural('nota', $folder->notes_count) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="editFolder({{ $folder->id }})"
                            class="px-3 py-1 text-xs text-[#2383E2] hover:bg-[#E7F3F8] rounded transition">
                        Editar
                    </button>
                    <button wire:click="deleteFolder({{ $folder->id }})"
                            wire:confirm="¿Eliminar esta carpeta? Las notas se moverán a 'Sin carpeta'"
                            class="px-3 py-1 text-xs text-red-600 hover:bg-red-50 rounded transition">
                        Eliminar
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <span class="text-6xl block mb-4">📁</span>
                <p class="text-[#787774] text-sm">No tienes carpetas creadas</p>
                <p class="text-[#9B9A97] text-xs mt-1">Crea una carpeta para organizar tus notas</p>
            </div>
        @endforelse
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-black/30 z-50 flex items-center justify-center" wire:click="closeModal">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md" wire:click.stop>
                <h3 class="text-lg font-semibold text-[#37352F] mb-4">
                    {{ $isEditing ? 'Editar carpeta' : 'Nueva carpeta' }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-[#37352F] mb-1">Nombre</label>
                        <input type="text" 
                               wire:model="name"
                               class="w-full px-3 py-2 border border-[#E9E9E7] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2383E2]"
                               placeholder="Mi carpeta"
                               autofocus>
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Icon --}}
                    <div>
                        <label class="block text-sm font-medium text-[#37352F] mb-1">Icono (emoji)</label>
                        <input type="text" 
                               wire:model="icon"
                               class="w-full px-3 py-2 border border-[#E9E9E7] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2383E2]"
                               placeholder="📁"
                               maxlength="10">
                        <p class="mt-1 text-xs text-[#9B9A97]">Puedes usar cualquier emoji</p>
                    </div>

                    {{-- Sort Order --}}
                    <div>
                        <label class="block text-sm font-medium text-[#37352F] mb-1">Orden (opcional)</label>
                        <input type="number" 
                               wire:model="sortOrder"
                               class="w-full px-3 py-2 border border-[#E9E9E7] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2383E2]"
                               placeholder="0">
                        <p class="mt-1 text-xs text-[#9B9A97]">Menor número aparece primero</p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 text-sm text-[#787774] hover:bg-[#EFEFED] rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm bg-[#2383E2] text-white rounded-lg hover:bg-[#1B74C9] transition">
                            {{ $isEditing ? 'Actualizar' : 'Crear' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    </div>
</div>

