<div class="min-h-screen bg-white flex">
    {{-- Sidebar --}}
    <aside class="w-60 border-r border-[#E9E9E7] flex-shrink-0 hidden lg:block">
        <div class="p-4 sticky top-0">
            {{-- New Note Button --}}
            <button wire:click="createNote"
                    class="w-full flex items-center gap-2 px-3 py-2 text-sm font-medium text-[#37352F] bg-[#F7F7F5] hover:bg-[#EFEFED] rounded transition mb-4">
                <span>+</span> Nueva nota
            </button>

            {{-- Search --}}
            <div class="relative mb-4">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Buscar notas..."
                       class="w-full pl-8 pr-3 py-1.5 text-sm border border-[#E9E9E7] rounded focus:border-[#2383E2] focus:ring-0 bg-white"
                       autocomplete="off">
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[#9B9A97]">🔍</span>
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[#9B9A97] hover:text-[#37352F]">×</button>
                @endif
            </div>

            {{-- All Notes --}}
            <button wire:click="clearFilters"
                    class="w-full flex items-center gap-2 px-3 py-1.5 text-sm rounded transition mb-1
                           {{ !$selectedFolder && !$selectedTag ? 'bg-[#E7F3F8] text-[#2383E2]' : 'text-[#787774] hover:bg-[#EFEFED]' }}">
                <span>📝</span>
                <span>Todas las notas</span>
                <span class="ml-auto text-xs opacity-60">{{ $totalNotes }}</span>
            </button>

            {{-- Folders --}}
            <div class="mt-4">
                <div class="flex items-center justify-between px-3 mb-1">
                    <span class="text-xs font-semibold text-[#9B9A97] uppercase tracking-wide">Carpetas</span>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('notes.folders') }}" wire:navigate 
                           class="text-[#9B9A97] hover:text-[#37352F] text-xs" title="Gestionar carpetas">⚙️</a>
                        <button x-data @click="$dispatch('open-folder-modal')" class="text-[#9B9A97] hover:text-[#37352F] text-xs">+</button>
                    </div>
                </div>
                
                <button wire:click="setFolder(0)"
                        class="w-full flex items-center gap-2 px-3 py-1.5 text-sm rounded transition
                               {{ $selectedFolder === 0 ? 'bg-[#E7F3F8] text-[#2383E2]' : 'text-[#787774] hover:bg-[#EFEFED]' }}">
                    <span>📄</span>
                    <span>Sin carpeta</span>
                    <span class="ml-auto text-xs opacity-60">{{ $unfolderedCount }}</span>
                </button>

                @foreach($folders as $folder)
                    <button wire:click="setFolder({{ $folder->id }})"
                            class="w-full flex items-center gap-2 px-3 py-1.5 text-sm rounded transition
                                   {{ $selectedFolder === $folder->id ? 'bg-[#E7F3F8] text-[#2383E2]' : 'text-[#787774] hover:bg-[#EFEFED]' }}">
                        <span>{{ $folder->icon }}</span>
                        <span class="truncate">{{ $folder->name }}</span>
                        <span class="ml-auto text-xs opacity-60">{{ $folder->notes_count }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Tags --}}
            <div class="mt-4">
                <div class="flex items-center justify-between px-3 mb-1">
                    <span class="text-xs font-semibold text-[#9B9A97] uppercase tracking-wide">Etiquetas</span>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('notes.tags') }}" wire:navigate 
                           class="text-[#9B9A97] hover:text-[#37352F] text-xs" title="Gestionar etiquetas">⚙️</a>
                        <button x-data @click="$dispatch('open-tag-modal')" class="text-[#9B9A97] hover:text-[#37352F] text-xs">+</button>
                    </div>
                </div>
                @if($tags->count() > 0)
                    <div class="mt-1 space-y-0.5">
                        @foreach($tags as $tag)
                            <button wire:click="setTag({{ $tag->id }})"
                                    class="w-full flex items-center gap-2 px-3 py-1.5 text-sm rounded transition
                                           {{ $selectedTag === $tag->id ? 'bg-[#E7F3F8] text-[#2383E2]' : 'text-[#787774] hover:bg-[#EFEFED]' }}">
                                <span class="w-2.5 h-2.5 rounded" style="background-color: {{ $tag->color }};"></span>
                                <span class="truncate">{{ $tag->name }}</span>
                            </button>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-[#9B9A97] px-3 py-2">No hay etiquetas</p>
                @endif
            </div>

            {{-- Trash Link --}}
            <div class="mt-6 pt-4 border-t border-[#E9E9E7]">
                <a href="{{ route('notes.trash') }}" wire:navigate
                   class="flex items-center gap-2 px-3 py-1.5 text-sm text-[#787774] hover:bg-[#EFEFED] rounded transition">
                    <span>🗑️</span>
                    <span>Papelera</span>
                </a>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-[#37352F]">
                    @if($selectedFolder === 0)
                        Sin carpeta
                    @elseif($selectedFolder)
                        {{ $folders->find($selectedFolder)?->icon }} {{ $folders->find($selectedFolder)?->name }}
                    @elseif($selectedTag)
                        {{ $tags->find($selectedTag)?->name }}
                    @elseif($search)
                        Resultados: "{{ $search }}"
                    @else
                        Todas las notas
                    @endif
                </h1>
            </div>

            {{-- Mobile: New Note --}}
            <button wire:click="createNote" class="lg:hidden px-3 py-1.5 bg-[#2383E2] text-white text-sm rounded">
                + Nueva
            </button>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-4 p-3 bg-[#DBEDDB] text-[#1C3829] rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Notes Grid --}}
        @if($notes->isEmpty())
            <div class="text-center py-16">
                <span class="text-6xl mb-4 block">📝</span>
                <h3 class="text-lg font-semibold text-[#37352F] mb-2">No hay notas</h3>
                <p class="text-[#787774] text-sm mb-4">
                    @if($search)
                        No se encontraron notas con "{{ $search }}"
                    @else
                        Crea tu primera nota para comenzar
                    @endif
                </p>
                <button wire:click="createNote"
                        class="inline-flex items-center gap-1 text-sm text-[#2383E2] hover:bg-[#E7F3F8] px-4 py-2 rounded transition font-medium">
                    + Crear nota
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($notes as $note)
                    <a href="{{ route('notes.edit', $note) }}" wire:navigate
                       class="group block p-4 border border-[#E9E9E7] rounded-lg hover:shadow-md hover:-translate-y-0.5 transition bg-white relative">
                        {{-- Pin indicator --}}
                        @if($note->is_pinned)
                            <span class="absolute top-2 right-2 text-xs">📌</span>
                        @endif

                        {{-- Icon & Title --}}
                        <div class="flex items-start gap-3 mb-2">
                            <span class="text-2xl">{{ $note->icon }}</span>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-[#37352F] truncate">{{ $note->title }}</h3>
                                <p class="text-xs text-[#9B9A97] mt-0.5">
                                    {{ $note->updated_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        {{-- Preview --}}
                        @if($note->content_text)
                            <p class="text-sm text-[#787774] line-clamp-2 mb-3">
                                {{ Str::limit($note->content_text, 100) }}
                            </p>
                        @endif

                        {{-- Tags --}}
                        @if($note->tags->count() > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach($note->tags->take(3) as $tag)
                                    <span class="px-1.5 py-0.5 rounded text-[10px]"
                                          style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Hover Actions --}}
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition flex gap-1" @click.stop>
                            <button @click.prevent="$wire.pinNote({{ $note->id }})"
                                    class="p-1 bg-white border border-[#E9E9E7] rounded hover:bg-[#EFEFED] text-xs"
                                    title="{{ $note->is_pinned ? 'Desfijar' : 'Fijar' }}">
                                {{ $note->is_pinned ? '📌' : '📍' }}
                            </button>
                            <button @click.prevent="$wire.deleteNote({{ $note->id }})"
                                    class="p-1 bg-white border border-[#E9E9E7] rounded hover:bg-red-50 text-xs"
                                    title="Eliminar">
                                🗑️
                            </button>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $notes->links() }}
            </div>
        @endif
    </main>

    {{-- New Folder Modal --}}
    <div x-data="{ open: false, name: '' }"
         @open-folder-modal.window="open = true; $nextTick(() => $refs.folderInput.focus())"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div @click.away="open = false" class="bg-white rounded-lg shadow-xl p-6 w-80">
            <h3 class="font-semibold text-[#37352F] mb-4">Nueva carpeta</h3>
            <input type="text" x-model="name" x-ref="folderInput" placeholder="Nombre de la carpeta"
                   class="w-full px-3 py-2 border border-[#E9E9E7] rounded focus:border-[#2383E2] focus:ring-0 mb-4"
                   @keydown.enter="if(name.trim()) { $wire.createFolder(name); open = false; name = ''; }">
            <div class="flex justify-end gap-2">
                <button @click="open = false" class="px-3 py-1.5 text-sm text-[#787774] hover:bg-[#EFEFED] rounded">
                    Cancelar
                </button>
                <button @click="if(name.trim()) { $wire.createFolder(name); open = false; name = ''; }"
                        class="px-3 py-1.5 text-sm bg-[#2383E2] text-white rounded hover:bg-[#1B74C9]">
                    Crear
                </button>
            </div>
        </div>
    </div>

    {{-- New Tag Modal --}}
    <div x-data="{ open: false, name: '', color: '#6366F1' }"
         @open-tag-modal.window="open = true; $nextTick(() => $refs.tagInput.focus())"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div @click.away="open = false" class="bg-white rounded-lg shadow-xl p-6 w-80">
            <h3 class="font-semibold text-[#37352F] mb-4">Nueva etiqueta</h3>
            <input type="text" x-model="name" x-ref="tagInput" placeholder="Nombre de la etiqueta"
                   class="w-full px-3 py-2 border border-[#E9E9E7] rounded focus:border-[#2383E2] focus:ring-0 mb-3"
                   @keydown.enter="if(name.trim()) { $wire.createTag(name, color); open = false; name = ''; color = '#6366F1'; }">
            
            <div class="mb-4">
                <label class="text-sm text-[#787774] mb-2 block">Color</label>
                <div class="flex gap-2 flex-wrap">
                    <button type="button" @click="color = '#6366F1'" 
                            :class="color === '#6366F1' ? 'ring-2 ring-offset-2 ring-[#6366F1]' : ''"
                            class="w-8 h-8 rounded-full bg-[#6366F1]"></button>
                    <button type="button" @click="color = '#EF4444'" 
                            :class="color === '#EF4444' ? 'ring-2 ring-offset-2 ring-[#EF4444]' : ''"
                            class="w-8 h-8 rounded-full bg-[#EF4444]"></button>
                    <button type="button" @click="color = '#10B981'" 
                            :class="color === '#10B981' ? 'ring-2 ring-offset-2 ring-[#10B981]' : ''"
                            class="w-8 h-8 rounded-full bg-[#10B981]"></button>
                    <button type="button" @click="color = '#F59E0B'" 
                            :class="color === '#F59E0B' ? 'ring-2 ring-offset-2 ring-[#F59E0B]' : ''"
                            class="w-8 h-8 rounded-full bg-[#F59E0B]"></button>
                    <button type="button" @click="color = '#8B5CF6'" 
                            :class="color === '#8B5CF6' ? 'ring-2 ring-offset-2 ring-[#8B5CF6]' : ''"
                            class="w-8 h-8 rounded-full bg-[#8B5CF6]"></button>
                    <button type="button" @click="color = '#EC4899'" 
                            :class="color === '#EC4899' ? 'ring-2 ring-offset-2 ring-[#EC4899]' : ''"
                            class="w-8 h-8 rounded-full bg-[#EC4899]"></button>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button @click="open = false; name = ''; color = '#6366F1';" class="px-3 py-1.5 text-sm text-[#787774] hover:bg-[#EFEFED] rounded">
                    Cancelar
                </button>
                <button @click="if(name.trim()) { $wire.createTag(name, color); open = false; name = ''; color = '#6366F1'; }"
                        class="px-3 py-1.5 text-sm bg-[#2383E2] text-white rounded hover:bg-[#1B74C9]">
                    Crear
                </button>
            </div>
        </div>
    </div>
</div>
