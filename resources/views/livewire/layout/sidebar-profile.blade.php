<div class="px-3 mb-3" x-data="{ 
    open: false,
    menuStyle: {},
    updatePosition() {
        const btn = this.$refs.profileBtn;
        const rect = btn.getBoundingClientRect();
        this.menuStyle = {
            position: 'fixed',
            bottom: (window.innerHeight - rect.top + 8) + 'px',
            left: rect.left + 'px',
            width: rect.width + 'px',
            zIndex: 9999
        };
    }
}" @resize.window="if(open) updatePosition()">
    {{-- Dropdown Menu (opens upward, fixed position) --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.outside="open = false"
         :style="menuStyle"
         class="py-2 bg-white dark:bg-zinc-800 rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden"
         style="transform-origin: bottom center;">
        
        {{-- Mi Perfil --}}
        <a href="{{ route('admin.settings.profile') }}" 
           wire:navigate
           @click="open = false"
           class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-700 dark:hover:text-blue-400 transition-all duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
            Mi Perfil
        </a>
        
        {{-- Historial XP --}}
        <a href="{{ route('admin.xp-history') }}" 
           wire:navigate
           @click="open = false"
           class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 dark:hover:text-amber-400 transition-all duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-amber-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
            </svg>
            Historial XP
        </a>
        
        {{-- Apariencia --}}
        <a href="{{ route('admin.settings.appearance') }}" 
           wire:navigate
           @click="open = false"
           class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-700 dark:hover:text-purple-400 transition-all duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-purple-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42" />
            </svg>
            Apariencia
        </a>
        
        <div class="my-1.5 mx-3 border-t border-zinc-200 dark:border-zinc-700"></div>
        
        {{-- Cerrar sesión --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                Cerrar sesión
            </button>
        </form>
    </div>
    
    {{-- Profile Card Button --}}
    <button x-ref="profileBtn"
            @click="updatePosition(); open = !open"
            class="w-full group flex items-center gap-3 p-3 rounded-xl bg-zinc-100 hover:bg-blue-50/50 dark:bg-zinc-800 dark:hover:bg-blue-900/20 border border-transparent hover:border-blue-200 dark:hover:border-blue-700 transition-all duration-200">
        
        {{-- Avatar con indicador online --}}
        <div class="relative flex-shrink-0">
            <img class="w-12 h-12 rounded-full object-cover border-2 border-white dark:border-zinc-700 shadow-sm group-hover:border-blue-200 dark:group-hover:border-blue-600 transition-colors" 
                 src="{{ $this->user->avatar_url }}" 
                 alt="Perfil">
            <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white dark:ring-zinc-800 bg-green-400"></span>
        </div>
        
        {{-- Info del usuario --}}
        <div class="flex-1 min-w-0 text-left">
            <p class="text-sm font-bold text-zinc-900 dark:text-white truncate group-hover:text-blue-700 dark:group-hover:text-blue-400 transition-colors">
                {{ $this->user->name }}
            </p>
            
            {{-- Clase del jugador --}}
            <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $this->playerClass['bg'] }} {{ $this->playerClass['text'] }} dark:bg-opacity-30 truncate">
                <span class="mr-1">{{ $this->playerClass['icon'] }}</span>
                {{ $this->playerClass['name'] }}
            </div>
            
            {{-- Barra de XP --}}
            <div class="mt-1.5 flex items-center gap-2">
                <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400">Nv.{{ $this->level?->current_level ?? 1 }}</span>
                <div class="flex-1 h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-amber-400 to-yellow-500 rounded-full transition-all duration-500"
                         style="width: {{ $this->level?->progress_percentage ?? 0 }}%"></div>
                </div>
                <span class="text-[10px] text-zinc-500 dark:text-zinc-400">{{ $this->level?->current_xp ?? 0 }}/{{ $this->level?->required_xp ?? 100 }}</span>
            </div>
        </div>
        
        {{-- Chevron (apunta arriba) --}}
        <div class="text-zinc-400 transition-transform duration-300" :class="{ 'rotate-180': open }">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd" d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832 6.29 12.77a.75.75 0 11-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z" clip-rule="evenodd" />
            </svg>
        </div>
    </button>
</div>
