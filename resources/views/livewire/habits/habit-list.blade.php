<div class="min-h-screen bg-[#FAFAFA] dark:bg-[#191919] pb-20">
    {{-- Header --}}
    <header class="max-w-6xl mx-auto px-4 sm:px-6 pt-8 sm:pt-12 pb-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
                <div class="text-3xl sm:text-4xl mb-3 sm:mb-4">💎</div>
                <h1 class="text-2xl sm:text-4xl font-bold text-[#37352F] dark:text-[#EFEFED]">Mis Hábitos</h1>
                <p class="text-[#787774] dark:text-[#9B9A97] mt-2 text-sm sm:text-base">Gestiona y completa tus rutinas diarias.</p>
            </div>
            <a href="{{ route('admin.habits.create') }}" 
               wire:navigate
               class="bg-[#2383E2] hover:bg-[#1B74C9] text-white px-4 py-2.5 rounded text-sm font-medium shadow-sm transition flex items-center justify-center gap-2 w-full sm:w-auto">
                <span>+</span> Nuevo Hábito
            </a>
        </div>

        {{-- Filters --}}
        <div class="border-b border-[#E9E9E7] dark:border-[#3E3E3A] flex flex-col gap-4 pb-4">
            <div class="flex items-center gap-1 overflow-x-auto pb-2 -mb-2">
                <button wire:click="setFilter('active')"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium cursor-pointer transition whitespace-nowrap
                            {{ $filter === 'active' ? 'text-[#37352F] dark:text-[#EFEFED] border-b-2 border-[#37352F] dark:border-[#EFEFED]' : 'text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] rounded' }}">
                    🗂️ Activos
                </button>
                <button wire:click="setFilter('archived')"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium cursor-pointer transition whitespace-nowrap
                            {{ $filter === 'archived' ? 'text-[#37352F] dark:text-[#EFEFED] border-b-2 border-[#37352F] dark:border-[#EFEFED]' : 'text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] rounded' }}">
                    📦 Archivados
                </button>
                <button wire:click="setFilter('all')"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium cursor-pointer transition whitespace-nowrap
                            {{ $filter === 'all' ? 'text-[#37352F] dark:text-[#EFEFED] border-b-2 border-[#37352F] dark:border-[#EFEFED]' : 'text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] rounded' }}">
                    📑 Todos
                </button>
                <div class="h-4 w-px bg-[#E9E9E7] dark:bg-[#3E3E3A] mx-2 hidden sm:block"></div>
                <div class="relative">
                    <select wire:model.live="categoryFilter"
                            class="text-sm text-[#37352F] dark:text-[#EFEFED] hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] px-2 py-1 rounded cursor-pointer transition appearance-none pr-6 bg-transparent border-none dark:bg-[#191919]">
                        <option value="all">📂 Categoría</option>
                        <option value="productivity">💼 Productividad</option>
                        <option value="health">🧘 Salud</option>
                        <option value="learning">📚 Aprendizaje</option>
                        <option value="social">👥 Social</option>
                        <option value="creative">🎨 Creatividad</option>
                        <option value="household">🏠 Hogar</option>
                        <option value="finance">💰 Finanzas</option>
                        <option value="personal">⭐ Personal</option>
                    </select>
                    <svg class="absolute right-1 top-1/2 -translate-y-1/2 pointer-events-none text-[#787774] dark:text-[#9B9A97]" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                </div>
            </div>

            {{-- Search --}}
            <div class="relative w-full">
                <span class="absolute left-3 top-2.5 text-[#9B9A97]">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Buscar..." 
                       class="w-full pl-9 py-2 bg-white dark:bg-[#252525] text-[#37352F] dark:text-[#EFEFED] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded text-sm transition-all focus:border-[#2383E2] focus:ring-2 focus:ring-[#2383E2]/20 outline-none placeholder-[#9B9A97]">
            </div>
        </div>
    </header>

    {{-- Habits Grid --}}
    <main class="max-w-6xl mx-auto px-4 sm:px-6">
        @if($habits->isEmpty())
            {{-- Empty State --}}
            <div class="bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg p-8 sm:p-12 text-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#F7F7F5] dark:bg-[#1F1F1F] rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-[#9B9A97]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-[#37352F] dark:text-[#EFEFED] mb-2">
                    {{ $search ? 'No se encontraron hábitos' : 'No tienes hábitos todavía' }}
                </h3>
                <p class="text-sm text-[#787774] dark:text-[#9B9A97] mb-6 max-w-md mx-auto">
                    {{ $search ? 'Intenta con otros términos de búsqueda.' : 'Comienza creando tu primer hábito.' }}
                </p>
                @if(!$search)
                    <a href="{{ route('admin.habits.create') }}" wire:navigate
                       class="inline-flex items-center gap-2 bg-[#2383E2] hover:bg-[#1B74C9] text-white px-5 py-2.5 rounded text-sm font-medium transition">
                        + Crear mi primer hábito
                    </a>
                @endif
            </div>
        @else
            {{-- Habits Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                @foreach($habits as $habit)
                    <div class="bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg overflow-hidden transition-all hover:shadow-md group">
                        {{-- Cover --}}
                        <div class="h-16 sm:h-20 w-full relative" style="background-color: {{ $habit->color ?? '#E7F3F8' }}30">
                            <div class="absolute -bottom-3 left-3 sm:left-4 bg-white dark:bg-[#252525] p-1 rounded shadow-sm text-xl sm:text-2xl w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center border border-[#E9E9E7] dark:border-[#3E3E3A]">
                                {{ $habit->icon ?? '⭐' }}
                            </div>
                            <a href="{{ route('admin.habits.edit', $habit) }}" wire:navigate
                               class="absolute top-2 right-2 p-1.5 bg-white/80 dark:bg-[#252525]/80 hover:bg-white dark:hover:bg-[#252525] rounded text-[#787774] dark:text-[#9B9A97] opacity-0 group-hover:opacity-100 transition">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                        </div>

                        {{-- Content --}}
                        <div class="pt-5 px-3 sm:px-4 pb-3 sm:pb-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-medium text-[#37352F] dark:text-[#EFEFED] text-sm sm:text-base truncate pr-2">{{ $habit->name }}</h3>
                                @if($habit->getDifficultyName())
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium whitespace-nowrap bg-[#E7F3F8] dark:bg-[#1B3A52] text-[#2383E2] dark:text-[#5BA3D0]">
                                        {{ $habit->getDifficultyIcon() }}
                                    </span>
                                @endif
                            </div>
                            
                            @if($habit->description)
                                <p class="text-xs text-[#787774] dark:text-[#9B9A97] mb-3 line-clamp-2">{{ $habit->description }}</p>
                            @endif

                            {{-- Stats --}}
                            <div class="space-y-1.5 mb-3 text-xs text-[#37352F] dark:text-[#EFEFED]">
                                <div class="flex items-center gap-2">
                                    <span class="opacity-50">🔥</span>
                                    <span>Racha: {{ $habit->current_streak ?? 0 }} días</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="opacity-50">⚡</span>
                                    <span>{{ $habit->points_reward ?? 0 }} XP</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="opacity-50">📅</span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-[#E3E2E0] dark:bg-[#3E3E3A] text-[#32302C] dark:text-[#EFEFED]">
                                        {{ $habit->frequency->label() }}
                                    </span>
                                </div>
                            </div>

                            {{-- Action Button --}}
                            @if($habit->isScheduledForToday())
                                <a href="{{ route('admin.habits.edit', $habit) }}" wire:navigate
                                   class="w-full py-2 rounded text-xs sm:text-sm font-medium text-center transition flex items-center justify-center gap-2
                                        {{ $habit->isCompletedToday() 
                                            ? 'bg-[#DBEDDB] dark:bg-[#1B3B2D] text-[#18443B] dark:text-[#5BA572]' 
                                            : 'bg-[#F7F7F5] dark:bg-[#1F1F1F] border border-[#E9E9E7] dark:border-[#3E3E3A] text-[#787774] dark:text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED]' }}">
                                    {{ $habit->isCompletedToday() ? '✓ Completado' : '○ Ver hábito' }}
                                </a>
                            @else
                                <div class="w-full py-2 rounded text-xs sm:text-sm font-medium text-center bg-[#F7F7F5] dark:bg-[#1F1F1F] border border-[#E9E9E7] dark:border-[#3E3E3A] text-[#9B9A97]">
                                    ○ No programado hoy
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
</div>
