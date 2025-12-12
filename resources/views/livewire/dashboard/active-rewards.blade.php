<div>
    {{-- Header with points --}}
    <div class="flex items-center justify-between mb-3">
        <span class="text-sm text-gray-500 dark:text-gray-400">
            💰 {{ number_format($availablePoints) }} pts disponibles
        </span>
        <a href="{{ route('rewards.index') }}" wire:navigate class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
            Ver todas →
        </a>
    </div>

    @if($hasRewards && $rewards->count() > 0)
        <div class="space-y-2">
            @foreach($rewards as $reward)
                @php
                    $canAfford = $this->canAfford($reward);
                    $claimedToday = $this->wasClaimedToday($reward);
                    $progress = $this->getProgressPercentage($reward);
                    $pointsNeeded = $this->getPointsNeeded($reward);
                @endphp
                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex gap-3 items-start">
                        <span class="text-2xl">{{ $reward->icon ?? '🎁' }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-800 dark:text-gray-200 text-sm truncate">{{ $reward->name }}</p>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-xs {{ $canAfford ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ number_format($reward->cost_points) }} pts
                                </span>
                                @if(!$canAfford)
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        Faltan {{ number_format($pointsNeeded) }}
                                    </span>
                                @endif
                            </div>
                            {{-- Progress bar --}}
                            <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-300
                                            {{ $canAfford ? 'bg-green-500' : 'bg-indigo-500' }}"
                                     style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                        @if($claimedToday)
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-medium rounded-lg whitespace-nowrap">
                                ✅ Hoy
                            </span>
                        @elseif($canAfford)
                            <button wire:click="claimReward({{ $reward->id }})"
                                    wire:loading.attr="disabled"
                                    class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition whitespace-nowrap">
                                <span wire:loading.remove wire:target="claimReward({{ $reward->id }})">Canjear</span>
                                <span wire:loading wire:target="claimReward({{ $reward->id }})">...</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <a href="{{ route('rewards.create') }}" wire:navigate
           class="block p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition group">
            <div class="flex gap-3 items-center">
                <span class="text-2xl">🎁</span>
                <div class="flex-1">
                    <p class="font-medium text-gray-800 dark:text-gray-200">Crear nueva recompensa</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Personaliza tus incentivos</p>
                </div>
                <span class="opacity-0 group-hover:opacity-100 text-gray-400 text-xl transition">+</span>
            </div>
        </a>
    @endif

    {{-- Flash messages --}}
    @if (session()->has('success'))
        <div class="mt-2 p-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded text-xs">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mt-2 p-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded text-xs">
            {{ session('error') }}
        </div>
    @endif
</div>
