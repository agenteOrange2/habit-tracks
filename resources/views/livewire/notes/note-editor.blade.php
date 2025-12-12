<div class="min-h-screen bg-white" x-data x-init="window.initNoteEditor($wire)">
    {{-- Header --}}
    <header class="h-12 flex items-center justify-between px-4 lg:px-12 border-b border-[#E9E9E7] sticky top-0 z-10 bg-white">
        <div class="flex items-center text-sm gap-1 text-[#37352F]">
            <a href="{{ route('notes.index') }}" class="opacity-50 hover:underline">Notas</a>
            <span class="opacity-30">/</span>
            <span class="font-medium truncate max-w-[200px]">{{ $title ?: 'Sin título' }}</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-1 text-sm text-[#787774] hover:bg-[#EFEFED] px-2 py-1 rounded">
                    📁 <span>{{ $folderId && $folders->firstWhere('id', $folderId) ? $folders->firstWhere('id', $folderId)->name : 'Sin carpeta' }}</span>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute right-0 mt-1 w-48 bg-white border border-[#E9E9E7] rounded-lg shadow-lg py-1 z-20">
                    <button wire:click="moveToFolder(null)" @click="open = false" class="w-full text-left px-3 py-1.5 text-sm hover:bg-[#EFEFED]">📄 Sin carpeta</button>
                    @foreach($folders as $folder)
                    <button wire:click="moveToFolder({{ $folder->id }})" @click="open = false" class="w-full text-left px-3 py-1.5 text-sm hover:bg-[#EFEFED]">{{ $folder->icon }} {{ $folder->name }}</button>
                    @endforeach
                </div>
            </div>
            <span id="save-status" class="text-xs text-[#27AE60]">✓</span>
        </div>
    </header>

    {{-- Content --}}
    <div class="max-w-3xl mx-auto px-4 lg:px-12 py-8">
        <div class="mb-4 text-5xl">{{ $icon }}</div>
        
        <input type="text" wire:model.live.debounce.500ms="title" placeholder="Sin título" 
               class="w-full text-4xl font-bold text-[#37352F] placeholder-[#D3D1CB] border-none outline-none bg-transparent p-0 mb-4 focus:ring-0">

        {{-- Tags --}}
        <div class="flex flex-wrap items-center gap-2 mb-6">
            @foreach($noteTags as $tag)
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                {{ $tag->name }}
                <button wire:click="removeTag({{ $tag->id }})" class="hover:opacity-70">×</button>
            </span>
            @endforeach
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-xs text-[#9B9A97] hover:bg-[#EFEFED] px-2 py-1 rounded">+ Etiqueta</button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute left-0 mt-1 w-48 bg-white border border-[#E9E9E7] rounded-lg shadow-lg py-1 z-20 max-h-64 overflow-y-auto">
                    @foreach($tags as $tag)
                        @if(!in_array($tag->id, $selectedTags))
                        <button wire:click="addTag({{ $tag->id }})" @click="open = false"
                                class="w-full text-left px-3 py-1.5 text-sm hover:bg-[#EFEFED] flex items-center gap-2">
                            <span class="w-3 h-3 rounded" style="background-color: {{ $tag->color }};"></span>
                            {{ $tag->name }}
                        </button>
                        @endif
                    @endforeach
                    <div class="border-t border-[#E9E9E7] mt-1 pt-1 px-2" x-data="{ newTag: '' }">
                        <input type="text" x-model="newTag" placeholder="Nueva etiqueta..." 
                               @keydown.enter.prevent="if(newTag.trim()) { $wire.createTag(newTag); newTag = ''; open = false; }"
                               class="w-full text-sm border-none outline-none p-1 focus:ring-0">
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-[#E9E9E7] mb-6">

        {{-- Quill Editor --}}
        <div wire:ignore>
            <div id="quill-editor"></div>
        </div>
        
        <template id="initial-content">{!! $plainContent !!}</template>
    </div>

    <style>
        #quill-editor { min-height: 500px; font-size: 16px; line-height: 1.6; }
        .ql-toolbar.ql-snow { border: none; border-bottom: 1px solid #E9E9E7; padding: 8px 0; }
        .ql-container.ql-snow { border: none; }
        .ql-editor { padding: 16px 0; }
        .ql-editor.ql-blank::before { color: #D3D1CB; font-style: normal; }
        .ql-editor h1 { font-size: 2em; font-weight: bold; margin: 0.5em 0; }
        .ql-editor h2 { font-size: 1.5em; font-weight: bold; margin: 0.5em 0; }
        .ql-editor h3 { font-size: 1.17em; font-weight: bold; margin: 0.5em 0; }
        .ql-editor p { margin: 0.5em 0; }
        .ql-editor blockquote { border-left: 3px solid #E9E9E7; padding-left: 1em; color: #787774; }
    </style>

    <script>
    window.initNoteEditor = function(wire) {
        // Load CSS
        if (!document.getElementById('quill-css')) {
            var link = document.createElement('link');
            link.id = 'quill-css';
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css';
            document.head.appendChild(link);
        }
        
        function init() {
            var container = document.getElementById('quill-editor');
            if (!container) return;
            
            var oldToolbar = container.previousElementSibling;
            if (oldToolbar && oldToolbar.classList.contains('ql-toolbar')) {
                oldToolbar.remove();
            }
            container.innerHTML = '';
            container.className = '';
            
            var template = document.getElementById('initial-content');
            var content = template ? template.innerHTML : '';
            
            var quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Escribe aquí...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'align': [] }],
                        ['blockquote', 'code-block'],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                }
            });
            
            if (content && content.trim()) {
                quill.root.innerHTML = content;
            }
            
            var saveTimeout;
            quill.on('text-change', function() {
                var status = document.getElementById('save-status');
                status.textContent = '...';
                status.className = 'text-xs text-[#9B9A97]';
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(function() {
                    wire.updateContent(quill.root.innerHTML);
                    status.textContent = '✓';
                    status.className = 'text-xs text-[#27AE60]';
                }, 1000);
            });
        }
        
        if (typeof Quill === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js';
            script.onload = init;
            document.head.appendChild(script);
        } else {
            init();
        }
    };
    </script>
</div>
