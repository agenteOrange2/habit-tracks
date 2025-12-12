<div>
    <div class="flex items-center justify-between mb-3">
        <span class="text-sm text-gray-500 dark:text-gray-400">📝 Notas recientes</span>
        <a href="{{ route('notes.index') }}" wire:navigate class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
            Ver todas →
        </a>
    </div>

    @if($hasNotes)
        <div class="space-y-2">
            @foreach($notes as $note)
                <a href="{{ route('notes.edit', $note) }}" wire:navigate
                   class="block p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <div class="flex gap-3 items-start">
                        <span class="text-xl">{{ $note->icon }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-800 dark:text-gray-200 text-sm truncate">{{ $note->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $note->updated_at->diffForHumans() }}
                            </p>
                        </div>
                        @if($note->is_pinned)
                            <span class="text-xs">📌</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <a href="{{ route('notes.create') }}" wire:navigate
           class="block p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition group">
            <div class="flex gap-3 items-center">
                <span class="text-2xl">📝</span>
                <div class="flex-1">
                    <p class="font-medium text-gray-800 dark:text-gray-200">Crear primera nota</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Captura tus ideas</p>
                </div>
                <span class="opacity-0 group-hover:opacity-100 text-gray-400 text-xl transition">+</span>
            </div>
        </a>
    @endif
</div>
