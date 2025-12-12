<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📜 Historial de Recompensas</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Has canjeado <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $totalClaims }}</span> recompensas
                por un total de <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($totalSpent) }}</span> puntos
            </p>
        </div>
        <a href="{{ route('rewards.index') }}" wire:navigate
           class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
            🎁 Ir a la Tienda
        </a>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Claims List --}}
    @if($claims->isEmpty())
        <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-xl">
            <span class="text-6xl">📜</span>
            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Sin historial</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Aún no has canjeado ninguna recompensa.</p>
            <a href="{{ route('rewards.index') }}" wire:navigate
               class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                Ver Recompensas
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($claims as $claim)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-start justify-between">
                        {{-- Reward Info --}}
                        <div class="flex items-center space-x-4">
                            <span class="text-3xl">{{ $claim->reward?->icon ?? '🎁' }}</span>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">
                                    {{ $claim->reward?->name ?? 'Recompensa eliminada' }}
                                </h3>
                                <div class="flex items-center space-x-3 text-sm text-gray-600 dark:text-gray-400">
                                    <span>{{ $claim->claimed_at->format('d M Y, H:i') }}</span>
                                    <span>•</span>
                                    <span class="font-medium text-indigo-600 dark:text-indigo-400">
                                        -{{ number_format($claim->points_spent) }} pts
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Enjoyed Toggle --}}
                        <button wire:click="toggleEnjoyed({{ $claim->id }})"
                                class="p-2 rounded-lg transition
                                       {{ $claim->was_enjoyed ? 'bg-green-100 dark:bg-green-900/30 text-green-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 hover:text-gray-600' }}">
                            <span class="text-xl">{{ $claim->was_enjoyed ? '😊' : '😐' }}</span>
                        </button>
                    </div>

                    {{-- Notes Section --}}
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                        @if($editingNotesId === $claim->id)
                            <div class="flex items-center space-x-2">
                                <input type="text" wire:model="editingNotes"
                                       class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                       placeholder="Escribe una nota...">
                                <button wire:click="saveNotes"
                                        class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg">
                                    Guardar
                                </button>
                                <button wire:click="cancelEditingNotes"
                                        class="px-3 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg">
                                    Cancelar
                                </button>
                            </div>
                        @else
                            <div class="flex items-center justify-between">
                                @if($claim->notes)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 italic">
                                        "{{ $claim->notes }}"
                                    </p>
                                @else
                                    <p class="text-sm text-gray-400 dark:text-gray-500">
                                        Sin notas
                                    </p>
                                @endif
                                <button wire:click="startEditingNotes({{ $claim->id }}, '{{ addslashes($claim->notes ?? '') }}')"
                                        class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $claim->notes ? 'Editar' : 'Agregar nota' }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $claims->links() }}
        </div>
    @endif
</div>
