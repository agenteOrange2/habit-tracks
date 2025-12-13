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
                                    <input type="checkbox" wire:model="recurrence_days" value="{{ $num }}" class="sr-only" />
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
