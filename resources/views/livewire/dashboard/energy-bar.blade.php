<div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-4">
    @if($energyStatus)
        <div class="space-y-2">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">⚡</span>
                    <h3 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Energía</h3>
                </div>
                <span class="text-sm font-semibold {{ $energyStatus['percentage'] < 30 ? 'text-amber-600 dark:text-amber-500' : 'text-zinc-900 dark:text-zinc-100' }}">
                    {{ round($energyStatus['current']) }}/{{ $energyStatus['max'] }}
                </span>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-zinc-200 dark:bg-zinc-800 rounded-full h-2.5 overflow-hidden">
                <div 
                    class="h-full rounded-full transition-all duration-300 {{ $energyStatus['percentage'] < 30 ? 'bg-amber-500' : 'bg-blue-500' }}"
                    style="width: {{ min(100, max(0, $energyStatus['percentage'])) }}%"
                ></div>
            </div>

            <!-- Status Text -->
            <p class="text-xs text-zinc-600 dark:text-zinc-400">
                @if($energyStatus['percentage'] >= 80)
                    Energía alta - ¡Estás listo para completar hábitos!
                @elseif($energyStatus['percentage'] >= 50)
                    Energía media - Buen nivel de energía
                @elseif($energyStatus['percentage'] >= 30)
                    Energía baja - Considera descansar pronto
                @else
                    Energía crítica - Necesitas descansar
                @endif
            </p>
        </div>
    @else
        <div class="text-center text-sm text-zinc-500 dark:text-zinc-400">
            Cargando estado de energía...
        </div>
    @endif
</div>
