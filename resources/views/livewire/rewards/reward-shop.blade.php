<div class="min-h-screen bg-[#FAFAFA] dark:bg-[#191919]">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-6 sm:py-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 sm:mb-8 gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-2xl sm:text-3xl">🎁</span>
                    <h1 class="text-2xl sm:text-3xl font-bold text-[#37352F] dark:text-[#EFEFED]">Tienda de Recompensas</h1>
                </div>
                <p class="text-[#787774] dark:text-[#9B9A97] text-sm">Canjea tus puntos por premios bien merecidos.</p>
            </div>

            {{-- Balance Card --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-[#FBF3DB] via-[#F8E8B8] to-[#F5D98A] dark:from-[#3D3520] dark:via-[#4A4025] dark:to-[#5C4813] px-4 sm:px-5 py-3 rounded-xl shadow-sm border border-[#E8D48A]/50 dark:border-[#5C4813]/50">
                <div class="absolute top-1 right-3 text-[10px] opacity-40">✨</div>
                <div class="absolute bottom-1 left-3 text-[8px] opacity-30">⭐</div>
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/60 dark:bg-black/20 backdrop-blur flex items-center justify-center text-xl shadow-inner">
                        💰
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] text-[#8B7355] dark:text-[#C9A85C] font-semibold uppercase tracking-wider">Tu Saldo</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-bold text-[#5C4813] dark:text-[#F5D98A]">{{ number_format($availablePoints) }}</span>
                            <span class="text-xs font-medium text-[#8B7355] dark:text-[#C9A85C]">puntos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-4 p-3 bg-[#DBEDDB] dark:bg-[#1B3D2F] text-[#1C3829] dark:text-[#27AE60] rounded text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-3 bg-[#FFE2DD] dark:bg-[#3D2222] text-[#5D1715] dark:text-[#EB5757] rounded text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Focus Mode Warning --}}
        @if($focusModeActive)
            <div class="mb-4 p-3 bg-[#FBF3DB] dark:bg-[#3D3520] text-[#49290E] dark:text-[#F2C94C] rounded text-sm flex items-center gap-2">
                <span>🔒</span>
                <span>Modo Focus activo. Algunas recompensas pueden estar bloqueadas.</span>
            </div>
        @endif

        {{-- Category Tabs --}}
        <div class="flex items-center gap-1 overflow-x-auto border-b border-[#E9E9E7] dark:border-[#3E3E3A] pb-0 mb-6 -mx-4 px-4 sm:mx-0 sm:px-0">
            <button wire:click="setCategoryFilter('all')"
                    class="px-3 py-2 text-sm font-medium transition whitespace-nowrap
                           {{ $categoryFilter === 'all' ? 'text-[#37352F] dark:text-[#EFEFED] border-b-2 border-[#37352F] dark:border-[#EFEFED]' : 'text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] rounded-t' }}">
                Todas
            </button>
            @foreach($categories as $category)
                <button wire:click="setCategoryFilter('{{ $category->value }}')"
                        class="px-3 py-2 text-sm font-medium transition whitespace-nowrap
                               {{ $categoryFilter === $category->value ? 'text-[#37352F] dark:text-[#EFEFED] border-b-2 border-[#37352F] dark:border-[#EFEFED]' : 'text-[#787774] dark:text-[#9B9A97] hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] rounded-t' }}">
                    <span class="hidden sm:inline">{{ $category->icon() }}</span> {{ $category->label() }}
                </button>
            @endforeach
            <a href="{{ route('rewards.create') }}" wire:navigate
               class="ml-auto text-sm text-[#2383E2] hover:bg-[#E7F3F8] dark:hover:bg-[#1B3A52] px-3 py-1.5 rounded transition font-medium flex items-center gap-1 whitespace-nowrap">
                <span>+</span> <span class="hidden sm:inline">Nueva Recompensa</span><span class="sm:hidden">Nueva</span>
            </a>
        </div>

        {{-- Rewards Grid --}}
        @if($rewards->isEmpty())
            <div class="text-center py-16">
                <span class="text-6xl mb-4 block">🎁</span>
                <h3 class="text-lg font-semibold text-[#37352F] dark:text-[#EFEFED] mb-2">No hay recompensas</h3>
                <p class="text-[#787774] dark:text-[#9B9A97] text-sm mb-4">¡Crea tu primera recompensa para motivarte!</p>
                <a href="{{ route('rewards.create') }}" wire:navigate
                   class="inline-flex items-center gap-1 text-sm text-[#2383E2] hover:bg-[#E7F3F8] dark:hover:bg-[#1B3A52] px-4 py-2 rounded transition font-medium">
                    + Crear Recompensa
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                @foreach($rewards as $reward)
                    @php
                        $isBlocked = $this->isRewardBlocked($reward);
                        $canAfford = $this->canAfford($reward);
                        $claimedToday = $this->wasClaimedToday($reward);
                        $progress = $this->getProgressPercentage($reward);
                        
                        $coverColor = match($reward->category->value) {
                            'entertainment' => '#EAE4F2',
                            'food' => '#FAEBDD',
                            'social' => '#DDEDEA',
                            'gaming', 'leisure' => '#DDEBF1',
                            'shopping' => '#FBE4E4',
                            default => '#DDEBF1',
                        };
                        
                        $coverColorDark = match($reward->category->value) {
                            'entertainment' => '#2D1F3D',
                            'food' => '#3D2A1F',
                            'social' => '#1B3D2F',
                            'gaming', 'leisure' => '#1B3A52',
                            'shopping' => '#3D2222',
                            default => '#1B3A52',
                        };
                        
                        $badgeStyle = match($reward->category->value) {
                            'entertainment' => 'background: #F6F3F9; color: #442A66;',
                            'food' => 'background: #FADEC9; color: #49290E;',
                            'social' => 'background: #DBEDDB; color: #1C3829;',
                            'gaming', 'leisure' => 'background: #D3E5EF; color: #183347;',
                            'shopping' => 'background: #FFE2DD; color: #5D1715;',
                            default => 'background: #D3E5EF; color: #183347;',
                        };
                    @endphp
                    
                    <div class="border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg overflow-hidden bg-white dark:bg-[#252525] hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer flex flex-col group relative {{ $isBlocked ? 'opacity-75' : '' }}">
                        {{-- Cover --}}
                        <div class="h-14 w-full relative bg-[{{ $coverColor }}] dark:bg-[{{ $coverColorDark }}]">
                            <a href="{{ route('rewards.edit', $reward) }}" wire:navigate
                               wire:click.stop
                               class="absolute top-2 right-2 p-1.5 bg-white/70 dark:bg-black/30 hover:bg-white dark:hover:bg-[#252525] rounded text-[#787774] dark:text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED] opacity-0 group-hover:opacity-100 transition shadow-sm">
                                ✏️
                            </a>
                        </div>
                        
                        <div class="px-4 pb-4 -mt-7 relative flex-1 flex flex-col">
                            {{-- Icon --}}
                            <div class="w-12 h-12 bg-white dark:bg-[#252525] rounded shadow-sm border border-[#E9E9E7] dark:border-[#3E3E3A] flex items-center justify-center text-2xl mb-3 {{ $isBlocked ? 'grayscale' : '' }}">
                                {{ $reward->icon }}
                            </div>

                            {{-- Category Badge & Name --}}
                            <div class="mb-1">
                                <span class="inline-flex px-1.5 py-0.5 rounded text-xs mb-2" style="{{ $badgeStyle }}">
                                    {{ $reward->category->label() }}
                                </span>
                                <h3 class="font-semibold text-[#37352F] dark:text-[#EFEFED] text-[15px] leading-tight">{{ $reward->name }}</h3>
                            </div>

                            {{-- Cost & Progress --}}
                            <div class="mt-2 mb-4">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-2xl font-bold {{ $canAfford ? 'text-[#37352F] dark:text-[#EFEFED]' : 'text-[#9B9A97]' }}">{{ number_format($reward->cost_points) }}</span>
                                    <span class="text-xs text-[#9B9A97] font-medium">pts</span>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="h-1 bg-[#F1F1EF] dark:bg-[#3E3E3A] rounded-full overflow-hidden flex-1">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $canAfford ? 'bg-[#27AE60]' : 'bg-[#F2C94C]' }}"
                                             style="width: {{ $progress }}%;"></div>
                                    </div>
                                    <span class="text-[10px] font-bold {{ $canAfford ? 'text-[#27AE60]' : 'text-[#F2C94C]' }}">{{ $progress }}%</span>
                                </div>
                                @if(!$canAfford)
                                    <p class="text-[10px] text-[#EB5757] mt-1">Faltan {{ number_format($reward->cost_points - $availablePoints) }} pts</p>
                                @endif
                            </div>

                            {{-- Action Button --}}
                            <div class="mt-auto pt-3 border-t border-[#F1F1EF] dark:border-[#3E3E3A]">
                                @if($isBlocked)
                                    <button disabled class="w-full py-1.5 px-3 bg-[#F7F7F5] dark:bg-[#1F1F1F] text-[#9B9A97] rounded text-sm cursor-not-allowed">
                                        🔒 Bloqueado
                                    </button>
                                @elseif($claimedToday)
                                    <button disabled class="w-full py-1.5 px-3 bg-[#EDF3EC] dark:bg-[#1B3D2F] text-[#27AE60] rounded text-sm cursor-default border border-[#DBEDDB] dark:border-[#27AE60]/30">
                                        ✅ Canjeado hoy
                                    </button>
                                @elseif($canAfford)
                                    <button wire:click.stop="claimReward({{ $reward->id }})"
                                            wire:loading.attr="disabled"
                                            class="w-full py-1.5 px-3 bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] text-[#37352F] dark:text-[#EFEFED] rounded text-sm font-medium hover:bg-[#EDF3EC] dark:hover:bg-[#1B3D2F] hover:border-[#DBEDDB] dark:hover:border-[#27AE60]/30 hover:text-[#18443B] dark:hover:text-[#27AE60] transition flex items-center justify-center gap-1.5">
                                        <span wire:loading.remove wire:target="claimReward({{ $reward->id }})">✨ Canjear</span>
                                        <span wire:loading wire:target="claimReward({{ $reward->id }})">...</span>
                                    </button>
                                @else
                                    <button disabled class="w-full py-1.5 px-3 bg-[#F7F7F5] dark:bg-[#1F1F1F] text-[#9B9A97] rounded text-sm cursor-not-allowed">
                                        🔒 Bloqueado
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $rewards->links() }}
            </div>
        @endif
    </div>
</div>
