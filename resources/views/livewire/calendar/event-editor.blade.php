<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.calendar.index') }}" wire:navigate>
            <flux:button variant="ghost" icon="arrow-left" size="sm" />
        </a>
        <flux:heading size="xl" level="1">
            {{ $this->isEditing ? '✏️ Editar Evento' : '➕ Nuevo Evento' }}
        </flux:heading>
    </div>

    <div class="max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                <!-- Habit Selection -->
                <div>
                    <flux:label>Hábito relacionado (opcional)</flux:label>
                    <flux:select wire:model.live="habit_id" placeholder="Seleccionar hábito...">
                        <flux:select.option value="">Sin hábito</flux:select.option>
                        @foreach($this->habits as $habit)
                            <flux:select.option value="{{ $habit->id }}">{{ $habit->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    @if($habit_id)
                        <flux:button type="button" size="xs" variant="subtle" class="mt-2" wire:click="selectHabit({{ $habit_id }})">
                            Usar nombre y color del hábito
                        </flux:button>
                    @endif
                </div>

                <!-- Title -->
                <div>
                    <flux:label required>Título</flux:label>
                    <flux:input wire:model="title" placeholder="Nombre del evento" />
                    @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div>
                    <flux:label>Descripción</flux:label>
                    <flux:textarea wire:model="description" placeholder="Descripción opcional..." rows="3" />
                </div>

                <!-- Date and Time -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <flux:label required>Fecha</flux:label>
                        <flux:input type="date" wire:model="date" />
                        @error('date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <flux:label required>Hora inicio</flux:label>
                        <flux:input type="time" wire:model="start_time" />
                        @error('start_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <flux:label required>Hora fin</flux:label>
                        <flux:input type="time" wire:model="end_time" />
                        @error('end_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Color -->
                <div>
                    <flux:label>Color</flux:label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model="color" class="w-10 h-10 rounded cursor-pointer border-0" />
                        <span class="text-sm text-zinc-500">{{ $color }}</span>
                    </div>
                </div>

                <!-- Reminder -->
                <div>
                    <flux:label>Recordatorio</flux:label>
                    <flux:select wire:model="reminder_minutes">
                        <flux:select.option value="">Sin recordatorio</flux:select.option>
                        <flux:select.option value="5">5 minutos antes</flux:select.option>
                        <flux:select.option value="15">15 minutos antes</flux:select.option>
                        <flux:select.option value="30">30 minutos antes</flux:select.option>
                        <flux:select.option value="60">1 hora antes</flux:select.option>
                        <flux:select.option value="1440">1 día antes</flux:select.option>
                    </flux:select>
                </div>

                <!-- Google Calendar Sync -->
                @if($this->isGoogleConnected)
                    <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white dark:bg-zinc-700 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">Sincronizar con Google Calendar</p>
                                <p class="text-sm text-zinc-500">El evento aparecerá en tu Google Calendar</p>
                            </div>
                        </div>
                        <flux:switch wire:model="sync_to_google" />
                    </div>
                @else
                    <div class="flex items-center justify-between p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white dark:bg-zinc-700 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">Google Calendar no conectado</p>
                                <p class="text-sm text-amber-600 dark:text-amber-400">Conecta tu cuenta para sincronizar eventos</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.calendar.settings') }}" wire:navigate>
                            <flux:button size="sm" variant="primary">Conectar</flux:button>
                        </a>
                    </div>
                @endif
                @error('sync_to_google') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Recurrence Section -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                <h3 class="font-semibold text-zinc-900 dark:text-white">🔄 Repetición</h3>

                <div>
                    <flux:label>Tipo de repetición</flux:label>
                    <flux:select wire:model.live="recurrence_type">
                        <flux:select.option value="">No se repite</flux:select.option>
                        <flux:select.option value="daily">Diariamente</flux:select.option>
                        <flux:select.option value="weekly">Semanalmente</flux:select.option>
                        <flux:select.option value="monthly">Mensualmente</flux:select.option>
                    </flux:select>
                </div>

                @if($recurrence_type === 'weekly')
                    <div>
                        <flux:label>Días de la semana</flux:label>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach([0 => 'Dom', 1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb'] as $num => $day)
                                <label class="flex items-center gap-1 px-3 py-1.5 rounded-lg border cursor-pointer transition-colors
                                    {{ in_array($num, $recurrence_days) ? 'bg-blue-100 border-blue-500 dark:bg-blue-900/50' : 'border-zinc-300 dark:border-zinc-600 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                                    <input type="checkbox" wire:model.live="recurrence_days" value="{{ $num }}" class="sr-only" />
                                    <span class="text-sm">{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($recurrence_type)
                    <div>
                        <flux:label>Repetir hasta</flux:label>
                        <flux:input type="date" wire:model="recurrence_end" />
                        @error('recurrence_end') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <div>
                    @if($this->isEditing)
                        <flux:button type="button" variant="danger" wire:click="delete" wire:confirm="¿Eliminar este evento?">
                            Eliminar
                        </flux:button>
                        @if($event->parent_event_id || $event->childEvents()->exists())
                            <flux:button type="button" variant="danger" wire:click="deleteAll" wire:confirm="¿Eliminar TODOS los eventos recurrentes?">
                                Eliminar todos
                            </flux:button>
                        @endif
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.calendar.index') }}" wire:navigate>
                        <flux:button type="button" variant="ghost">Cancelar</flux:button>
                    </a>
                    <flux:button type="submit" variant="primary">
                        {{ $this->isEditing ? 'Guardar cambios' : 'Crear evento' }}
                    </flux:button>
                </div>
            </div>
        </form>
    </div>
</div>
