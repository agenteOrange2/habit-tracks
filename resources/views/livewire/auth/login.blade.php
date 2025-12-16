<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header 
            :title="__('Bienvenido de nuevo')" 
            :description="__('Ingresa tus credenciales para continuar con tus hábitos')" 
        />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Correo electrónico')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="tu@email.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Contraseña')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Tu contraseña')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" :href="route('password.request')" wire:navigate>
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Recordarme')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full shadow-lg hover:shadow-xl transition-shadow" data-test="login-button">
                    {{ __('Iniciar sesión') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('¿No tienes una cuenta?') }}</span>
                <flux:link class="font-semibold hover:text-blue-600 dark:hover:text-blue-400 transition-colors" :href="route('register')" wire:navigate>{{ __('Regístrate') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts.auth>
