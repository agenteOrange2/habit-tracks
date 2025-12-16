@php
    $user = auth()->user();
    $avatarUrl = $user->custom_avatar
        ? asset('storage/' . $user->custom_avatar)
        : 'https://api.dicebear.com/7.x/notionists/svg?seed=' . ($user->avatar_seed ?? $user->email);
    $coverUrl = $user->cover_image
        ? asset('storage/' . $user->cover_image)
        : 'https://images.unsplash.com/photo-1506259091721-347f798196d4?auto=format&fit=crop&w=1200&q=80';

    $playerClasses = [
        'guerrero' => ['name' => 'Guerrero', 'icon' => '⚔️', 'bg' => 'bg-red-100', 'text' => 'text-red-700'],
        'mago' => ['name' => 'Mago', 'icon' => '🔮', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
        'sanador' => ['name' => 'Sanador', 'icon' => '🌿', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
        'arquero' => ['name' => 'Arquero', 'icon' => '🏹', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
        'programador' => ['name' => 'Programador', 'icon' => '💻', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
    ];
    $classConfig = $playerClasses[$user->player_class ?? 'programador'] ?? $playerClasses['programador'];
@endphp

{{-- Cover Image --}}
<div
    class="h-64 w-full bg-gradient-to-r from-zinc-200 via-zinc-100 to-zinc-200 dark:from-zinc-800 dark:via-zinc-700 dark:to-zinc-800 relative overflow-hidden">
    @if ($user->cover_image)
        <img src="{{ $coverUrl }}" class="w-full h-full object-cover">
    @else
        {{-- Default gradient pattern --}}
        <img src="{{ asset('bg-cover/bg_habit_xp.jpg') }}" class="w-full h-full object-cover opacity-60 dark:opacity-40">
    @endif
</div>

<div class="max-w-4xl mx-auto w-full px-6 md:px-12 py-8 sm:py-16">
    {{-- Avatar and Info --}}
    <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-8 mb-6">
        <div
            class="w-24 h-24 sm:w-36 sm:h-36 bg-white dark:bg-zinc-800 rounded-xl shadow-lg flex items-center justify-center border-4 border-white dark:border-zinc-900 overflow-hidden flex-shrink-0">
            <img src="{{ $avatarUrl }}" alt="Avatar" class="w-full h-full object-contain p-1">
        </div>
        <div class="pb-1">
            <h2 class="text-lg sm:text-xl font-bold text-zinc-900 dark:text-white pb-2 sm:pb-4">{{ $user->name }}</h2>
            <div class="flex flex-col items-start gap-2 sm:gap-3 text-sm">
                <span
                    class="px-2 py-0.5 rounded text-base sm:text-lg font-medium {{ $classConfig['bg'] }} {{ $classConfig['text'] }}">
                    {{ $classConfig['icon'] }} {{ $classConfig['name'] }}
                </span>
                <span class="text-zinc-500 text-sm sm:text-lg font-normal dark:text-zinc-400 pt-1 break-all">{{ $user->email }}</span>
            </div>
        </div>
    </div>
</div>
