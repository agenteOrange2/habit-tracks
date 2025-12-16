<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    @stack('styles')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('admin.dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse"
            wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            {{-- Principal --}}
            <flux:navlist.group heading="Principal" class="grid">
                <a href="{{ route('admin.dashboard') }}" 
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-base rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-zinc-200 dark:bg-zinc-700 font-medium' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }} text-zinc-700 dark:text-zinc-300">
                    <span class="text-lg">🏠</span>
                    <span>Dashboard</span>
                </a>
            </flux:navlist.group>

            {{-- Gestión --}}
            <flux:navlist.group heading="Gestión" class="grid">
                <a href="{{ route('admin.habits.index') }}" 
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-base rounded-lg transition-colors {{ request()->routeIs('admin.habits.*') ? 'bg-zinc-200 dark:bg-zinc-700 font-medium' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }} text-zinc-700 dark:text-zinc-300">
                    <span class="text-lg">✅</span>
                    <span>Hábitos</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" 
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-base rounded-lg transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-zinc-200 dark:bg-zinc-700 font-medium' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }} text-zinc-700 dark:text-zinc-300">
                    <span class="text-lg">🏷️</span>
                    <span>Categorías</span>
                </a>
                <a href="{{ route('admin.difficulties.index') }}" 
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-base rounded-lg transition-colors {{ request()->routeIs('admin.difficulties.*') ? 'bg-zinc-200 dark:bg-zinc-700 font-medium' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }} text-zinc-700 dark:text-zinc-300">
                    <span class="text-lg">📊</span>
                    <span>Dificultades</span>
                </a>
            </flux:navlist.group>

            {{-- Productividad --}}
            <flux:navlist.group heading="Productividad" class="grid">
                <a href="{{ route('admin.pomodoro') }}" 
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-base rounded-lg transition-colors {{ request()->routeIs('admin.pomodoro.*') ? 'bg-zinc-200 dark:bg-zinc-700 font-medium' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }} text-zinc-700 dark:text-zinc-300">
                    <span class="text-lg">🍅</span>
                    <span>Pomodoro</span>
                </a>
                <a href="{{ route('notes.index') }}" 
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-base rounded-lg transition-colors {{ request()->routeIs('notes.*') ? 'bg-zinc-200 dark:bg-zinc-700 font-medium' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }} text-zinc-700 dark:text-zinc-300">
                    <span class="text-lg">📝</span>
                    <span>Notas</span>
                </a>
                <a href="{{ route('admin.journal.index') }}" 
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-base rounded-lg transition-colors {{ request()->routeIs('admin.journal.*') ? 'bg-zinc-200 dark:bg-zinc-700 font-medium' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }} text-zinc-700 dark:text-zinc-300">
                    <span class="text-lg">📔</span>
                    <span>Diario</span>
                </a>
                <a href="{{ route('admin.calendar.index') }}" 
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-base rounded-lg transition-colors {{ request()->routeIs('admin.calendar.*') ? 'bg-zinc-200 dark:bg-zinc-700 font-medium' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }} text-zinc-700 dark:text-zinc-300">
                    <span class="text-lg">📅</span>
                    <span>Calendario</span>
                </a>
            </flux:navlist.group>

            {{-- Recompensas --}}
            <flux:navlist.group heading="Recompensas" class="grid">
                <a href="{{ route('rewards.index') }}"
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-base rounded-lg transition-colors {{ request()->routeIs('rewards.*') ? 'bg-zinc-200 dark:bg-zinc-700 font-medium' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }} text-zinc-700 dark:text-zinc-300">
                    <span class="text-lg">🎁</span>
                    <span>Recompensas</span>
                </a>
                <a href="{{ route('admin.achievements.index') }}"
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-base rounded-lg transition-colors {{ request()->routeIs('admin.achievements.*') ? 'bg-zinc-200 dark:bg-zinc-700 font-medium' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }} text-zinc-700 dark:text-zinc-300">
                    <span class="text-lg">🏆</span>
                    <span>Logros</span>
                </a>
                <a href="{{ route('admin.xp-history') }}"
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-base rounded-lg transition-colors {{ request()->routeIs('admin.xp-history') ? 'bg-zinc-200 dark:bg-zinc-700 font-medium' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }} text-zinc-700 dark:text-zinc-300">
                    <span class="text-lg">⚡</span>
                    <span>Historial XP</span>
                </a>
            </flux:navlist.group>

            {{-- Admin (Solo para administradores) --}}
            @if(auth()->user()->is_admin)
                <flux:navlist.group heading="Administración" class="grid">
                    <a href="{{ route('admin.users.index') }}"
                       wire:navigate
                       class="flex items-center gap-2 px-3 py-2 text-base rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-zinc-200 dark:bg-zinc-700 font-medium' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }} text-zinc-700 dark:text-zinc-300">
                        <span class="text-lg">👥</span>
                        <span>Usuarios</span>
                    </a>
                </flux:navlist.group>
            @endif
        </flux:navlist>

        <!-- Pomodoro Timer Widget -->
        {{-- 
        @auth
            <div x-data="{}"
                x-show="$store.pomodoro && ($store.pomodoro.timerState === 'running' || $store.pomodoro.timerState === 'paused')"
                class="mx-4 mb-4 p-4 bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-red-700 dark:text-red-300 uppercase tracking-wider">
                        🍅 Pomodoro
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full"
                        :class="$store.pomodoro.timerState === 'running' ?
                            'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' :
                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300'"
                        x-text="$store.pomodoro.timerState === 'running' ? 'En progreso' : 'Pausado'"></span>
                </div>

                <div class="text-center mb-3">
                    <div class="text-3xl font-bold text-red-900 dark:text-red-100 font-mono"
                        x-text="$store.pomodoro.getFormattedTime()">
                        25:00
                    </div>
                    <div class="text-xs text-red-600 dark:text-red-400 mt-1">
                        <span
                            x-text="$store.pomodoro.timerType === 'pomodoro' ? 'Sesión de enfoque' : 
                                         $store.pomodoro.timerType === 'short_break' ? 'Descanso corto' : 
                                         'Descanso largo'"></span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button x-show="$store.pomodoro.timerState === 'running'" @click="$store.pomodoro.pauseTimer()"
                        class="flex-1 px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                        ⏸ Pausar
                    </button>
                    <button x-show="$store.pomodoro.timerState === 'paused'" @click="$store.pomodoro.resumeTimer()"
                        class="flex-1 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded transition-colors">
                        ▶ Reanudar
                    </button>
                    <button @click="$store.pomodoro.stopTimer()"
                        class="flex-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded transition-colors">
                        ⏹ Detener
                    </button>
                </div>

                <div
                    class="mt-3 pt-3 border-t border-red-200 dark:border-red-800 flex justify-between text-xs text-red-700 dark:text-red-300">
                    <span>Ciclo: <span class="font-semibold" x-text="`${$store.pomodoro.cycleCount}/4`">0/4</span></span>
                    <a href="{{ route('admin.pomodoro') }}" wire:navigate
                        class="text-red-600 dark:text-red-400 hover:underline">
                        Ver detalles →
                    </a>
                </div>
            </div>
        @endauth
        --}}
        <flux:spacer />

        {{-- User Profile Card with Level --}}
        @auth
            <livewire:layout.sidebar-profile />
        @endauth
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        {{-- Mobile Profile Badge --}}
        @auth
            @php
                $mobileUser = auth()->user();
                $mobileLevel = $mobileUser->level;
            @endphp
            <flux:dropdown position="top" align="end">
                <button class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                    <img class="w-8 h-8 rounded-full object-cover border-2 border-white dark:border-zinc-700 shadow-sm" 
                         src="{{ $mobileUser->avatar_url }}" 
                         alt="Perfil">
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/30 px-1.5 py-0.5 rounded">
                        Nv.{{ $mobileLevel?->current_level ?? 1 }}
                    </span>
                </button>

                <flux:menu class="w-56">
                    {{-- User info --}}
                    <div class="px-3 py-2 border-b border-zinc-200 dark:border-zinc-700">
                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $mobileUser->name }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $mobileUser->email }}</p>
                        {{-- XP Progress --}}
                        <div class="mt-2 flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-amber-400 to-yellow-500 rounded-full"
                                     style="width: {{ $mobileLevel?->progress_percentage ?? 0 }}%"></div>
                            </div>
                            <span class="text-[10px] text-zinc-500">{{ $mobileLevel?->current_xp ?? 0 }}/{{ $mobileLevel?->required_xp ?? 100 }} XP</span>
                        </div>
                    </div>

                    <flux:menu.item :href="route('admin.settings.profile')" icon="cog" wire:navigate>
                        Configuración
                    </flux:menu.item>
                    
                    <flux:menu.item :href="route('admin.xp-history')" icon="bolt" wire:navigate>
                        Historial XP
                    </flux:menu.item>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full text-red-600 dark:text-red-400">
                            Cerrar sesión
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        @endauth
    </flux:header>

    {{ $slot }}

    {{-- Level Up Modal --}}
    @auth
        <livewire:level-up-modal />
    @endauth

    <!-- Floating Timer Widget - DISABLED (too complex, using sidebar timer instead) -->
    {{-- @auth
            <livewire:pomodoro.floating-timer-widget />
        @endauth --}}

    <!-- Toast Notifications -->
    <div x-data="{
        show: false,
        message: '',
        type: 'success',
        timeout: null
    }"
        @notification.window="
                show = true;
                message = $event.detail.message;
                type = $event.detail.type || 'success';
                clearTimeout(timeout);
                timeout = setTimeout(() => { show = false }, 5000);
            "
        x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2" class="fixed bottom-4 right-4 z-50 max-w-sm"
        style="display: none;">
        <div class="rounded-lg shadow-lg p-4 flex items-start gap-3"
            :class="{
                'bg-green-50 border border-green-200 dark:bg-green-900/30 dark:border-green-800': type === 'success',
                'bg-red-50 border border-red-200 dark:bg-red-900/30 dark:border-red-800': type === 'error',
                'bg-blue-50 border border-blue-200 dark:bg-blue-900/30 dark:border-blue-800': type === 'info',
                'bg-yellow-50 border border-yellow-200 dark:bg-yellow-900/30 dark:border-yellow-800': type === 'warning'
            }">
            <!-- Icon -->
            <div class="flex-shrink-0">
                <svg x-show="type === 'success'" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg x-show="type === 'error'" class="h-5 w-5 text-red-600 dark:text-red-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
                <svg x-show="type === 'info'" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <svg x-show="type === 'warning'" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>

            <!-- Message -->
            <div class="flex-1">
                <p class="text-sm font-medium"
                    :class="{
                        'text-green-800 dark:text-green-200': type === 'success',
                        'text-red-800 dark:text-red-200': type === 'error',
                        'text-blue-800 dark:text-blue-200': type === 'info',
                        'text-yellow-800 dark:text-yellow-200': type === 'warning'
                    }"
                    x-text="message"></p>
            </div>

            <!-- Close button -->
            <button @click="show = false"
                class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    </div>

    @fluxScripts
    @stack('scripts')
</body>

</html>
