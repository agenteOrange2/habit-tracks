<div class="space-y-6">
    {{-- XP Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Hoy</div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($this->xpSummary['today']) }}</div>
            <div class="text-xs text-zinc-400">XP</div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Esta semana</div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($this->xpSummary['this_week']) }}</div>
            <div class="text-xs text-zinc-400">XP</div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Este mes</div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($this->xpSummary['this_month']) }}</div>
            <div class="text-xs text-zinc-400">XP</div>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-700">
            <div class="text-xs text-amber-600 dark:text-amber-400 mb-1">Total</div>
            <div class="text-2xl font-bold text-amber-700 dark:text-amber-300">{{ number_format($this->xpSummary['total']) }}</div>
            <div class="text-xs text-amber-500">XP</div>
        </div>
    </div>

    {{-- Milestone Badges --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">🏆 Insignias Milestone</h3>
        <div class="flex flex-wrap gap-4">
            @foreach($this->milestoneBadges as $badge)
                <div class="flex flex-col items-center p-3 rounded-xl {{ $badge['achieved'] ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700' : 'bg-zinc-100 dark:bg-zinc-700/50 border border-zinc-200 dark:border-zinc-600 opacity-50' }}">
                    <span class="text-3xl mb-1">{{ $badge['icon'] }}</span>
                    <span class="text-xs font-medium {{ $badge['achieved'] ? 'text-amber-700 dark:text-amber-300' : 'text-zinc-500 dark:text-zinc-400' }}">{{ $badge['name'] }}</span>
                    <span class="text-xs {{ $badge['achieved'] ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-400 dark:text-zinc-500' }}">Nivel {{ $badge['level'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">📜 Historial de XP</h3>
        
        @if($this->recentTransactions->isEmpty())
            <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                <div class="text-4xl mb-2">📭</div>
                <p>Aún no tienes transacciones de XP</p>
                <p class="text-sm">¡Completa hábitos para ganar XP!</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($this->recentTransactions as $transaction)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-zinc-50 dark:bg-zinc-700/50 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">{{ $transaction->source_icon }}</span>
                            <div>
                                <div class="font-medium text-zinc-900 dark:text-white text-sm">{{ $transaction->source_name }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $transaction->source_label }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-green-600 dark:text-green-400">+{{ $transaction->amount }} XP</div>
                            <div class="text-xs text-zinc-400">{{ $transaction->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
