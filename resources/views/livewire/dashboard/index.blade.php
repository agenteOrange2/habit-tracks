<div class="flex h-full w-full flex-1 flex-col gap-6">
    {{-- Greeting Section with Pomodoro Timer --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                {{ $greeting }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Aquí está tu resumen de hoy
            </p>
        </div>
        <livewire:dashboard.pomodoro-timer />
    </div>

    {{-- Stats Cards Section --}}
    <livewire:dashboard.stats-cards />

    {{-- Energy Bar Section --}}
    <div class="w-full">
        <livewire:dashboard.energy-bar />
    </div>

    {{-- Quick Actions Section --}}
    <div class="w-full">
        <livewire:dashboard.quick-actions />
    </div>

    {{-- Habits List Section --}}
    <livewire:dashboard.habits-list />

    {{-- Weekly Progress and Streak Calendar Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Weekly Progress --}}
        <div class="w-full">
            <livewire:dashboard.weekly-progress />
        </div>

        {{-- Streak Calendar --}}
        <div class="w-full">
            <livewire:dashboard.streak-calendar />
        </div>
    </div>
</div>
