<div class="min-h-screen bg-white">
    <div class="max-w-4xl mx-auto px-4 lg:px-8 py-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('notes.index') }}" wire:navigate class="text-[#787774] hover:text-[#37352F]">
                    ← Volver
                </a>
                <h1 class="text-2xl font-bold text-[#37352F]">🗑️ Papelera</h1>
            </div>

            @if($notes->count() > 0)
                <button wire:click="emptyTrash"
                        wire:confirm="¿Vaciar la papelera? Esta acción no se puede deshacer."
                        class="text-sm text-red-500 hover:bg-red-50 px-3 py-1.5 rounded transition">
                    Vaciar papelera
                </button>
            @endif
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-4 p-3 bg-[#DBEDDB] text-[#1C3829] rounded text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-3 bg-[#FFE2E2] text-[#C41C1C] rounded text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Info --}}
        <p class="text-sm text-[#787774] mb-6">
            Las notas en la papelera se eliminan automáticamente después de 30 días.
        </p>

        {{-- Notes List --}}
        @if($notes->isEmpty())
            <div class="text-center py-16">
                <span class="text-6xl mb-4 block">🗑️</span>
                <h3 class="text-lg font-semibold text-[#37352F] mb-2">Papelera vacía</h3>
                <p class="text-[#787774] text-sm">No hay notas eliminadas</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($notes as $note)
                    <div class="flex items-center gap-4 p-4 border border-[#E9E9E7] rounded-lg bg-white">
                        <span class="text-2xl opacity-50">{{ $note->icon }}</span>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-[#37352F] truncate">{{ $note->title }}</h3>
                            <p class="text-xs text-[#9B9A97]">
                                Eliminada {{ $note->deleted_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="restore({{ $note->id }})"
                                    class="px-3 py-1.5 text-sm text-[#2383E2] hover:bg-[#E7F3F8] rounded transition">
                                Restaurar
                            </button>
                            <button wire:click="permanentDelete({{ $note->id }})"
                                    wire:confirm="¿Eliminar permanentemente? Esta acción no se puede deshacer."
                                    class="px-3 py-1.5 text-sm text-red-500 hover:bg-red-50 rounded transition">
                                Eliminar
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
