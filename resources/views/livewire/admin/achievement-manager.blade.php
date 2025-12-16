<div class="min-h-screen bg-[#FAFAFA] dark:bg-[#191919]">
    <div class="max-w-6xl mx-auto py-6 sm:py-8 px-4">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#37352F] dark:text-[#EFEFED] flex items-center gap-2">
                    <span>🏆</span> Gestión de Logros
                </h1>
                <p class="text-sm text-[#787774] dark:text-[#9B9A97] mt-1">Administra los logros que pueden desbloquear los usuarios</p>
            </div>
            <button wire:click="openModal"
                    class="px-4 py-2 bg-[#2383E2] hover:bg-[#1B74C9] text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2 w-full sm:w-auto">
                <span>+</span> Nuevo Logro
            </button>
        </div>

        {{-- Flash Messages --}}
        @if(session()->has('success'))
            <div class="mb-4 p-3 bg-[#DBEDDB] dark:bg-[#1B3D2F] border border-[#27AE60]/20 rounded-lg text-[#1C3829] dark:text-[#27AE60] text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mb-4 p-3 bg-[#FFE2DD] dark:bg-[#3D2222] border border-[#EB5757]/20 rounded-lg text-[#5D1715] dark:text-[#EB5757] text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Stats Summary --}}
        <div class="grid grid-cols-3 gap-2 sm:gap-4 mb-6 sm:mb-8">
            <div class="bg-[#DBEDDB] dark:bg-[#1B3D2F] border border-[#27AE60]/20 rounded-lg p-3 sm:p-4 text-center">
                <div class="text-2xl sm:text-3xl font-bold text-[#27AE60]">{{ $unlockedAchievements->count() }}</div>
                <div class="text-xs sm:text-sm text-[#1C3829] dark:text-[#27AE60]">Desbloqueados</div>
            </div>
            <div class="bg-[#FFE2DD] dark:bg-[#3D2222] border border-[#EB5757]/20 rounded-lg p-3 sm:p-4 text-center">
                <div class="text-2xl sm:text-3xl font-bold text-[#EB5757]">{{ $lockedAchievements->count() }}</div>
                <div class="text-xs sm:text-sm text-[#5D1715] dark:text-[#EB5757]">Por Desbloquear</div>
            </div>
            <div class="bg-[#E7F3F8] dark:bg-[#1B3A52] border border-[#2383E2]/20 rounded-lg p-3 sm:p-4 text-center">
                <div class="text-2xl sm:text-3xl font-bold text-[#2383E2]">{{ $secretAchievements->count() }}</div>
                <div class="text-xs sm:text-sm text-[#183347] dark:text-[#2383E2]">Secretos</div>
            </div>
        </div>

        {{-- Unlocked Achievements --}}
        @if($unlockedAchievements->count() > 0)
            <div class="mb-6 sm:mb-8">
                <h2 class="text-base sm:text-lg font-semibold text-[#27AE60] mb-3 flex items-center gap-2">
                    <span>✅</span> Logros Desbloqueados
                    <span class="text-xs font-normal bg-[#DBEDDB] dark:bg-[#1B3D2F] px-2 py-0.5 rounded">{{ $unlockedAchievements->count() }}</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    @foreach($unlockedAchievements as $achievement)
                        <div class="bg-[#DBEDDB] dark:bg-[#1B3D2F]/50 border border-[#27AE60]/30 rounded-lg p-3 sm:p-4 hover:shadow-md transition group relative">
                            <div class="absolute top-2 right-2">
                                <span class="text-[#27AE60] text-lg">✓</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-2xl sm:text-3xl">{{ $achievement->icon }}</span>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-[#37352F] dark:text-[#EFEFED] text-sm sm:text-base">{{ $achievement->name }}</h3>
                                    <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9A97] line-clamp-2">{{ $achievement->description }}</p>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between text-xs">
                                <span class="bg-white dark:bg-[#252525] px-2 py-1 rounded text-[#787774] dark:text-[#9B9A97]">
                                    {{ $categories[$achievement->category] ?? $achievement->category }}
                                </span>
                                <span class="font-semibold text-[#27AE60]">+{{ $achievement->points_reward }} XP</span>
                            </div>

                            <div class="mt-3 pt-3 border-t border-[#27AE60]/20 flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                <button wire:click="editAchievement({{ $achievement->id }})" class="text-xs text-[#2383E2] hover:underline">Editar</button>
                                <button wire:click="deleteAchievement({{ $achievement->id }})" wire:confirm="¿Eliminar este logro?" class="text-xs text-[#EB5757] hover:underline">Eliminar</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Locked Achievements --}}
        @if($lockedAchievements->count() > 0)
            <div class="mb-6 sm:mb-8">
                <h2 class="text-base sm:text-lg font-semibold text-[#EB5757] mb-3 flex items-center gap-2">
                    <span>🔓</span> Por Desbloquear
                    <span class="text-xs font-normal bg-[#FFE2DD] dark:bg-[#3D2222] px-2 py-0.5 rounded text-[#EB5757]">{{ $lockedAchievements->count() }}</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    @foreach($lockedAchievements as $achievement)
                        <div class="bg-[#FFE2DD] dark:bg-[#3D2222]/50 border border-[#EB5757]/20 rounded-lg p-3 sm:p-4 hover:shadow-md transition group opacity-75 hover:opacity-100">
                            <div class="flex items-start gap-3">
                                <span class="text-2xl sm:text-3xl grayscale">{{ $achievement->icon }}</span>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-[#37352F] dark:text-[#EFEFED] text-sm sm:text-base">{{ $achievement->name }}</h3>
                                    <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9A97] line-clamp-2">{{ $achievement->description }}</p>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 text-[#787774] dark:text-[#9B9A97]">
                                    <span class="bg-[#FFE2DD] dark:bg-[#3D2222] px-2 py-1 rounded">{{ $achievementTypes[$achievement->requirement_type] ?? $achievement->requirement_type }}</span>
                                    <span>≥ {{ $achievement->requirement_value }}</span>
                                </div>
                                <span class="font-semibold text-[#EB5757]">+{{ $achievement->points_reward }} XP</span>
                            </div>

                            <div class="mt-3 pt-3 border-t border-[#EB5757]/20 flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                <button wire:click="editAchievement({{ $achievement->id }})" class="text-xs text-[#2383E2] hover:underline">Editar</button>
                                <button wire:click="deleteAchievement({{ $achievement->id }})" wire:confirm="¿Eliminar este logro?" class="text-xs text-[#EB5757] hover:underline">Eliminar</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Secret Achievements --}}
        @if($secretAchievements->count() > 0)
            <div class="mb-6 sm:mb-8">
                <h2 class="text-base sm:text-lg font-semibold text-[#2383E2] mb-3 flex items-center gap-2">
                    <span>🔒</span> Logros Secretos
                    <span class="text-xs font-normal bg-[#E7F3F8] dark:bg-[#1B3A52] px-2 py-0.5 rounded text-[#2383E2]">{{ $secretAchievements->count() }}</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    @foreach($secretAchievements as $achievement)
                        <div class="bg-[#E7F3F8] dark:bg-[#1B3A52]/50 border border-[#2383E2]/20 rounded-lg p-3 sm:p-4 hover:shadow-md transition group">
                            <div class="flex items-start gap-3">
                                <span class="text-2xl sm:text-3xl">{{ $achievement->icon }}</span>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-[#37352F] dark:text-[#EFEFED] text-sm sm:text-base flex items-center gap-2">
                                        {{ $achievement->name }}
                                        <span class="text-[10px] bg-[#2383E2]/20 text-[#2383E2] px-1.5 py-0.5 rounded">Secreto</span>
                                    </h3>
                                    <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9A97] line-clamp-2">{{ $achievement->description }}</p>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 text-[#787774] dark:text-[#9B9A97]">
                                    <span class="bg-[#E7F3F8] dark:bg-[#1B3A52] px-2 py-1 rounded">{{ $achievementTypes[$achievement->requirement_type] ?? $achievement->requirement_type }}</span>
                                    <span>≥ {{ $achievement->requirement_value }}</span>
                                </div>
                                <span class="font-semibold text-[#2383E2]">+{{ $achievement->points_reward }} XP</span>
                            </div>

                            <div class="mt-3 pt-3 border-t border-[#2383E2]/20 flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                <button wire:click="editAchievement({{ $achievement->id }})" class="text-xs text-[#2383E2] hover:underline">Editar</button>
                                <button wire:click="deleteAchievement({{ $achievement->id }})" wire:confirm="¿Eliminar este logro?" class="text-xs text-[#EB5757] hover:underline">Eliminar</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Empty State --}}
        @if($unlockedAchievements->count() === 0 && $lockedAchievements->count() === 0 && $secretAchievements->count() === 0)
            <div class="text-center py-12 bg-white dark:bg-[#252525] rounded-lg border border-[#E9E9E7] dark:border-[#3E3E3A]">
                <span class="text-4xl mb-4 block">🏆</span>
                <h3 class="text-lg font-semibold text-[#37352F] dark:text-[#EFEFED] mb-2">No hay logros creados</h3>
                <p class="text-sm text-[#787774] dark:text-[#9B9A97] mb-4">Crea tu primer logro para motivar a los usuarios</p>
                <button wire:click="openModal" class="px-4 py-2 bg-[#2383E2] hover:bg-[#1B74C9] text-white text-sm font-medium rounded-lg transition">
                    Crear Primer Logro
                </button>
            </div>
        @endif
    </div>

    {{-- Modal - Full screen on mobile, centered on desktop --}}
    @if($showModal)
        <div class="fixed inset-0 bg-black/50 dark:bg-black/70 z-50 overflow-y-auto"
             x-data @keydown.escape.window="$wire.closeModal()">
            <div class="min-h-screen sm:min-h-0 sm:flex sm:items-center sm:justify-center sm:p-4">
                <div class="bg-white dark:bg-[#252525] w-full min-h-screen sm:min-h-0 sm:max-w-lg sm:rounded-xl sm:max-h-[90vh] sm:overflow-hidden border-0 sm:border border-[#E9E9E7] dark:border-[#3E3E3A] shadow-xl flex flex-col"
                     @click.outside="$wire.closeModal()">
                    {{-- Modal Header - Fixed --}}
                    <div class="flex-shrink-0 bg-white dark:bg-[#252525] px-4 py-3 border-b border-[#E9E9E7] dark:border-[#3E3E3A] flex justify-between items-center">
                        <h2 class="text-base font-semibold text-[#37352F] dark:text-[#EFEFED]">
                            {{ $isEditing ? 'Editar Logro' : 'Nuevo Logro' }}
                        </h2>
                        <button wire:click="closeModal" class="text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED] p-2 -mr-2">✕</button>
                    </div>

                    {{-- Modal Body - Scrollable --}}
                    <div class="flex-1 overflow-y-auto">
                        <form wire:submit="save" class="p-4 space-y-4" id="achievement-form">
                            {{-- Icon Picker --}}
                            <div>
                                <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-2">Icono</label>
                                <div class="grid grid-cols-8 sm:grid-cols-10 gap-1">
                                    @foreach($commonIcons as $emoji)
                                        <button type="button" wire:click="$set('icon', '{{ $emoji }}')"
                                                class="text-lg sm:text-xl aspect-square flex items-center justify-center hover:bg-[#EFEFED] dark:hover:bg-[#1F1F1F] rounded transition {{ $icon === $emoji ? 'bg-[#E7F3F8] dark:bg-[#1B3A52] ring-2 ring-[#2383E2]' : '' }}">
                                            {{ $emoji }}
                                        </button>
                                    @endforeach
                                </div>
                                @error('icon') <span class="text-[#EB5757] text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Name --}}
                            <div>
                                <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-1">Nombre</label>
                                <input type="text" wire:model="name"
                                       class="w-full px-3 py-2.5 border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#1F1F1F] text-[#37352F] dark:text-[#EFEFED] focus:ring-2 focus:ring-[#2383E2] focus:border-[#2383E2] text-base"
                                       placeholder="Ej: Primer Paso">
                                @error('name') <span class="text-[#EB5757] text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Description --}}
                            <div>
                                <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-1">Descripción</label>
                                <textarea wire:model="description" rows="2"
                                          class="w-full px-3 py-2.5 border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#1F1F1F] text-[#37352F] dark:text-[#EFEFED] focus:ring-2 focus:ring-[#2383E2] focus:border-[#2383E2] text-base"
                                          placeholder="Ej: Completa tu primer hábito"></textarea>
                                @error('description') <span class="text-[#EB5757] text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Category --}}
                            <div>
                                <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-1">Categoría</label>
                                <select wire:model="category"
                                        class="w-full px-3 py-2.5 text-base border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#1F1F1F] text-[#37352F] dark:text-[#EFEFED] focus:ring-2 focus:ring-[#2383E2]">
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Requirement Type --}}
                            <div>
                                <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-1">Tipo de Requisito</label>
                                <select wire:model="requirement_type"
                                        class="w-full px-3 py-2.5 text-base border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#1F1F1F] text-[#37352F] dark:text-[#EFEFED] focus:ring-2 focus:ring-[#2383E2]">
                                    @foreach($achievementTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Value & Points --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-1">Valor Requerido</label>
                                    <input type="number" wire:model="requirement_value" min="1"
                                           class="w-full px-3 py-2.5 text-base border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#1F1F1F] text-[#37352F] dark:text-[#EFEFED] focus:ring-2 focus:ring-[#2383E2]">
                                    @error('requirement_value') <span class="text-[#EB5757] text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#37352F] dark:text-[#EFEFED] mb-1">Recompensa XP</label>
                                    <input type="number" wire:model="points_reward" min="1" max="1000"
                                           class="w-full px-3 py-2.5 text-base border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#1F1F1F] text-[#37352F] dark:text-[#EFEFED] focus:ring-2 focus:ring-[#2383E2]">
                                    @error('points_reward') <span class="text-[#EB5757] text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Secret Toggle --}}
                            <div class="flex items-center gap-3 py-2">
                                <input type="checkbox" wire:model="is_secret" id="is_secret"
                                       class="w-5 h-5 text-[#2383E2] border-[#E9E9E7] dark:border-[#3E3E3A] rounded focus:ring-[#2383E2] bg-white dark:bg-[#1F1F1F]">
                                <label for="is_secret" class="text-base text-[#37352F] dark:text-[#EFEFED]">
                                    🔒 Logro Secreto
                                </label>
                            </div>
                        </form>
                    </div>

                    {{-- Modal Footer - Fixed --}}
                    <div class="flex-shrink-0 bg-white dark:bg-[#252525] flex gap-3 p-4 border-t border-[#E9E9E7] dark:border-[#3E3E3A]">
                        <button type="button" wire:click="closeModal"
                                class="flex-1 px-4 py-3 text-[#787774] dark:text-[#9B9A97] bg-[#F7F7F5] dark:bg-[#1F1F1F] hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] rounded-lg transition text-base font-medium">
                            Cancelar
                        </button>
                        <button type="submit" form="achievement-form"
                                class="flex-1 px-4 py-3 bg-[#2383E2] hover:bg-[#1B74C9] text-white font-medium rounded-lg transition text-base">
                            {{ $isEditing ? 'Guardar' : 'Crear' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
