@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center space-y-2">
    <flux:heading size="xl" class="font-semibold text-[#37352F] dark:text-[#EFEFED]">
        {{ $title }}
    </flux:heading>
    <flux:subheading class="text-[#787774] dark:text-[#9B9A97]">
        {{ $description }}
    </flux:subheading>
</div>
