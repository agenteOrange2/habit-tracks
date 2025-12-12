<div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h3 class="font-bold text-slate-800 text-lg">Cronología del Día</h3>
        <button class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
            </svg>
        </button>
    </div>

    <div wire:loading class="flex items-center justify-center py-8">
        <svg class="animate-spin h-8 w-8 text-brand-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <div wire:loading.remove>
        @if(count($events) > 0)
            <div class="relative">
                {{-- Timeline line --}}
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                {{-- Timeline events --}}
                <div class="space-y-6">
                    @foreach($events as $event)
                        <div class="relative flex items-start gap-4">
                            {{-- Timeline dot --}}
                            <div class="relative z-10 flex-shrink-0">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl shadow-md
                                    {{ $event['type'] === 'completed' ? 'bg-gradient-to-br from-green-400 to-emerald-500' : 'bg-gray-100 border-2 border-gray-300' }}">
                                    {{ $event['icon'] }}
                                </div>
                            </div>

                            {{-- Event content --}}
                            <div class="flex-1 min-w-0 pt-1">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-slate-800 text-sm {{ $event['type'] === 'pending' ? 'text-slate-500' : '' }}">
                                            {{ $event['name'] }}
                                        </h4>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            {{ $event['time_formatted'] }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if($event['type'] === 'completed' && $event['points'])
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-600 border border-green-200">
                                                +{{ $event['points'] }} XP
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-200">
                                                Pendiente
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 mb-4">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-700 mb-2">
                    No hay actividades para hoy
                </h3>
                <p class="text-sm text-slate-500 mb-6">
                    Crea hábitos para comenzar a ver tu cronología diaria
                </p>
                <a 
                    href="{{ route('admin.habits.create') }}"
                    wire:navigate
                    class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear mi primer hábito
                </a>
            </div>
        @endif
    </div>
</div>
