<div class="min-h-screen bg-[#FAFAFA] dark:bg-[#191919]" x-data x-init="window.initJournalEditor($wire)">
    {{-- Header (sin sticky) --}}
    <header class="h-12 flex items-center justify-between px-4 lg:px-8 border-b border-[#E9E9E7] dark:border-[#3E3E3A] bg-[#FAFAFA] dark:bg-[#191919]">
        <div class="flex items-center gap-2 text-sm text-[#37352F] dark:text-[#EFEFED]">
            <a href="{{ route('admin.journal.index') }}" class="opacity-50 hover:underline hover:opacity-100">Diario</a>
            <span class="opacity-30">/</span>
            <span class="font-medium">{{ $date->translatedFormat('j F Y') }}</span>
        </div>
        <span id="save-status" class="text-xs text-[#27AE60]">✓</span>
    </header>

    {{-- Content --}}
    <div class="max-w-3xl mx-auto px-4 lg:px-8 py-8">
        {{-- Date Header --}}
        <div class="text-center mb-8">
            <div class="text-4xl mb-2">📔</div>
            <h1 class="text-2xl font-bold text-[#37352F] dark:text-[#EFEFED]">{{ $date->translatedFormat('l') }}</h1>
            <p class="text-[#9B9A97]">{{ $date->translatedFormat('j \d\e F \d\e Y') }}</p>
        </div>

        {{-- Mood Selector --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-2">¿Cómo te sientes hoy?</label>
            <div class="flex items-center justify-center gap-2 flex-wrap">
                @foreach($moods as $m)
                <button wire:click="setMood('{{ $m->value }}')"
                        class="p-2 sm:p-3 rounded-lg text-xl sm:text-2xl transition {{ $mood === $m->value ? $m->bgColor() . ' ring-2 ring-offset-2 ring-[#9B9A97] dark:ring-offset-[#191919]' : 'hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F]' }}"
                        title="{{ $m->label() }}">
                    {{ $m->emoji() }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Energy Level --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-2">Nivel de energía</label>
            <div class="flex items-center justify-center gap-2">
                @for($i = 1; $i <= 5; $i++)
                <button wire:click="setEnergyLevel({{ $i }})"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg text-sm font-medium transition {{ $energyLevel === $i ? 'bg-[#37352F] dark:bg-[#EFEFED] text-white dark:text-[#191919]' : 'bg-[#F7F7F5] dark:bg-[#252525] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] text-[#37352F] dark:text-[#EFEFED]' }}">
                    {{ $i }}
                </button>
                @endfor
            </div>
            <div class="flex justify-center gap-8 mt-1 text-xs text-[#9B9A97]">
                <span>Bajo</span>
                <span>Alto</span>
            </div>
        </div>
        
        {{-- Category Selector --}}
        @if($categories->count() > 0)
        <div class="mb-6">
            <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-2">Categoría</label>
            <div class="flex items-center justify-center gap-2 flex-wrap">
                <button wire:click="setCategory(null)"
                        class="px-3 py-1.5 rounded-lg text-sm transition {{ $categoryId === null ? 'bg-[#37352F] dark:bg-[#EFEFED] text-white dark:text-[#191919]' : 'bg-[#F7F7F5] dark:bg-[#252525] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] text-[#37352F] dark:text-[#EFEFED]' }}">
                    Sin categoría
                </button>
                @foreach($categories as $category)
                <button wire:click="setCategory({{ $category->id }})"
                        class="px-3 py-1.5 rounded-lg text-sm transition flex items-center gap-1 {{ $categoryId === $category->id ? 'ring-2 ring-offset-2 dark:ring-offset-[#191919]' : 'hover:opacity-80' }}"
                        style="background-color: {{ $category->color }}20; color: {{ $category->color }}; {{ $categoryId === $category->id ? 'ring-color: ' . $category->color : '' }}">
                    {{ $category->icon }} {{ $category->name }}
                </button>
                @endforeach
            </div>
        </div>
        @endif

        <hr class="border-[#E9E9E7] dark:border-[#3E3E3A] mb-6">

        {{-- Editor --}}
        <div wire:ignore>
            <div id="journal-editor"></div>
        </div>
        
        <template id="initial-content">{!! $content !!}</template>

        {{-- Prompts --}}
        <div class="mt-8 p-4 bg-[#F7F6F3] dark:bg-[#252525] rounded-lg border border-[#E9E9E7] dark:border-[#3E3E3A]">
            <h3 class="text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-2">💡 Ideas para escribir</h3>
            <ul class="text-sm text-[#787774] dark:text-[#9B9A97] space-y-1">
                <li>• ¿Qué fue lo mejor que te pasó hoy?</li>
                <li>• ¿Qué aprendiste hoy?</li>
                <li>• ¿Por qué estás agradecido?</li>
                <li>• ¿Qué te gustaría mejorar mañana?</li>
            </ul>
        </div>
    </div>

    <style>
        #journal-editor { min-height: 300px; font-size: 16px; line-height: 1.8; }
        .ql-toolbar.ql-snow { 
            border: none; 
            border-bottom: 1px solid #E9E9E7; 
            padding: 8px 0;
            background: transparent;
        }
        .dark .ql-toolbar.ql-snow { border-bottom-color: #3E3E3A; }
        .ql-container.ql-snow { border: none; }
        .ql-editor { padding: 16px 0; color: #37352F; }
        .dark .ql-editor { color: #EFEFED; }
        .ql-editor.ql-blank::before { color: #D3D1CB; font-style: normal; }
        .dark .ql-editor.ql-blank::before { color: #5A5A5A; }
        
        /* Dark mode toolbar icons */
        .dark .ql-toolbar .ql-stroke { stroke: #9B9A97; }
        .dark .ql-toolbar .ql-fill { fill: #9B9A97; }
        .dark .ql-toolbar .ql-picker { color: #9B9A97; }
        .dark .ql-toolbar button:hover .ql-stroke { stroke: #EFEFED; }
        .dark .ql-toolbar button:hover .ql-fill { fill: #EFEFED; }
        .dark .ql-toolbar .ql-picker:hover { color: #EFEFED; }
        .dark .ql-toolbar .ql-picker-options { background: #252525; border-color: #3E3E3A; }
        .dark .ql-toolbar .ql-picker-item { color: #EFEFED; }
        .dark .ql-toolbar .ql-picker-item:hover { color: #2383E2; }
    </style>

    <script>
    window.initJournalEditor = function(wire) {
        if (!document.getElementById('quill-css')) {
            var link = document.createElement('link');
            link.id = 'quill-css';
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css';
            document.head.appendChild(link);
        }
        
        function init() {
            var container = document.getElementById('journal-editor');
            if (!container) return;
            
            var oldToolbar = container.previousElementSibling;
            if (oldToolbar && oldToolbar.classList.contains('ql-toolbar')) {
                oldToolbar.remove();
            }
            container.innerHTML = '';
            container.className = '';
            
            var template = document.getElementById('initial-content');
            var content = template ? template.innerHTML : '';
            
            var quill = new Quill('#journal-editor', {
                theme: 'snow',
                placeholder: 'Escribe tus pensamientos...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link'],
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
