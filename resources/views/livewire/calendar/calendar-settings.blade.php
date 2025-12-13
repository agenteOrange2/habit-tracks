<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.calendar.index') }}" wire:navigate>
            <flux:button variant="ghost" icon="arrow-left" size="sm" />
        </a>
        <flux:heading size="xl" level="1">⚙️ Configuración del Calendario</flux:heading>
    </div>

    @if (session('message'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">
            {{ session('message') }}
        </flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" icon="exclamation-circle" class="mb-4">
            {{ session('error') }}
        </flux:callout>
    @endif

    <div class="max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <!-- General Settings -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                <h3 class="font-semibold text-zinc-900 dark:text-white">📋 Configuración General</h3>

                <div>
                    <flux:label>Vista predeterminada</flux:label>
                    <flux:select wire:model.live="default_view">
                        <flux:select.option value="month">Mes</flux:select.option>
                        <flux:select.option value="week">Semana</flux:select.option>
                        <flux:select.option value="day">Día</flux:select.option>
                    </flux:select>
                </div>

                <div>
                    <flux:label>Duración predeterminada de eventos (minutos)</flux:label>
                    <flux:input type="number" wire:model.lazy="default_duration" min="15" max="480" step="15" />
                    @error('default_duration') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <flux:label>Recordatorio predeterminado (minutos antes)</flux:label>
                    <flux:input type="number" wire:model.lazy="default_reminder" min="0" max="1440" />
                    @error('default_reminder') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Working Hours -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                <h3 class="font-semibold text-zinc-900 dark:text-white">🕐 Horario de Trabajo</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:label>Hora de inicio</flux:label>
                        <flux:input type="time" wire:model.lazy="working_hours_start" />
                    </div>
                    <div>
                        <flux:label>Hora de fin</flux:label>
                        <flux:input type="time" wire:model.lazy="working_hours_end" />
                    </div>
                </div>
            </div>

            <!-- Google Calendar Integration -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                <h3 class="font-semibold text-zinc-900 dark:text-white">📆 Google Calendar</h3>

                @if($googleConnected)
                    <div class="flex items-center gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <span class="text-green-600 dark:text-green-400">✓</span>
                        <span class="text-sm text-green-700 dark:text-green-300">Conectado a Google Calendar</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <flux:checkbox wire:model.live="auto_sync" />
                        <flux:label>Sincronizar automáticamente nuevos eventos</flux:label>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <flux:button type="button" variant="primary" size="sm" wire:click="syncExistingEvents" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="syncExistingEvents">Sincronizar eventos existentes</span>
                            <span wire:loading wire:target="syncExistingEvents">Sincronizando...</span>
                        </flux:button>
                        <flux:button type="button" variant="danger" size="sm" wire:click="disconnectGoogle" wire:confirm="¿Desconectar Google Calendar?">
                            Desconectar
                        </flux:button>
                    </div>
                @elseif($googleConfigured)
                    <div class="flex items-center gap-3 p-3 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                        <span class="text-zinc-500">○</span>
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">No conectado</span>
                    </div>

                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Conecta tu cuenta de Google para sincronizar eventos con Google Calendar.
                    </p>

                    <a href="{{ route('admin.calendar.google.connect') }}">
                        <flux:button type="button" variant="primary" size="sm" icon="link">
                            Conectar Google Calendar
                        </flux:button>
                    </a>
                @else
                    <div class="flex items-center gap-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                        <span class="text-amber-600 dark:text-amber-400">⚠</span>
                        <span class="text-sm text-amber-700 dark:text-amber-300">Integración no configurada</span>
                    </div>

                    <div class="text-sm text-zinc-500 dark:text-zinc-400 space-y-2">
                        <p>Para habilitar la sincronización con Google Calendar, necesitas configurar las credenciales de OAuth en tu archivo <code class="bg-zinc-100 dark:bg-zinc-800 px-1 rounded">.env</code>:</p>
                        <div class="bg-zinc-100 dark:bg-zinc-800 p-3 rounded-lg font-mono text-xs">
                            <p>GOOGLE_CLIENT_ID=tu_client_id</p>
                            <p>GOOGLE_CLIENT_SECRET=tu_client_secret</p>
                            <p>GOOGLE_REDIRECT_URI=${APP_URL}/calendar/google/callback</p>
                        </div>
                        <p class="text-xs">
                            Puedes obtener estas credenciales en 
                            <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="text-blue-500 hover:underline">Google Cloud Console</a>.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    Guardar configuración
                </flux:button>
            </div>
        </form>
    </div>
</div>
