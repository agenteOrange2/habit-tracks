<div class="min-h-screen bg-white pb-20">
    {{-- Header estilo Notion --}}
    <header class="max-w-6xl mx-auto px-6 pt-12 pb-6">
        <div class="flex items-end justify-between mb-8">
            <div>
                <div class="text-4xl mb-4">💎</div>
                <h1 class="text-4xl font-bold text-[#37352F]">Mis Hábitos</h1>
                <p class="text-[#787774] mt-2 text-base">Gestiona y completa tus rutinas diarias.</p>
            </div>
            <a href="{{ route('admin.habits.create') }}" 
               wire:navigate
               class="bg-[#2383E2] hover:bg-[#1B74C9] text-white px-4 py-2 rounded text-sm font-medium shadow-sm transition flex items-center gap-2">
                <span>+</span> Nuevo Hábito
            </a>
        </div>

        <div class="border-b border-[#E9E9E7] flex flex-col md:flex-row md:items-center justify-between gap-4 pb-0">
            
            <div class="flex items-center gap-1 overflow-x-auto">
                <button wire:click="setFilter('active')"
                        class="flex items-center gap-2 px-2 py-1.5 text-sm font-medium cursor-pointer transition
                            {{ $filter === 'active' ? 'text-[#37352F] border-b-2 border-[#37352F]' : 'text-[#787774] hover:bg-[#EFEFED] rounded' }}">
                    <span>🗂️</span> Activos
                </button>
                <button wire:click="setFilter('archived')"
                        class="flex items-center gap-2 px-2 py-1.5 text-sm font-medium cursor-pointer transition
                            {{ $filter === 'archived' ? 'text-[#37352F] border-b-2 border-[#37352F]' : 'text-[#787774] hover:bg-[#EFEFED] rounded' }}">
                    <span>📦</span> Archivados
                </button>
                <button wire:click="setFilter('all')"
                        class="flex items-center gap-2 px-2 py-1.5 text-sm font-medium cursor-pointer transition
                            {{ $filter === 'all' ? 'text-[#37352F] border-b-2 border-[#37352F]' : 'text-[#787774] hover:bg-[#EFEFED] rounded' }}">
                    <span>📑</span> Todos
                </button>
                <div class="h-4 w-px bg-gray-200 mx-2 hidden md:block"></div>
                <div class="relative">
                    <select wire:model.live="categoryFilter"
                            class="flex items-center gap-1 text-sm text-[#37352F] hover:bg-[#EFEFED] px-2 py-1 rounded cursor-pointer transition appearance-none pr-6 bg-transparent border-none">
                        <option value="all">📂 Por Categoría</option>
                        <option value="productivity">💼 Productividad</option>
                        <option value="health">🧘 Salud</option>
                        <option value="learning">📚 Aprendizaje</option>
                        <option value="social">👥 Social</option>
                        <option value="creative">🎨 Creatividad</option>
                        <option value="household">🏠 Hogar</option>
                        <option value="finance">💰 Finanzas</option>
                        <option value="personal">⭐ Personal</option>
                    </select>
                    <svg class="absolute right-1 top-1/2 -translate-y-1/2 pointer-events-none" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                </div>
            </div>

            <div class="relative w-full md:w-64 mb-2 md:mb-0">
                <span class="absolute left-2.5 top-2 text-gray-400">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Buscar..." 
                       class="w-full pl-8 py-1.5 bg-transparent text-[#37352F] border border-[rgba(55,53,47,0.16)] rounded text-sm transition-all hover:border-[rgba(55,53,47,0.3)] focus:border-[#2383E2] focus:ring-2 focus:ring-[rgba(35,131,226,0.2)] outline-none">
            </div>
        </div>
    </header>

    {{-- Habits Grid Section --}}
    <main class="max-w-6xl mx-auto px-6">
        @if($habits->isEmpty())
            {{-- Empty State --}}
            <div class="bg-white border border-[#E9E9E7] rounded p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-[#37352F] mb-2">
                    @if($search)
                        No se encontraron hábitos
                    @else
                        No tienes hábitos todavía
                    @endif
                </h3>
                <p class="text-[#787774] mb-6 max-w-md mx-auto">
                    @if($search)
                        Intenta con otros términos de búsqueda o ajusta los filtros.
                    @else
                        Comienza creando tu primer hábito para empezar a construir tu rutina diaria.
                    @endif
                </p>
                @if(!$search)
                    <a href="{{ route('admin.habits.create') }}" 
                       wire:navigate
                       class="inline-flex items-center gap-2 bg-[#2383E2] hover:bg-[#1B74C9] text-white px-6 py-3 rounded text-sm font-medium shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Crear mi primer hábito
                    </a>
                @endif
            </div>
        @else
            {{-- Habits Grid estilo Notion --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($habits as $habit)
                    <div class="border border-[#E9E9E7] rounded overflow-hidden transition-all hover:bg-[#FBFBFA] shadow-[0_1px_2px_rgba(0,0,0,0.05)] hover:shadow-[0_4px_6px_rgba(0,0,0,0.05)] bg-white cursor-pointer group relative">
                        {{-- Cover con color --}}
                        <div class="h-20 w-full relative" style="background-color: {{ $habit->color ?? '#E7F3F8' }}20">
                            <div class="absolute -bottom-4 left-4 bg-white p-1 rounded shadow-sm text-2xl w-10 h-10 flex items-center justify-center">
                                {{ $habit->icon ?? '⭐' }}
                            </div>
                            <a href="{{ route('admin.habits.edit', $habit) }}" 
                               wire:navigate
                               class="absolute top-2 right-2 p-1 bg-white/50 hover:bg-white rounded text-gray-500 opacity-0 group-hover:opacity-100 transition">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                        </div>

                        <div class="pt-6 px-4 pb-4">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="font-medium text-[#37352F] text-base truncate pr-2">{{ $habit->name }}</h3>
                                @if($habit->getDifficultyName())
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium whitespace-nowrap bg-[#E7F3F8] text-[#2383E2]">
                                        {{ $habit->getDifficultyIcon() }} {{ $habit->getDifficultyName() }}
                                    </span>
                                @endif
                            </div>
                            @if($habit->description)
                                <p class="text-xs text-[#787774] mb-4 line-clamp-2">{{ $habit->description }}</p>
                            @endif

                            <div class="space-y-2 mb-4">
                                <div class="flex items-center gap-2 text-xs text-[#37352F]">
                                    <span class="opacity-40 w-4 text-center">🔥</span>
                                    <span>Racha: {{ $habit->current_streak ?? 0 }} días</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-[#37352F]">
                                    <span class="opacity-40 w-4 text-center">⚡</span>
                                    <span>{{ $habit->points_reward ?? 0 }} XP</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-[#37352F]">
                                    <span class="opacity-40 w-4 text-center">📅</span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-[#E3E2E0] text-[#32302C]">
                                        {{ $habit->frequency->label() }}
                                    </span>
                                </div>
                            </div>

                            @if($habit->isScheduledForToday())
                                <a href="{{ route('admin.habits.edit', $habit) }}" 
                                   wire:navigate
                                   class="w-full py-1.5 rounded text-[13px] font-medium text-center transition flex items-center justify-center gap-2
                                            {{ $habit->isCompletedToday() 
                                                ? 'bg-[#EDF3EC] text-[#18443B] border border-transparent hover:bg-[#DBEDDB]' 
                                                : 'bg-white border border-[#E9E9E7] text-[#9B9A97] hover:bg-[#F7F7F5] hover:text-[#37352F]' }}">
                                    <span>{{ $habit->isCompletedToday() ? '✓' : '○' }}</span>
                                    {{ $habit->isCompletedToday() ? 'Completado' : 'Ver hábito' }}
                                </a>
                            @else
                                <div class="w-full py-1.5 rounded text-[13px] font-medium text-center bg-white border border-[#E9E9E7] text-[#9B9A97] flex items-center justify-center gap-2">
                                    <span class="text-xs">○</span> No programado hoy
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $habits->links() }}
            </div>
        @endif
    </main>

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
</div>
