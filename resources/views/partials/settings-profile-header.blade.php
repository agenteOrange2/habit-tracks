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
<div class="h-64 w-full bg-gradient-to-r from-zinc-200 via-zinc-100 to-zinc-200 dark:from-zinc-800 dark:via-zinc-700 dark:to-zinc-800 relative overflow-hidden">
    @if($user->cover_image)
        <img src="{{ $coverUrl }}" class="w-full h-full object-cover">
    @else
        {{-- Default gradient pattern --}}
        <div class="absolute inset-0 bg-gradient-to-br from-blue-100 via-purple-50 to-pink-100 dark:from-blue-900/30 dark:via-purple-900/20 dark:to-pink-900/30"></div>
        <div class="absolute inset-0 opacity-30" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%239C92AC\' fill-opacity=\'0.15\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    @endif
</div>

<div class="max-w-4xl mx-auto w-full px-6 md:px-12 py-16">
    {{-- Avatar and Info --}}
    <div class="flex items-end gap-4 -mt-8 mb-6">
        <div class="w-36 h-36 bg-white dark:bg-zinc-800 rounded-xl shadow-lg flex items-center justify-center border-4 border-white dark:border-zinc-900 overflow-hidden flex-shrink-0">
            <img src="{{ $avatarUrl }}" alt="Avatar" class="w-full h-full object-contain p-1">
        </div>
        <div class="pb-1">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white pb-4">{{ $user->name }}</h2>
            <div class="flex flex-col items-start gap-3 text-sm">
                <span class="px-2 py-0.5 rounded text-lg font-medium {{ $classConfig['bg'] }} {{ $classConfig['text'] }}">
                    {{ $classConfig['icon'] }} {{ $classConfig['name'] }}
                </span>
                <span class="text-zinc-500 text-lg font-normal dark:text-zinc-400 pt-1">{{ $user->email }}</span>                                
            </div>
        </div>
    </div>
</div>
