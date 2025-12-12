<div class="min-h-screen bg-white">
    {{-- Header --}}
    <header class="h-14 flex items-center justify-between px-4 lg:px-8 border-b border-[#E9E9E7] sticky top-0 z-10 bg-white">
        <div class="flex items-center gap-4">
            <h1 class="text-xl font-semibold text-[#37352F]">📔 Mi Diario</h1>
            <div class="hidden sm:flex items-center gap-2 text-sm text-[#9B9A97]">
                <span>{{ $totalEntries }} entradas</span>
                <span>•</span>
                <span>{{ $thisMonthEntries }} este mes</span>
            </div>
        </div>
        <a href="{{ route('admin.journal.create') }}" 
           class="flex items-center gap-2 px-4 py-2 bg-[#37352F] text-white rounded-lg hover:bg-[#2f2d2a] transition text-sm">
            ✏️ Nueva entrada
        </a>
    </header>

    <div class="max-w-4xl mx-auto px-4 lg:px-8 py-6">
        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-3 mb-6">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar en el diario..."
                       class="w-full px-3 py-2 border border-[#E9E9E7] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#37352F]/20">
            </div>

            {{-- Mood Filter --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 border border-[#E9E9E7] rounded-lg text-sm hover:bg-[#EFEFED]">
                    @if($filterMood)
                        {{ App\Enums\Mood::from($filterMood)->emoji() }}
                    @else
                        😊
                    @endif
                    <span>Estado</span>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute left-0 mt-1 w-40 bg-white border border-[#E9E9E7] rounded-lg shadow-lg py-1 z-20">
                    <button wire:click="setMoodFilter(null)" @click="open = false" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-[#EFEFED]">Todos</button>
                    @foreach($moods as $mood)
                    <button wire:click="setMoodFilter('{{ $mood->value }}')" @click="open = false"
                            class="w-full text-left px-3 py-2 text-sm hover:bg-[#EFEFED] flex items-center gap-2">
                        {{ $mood->emoji() }} {{ $mood->label() }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Month Filter --}}
            @if($months->count() > 0)
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 border border-[#E9E9E7] rounded-lg text-sm hover:bg-[#EFEFED]">
                    📅 <span>{{ $filterMonth ? \Carbon\Carbon::parse($filterMonth)->translatedFormat('M Y') : 'Mes' }}</span>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute left-0 mt-1 w-40 bg-white border border-[#E9E9E7] rounded-lg shadow-lg py-1 z-20 max-h-48 overflow-y-auto">
                    <button wire:click="setMonthFilter(null)" @click="open = false" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-[#EFEFED]">Todos</button>
                    @foreach($months as $month)
                    <button wire:click="setMonthFilter('{{ $month }}')" @click="open = false"
                            class="w-full text-left px-3 py-2 text-sm hover:bg-[#EFEFED]">
                        {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            @if($search || $filterMood || $filterMonth)
            <button wire:click="clearFilters" class="text-sm text-[#9B9A97] hover:text-[#37352F]">
                ✕ Limpiar
            </button>
            @endif
        </div>

        {{-- Entries --}}
        @if($entries->isEmpty())
            <div class="text-center py-16">
                <div class="text-6xl mb-4">📔</div>
                <h3 class="text-lg font-medium text-[#37352F] mb-2">Tu diario está vacío</h3>
                <p class="text-[#9B9A97] mb-4">Comienza a escribir tus pensamientos y reflexiones</p>
                <a href="{{ route('admin.journal.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-[#37352F] text-white rounded-lg hover:bg-[#2f2d2a] transition text-sm">
                    ✏️ Escribir primera entrada
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($entries as $entry)
                <div class="group border border-[#E9E9E7] rounded-lg p-4 hover:shadow-md transition bg-white relative">
                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            @if($entry->mood)
                            <span class="text-2xl" title="{{ $entry->mood->label() }}">{{ $entry->mood->emoji() }}</span>
                            @endif
                            <div>
                                <div class="font-medium text-[#37352F]">
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
                               class="p-1.5 hover:bg-[#EFEFED] rounded text-sm" title="Editar">✏️</a>
                            <button @click.stop="if(confirm('¿Eliminar esta entrada?')) $wire.deleteEntry({{ $entry->id }})"
                                    class="p-1.5 hover:bg-red-50 rounded text-sm" title="Eliminar">🗑️</button>
                        </div>
                    </div>

                    {{-- Content Preview --}}
                    <div class="text-[#37352F] text-sm line-clamp-3 prose prose-sm max-w-none">
                        {!! Str::limit(strip_tags($entry->content), 300) !!}
                    </div>

                    {{-- Footer --}}
                    <div class="mt-3 pt-3 border-t border-[#E9E9E7] flex items-center justify-between text-xs text-[#9B9A97]">
                        <span>{{ $entry->word_count }} palabras</span>
                        <a href="{{ route('admin.journal.edit', $entry) }}" class="hover:text-[#37352F]">
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
</div>
