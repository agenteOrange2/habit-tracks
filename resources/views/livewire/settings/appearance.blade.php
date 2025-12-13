<div class="flex h-full w-full flex-1 flex-col">
    {{-- Mini Profile Header --}}
    @include('partials.settings-profile-header')

    <div class="max-w-4xl mx-auto w-full px-6 md:px-12 pb-12">
        {{-- Page Title --}}
        <div class="mb-8 pt-2">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white mb-1 flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                </svg>
                Apariencia
            </h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm">Personaliza cómo se ve la aplicación</p>
        </div>

        {{-- Theme Selection --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="font-bold text-zinc-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 text-[10px] px-1.5 py-0.5 rounded uppercase tracking-wider">Tema</span>
                Modo de visualización
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" x-data>
                {{-- Light Mode --}}
                <label 
                    class="relative cursor-pointer group"
                    :class="$flux.appearance === 'light' ? 'ring-2 ring-zinc-900 dark:ring-white rounded-xl' : ''"
                >
                    <input type="radio" name="theme" value="light" class="sr-only" x-model="$flux.appearance">
                    <div class="border border-zinc-200 dark:border-zinc-600 rounded-xl overflow-hidden hover:shadow-lg transition-all">
                        <div class="h-24 bg-white flex items-center justify-center">
                            <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-700 text-center">
                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Claro</span>
                        </div>
                    </div>
                    <div x-show="$flux.appearance === 'light'" class="absolute top-2 right-2 w-5 h-5 bg-zinc-900 dark:bg-white rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3 text-white dark:text-zinc-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </label>

                {{-- Dark Mode --}}
                <label 
                    class="relative cursor-pointer group"
                    :class="$flux.appearance === 'dark' ? 'ring-2 ring-zinc-900 dark:ring-white rounded-xl' : ''"
                >
                    <input type="radio" name="theme" value="dark" class="sr-only" x-model="$flux.appearance">
                    <div class="border border-zinc-200 dark:border-zinc-600 rounded-xl overflow-hidden hover:shadow-lg transition-all">
                        <div class="h-24 bg-zinc-800 flex items-center justify-center">
                            <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-700 text-center">
                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Oscuro</span>
                        </div>
                    </div>
                    <div x-show="$flux.appearance === 'dark'" class="absolute top-2 right-2 w-5 h-5 bg-zinc-900 dark:bg-white rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3 text-white dark:text-zinc-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </label>

                {{-- System Mode --}}
                <label 
                    class="relative cursor-pointer group"
                    :class="$flux.appearance === 'system' ? 'ring-2 ring-zinc-900 dark:ring-white rounded-xl' : ''"
                >
                    <input type="radio" name="theme" value="system" class="sr-only" x-model="$flux.appearance">
                    <div class="border border-zinc-200 dark:border-zinc-600 rounded-xl overflow-hidden hover:shadow-lg transition-all">
                        <div class="h-24 bg-gradient-to-r from-white to-zinc-800 flex items-center justify-center">
                            <svg class="w-10 h-10 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-700 text-center">
                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Sistema</span>
                        </div>
                    </div>
                    <div x-show="$flux.appearance === 'system'" class="absolute top-2 right-2 w-5 h-5 bg-zinc-900 dark:bg-white rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3 text-white dark:text-zinc-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </label>
            </div>

            <p class="mt-4 text-xs text-zinc-500 dark:text-zinc-400">
                El modo "Sistema" se ajusta automáticamente según la configuración de tu dispositivo.
            </p>
        </div>

        {{-- Back to Profile Link --}}
        <div class="mt-8 pt-6 border-t border-zinc-200 dark:border-zinc-700">
            <a href="{{ route('admin.settings.profile') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al perfil
            </a>
        </div>
    </div>
</div>
