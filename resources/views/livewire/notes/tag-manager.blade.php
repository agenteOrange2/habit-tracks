<div class="min-h-screen bg-white dark:bg-[#191919] p-4 sm:p-6">
    <div class="max-w-6xl mx-auto">
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('notes.index') }}" wire:navigate 
               class="inline-flex items-center gap-2 text-sm text-[#787774] dark:text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED] transition">
                ← Volver a Notas
            </a>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-4 p-3 bg-[#DBEDDB] dark:bg-green-900/30 text-[#1C3829] dark:text-green-300 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-[#37352F] dark:text-[#EFEFED]">Gestión de Etiquetas</h2>
            <button wire:click="openModal"
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-[#2383E2] text-white text-sm rounded hover:bg-[#1B74C9] transition w-full sm:w-auto">
                <span>+</span> Nueva etiqueta
            </button>
        </div>

        {{-- Tags Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($tags as $tag)
                <div class="p-4 bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg hover:shadow-sm transition">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            <span class="w-4 h-4 rounded flex-shrink-0" style="background-color: {{ $tag->color }};"></span>
                            <h3 class="font-medium text-[#37352F] dark:text-[#EFEFED] truncate">{{ $tag->name }}</h3>
                        </div>
                    </div>
                    <p class="text-xs text-[#9B9A97] mb-3">{{ $tag->notes_count }} {{ Str::plural('nota', $tag->notes_count) }}</p>
                    <div class="flex gap-2">
                        <button wire:click="editTag({{ $tag->id }})"
                                class="flex-1 px-3 py-1.5 text-xs text-[#2383E2] hover:bg-[#E7F3F8] dark:hover:bg-[#2383E2]/20 rounded transition">
                            Editar
                        </button>
                        <button wire:click="deleteTag({{ $tag->id }})"
                                wire:confirm="¿Eliminar esta etiqueta?"
                                class="flex-1 px-3 py-1.5 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition">
                            Eliminar
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <span class="text-6xl block mb-4">🏷️</span>
                    <p class="text-[#787774] dark:text-[#9B9A97] text-sm">No tienes etiquetas creadas</p>
                    <p class="text-[#9B9A97] dark:text-[#5A5A5A] text-xs mt-1">Crea etiquetas para categorizar tus notas</p>
                </div>
            @endforelse
        </div>

        {{-- Modal --}}
        @if($showModal)
            <div class="fixed inset-0 bg-black/30 dark:bg-black/50 z-50 flex items-center justify-center p-4" wire:click="closeModal">
                <div class="bg-white dark:bg-[#252525] rounded-lg shadow-xl p-4 sm:p-6 w-full max-w-md" wire:click.stop>
                    <h3 class="text-lg font-semibold text-[#37352F] dark:text-[#EFEFED] mb-4">
                        {{ $isEditing ? 'Editar etiqueta' : 'Nueva etiqueta' }}
                    </h3>

                    <form wire:submit="save" class="space-y-4">
                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-1">Nombre</label>
                            <input type="text" 
                                   wire:model="name"
                                   class="w-full px-3 py-2 border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2383E2] bg-white dark:bg-[#191919] text-[#37352F] dark:text-[#EFEFED]"
                                   placeholder="Mi etiqueta"
                                   autofocus>
                            @error('name')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Color --}}
                        <div>
                            <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-2">Color</label>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach($availableColors as $colorCode => $colorName)
                                    <button type="button"
                                            wire:click="$set('color', '{{ $colorCode }}')"
                                            class="w-full h-10 sm:h-12 rounded-lg border-2 transition {{ $color === $colorCode ? 'border-[#2383E2] ring-2 ring-[#2383E2] ring-offset-2 dark:ring-offset-[#252525]' : 'border-[#E9E9E7] dark:border-[#3E3E3A] hover:border-[#9B9A97]' }}"
                                            style="background-color: {{ $colorCode }};"
                                            title="{{ ucfirst($colorName) }}">
                                        @if($color === $colorCode)
                                            <span class="text-xl">✓</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                            @error('color')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Preview --}}
                        <div class="p-3 bg-[#F7F7F5] dark:bg-[#191919] rounded-lg">
                            <p class="text-xs text-[#9B9A97] mb-2">Vista previa:</p>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-sm"
                                  style="background-color: {{ $color }}20; color: {{ $color }};">
                                {{ $name ?: 'Mi etiqueta' }}
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 pt-4">
                            <button type="button"
                                    wire:click="closeModal"
                                    class="px-4 py-2 text-sm text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#3E3E3A] rounded-lg transition">
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

