<div class="min-h-screen bg-[#FAFAFA] dark:bg-[#191919] flex" x-data="{ sidebarOpen: false }">
    {{-- Mobile Sidebar Overlay --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 dark:bg-black/70 z-40 lg:hidden"
         x-cloak></div>
    
    {{-- Sidebar --}}
    <aside class="w-60 border-r border-[#E9E9E7] dark:border-[#3E3E3A] flex-shrink-0 bg-white dark:bg-[#191919] z-50
                  fixed inset-y-0 left-0 transform transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="p-4 sticky top-0 h-full overflow-y-auto">
            {{-- Close button for mobile --}}
            <button @click="sidebarOpen = false" class="lg:hidden absolute top-2 right-2 p-2 text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED]">
                ✕
            </button>
            {{-- New Note Button --}}
            <button wire:click="createNote"
                    class="w-full flex items-center gap-2 px-3 py-2 text-sm font-medium text-[#37352F] dark:text-[#EFEFED] bg-[#F7F7F5] dark:bg-[#252525] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] rounded transition mb-4">
                <span>+</span> Nueva nota
            </button>

            {{-- Search --}}
            <div class="relative mb-4">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Buscar notas..."
                       class="w-full pl-8 pr-3 py-1.5 text-sm border border-[#E9E9E7] dark:border-[#3E3E3A] rounded focus:border-[#2383E2] focus:ring-0 bg-white dark:bg-[#252525] text-[#37352F] dark:text-[#EFEFED] placeholder-[#9B9A97]"
                       autocomplete="off">
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[#9B9A97]">🔍</span>
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED]">×</button>
                @endif
            </div>

            {{-- All Notes --}}
            <button wire:click="clearFilters"
                    class="w-full flex items-center gap-2 px-3 py-1.5 text-sm rounded transition mb-1
                           {{ !$selectedFolder && !$selectedTag ? 'bg-[#E7F3F8] dark:bg-[#1B3A52] text-[#2383E2]' : 'text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F]' }}">
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
                           class="text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED] text-xs" title="Gestionar carpetas">⚙️</a>
                        <button x-data @click="$dispatch('open-folder-modal')" class="text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED] text-xs">+</button>
                    </div>
                </div>
                
                <button wire:click="setFolder(0)"
                        class="w-full flex items-center gap-2 px-3 py-1.5 text-sm rounded transition
                               {{ $selectedFolder === 0 ? 'bg-[#E7F3F8] dark:bg-[#1B3A52] text-[#2383E2]' : 'text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F]' }}">
                    <span>📄</span>
                    <span>Sin carpeta</span>
                    <span class="ml-auto text-xs opacity-60">{{ $unfolderedCount }}</span>
                </button>

                @foreach($folders as $folder)
                    <button wire:click="setFolder({{ $folder->id }})"
                            class="w-full flex items-center gap-2 px-3 py-1.5 text-sm rounded transition
                                   {{ $selectedFolder === $folder->id ? 'bg-[#E7F3F8] dark:bg-[#1B3A52] text-[#2383E2]' : 'text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F]' }}">
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
                           class="text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED] text-xs" title="Gestionar etiquetas">⚙️</a>
                        <button x-data @click="$dispatch('open-tag-modal')" class="text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED] text-xs">+</button>
                    </div>
                </div>
                @if($tags->count() > 0)
                    <div class="mt-1 space-y-0.5">
                        @foreach($tags as $tag)
                            <button wire:click="setTag({{ $tag->id }})"
                                    class="w-full flex items-center gap-2 px-3 py-1.5 text-sm rounded transition
                                           {{ $selectedTag === $tag->id ? 'bg-[#E7F3F8] dark:bg-[#1B3A52] text-[#2383E2]' : 'text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F]' }}">
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
            <div class="mt-6 pt-4 border-t border-[#E9E9E7] dark:border-[#3E3E3A]">
                <a href="{{ route('notes.trash') }}" wire:navigate
                   class="flex items-center gap-2 px-3 py-1.5 text-sm text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] rounded transition">
                    <span>🗑️</span>
                    <span>Papelera</span>
                </a>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 p-4 lg:p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                {{-- Mobile Menu Button --}}
                <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-xl lg:text-2xl font-bold text-[#37352F] dark:text-[#EFEFED]">
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
        
        {{-- Mobile Search --}}
        <div class="lg:hidden mb-4">
            <div class="relative">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Buscar notas..."
                       class="w-full pl-8 pr-3 py-2 text-sm border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg focus:border-[#2383E2] focus:ring-0 bg-white dark:bg-[#252525] text-[#37352F] dark:text-[#EFEFED] placeholder-[#9B9A97]"
                       autocomplete="off">
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[#9B9A97]">🔍</span>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-4 p-3 bg-[#DBEDDB] dark:bg-[#1B3D2F] text-[#1C3829] dark:text-[#27AE60] rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Notes Grid --}}
        @if($notes->isEmpty())
            <div class="text-center py-16">
                <span class="text-6xl mb-4 block">📝</span>
                <h3 class="text-lg font-semibold text-[#37352F] dark:text-[#EFEFED] mb-2">No hay notas</h3>
                <p class="text-[#787774] dark:text-[#9B9A97] text-sm mb-4">
                    @if($search)
                        No se encontraron notas con "{{ $search }}"
                    @else
                        Crea tu primera nota para comenzar
                    @endif
                </p>
                <button wire:click="createNote"
                        class="inline-flex items-center gap-1 text-sm text-[#2383E2] hover:bg-[#E7F3F8] dark:hover:bg-[#1B3A52] px-4 py-2 rounded transition font-medium">
                    + Crear nota
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($notes as $note)
                    <a href="{{ route('notes.edit', $note) }}" wire:navigate
                       class="group block p-4 border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg hover:shadow-md hover:-translate-y-0.5 transition bg-white dark:bg-[#252525] relative">
                        {{-- Pin indicator --}}
                        @if($note->is_pinned)
                            <span class="absolute top-2 right-2 text-xs">📌</span>
                        @endif

                        {{-- Icon & Title --}}
                        <div class="flex items-start gap-3 mb-2">
                            <span class="text-2xl">{{ $note->icon }}</span>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-[#37352F] dark:text-[#EFEFED] truncate">{{ $note->title }}</h3>
                                <p class="text-xs text-[#9B9A97] mt-0.5">
                                    {{ $note->updated_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        {{-- Preview --}}
                        @if($note->content_text)
                            <p class="text-sm text-[#787774] dark:text-[#9B9A97] line-clamp-2 mb-3">
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
                                    class="p-1 bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] text-xs"
                                    title="{{ $note->is_pinned ? 'Desfijar' : 'Fijar' }}">
                                {{ $note->is_pinned ? '📌' : '📍' }}
                            </button>
                            <button @click.prevent="$wire.deleteNote({{ $note->id }})"
                                    class="p-1 bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded hover:bg-[#FFE2DD] dark:hover:bg-[#3D2222] text-xs"
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
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 dark:bg-black/60 p-4">
        <div @click.away="open = false" class="bg-white dark:bg-[#252525] rounded-lg shadow-xl p-6 w-full max-w-sm border border-[#E9E9E7] dark:border-[#3E3E3A]">
            <h3 class="font-semibold text-[#37352F] dark:text-[#EFEFED] mb-4">Nueva carpeta</h3>
            <input type="text" x-model="name" x-ref="folderInput" placeholder="Nombre de la carpeta"
                   class="w-full px-3 py-2 border border-[#E9E9E7] dark:border-[#3E3E3A] rounded focus:border-[#2383E2] focus:ring-0 mb-4 bg-white dark:bg-[#1F1F1F] text-[#37352F] dark:text-[#EFEFED] placeholder-[#9B9A97]"
                   @keydown.enter="if(name.trim()) { $wire.createFolder(name); open = false; name = ''; }">
            <div class="flex justify-end gap-2">
                <button @click="open = false" class="px-3 py-1.5 text-sm text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] rounded">
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
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 dark:bg-black/60 p-4">
        <div @click.away="open = false" class="bg-white dark:bg-[#252525] rounded-lg shadow-xl p-6 w-full max-w-sm border border-[#E9E9E7] dark:border-[#3E3E3A]">
            <h3 class="font-semibold text-[#37352F] dark:text-[#EFEFED] mb-4">Nueva etiqueta</h3>
            <input type="text" x-model="name" x-ref="tagInput" placeholder="Nombre de la etiqueta"
                   class="w-full px-3 py-2 border border-[#E9E9E7] dark:border-[#3E3E3A] rounded focus:border-[#2383E2] focus:ring-0 mb-3 bg-white dark:bg-[#1F1F1F] text-[#37352F] dark:text-[#EFEFED] placeholder-[#9B9A97]"
                   @keydown.enter="if(name.trim()) { $wire.createTag(name, color); open = false; name = ''; color = '#6366F1'; }">
            
            <div class="mb-4">
                <label class="text-sm text-[#787774] dark:text-[#9B9A97] mb-2 block">Color</label>
                <div class="flex gap-2 flex-wrap">
                    <button type="button" @click="color = '#6366F1'" 
                            :class="color === '#6366F1' ? 'ring-2 ring-offset-2 ring-[#6366F1] dark:ring-offset-[#252525]' : ''"
                            class="w-8 h-8 rounded-full bg-[#6366F1]"></button>
                    <button type="button" @click="color = '#EF4444'" 
                            :class="color === '#EF4444' ? 'ring-2 ring-offset-2 ring-[#EF4444] dark:ring-offset-[#252525]' : ''"
                            class="w-8 h-8 rounded-full bg-[#EF4444]"></button>
                    <button type="button" @click="color = '#10B981'" 
                            :class="color === '#10B981' ? 'ring-2 ring-offset-2 ring-[#10B981] dark:ring-offset-[#252525]' : ''"
                            class="w-8 h-8 rounded-full bg-[#10B981]"></button>
                    <button type="button" @click="color = '#F59E0B'" 
                            :class="color === '#F59E0B' ? 'ring-2 ring-offset-2 ring-[#F59E0B] dark:ring-offset-[#252525]' : ''"
                            class="w-8 h-8 rounded-full bg-[#F59E0B]"></button>
                    <button type="button" @click="color = '#8B5CF6'" 
                            :class="color === '#8B5CF6' ? 'ring-2 ring-offset-2 ring-[#8B5CF6] dark:ring-offset-[#252525]' : ''"
                            class="w-8 h-8 rounded-full bg-[#8B5CF6]"></button>
                    <button type="button" @click="color = '#EC4899'" 
                            :class="color === '#EC4899' ? 'ring-2 ring-offset-2 ring-[#EC4899] dark:ring-offset-[#252525]' : ''"
                            class="w-8 h-8 rounded-full bg-[#EC4899]"></button>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button @click="open = false; name = ''; color = '#6366F1';" class="px-3 py-1.5 text-sm text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] rounded">
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
