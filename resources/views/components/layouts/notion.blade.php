<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FFFFFF;
            color: #37352F;
            -webkit-font-smoothing: antialiased;
        }
        .notion-sidebar { background-color: #F7F7F5; }
        .notion-border { border: 1px solid #E9E9E7; }
        .notion-hover:hover { background-color: #EFEFED; }
        
        .bg-notion-gray { background-color: #F1F1EF; }
        .bg-notion-blue { background-color: #E7F3F8; color: #154664; }
        .bg-notion-red { background-color: #FDEBEC; color: #5D1715; }
        .bg-notion-purple { background-color: #F6F3F9; color: #392558; }
        .bg-notion-green { background-color: #EDF3EC; color: #18443B; }
        .bg-notion-orange { background-color: #FAEBDD; color: #8F4C09; }

        .notion-checkbox {
            appearance: none;
            width: 16px;
            height: 16px;
            border: 2px solid #37352F;
            border-radius: 3px;
            display: grid;
            place-content: center;
        }
        .notion-checkbox::before {
            content: "";
            width: 10px;
            height: 10px;
            transform: scale(0);
            transition: 120ms transform ease-in-out;
            box-shadow: inset 1em 1em white;
            background-color: #2EAADC;
            transform-origin: center;
            clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
        }
        .notion-checkbox:checked {
            background-color: #2EAADC;
            border-color: #2EAADC;
        }
        .notion-checkbox:checked::before {
            transform: scale(1);
        }
    </style>
</head>
<body class="h-screen flex overflow-hidden text-sm">

    {{-- Sidebar --}}
    <aside class="w-64 notion-sidebar border-r border-gray-200 flex flex-col flex-shrink-0">
        <div class="p-3 m-2 hover:bg-gray-200 rounded cursor-pointer transition flex items-center gap-2">
            <div class="w-5 h-5 bg-orange-800 rounded text-white flex items-center justify-center text-xs font-bold">
                {{ auth()->user()->initials() }}
            </div>
            <span class="font-medium text-gray-700 truncate">FocusFlow</span>
            <span class="text-xs text-gray-400 ml-auto">▼</span>
        </div>

        <nav class="flex-1 px-2 space-y-0.5 overflow-y-auto">
            <div class="px-3 py-1 mt-4 text-xs font-bold text-gray-500">Navigation</div>
            <a href="{{ route('admin.dashboard') }}" 
               wire:navigate
               class="flex items-center px-3 py-1 text-gray-600 rounded hover:bg-gray-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-200 font-medium' : '' }}">
                <span class="mr-2">🏠</span> Resumen General
            </a>
            <a href="{{ route('admin.habits.index') }}" 
               wire:navigate
               class="flex items-center px-3 py-1 text-gray-600 rounded hover:bg-gray-200 {{ request()->routeIs('admin.habits.*') ? 'bg-gray-200 font-medium' : '' }}">
                <span class="mr-2">✅</span> Hábitos
            </a>
            <a href="#" 
               class="flex items-center px-3 py-1 text-gray-600 rounded hover:bg-gray-200">
                <span class="mr-2">📊</span> Estadísticas
            </a>
            <a href="{{ route('admin.settings.profile') }}" 
               wire:navigate
               class="flex items-center px-3 py-1 text-gray-600 rounded hover:bg-gray-200 {{ request()->routeIs('admin.settings.*') ? 'bg-gray-200 font-medium' : '' }}">
                <span class="mr-2">⚙️</span> Configuración
            </a>

            <div class="px-3 py-1 mt-6 text-xs font-bold text-gray-500">Repository</div>
            <a href="https://github.com/laravel/livewire" target="_blank" class="flex items-center px-3 py-1 text-gray-600 rounded hover:bg-gray-200">
                <span class="mr-2">📂</span> Documentación
            </a>
        </nav>

        <div class="p-3 border-t border-gray-200">
            <div class="flex items-center gap-2 text-gray-600 hover:bg-gray-200 p-1 rounded cursor-pointer" x-data @click="$dispatch('open-modal', 'user-menu')">
                <div class="w-5 h-5 bg-gray-300 rounded-full flex items-center justify-center text-[10px]">
                    {{ auth()->user()->initials() }}
                </div>
                <span class="text-sm truncate">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 overflow-y-auto">
        <header class="h-12 flex items-center justify-between px-6 sticky top-0 bg-white/95 backdrop-blur z-10 border-b border-gray-100">
            <div class="text-gray-400 text-xs flex gap-1">
                <span class="hover:underline cursor-pointer">FocusFlow</span>
                <span>/</span>
                <span class="text-gray-700 cursor-pointer">{{ $title ?? 'Dashboard' }}</span>
            </div>
            <div class="flex items-center gap-2">
                <livewire:dashboard.pomodoro-timer />
            </div>
        </header>

        <div class="max-w-4xl mx-auto px-6 py-8 pb-20">
            {{ $slot }}
        </div>
    </main>

    {{-- User Menu Modal --}}
    <x-modal name="user-menu" maxWidth="sm">
        <div class="p-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center text-lg font-bold">
                    {{ auth()->user()->initials() }}
                </div>
                <div>
                    <div class="font-semibold text-gray-900">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                </div>
            </div>
            
            <div class="space-y-1">
                <a href="{{ route('admin.settings.profile') }}" 
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                    <span>⚙️</span> Configuración
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded">
                        <span>🚪</span> Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </x-modal>

    @livewireScripts
</body>
</html>
