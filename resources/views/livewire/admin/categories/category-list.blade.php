<div class="min-h-screen bg-[#FAFAFA] dark:bg-[#191919] pb-20">
    {{-- Header --}}
    <header class="max-w-4xl mx-auto px-4 sm:px-6 pt-8 sm:pt-12 pb-6">
        <div class="flex items-center sm:items-end justify-between mb-6 pb-4 border-b border-[#E9E9E7] dark:border-[#3E3E3A]">
            <div class="flex items-center gap-3">
                <span class="text-2xl sm:text-3xl">🏷️</span>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-[#37352F] dark:text-[#EFEFED]">Categorías</h1>
                    <p class="text-xs sm:text-sm text-[#9B9A97]">Administra las opciones de tus propiedades.</p>
                </div>
            </div>
            <button onclick="Livewire.dispatch('openCategoryForm')" 
                    class="bg-[#2383E2] hover:bg-[#1B74C9] text-white px-3 py-1.5 rounded text-sm font-medium shadow-sm transition flex items-center gap-1">
                <span>+</span> <span class="hidden sm:inline">Nueva</span>
            </button>
        </div>
    </header>

    {{-- Categories Table --}}
    <main class="max-w-4xl mx-auto px-4 sm:px-6">
        @if($categories->isEmpty())
            {{-- Empty State --}}
            <div class="border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#252525] p-8 sm:p-12 text-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#F7F7F5] dark:bg-[#1F1F1F] rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl sm:text-4xl">🏷️</span>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-[#37352F] dark:text-[#EFEFED] mb-2">No hay categorías todavía</h3>
                <p class="text-sm text-[#787774] dark:text-[#9B9A97] mb-6 max-w-md mx-auto">
                    Comienza creando tu primera categoría para organizar tus hábitos.
                </p>
                <button onclick="Livewire.dispatch('openCategoryForm')" 
                        class="inline-flex items-center gap-2 bg-[#2383E2] hover:bg-[#1B74C9] text-white px-5 py-2.5 rounded text-sm font-medium transition">
                    + Crear primera categoría
                </button>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="hidden md:block border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#252525] overflow-hidden">
                
                {{-- Table Header --}}
                <div class="flex text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wide bg-[#F7F7F5] dark:bg-[#1F1F1F] border-b border-[#E9E9E7] dark:border-[#3E3E3A]">
                    <div class="flex-1 px-4 py-2 border-r border-[#E9E9E7] dark:border-[#3E3E3A]">Nombre</div>
                    <div class="w-40 px-4 py-2 border-r border-[#E9E9E7] dark:border-[#3E3E3A]">Vista Previa</div>
                    <div class="w-24 px-4 py-2 border-r border-[#E9E9E7] dark:border-[#3E3E3A] text-center">Hábitos</div>
                    <div class="w-20 px-4 py-2 text-center">Estado</div>
                    <div class="w-20 px-4 py-2 text-center">Acciones</div>
                </div>

                {{-- Table Rows --}}
                <div id="categories-sortable">
                @foreach($categories as $category)
                    <div class="group flex items-center text-sm border-b border-[#E9E9E7] dark:border-[#3E3E3A] hover:bg-[#F7F7F5] dark:hover:bg-[#1F1F1F] transition h-12" data-id="{{ $category->id }}">
                        <div class="flex-1 px-4 flex items-center gap-2 text-[#37352F] dark:text-[#EFEFED]">
                            <span class="drag-handle opacity-30 cursor-grab text-lg leading-none">⋮⋮</span>
                            <span class="text-lg">{{ $category->icon ?? '📁' }}</span>
                            <span class="font-medium">{{ $category->name }}</span>
                        </div>
                        
                        <div class="w-40 px-4">
                            <span class="px-1.5 py-0.5 rounded text-xs font-medium inline-block" 
                                  style="background-color: {{ $category->color }}20; color: {{ $category->color }};">
                                {{ $category->name }}
                            </span>
                        </div>
                        
                        <div class="w-24 px-4 text-center text-[#787774] dark:text-[#9B9A97]">
                            {{ $category->habits_count }}
                        </div>
                        
                        <div class="w-20 px-4 flex justify-center">
                            <button wire:click="toggleActive({{ $category->id }})"
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors
                                        {{ $category->is_active ? 'bg-[#2383E2]' : 'bg-[#E3E2E0] dark:bg-[#3E3E3A]' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                    {{ $category->is_active ? 'translate-x-5' : 'translate-x-0.5' }}"></span>
                            </button>
                        </div>
                        
                        <div class="w-20 flex justify-center gap-1 opacity-0 group-hover:opacity-100 transition">
                            <button onclick="Livewire.dispatch('editCategory', { categoryId: {{ $category->id }} })"
                                    class="p-1 text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] rounded transition">
                                ✏️
                            </button>
                            <button wire:click="confirmDelete({{ $category->id }})"
                                    class="p-1 text-[#9B9A97] hover:text-[#EB5757] hover:bg-[#FFE2DD] dark:hover:bg-[#3D2222] rounded transition">
                                🗑️
                            </button>
                        </div>
                    </div>
                @endforeach
                </div>

                <div onclick="Livewire.dispatch('openCategoryForm')" 
                     class="flex items-center px-4 py-2 text-[#9B9A97] text-sm hover:bg-[#F7F7F5] dark:hover:bg-[#1F1F1F] cursor-pointer transition">
                    <span class="mr-2">+</span> Nueva...
                </div>
            </div>
            
            {{-- Mobile Cards --}}
            <div class="md:hidden space-y-3">
                @foreach($categories as $index => $category)
                    <div class="bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">{{ $category->icon ?? '📁' }}</span>
                                <span class="font-medium text-[#37352F] dark:text-[#EFEFED]">{{ $category->name }}</span>
                            </div>
                            <button wire:click="toggleActive({{ $category->id }})"
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors
                                        {{ $category->is_active ? 'bg-[#2383E2]' : 'bg-[#E3E2E0] dark:bg-[#3E3E3A]' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                    {{ $category->is_active ? 'translate-x-5' : 'translate-x-0.5' }}"></span>
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-between text-sm">
                            <span class="px-2 py-1 rounded text-xs font-medium" 
                                  style="background-color: {{ $category->color }}20; color: {{ $category->color }};">
                                {{ $category->name }}
                            </span>
                            <span class="text-[#787774] dark:text-[#9B9A97]">{{ $category->habits_count }} hábitos</span>
                        </div>
                        
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-[#E9E9E7] dark:border-[#3E3E3A]">
                            <div class="flex items-center gap-1">
                                @if($index > 0)
                                    <button wire:click="moveUp({{ $category->id }})" class="p-1.5 text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] rounded">↑</button>
                                @endif
                                @if($index < count($categories) - 1)
                                    <button wire:click="moveDown({{ $category->id }})" class="p-1.5 text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] rounded">↓</button>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <button onclick="Livewire.dispatch('editCategory', { categoryId: {{ $category->id }} })"
                                        class="px-3 py-1.5 text-sm text-[#2383E2] hover:bg-[#E7F3F8] dark:hover:bg-[#1B3A52] rounded">
                                    Editar
                                </button>
                                <button wire:click="confirmDelete({{ $category->id }})"
                                        class="px-3 py-1.5 text-sm text-[#EB5757] hover:bg-[#FFE2DD] dark:hover:bg-[#3D2222] rounded">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <button onclick="Livewire.dispatch('openCategoryForm')" 
                        class="w-full py-3 border-2 border-dashed border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg text-[#9B9A97] text-sm hover:border-[#2383E2] hover:text-[#2383E2] transition">
                    + Nueva categoría
                </button>
            </div>
        @endif
    </main>

    {{-- Delete Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black/40 dark:bg-black/60 z-50 flex items-center justify-center p-4" wire:click="closeDeleteModal">
            <div class="bg-white dark:bg-[#252525] w-full max-w-sm rounded-lg shadow-2xl border border-[#E9E9E7] dark:border-[#3E3E3A] overflow-hidden" wire:click.stop>
                
                <div class="bg-[#F7F7F5] dark:bg-[#1F1F1F] px-4 py-3 border-b border-[#E9E9E7] dark:border-[#3E3E3A] flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-[#37352F] dark:text-[#EFEFED]">Confirmar Eliminación</h3>
                    <button wire:click="closeDeleteModal" class="text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED] transition">✕</button>
                </div>

                <div class="p-5">
                    @if($deleteError)
                        <div class="bg-[#FFE2DD] dark:bg-[#3D2222] border border-[#EB5757]/20 rounded p-3 mb-4">
                            <p class="text-sm font-medium text-[#D44C47] dark:text-[#EB5757]">{{ $deleteError }}</p>
                        </div>
                    @else
                        <p class="text-sm text-[#37352F] dark:text-[#EFEFED] mb-2">
                            ¿Eliminar <strong>"{{ $categoryToDeleteName }}"</strong>?
                        </p>
                        <p class="text-sm text-[#787774] dark:text-[#9B9A97]">Esta acción no se puede deshacer.</p>
                    @endif
                </div>

                <div class="p-4 border-t border-[#E9E9E7] dark:border-[#3E3E3A] flex justify-end gap-2 bg-[#F7F7F5] dark:bg-[#1F1F1F]">
                    <button wire:click="closeDeleteModal" 
                            class="px-3 py-1.5 text-sm font-medium text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] rounded transition">
                        {{ $deleteError ? 'Cerrar' : 'Cancelar' }}
                    </button>
                    @if(!$deleteError)
                        <button wire:click="delete" 
                                class="px-3 py-1.5 text-sm font-medium text-white bg-[#EB5757] hover:bg-[#D44C47] rounded transition">
                            Eliminar
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Loading --}}
    <div wire:loading class="fixed inset-0 bg-black/30 dark:bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-[#252525] rounded-lg p-5 shadow-xl border border-[#E9E9E7] dark:border-[#3E3E3A]">
            <div class="flex items-center gap-3">
                <svg class="animate-spin h-5 w-5 text-[#2383E2]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-[#37352F] dark:text-[#EFEFED]">Cargando...</span>
            </div>
        </div>
    </div>

    @livewire('admin.categories.category-form')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortableElement = document.getElementById('categories-sortable');
            if (sortableElement && window.Sortable) {
                window.Sortable.create(sortableElement, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function(evt) {
                        const orderedIds = Array.from(sortableElement.children).map(el => parseInt(el.getAttribute('data-id')));
                        const component = window.Livewire.find(sortableElement.closest('[wire\\:id]').getAttribute('wire:id'));
                        component.call('updateOrder', orderedIds);
                    }
                });
            }
        });
    </script>
</div>
