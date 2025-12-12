<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📊 Estadísticas de Recompensas</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Analiza tus patrones de gasto de puntos</p>
        </div>
        <a href="{{ route('rewards.index') }}" wire:navigate
           class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
            🎁 Ir a la Tienda
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        {{-- Total Points Spent --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                    <span class="text-2xl">💰</span>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Puntos Gastados</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalPointsSpent) }}</p>
                </div>
            </div>
        </div>

        {{-- Total Rewards Claimed --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <span class="text-2xl">🎁</span>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Recompensas Canjeadas</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalRewardsClaimed) }}</p>
                </div>
            </div>
        </div>

        {{-- Average Points per Claim --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                    <span class="text-2xl">📈</span>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Promedio por Canje</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($averagePointsPerClaim, 1) }} pts</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Most Claimed Categories --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">🏆 Categorías Favoritas</h2>
            @if(empty($mostClaimedCategories))
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">Sin datos aún</p>
            @else
                <div class="space-y-3">
                    @foreach($mostClaimedCategories as $index => $category)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg font-bold text-gray-400 w-6">{{ $index + 1 }}</span>
                                <span class="text-xl">{{ $category['icon'] }}</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $category['name'] }}</span>
                            </div>
                            <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded-full text-sm font-medium">
                                {{ $category['count'] }} canjes
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Most Claimed Rewards --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">⭐ Recompensas Favoritas</h2>
            @if(empty($mostClaimedRewards))
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">Sin datos aún</p>
            @else
                <div class="space-y-3">
                    @foreach($mostClaimedRewards as $index => $reward)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg font-bold text-gray-400 w-6">{{ $index + 1 }}</span>
                                <span class="text-xl">{{ $reward['icon'] }}</span>
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $reward['name'] }}</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $reward['category'] }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-sm font-medium">
                                {{ $reward['count'] }} canjes
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
