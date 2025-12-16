<div class="min-h-screen bg-[#FAFAFA] dark:bg-[#191919]">
    {{-- Header (sin sticky) --}}
    <header class="h-14 flex items-center justify-between px-4 lg:px-8 border-b border-[#E9E9E7] dark:border-[#3E3E3A] bg-[#FAFAFA] dark:bg-[#191919]">
        <div class="flex items-center gap-4">
            <h1 class="text-xl font-semibold text-[#37352F] dark:text-[#EFEFED]">📔 Mi Diario</h1>
            <div class="hidden sm:flex items-center gap-2 text-sm text-[#9B9A97]">
                <span>{{ $totalEntries }} entradas</span>
                <span>•</span>
                <span>{{ $thisMonthEntries }} este mes</span>
            </div>
        </div>
        <a href="{{ route('admin.journal.create') }}" 
           class="flex items-center gap-2 px-4 py-2 bg-[#37352F] dark:bg-[#EFEFED] text-white dark:text-[#191919] rounded-lg hover:bg-[#2f2d2a] dark:hover:bg-[#D3D3D3] transition text-sm">
            ✏️ Nueva entrada
        </a>
    </header>

    <div class="max-w-4xl mx-auto px-4 lg:px-8 py-6">
        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-3 mb-6">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar en el diario..."
                       class="w-full px-3 py-2 border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg text-sm bg-white dark:bg-[#252525] text-[#37352F] dark:text-[#EFEFED] placeholder-[#9B9A97] focus:outline-none focus:ring-2 focus:ring-[#37352F]/20 dark:focus:ring-[#2383E2]/40">
            </div>

            {{-- Mood Filter --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg text-sm text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] bg-white dark:bg-[#252525]">
                    @if($filterMood)
                        {{ App\Enums\Mood::from($filterMood)->emoji() }}
                    @else
                        😊
                    @endif
                    <span>Estado</span>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute left-0 mt-1 w-40 bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg shadow-lg py-1 z-20">
                    <button wire:click="setMoodFilter(null)" @click="open = false" 
                            class="w-full text-left px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F]">Todos</button>
                    @foreach($moods as $mood)
                    <button wire:click="setMoodFilter('{{ $mood->value }}')" @click="open = false"
                            class="w-full text-left px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] flex items-center gap-2">
                        {{ $mood->emoji() }} {{ $mood->label() }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Month Filter --}}
            @if($months->count() > 0)
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg text-sm text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] bg-white dark:bg-[#252525]">
                    📅 <span>{{ $filterMonth ? \Carbon\Carbon::parse($filterMonth)->translatedFormat('M Y') : 'Mes' }}</span>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute left-0 mt-1 w-40 bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg shadow-lg py-1 z-20 max-h-48 overflow-y-auto">
                    <button wire:click="setMonthFilter(null)" @click="open = false" 
                            class="w-full text-left px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F]">Todos</button>
                    @foreach($months as $month)
                    <button wire:click="setMonthFilter('{{ $month }}')" @click="open = false"
                            class="w-full text-left px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F]">
                        {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
            
            {{-- Category Filter --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg text-sm text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] bg-white dark:bg-[#252525]">
                    @if($filterCategory !== null)
                        @if($filterCategory === 0)
                            📄 Sin categoría
                        @else
                            @php $selectedCat = $categories->find($filterCategory); @endphp
                            {{ $selectedCat?->icon ?? '📁' }} {{ $selectedCat?->name ?? 'Categoría' }}
                        @endif
                    @else
                        📁 Categoría
                    @endif
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute left-0 mt-1 w-56 bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg shadow-lg py-1 z-20 max-h-64 overflow-y-auto">
                    <button wire:click="setCategoryFilter(null)" @click="open = false" 
                            class="w-full text-left px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F]">Todas</button>
                    <button wire:click="setCategoryFilter(0)" @click="open = false"
                            class="w-full text-left px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] flex items-center gap-2">
                        📄 Sin categoría <span class="ml-auto text-xs text-[#9B9A97]">{{ $uncategorizedCount }}</span>
                    </button>
                    @foreach($categories as $category)
                    <button wire:click="setCategoryFilter({{ $category->id }})" @click="open = false"
                            class="w-full text-left px-3 py-2 text-sm text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] flex items-center gap-2">
                        <span class="w-3 h-3 rounded" style="background-color: {{ $category->color }};"></span>
                        {{ $category->icon }} {{ $category->name }}
                        <span class="ml-auto text-xs text-[#9B9A97]">{{ $category->entries_count }}</span>
                    </button>
                    @endforeach
                    <div class="border-t border-[#E9E9E7] dark:border-[#3E3E3A] mt-1 pt-1">
                        <button wire:click="openCategoryForm" @click="open = false"
                                class="w-full text-left px-3 py-2 text-sm text-[#2383E2] hover:bg-[#E7F3F8] dark:hover:bg-[#1B3A52] flex items-center gap-2">
                            + Nueva categoría
                        </button>
                    </div>
                </div>
            </div>

            @if($search || $filterMood || $filterMonth || $filterCategory !== null)
            <button wire:click="clearFilters" class="text-sm text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED]">
                ✕ Limpiar
            </button>
            @endif
        </div>

        {{-- Entries --}}
        @if($entries->isEmpty())
            <div class="text-center py-16">
                <div class="text-6xl mb-4">📔</div>
                <h3 class="text-lg font-medium text-[#37352F] dark:text-[#EFEFED] mb-2">Tu diario está vacío</h3>
                <p class="text-[#9B9A97] mb-4">Comienza a escribir tus pensamientos y reflexiones</p>
                <a href="{{ route('admin.journal.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-[#37352F] dark:bg-[#EFEFED] text-white dark:text-[#191919] rounded-lg hover:bg-[#2f2d2a] dark:hover:bg-[#D3D3D3] transition text-sm">
                    ✏️ Escribir primera entrada
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($entries as $entry)
                <div class="group border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg p-4 hover:shadow-md transition bg-white dark:bg-[#252525] relative">
                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            @if($entry->mood)
                            <span class="text-2xl" title="{{ $entry->mood->label() }}">{{ $entry->mood->emoji() }}</span>
                            @endif
                            <div>
                                <div class="font-medium text-[#37352F] dark:text-[#EFEFED]">
                                    {{ $entry->created_at->translatedFormat('l, j F Y') }}
                                </div>
                                <div class="text-xs text-[#9B9A97]">
                                    {{ $entry->created_at->format('H:i') }}
                                    @if($entry->energy_level)
                                    • Energía: {{ $entry->energy_level }}/5
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        {{-- Actions --}}
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition">
                            <a href="{{ route('admin.journal.edit', $entry) }}" 
                               class="p-1.5 hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] rounded text-sm" title="Editar">✏️</a>
                            <button @click.stop="if(confirm('¿Eliminar esta entrada?')) $wire.deleteEntry({{ $entry->id }})"
                                    class="p-1.5 hover:bg-[#FFE2DD] dark:hover:bg-[#3D2222] rounded text-sm" title="Eliminar">🗑️</button>
                        </div>
                    </div>

                    {{-- Content Preview --}}
                    <div class="text-[#37352F] dark:text-[#EFEFED] text-sm line-clamp-3 prose prose-sm dark:prose-invert max-w-none">
                        {!! Str::limit(strip_tags($entry->content), 300) !!}
                    </div>

                    {{-- Category Badge --}}
                    @if($entry->category)
                        <div class="mt-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs"
                                  style="background-color: {{ $entry->category->color }}20; color: {{ $entry->category->color }};">
                                {{ $entry->category->icon }} {{ $entry->category->name }}
                            </span>
                        </div>
                    @endif

                    {{-- Footer --}}
                    <div class="mt-3 pt-3 border-t border-[#E9E9E7] dark:border-[#3E3E3A] flex items-center justify-between text-xs text-[#9B9A97]">
                        <span>{{ $entry->word_count }} palabras</span>
                        <a href="{{ route('admin.journal.edit', $entry) }}" class="hover:text-[#37352F] dark:hover:text-[#EFEFED]">
                            Leer más →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
    
    {{-- Category Form Modal --}}
    @if($showCategoryForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 dark:bg-black/70" wire:click.self="closeCategoryForm">
            <div class="bg-white dark:bg-[#252525] rounded-xl shadow-xl max-w-md w-full p-6 border border-[#E9E9E7] dark:border-[#3E3E3A]">
                <h3 class="text-lg font-bold text-[#37352F] dark:text-[#EFEFED] mb-4">
                    {{ $isEditingCategory ? 'Editar Categoría' : 'Nueva Categoría' }}
                </h3>
                
                <form wire:submit="saveCategory" class="space-y-4">
                    {{-- Icon Selector --}}
                    <div>
                        <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-2">Icono</label>
                        <div class="flex flex-wrap gap-2 p-3 bg-[#F7F7F5] dark:bg-[#1F1F1F] rounded-lg max-h-24 overflow-y-auto">
                            @foreach($availableIcons as $iconOption)
                                <button type="button"
                                        wire:click="$set('categoryIcon', '{{ $iconOption }}')"
                                        class="w-8 h-8 flex items-center justify-center text-lg rounded transition
                                               {{ $categoryIcon === $iconOption ? 'bg-[#2383E2] text-white' : 'hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A]' }}">
                                    {{ $iconOption }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Color Selector --}}
                    <div>
                        <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-2">Color</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($availableColors as $colorOption)
                                <button type="button"
                                        wire:click="$set('categoryColor', '{{ $colorOption }}')"
                                        class="w-8 h-8 rounded-full transition {{ $categoryColor === $colorOption ? 'ring-2 ring-offset-2 ring-[#37352F] dark:ring-[#EFEFED] dark:ring-offset-[#252525]' : '' }}"
                                        style="background-color: {{ $colorOption }};"></button>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-1">Nombre</label>
                        <input type="text" wire:model="categoryName" 
                               class="w-full px-3 py-2 border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#1F1F1F] text-[#37352F] dark:text-[#EFEFED] focus:outline-none focus:ring-2 focus:ring-[#2383E2]"
                               placeholder="Ej: Reflexiones">
                        @error('categoryName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Preview --}}
                    <div class="p-3 bg-[#F7F7F5] dark:bg-[#1F1F1F] rounded-lg">
                        <span class="text-xs text-[#9B9A97] block mb-1">Vista previa:</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-sm"
                              style="background-color: {{ $categoryColor }}20; color: {{ $categoryColor }};">
                            {{ $categoryIcon }} {{ $categoryName ?: 'Nombre' }}
                        </span>
                    </div>
                    
                    {{-- Actions --}}
                    <div class="flex gap-3 justify-end pt-4 border-t border-[#E9E9E7] dark:border-[#3E3E3A]">
                        <button type="button" wire:click="closeCategoryForm"
                                class="px-4 py-2 text-sm font-medium text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-[#2383E2] hover:bg-[#1B74C9] rounded-lg transition">
                            {{ $isEditingCategory ? 'Guardar' : 'Crear' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
