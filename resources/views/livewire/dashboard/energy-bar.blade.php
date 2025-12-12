<div>
    @if($energyStatus)
        <div class="flex items-center gap-2 mb-2">
            <span class="text-lg">⚡</span>
            <h3 class="font-medium text-gray-900">Energía</h3>
            <span class="ml-auto text-xs text-gray-500 font-mono">{{ round($energyStatus['current']) }}/{{ $energyStatus['max'] }}</span>
        </div>
        <div class="w-full bg-gray-100 rounded h-2 overflow-hidden">
            <div 
                class="h-full {{ $energyStatus['percentage'] < 30 ? 'bg-amber-500' : 'bg-blue-500' }}"
                style="width: {{ min(100, max(0, $energyStatus['percentage'])) }}%"
            ></div>
        </div>
        <p class="text-xs text-gray-400 mt-1">
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
    @endif
</div>
