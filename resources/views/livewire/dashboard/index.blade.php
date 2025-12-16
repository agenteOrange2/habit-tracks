<div class="flex h-full w-full flex-1 flex-col">
    {{-- Notion Style CSS --}}
    <style>
        /* Light Mode Borders */
        .notion-border {
            border: 1px solid #E9E9E7;
        }

        /* Dark Mode Borders */
        .dark .notion-border {
            border-color: #3F3F46;
        }

        /* Light Mode Hover */
        .notion-hover:hover {
            background-color: #EFEFED;
        }

        /* Dark Mode Hover */
        .dark .notion-hover:hover {
            background-color: #3F3F46;
        }

        /* Light Mode Background Colors */
        .bg-notion-gray {
            background-color: #F1F1EF;
        }

        .bg-notion-blue {
            background-color: #E7F3F8;
            color: #154664;
        }

        .bg-notion-red {
            background-color: #FDEBEC;
            color: #5D1715;
        }

        .bg-notion-purple {
            background-color: #F6F3F9;
            color: #392558;
        }

        .bg-notion-green {
            background-color: #EDF3EC;
            color: #18443B;
        }

        .bg-notion-orange {
            background-color: #FAEBDD;
            color: #8F4C09;
        }

        /* Dark Mode Background Colors */
        .dark .bg-notion-gray {
            background-color: #27272A;
            color: #E4E4E7;
        }

        .dark .bg-notion-blue {
            background-color: #1E3A5F;
            color: #93C5FD;
        }

        .dark .bg-notion-red {
            background-color: #4C1D1D;
            color: #FCA5A5;
        }

        .dark .bg-notion-purple {
            background-color: #3B2C4F;
            color: #C4B5FD;
        }

        .dark .bg-notion-green {
            background-color: #1E3A2F;
            color: #86EFAC;
        }

        .dark .bg-notion-orange {
            background-color: #4C3A1D;
            color: #FCD34D;
        }

        /* Notion Checkbox - Light Mode */
        .notion-checkbox {
            appearance: none;
            width: 16px;
            height: 16px;
            border: 2px solid #37352F;
            border-radius: 3px;
            display: grid;
            place-content: center;
            cursor: pointer;
        }

        /* Notion Checkbox - Dark Mode */
        .dark .notion-checkbox {
            border-color: #A1A1AA;
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

    {{-- Greeting Section --}}
    <div class="header_habit flex flex-col sm:flex-row justify-between items-start mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-4xl font-bold text-[#37352F] dark:text-zinc-50 mb-2">{{ $this->greeting }}</h1>
            <p class="text-gray-500 dark:text-zinc-400 text-base">Aquí está tu resumen de hoy.</p>
        </div>
        <div class="pomodoro_box">
            <livewire:dashboard.pomodoro-timer />
        </div>
    </div>

    {{-- Stats Cards - 3 columnas --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        {{-- Nivel --}}
        <div class="p-4 rounded border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
            <div class="flex justify-between items-start mb-2">
                <div class="flex items-center gap-2 text-gray-500 dark:text-zinc-400 text-xs uppercase tracking-wide font-semibold">Nivel
                    Actual</div>
                <span class="text-base">🌱</span>
            </div>
            <div class="text-xl font-semibold text-gray-900 dark:text-zinc-50 mb-1">Nivel {{ $this->userLevel->current_level ?? 1 }}</div>
            <div class="text-xs text-gray-400 dark:text-zinc-500 mb-2">{{ $this->userLevel->level_title ?? 'Principiante 🌱' }}</div>
            <div class="w-full bg-gray-100 dark:bg-zinc-700 rounded-full h-1.5">
                <div class="bg-green-500 h-1.5 rounded-full transition-all duration-300"
                    style="width: {{ min(100, max(0, $this->userLevel->progress_percentage ?? 0)) }}%"></div>
            </div>
            <div class="text-xs text-gray-400 dark:text-zinc-500 mt-1">{{ $this->userLevel->current_xp ?? 0 }} / {{ $this->userLevel->required_xp ?? 100 }} XP</div>
        </div>

        {{-- Racha --}}
        <div class="p-4 rounded border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
            <div class="flex justify-between items-start mb-2">
                <div class="flex items-center gap-2 text-gray-500 dark:text-zinc-400 text-xs uppercase tracking-wide font-semibold">Racha
                </div>
                <span class="text-base">🔥</span>
            </div>
            <div class="text-xl font-semibold text-gray-900 dark:text-zinc-50 mb-1">{{ $this->currentStreak }} Días</div>
            <div class="text-xs text-gray-400 dark:text-zinc-500">¡Sigue así!</div>
        </div>

        {{-- Completados Hoy --}}
        <div class="p-4 rounded border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
            <div class="flex justify-between items-start mb-2">
                <div class="flex items-center gap-2 text-gray-500 dark:text-zinc-400 text-xs uppercase tracking-wide font-semibold">Hoy
                </div>
                <span class="text-base">✅</span>
            </div>
            <div class="text-xl font-semibold text-gray-900 dark:text-zinc-50 mb-1">{{ number_format($completionRate, 0) }}%</div>
            <div class="text-xs text-gray-400 dark:text-zinc-500">Hábitos completados</div>
        </div>
    </div>

    {{-- Logros, Recompensas y Notas - 3 columnas --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div>
            <h3 class="font-medium text-gray-900 dark:text-zinc-50 mb-2">Logros Recientes</h3>
            <livewire:dashboard.recent-achievements />
        </div>
        <div>
            <h3 class="font-medium text-gray-900 dark:text-zinc-50 mb-2">Recompensas Activas</h3>
            <livewire:dashboard.active-rewards />
        </div>
        <div>
            <h3 class="font-medium text-gray-900 dark:text-zinc-50 mb-2">Notas Recientes</h3>
            <div class="p-4 rounded border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                <livewire:dashboard.recent-notes />
            </div>
        </div>
    </div>

    {{-- Energía --}}
    <div class="mb-10">
        <livewire:dashboard.energy-bar />
    </div>

    <hr class="border-gray-200 dark:border-zinc-700 mb-8">

    {{-- Acciones Rápidas --}}
    <div class="mb-10">
        <h3 class="font-semibold text-gray-900 dark:text-zinc-50 mb-4">Acciones Rápidas</h3>
        <livewire:dashboard.quick-actions />
    </div>

    {{-- Hábitos de Hoy --}}
    <div class="mb-12">
        <h3 class="font-semibold text-gray-900 dark:text-zinc-50 mb-4">Hábitos de Hoy</h3>
        <livewire:dashboard.habits-list />
    </div>

    {{-- Progreso Semanal y Calendario - 2 columnas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h3 class="font-semibold text-gray-900 dark:text-zinc-50 mb-3 flex items-center gap-2">
                <span>Progreso Semanal</span>
                <span class="text-base">📊</span>
            </h3>
            <livewire:dashboard.weekly-progress />
        </div>

        <div>
            <livewire:dashboard.streak-calendar />
        </div>
    </div>
</div>
