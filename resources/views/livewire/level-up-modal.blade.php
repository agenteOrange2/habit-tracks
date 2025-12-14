<div>
    @if($show)
    <div 
        x-data="{ 
            show: true,
            confetti: [],
            init() {
                // Generate confetti
                for (let i = 0; i < 50; i++) {
                    this.confetti.push({
                        left: Math.random() * 100,
                        delay: Math.random() * 0.5,
                        duration: 2 + Math.random() * 2,
                        color: ['#fbbf24', '#f59e0b', '#eab308', '#84cc16', '#22c55e', '#3b82f6', '#8b5cf6', '#ec4899'][Math.floor(Math.random() * 8)]
                    });
                }
                // Auto dismiss after 5 seconds
                setTimeout(() => { $wire.dismiss() }, 5000);
            }
        }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex items-center justify-center"
        @click.self="$wire.dismiss()"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        {{-- Confetti --}}
        <template x-for="(c, i) in confetti" :key="i">
            <div 
                class="absolute w-3 h-3 rounded-sm animate-confetti"
                :style="`left: ${c.left}%; top: -10px; background-color: ${c.color}; animation-delay: ${c.delay}s; animation-duration: ${c.duration}s;`"
            ></div>
        </template>

        {{-- Modal Content --}}
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-300 delay-100"
            x-transition:enter-start="opacity-0 scale-75"
            x-transition:enter-end="opacity-100 scale-100"
            class="relative bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center overflow-hidden"
        >
            {{-- Milestone glow effect --}}
            @if($isMilestone)
            <div class="absolute inset-0 bg-gradient-to-r from-amber-400/20 via-yellow-400/20 to-amber-400/20 animate-pulse"></div>
            @endif

            <div class="relative">
                {{-- Icon --}}
                <div class="text-6xl mb-4 animate-bounce">
                    @if($isMilestone)
                        🏆
                    @else
                        ⬆️
                    @endif
                </div>

                {{-- Title --}}
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white mb-2">
                    @if($isMilestone)
                        ¡Milestone Alcanzado!
                    @else
                        ¡Subiste de Nivel!
                    @endif
                </h2>

                {{-- Level --}}
                <div class="text-5xl font-black bg-gradient-to-r from-amber-500 to-yellow-500 bg-clip-text text-transparent mb-2">
                    Nivel {{ $newLevel }}
                </div>

                {{-- Title --}}
                <div class="text-lg text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ $levelTitle }}
                </div>

                {{-- Bonus --}}
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 dark:bg-green-900/30 rounded-full mb-6">
                    <span class="text-green-600 dark:text-green-400 font-semibold">
                        +{{ $bonusPoints }} puntos bonus
                    </span>
                    <span>🎁</span>
                </div>

                {{-- Dismiss button --}}
                <button 
                    wire:click="dismiss"
                    class="w-full py-3 px-6 bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl"
                >
                    ¡Genial!
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes confetti-fall {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
        .animate-confetti {
            animation: confetti-fall linear forwards;
        }
    </style>
    @endif
</div>
