<div class="flex h-full w-full flex-1 flex-col" x-data="{ showDisableConfirm: false }">
    {{-- Mini Profile Header --}}
    @include('partials.settings-profile-header')

    <div class="max-w-4xl mx-auto w-full px-6 md:px-12 pb-12">
        {{-- Page Title --}}
        <div class="mb-8 pt-2">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white mb-1 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Autenticación de Dos Factores
            </h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm">Añade una capa extra de seguridad a tu cuenta</p>
        </div>

        {{-- Status Card --}}
        <div class="space-y-6">
            @if ($twoFactorEnabled)
                {{-- Enabled State --}}
                <div class="bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800 p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-green-800 dark:text-green-300">2FA Activado</h3>
                                <span class="px-2 py-0.5 bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200 text-xs font-medium rounded-full">Activo</span>
                            </div>
                            <p class="text-sm text-green-700 dark:text-green-400">
                                Tu cuenta está protegida con autenticación de dos factores.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Recovery Codes Section --}}
                <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-zinc-900 dark:text-white mb-2">Códigos de Recuperación</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                                Guarda estos códigos en un lugar seguro.
                            </p>
                            <livewire:settings.two-factor.recovery-codes :$requiresConfirmation/>
                        </div>
                    </div>
                </div>

                {{-- Disable Button --}}
                <div class="pt-4">
                    <button 
                        @click="showDisableConfirm = true"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                        Desactivar 2FA
                    </button>
                </div>

                {{-- Disable Confirmation Modal --}}
                <div x-show="showDisableConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
                    <div @click.outside="showDisableConfirm = false" class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-md w-full p-6">
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">¿Desactivar 2FA?</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-6">
                            Tu cuenta será menos segura. ¿Estás seguro?
                        </p>
                        <div class="flex gap-3 justify-end">
                            <button type="button" @click="showDisableConfirm = false" class="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-lg transition-colors">
                                Cancelar
                            </button>
                            <button wire:click="disable" @click="showDisableConfirm = false" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                                Sí, desactivar
                            </button>
                        </div>
                    </div>
                </div>
            @else
                {{-- Disabled State --}}
                <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-zinc-200 dark:bg-zinc-700 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-zinc-900 dark:text-white">2FA Desactivado</h3>
                                <span class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-600 text-zinc-600 dark:text-zinc-300 text-xs font-medium rounded-full">Inactivo</span>
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                Activa la autenticación de dos factores para mayor seguridad.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- How it works --}}
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="font-bold text-zinc-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 text-[10px] px-1.5 py-0.5 rounded uppercase tracking-wider">Info</span>
                        ¿Cómo funciona?
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-xs font-bold text-blue-600 dark:text-blue-400">1</div>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300">Descarga Google Authenticator o Authy</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-xs font-bold text-blue-600 dark:text-blue-400">2</div>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300">Escanea el código QR</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-xs font-bold text-blue-600 dark:text-blue-400">3</div>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300">Ingresa el código de 6 dígitos</p>
                        </div>
                    </div>
                </div>

                {{-- Enable Button --}}
                <div class="pt-4">
                    <button 
                        wire:click="enable"
                        class="bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 px-6 py-2.5 rounded-lg shadow hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors text-sm font-medium flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Activar 2FA
                    </button>
                </div>
            @endif
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

    {{-- Setup Modal --}}
    <div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
        <div @click.outside="$wire.closeModal()" class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-md w-full p-6 relative">
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ $this->modalConfig['title'] }}</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $this->modalConfig['description'] }}</p>
            </div>

            @if ($showVerificationStep)
                <div class="space-y-4">
                    <div class="flex flex-col items-center">
                        <input type="text" wire:model="code" maxlength="6" placeholder="000000" class="w-36 text-center text-xl font-mono tracking-widest px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        @error('code') <span class="text-red-500 text-xs mt-2">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex gap-3">
                        <button wire:click="resetVerification" class="flex-1 px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-600 rounded-lg">Atrás</button>
                        <button wire:click="confirmTwoFactor" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Confirmar</button>
                    </div>
                </div>
            @else
                @error('setupData')
                    <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300 text-sm">{{ $message }}</div>
                @enderror

                <div class="flex justify-center mb-4">
                    <div class="w-40 h-40 bg-white rounded-lg border border-zinc-200 p-2 flex items-center justify-center">
                        @empty($qrCodeSvg)
                            <svg class="w-6 h-6 text-zinc-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        @else
                            {!! $qrCodeSvg !!}
                        @endempty
                    </div>
                </div>

                <button wire:click="showVerificationIfNecessary" @if($errors->has('setupData')) disabled @endif class="w-full px-4 py-2 text-sm font-medium text-white bg-zinc-900 dark:bg-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 rounded-lg disabled:opacity-50">
                    {{ $this->modalConfig['buttonText'] }}
                </button>

                @if($manualSetupKey)
                <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <p class="text-xs text-zinc-500 text-center mb-2">O ingresa manualmente:</p>
                    <code class="block text-center text-xs bg-zinc-100 dark:bg-zinc-700 p-2 rounded font-mono text-zinc-700 dark:text-zinc-300">{{ $manualSetupKey }}</code>
                </div>
                @endif
            @endif

            <button wire:click="closeModal" class="absolute top-3 right-3 text-zinc-400 hover:text-zinc-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>
</div>
