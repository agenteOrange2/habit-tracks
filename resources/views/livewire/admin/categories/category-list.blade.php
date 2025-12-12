<div class="min-h-screen bg-white pb-20">
    {{-- Header estilo Notion --}}
    <header class="max-w-4xl mx-auto px-6 pt-12 pb-6">
        <div class="flex items-end justify-between mb-6 pb-4 border-b border-[#E9E9E7]">
            <div class="flex items-center gap-3">
                <span class="text-3xl">🏷️</span>
                <div>
                    <h1 class="text-2xl font-bold text-[#37352F]">Categorías</h1>
                    <p class="text-sm text-[#9B9A97]">Administra las opciones de tus propiedades.</p>
                </div>
            </div>
            <button onclick="Livewire.dispatch('openCategoryForm')" 
                    class="bg-[#2383E2] hover:bg-[#1B74C9] text-white px-3 py-1.5 rounded text-sm font-medium shadow-sm transition flex items-center gap-1">
                <span>+</span> Nueva
            </button>
        </div>
    </header>

    {{-- Categories Table --}}
    <main class="max-w-4xl mx-auto px-6">
        @if($categories->isEmpty())
            {{-- Empty State --}}
            <div class="border border-[#E9E9E7] rounded-sm bg-white p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">🏷️</span>
                </div>
                <h3 class="text-lg font-bold text-[#37352F] mb-2">No hay categorías todavía</h3>
                <p class="text-[#787774] mb-6 max-w-md mx-auto">
                    Comienza creando tu primera categoría para organizar tus hábitos.
                </p>
                <button onclick="Livewire.dispatch('openCategoryForm')" 
                        class="inline-flex items-center gap-2 bg-[#2383E2] hover:bg-[#1B74C9] text-white px-6 py-3 rounded text-sm font-medium shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear primera categoría
                </button>
            </div>
        @else
            <div class="border border-[#E9E9E7] rounded-sm bg-white overflow-hidden">
                
                {{-- Table Header --}}
                <div class="flex text-[11px] font-semibold text-[#9B9A97] uppercase tracking-wide bg-[#F7F7F5] border-b border-[#E9E9E7]">
                    <div class="flex-1 px-4 py-2 border-r border-[#E9E9E7]">Nombre de Categoría</div>
                    <div class="w-40 px-4 py-2 border-r border-[#E9E9E7]">Color / Vista Previa</div>
                    <div class="w-32 px-4 py-2 border-r border-[#E9E9E7] text-center">Hábitos</div>
                    <div class="w-24 px-4 py-2 text-center">Estado</div>
                    <div class="w-24 px-4 py-2 text-center">Acciones</div>
                </div>

                {{-- Table Rows --}}
                <div id="categories-sortable">
                @foreach($categories as $category)
                    <div class="group flex items-center text-sm border-b border-[#E9E9E7] hover:bg-[#F7F7F5] transition h-12" data-id="{{ $category->id }}">
                        {{-- Name Column --}}
                        <div class="flex-1 px-4 flex items-center gap-2 text-[#37352F]">
                            <span class="drag-handle opacity-30 cursor-grab text-lg leading-none">⋮⋮</span>
                            <span class="text-lg">{{ $category->icon ?? '📁' }}</span>
                            <span class="font-medium">{{ $category->name }}</span>
                        </div>
                        
                        {{-- Color Preview Column --}}
                        <div class="w-40 px-4">
                            <span class="px-1.5 py-0.5 rounded text-xs font-medium inline-block" 
                                  style="background-color: {{ $category->color }}20; color: {{ $category->color }};">
                                {{ $category->name }}
                            </span>
                        </div>
                        
                        {{-- Habits Count Column --}}
                        <div class="w-32 px-4 text-center">
                            <span class="text-[#787774]">{{ $category->habits_count }}</span>
                        </div>
                        
                        {{-- Status Column --}}
                        <div class="w-24 px-4 flex justify-center">
                            <button wire:click="toggleActive({{ $category->id }})"
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-[#2383E2] focus:ring-offset-2
                                        {{ $category->is_active ? 'bg-[#2383E2]' : 'bg-gray-200' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                    {{ $category->is_active ? 'translate-x-5' : 'translate-x-0.5' }}">
                                </span>
                            </button>
                        </div>
                        
                        {{-- Actions Column --}}
                        <div class="w-24 flex justify-center gap-1 opacity-0 group-hover:opacity-100 transition">
                            <button onclick="Livewire.dispatch('editCategory', { categoryId: {{ $category->id }} })"
                                    class="p-1 text-gray-400 hover:text-[#37352F] hover:bg-gray-200 rounded transition"
                                    title="Editar">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </button>
                            <button wire:click="confirmDelete({{ $category->id }})"
                                    class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition"
                                    title="Eliminar">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
                </div>

                {{-- Add New Row --}}
                <div onclick="Livewire.dispatch('openCategoryForm')" 
                     class="flex items-center px-4 py-2 text-[#9B9A97] text-sm hover:bg-[#F7F7F5] cursor-pointer transition">
                    <span class="mr-2">+</span> Nueva...
                </div>
            </div>
        @endif
    </main>

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center" wire:click="closeDeleteModal">
            <div class="bg-white w-full max-w-sm rounded-lg shadow-2xl border border-[#E9E9E7] overflow-hidden transform scale-100 transition-all"
                 wire:click.stop>
                
                {{-- Modal Header --}}
                <div class="bg-[#FBFBFA] px-4 py-3 border-b border-[#E9E9E7] flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-[#37352F]">Confirmar Eliminación</h3>
                    <button wire:click="closeDeleteModal" class="text-gray-400 hover:text-gray-700 transition">✕</button>
                </div>

                {{-- Modal Body --}}
                <div class="p-5">
                    @if($deleteError)
                        <div class="bg-red-50 border border-red-200 rounded p-3 mb-4">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-red-800">No se puede eliminar</p>
                                    <p class="text-sm text-red-700 mt-1">{{ $deleteError }}</p>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-[#787774]">
                            Para eliminar esta categoría, primero debes reasignar o eliminar los hábitos asociados.
                        </p>
                    @else
                        <p class="text-sm text-[#37352F] mb-2">
                            ¿Estás seguro de que deseas eliminar la categoría 
                            <strong>"{{ $categoryToDeleteName }}"</strong>?
                        </p>
                        <p class="text-sm text-[#787774]">
                            Esta acción no se puede deshacer.
                        </p>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="p-4 border-t border-[#E9E9E7] flex justify-end gap-2 bg-[#FBFBFA]">
                    <button wire:click="closeDeleteModal" 
                            class="px-3 py-1.5 text-sm font-medium text-[#555555] hover:bg-[#EFEFED] rounded transition">
                        {{ $deleteError ? 'Cerrar' : 'Cancelar' }}
                    </button>
                    @if(!$deleteError)
                        <button wire:click="delete" 
                                class="px-3 py-1.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded shadow-sm transition">
                            Eliminar
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Loading State --}}
    <div wire:loading class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded p-6 shadow-xl">
            <div class="flex items-center gap-3">
                <svg class="animate-spin h-5 w-5 text-[#2383E2]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-[#37352F]">Cargando...</span>
            </div>
        </div>
    </div>

    {{-- Category Form Modal --}}
    @livewire('admin.categories.category-form')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortableElement = document.getElementById('categories-sortable');
            if (sortableElement && window.Sortable) {
                window.Sortable.create(sortableElement, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'bg-blue-50',
                    dragClass: 'opacity-50',
                    onEnd: function(evt) {
                        const orderedIds = Array.from(sortableElement.children)
                            .map(el => parseInt(el.getAttribute('data-id')));
                        
                        const component = window.Livewire.find(
                            sortableElement.closest('[wire\\:id]').getAttribute('wire:id')
                        );
                        component.call('updateOrder', orderedIds);
                    }
                });
            }
        });
    </script>
</div>
