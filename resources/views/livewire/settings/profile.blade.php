<div class="flex h-full w-full flex-1 flex-col" x-data="{ classDropdownOpen: false, showDeleteModal: false, showPasswordSection: false }">
    {{-- Cover Image --}}
    <div class="h-64 w-full bg-gradient-to-r from-zinc-100 to-zinc-200 dark:from-zinc-800 dark:to-zinc-900 relative overflow-hidden group">
        @if($this->currentCoverUrl)
            <img src="{{ $this->currentCoverUrl }}" class="w-full h-full object-cover">
        @else
            <img src="https://images.unsplash.com/photo-1506259091721-347f798196d4?auto=format&fit=crop&w=1200&q=80" 
                 class="w-full h-full object-cover opacity-60 dark:opacity-40">
        @endif
        
        {{-- Cover Upload Button --}}
        <label class="absolute bottom-3 right-3 bg-white/90 dark:bg-zinc-800/90 hover:bg-white dark:hover:bg-zinc-700 text-xs px-3 py-1.5 rounded-lg shadow cursor-pointer flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="text-zinc-700 dark:text-zinc-300">Cambiar portada</span>
            <input type="file" wire:model="newCover" accept="image/*" class="hidden">
        </label>
        
        <div wire:loading wire:target="newCover" class="absolute inset-0 bg-black/50 flex items-center justify-center">
            <div class="text-white text-sm">Subiendo...</div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto w-full px-6 md:px-12 pb-12">
        {{-- Avatar --}}
        <div class="relative -mt-12 mb-6 inline-block group">
            <div class="w-24 h-24 bg-white dark:bg-zinc-800 rounded-xl shadow-lg flex items-center justify-center border-4 border-white dark:border-zinc-900 overflow-hidden">
                <img src="{{ $this->currentAvatarUrl }}" alt="Avatar" class="w-full h-full object-contain p-1">
            </div>
            @if($custom_avatar)
                <button wire:click="removeCustomAvatar" class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs shadow opacity-0 group-hover:opacity-100 transition-opacity" title="Eliminar foto">✕</button>
            @endif
        </div>

        {{-- Flash Messages --}}
        @if (session('message'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-300 text-sm">✓ {{ session('message') }}</div>
        @endif
        @if (session('passwordMessage'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-300 text-sm">✓ {{ session('passwordMessage') }}</div>
        @endif
        @error('newAvatar') <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300 text-sm">{{ $message }}</div> @enderror
        @error('newCover') <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300 text-sm">{{ $message }}</div> @enderror

        <form wire:submit="updateProfileInformation" class="space-y-8">
            {{-- Name --}}
            <div>
                <input type="text" wire:model="name" class="w-full text-3xl md:text-4xl font-bold bg-transparent border-none focus:ring-0 p-0 text-zinc-900 dark:text-white placeholder-zinc-300 dark:placeholder-zinc-600" placeholder="Tu nombre">
                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Properties Section --}}
            <div class="space-y-3 border-b border-zinc-200 dark:border-zinc-700 pb-6">
                {{-- Class/Role --}}
                <div class="flex items-center py-2">
                    <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400 text-sm w-40 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span>Clase / Rol</span>
                    </div>
                    <div class="flex-1 relative">
                        <button type="button" @click="classDropdownOpen = !classDropdownOpen" class="px-3 py-1 rounded-md text-sm cursor-pointer transition-colors flex items-center gap-2 {{ $this->selectedClassConfig['bg'] }} {{ $this->selectedClassConfig['text'] }}">
                            <span>{{ $this->selectedClassConfig['icon'] }}</span>
                            <span>{{ $this->selectedClassConfig['name'] }}</span>
                            <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="classDropdownOpen" @click.outside="classDropdownOpen = false" x-transition class="absolute top-10 left-0 w-64 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg z-20 py-1">
                            <p class="px-3 py-2 text-xs text-zinc-500 uppercase font-bold tracking-wider">Selecciona una clase</p>
                            @foreach($playerClasses as $class)
                                <div wire:click="selectClass('{{ $class['id'] }}')" @click="classDropdownOpen = false" class="px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer flex items-center gap-3">
                                    <div class="w-6 h-6 flex items-center justify-center rounded {{ $class['bg'] }} {{ $class['text'] }}">{{ $class['icon'] }}</div>
                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $class['name'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Level & XP --}}
                @if($this->userLevel)
                <div class="flex items-center py-2">
                    <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400 text-sm w-40 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>Nivel & XP</span>
                    </div>
                    <div class="flex-1 flex items-center gap-3">
                        <span class="text-sm font-mono bg-zinc-100 dark:bg-zinc-700 px-2 py-0.5 rounded text-zinc-700 dark:text-zinc-300">LVL {{ $this->userLevel->level ?? 1 }}</span>
                        <div class="w-48 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden relative group">
                            @php $xp = $this->userLevel->experience ?? 0; $nextLevel = ($this->userLevel->level ?? 1) * 1000; $progress = min(100, ($xp / $nextLevel) * 100); @endphp
                            <div class="h-full bg-zinc-800 dark:bg-zinc-300 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Email --}}
                <div class="flex items-center py-2">
                    <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400 text-sm w-40 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>Email</span>
                    </div>
                    <div class="flex-1">
                        <input type="email" wire:model="email" class="w-full bg-transparent border-b border-transparent hover:border-zinc-300 dark:hover:border-zinc-600 focus:border-zinc-500 focus:outline-none py-1 px-1 transition-colors text-zinc-700 dark:text-zinc-300" placeholder="tu@email.com">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Avatar Gallery --}}
            <div>
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2 text-zinc-900 dark:text-white">
                    <span class="bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 text-[10px] px-1.5 py-0.5 rounded uppercase tracking-wider">Galería</span>
                    Apariencia
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach($avatarOptions as $avatar)
                        <div wire:click="selectAvatar('{{ $avatar['seed'] }}')" class="border rounded-xl overflow-hidden cursor-pointer hover:shadow-lg transition-all {{ $avatar_seed === $avatar['seed'] && !$custom_avatar ? 'ring-2 ring-zinc-900 dark:ring-white border-transparent' : 'border-zinc-200 dark:border-zinc-700' }}">
                            <div class="h-24 {{ $avatar['bg'] }} relative">
                                <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatar['seed'] }}" class="w-full h-full object-contain p-2" alt="{{ $avatar['name'] }}">
                            </div>
                            <div class="p-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800">{{ $avatar['name'] }}</div>
                        </div>
                    @endforeach
                    <label class="border border-dashed border-zinc-300 dark:border-zinc-600 rounded-xl overflow-hidden cursor-pointer hover:shadow-lg transition-all {{ $custom_avatar ? 'ring-2 ring-zinc-900 dark:ring-white border-transparent' : '' }}">
                        @if($custom_avatar)
                            <div class="h-24 bg-zinc-100 dark:bg-zinc-700"><img src="{{ asset('storage/' . $custom_avatar) }}" class="w-full h-full object-cover" alt="Mi foto"></div>
                            <div class="p-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800">Mi foto</div>
                        @else
                            <div class="h-24 bg-zinc-50 dark:bg-zinc-800 flex flex-col items-center justify-center text-zinc-400">
                                <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                <span class="text-xs">Subir foto</span>
                            </div>
                            <div class="p-2 text-sm font-medium text-zinc-400 bg-white dark:bg-zinc-800">Personalizado</div>
                        @endif
                        <input type="file" wire:model="newAvatar" accept="image/*" class="hidden">
                    </label>
                </div>
                <div wire:loading wire:target="newAvatar" class="mt-4 text-sm text-zinc-500">Subiendo avatar...</div>
            </div>

            {{-- Save Profile Button --}}
            <div class="flex justify-start">
                <button type="submit" class="bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 px-6 py-2.5 rounded-lg shadow hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors text-sm font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Guardar Perfil
                </button>
            </div>
        </form>


        {{-- Security Section --}}
        <div class="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-700">
            <h3 class="font-bold text-lg mb-6 flex items-center gap-2 text-zinc-900 dark:text-white">
                <span class="bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 text-[10px] px-1.5 py-0.5 rounded uppercase tracking-wider">Seguridad</span>
                Contraseña y Acceso
            </h3>

            {{-- Password Change --}}
            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 mb-4">
                <div class="flex items-center justify-between cursor-pointer" @click="showPasswordSection = !showPasswordSection">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-zinc-200 dark:bg-zinc-700 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-zinc-900 dark:text-white">Cambiar Contraseña</h4>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Actualiza tu contraseña de acceso</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-zinc-400 transition-transform" :class="showPasswordSection ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
                
                <div x-show="showPasswordSection" x-collapse class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <form wire:submit="updatePassword" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Contraseña actual</label>
                            <input type="password" wire:model="current_password" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-zinc-500">
                            @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nueva contraseña</label>
                            <input type="password" wire:model="password" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-zinc-500">
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Confirmar contraseña</label>
                            <input type="password" wire:model="password_confirmation" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-zinc-500">
                        </div>
                        <button type="submit" class="bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 px-4 py-2 rounded-lg text-sm font-medium hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors">
                            Actualizar Contraseña
                        </button>
                    </form>
                </div>
            </div>

            {{-- Two Factor Link --}}
            <a href="{{ route('admin.settings.two-factor') }}" wire:navigate class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 mb-4 flex items-center justify-between hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-zinc-900 dark:text-white">Autenticación de Dos Factores</h4>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Añade una capa extra de seguridad</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>

            {{-- Appearance Link --}}
            <a href="{{ route('admin.settings.appearance') }}" wire:navigate class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 flex items-center justify-between hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-zinc-900 dark:text-white">Apariencia</h4>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Tema claro, oscuro o del sistema</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>


        {{-- Danger Zone --}}
        <div class="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-700">
            <div class="flex gap-3 p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800">
                <div class="text-xl">⚠️</div>
                <div class="flex-1">
                    <h4 class="font-bold text-sm text-red-700 dark:text-red-300">Zona de Peligro</h4>
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1 mb-4">Una vez que elimines tu cuenta, todos tus datos serán eliminados permanentemente.</p>
                    
                    <button type="button" @click="showDeleteModal = true" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Eliminar mi cuenta
                    </button>
                </div>
            </div>
        </div>

        {{-- Delete Modal --}}
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
            <div @click.outside="showDeleteModal = false" class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">¿Eliminar cuenta?</h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">Esta acción eliminará permanentemente tu cuenta. Ingresa tu contraseña para confirmar.</p>
                
                <form wire:submit="deleteUser">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Contraseña</label>
                        <input type="password" wire:model="deletePassword" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-red-500" placeholder="Tu contraseña actual">
                        @error('deletePassword') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-lg transition-colors">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Sí, eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
