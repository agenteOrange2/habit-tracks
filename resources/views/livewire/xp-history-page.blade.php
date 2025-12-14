<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.settings.profile') }}" wire:navigate>
            <flux:button variant="ghost" icon="arrow-left" size="sm" />
        </a>
        <flux:heading size="xl" level="1">⭐ Historial de XP</flux:heading>
    </div>

    <livewire:x-p-history />
</div>
