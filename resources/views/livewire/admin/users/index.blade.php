<div class="min-h-screen bg-[#FAFAFA] dark:bg-[#191919]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <h1 class="text-3xl font-bold text-[#37352F] dark:text-[#EFEFED]">
                    👥 Usuarios Registrados
                </h1>
                <a href="{{ route('admin.dashboard') }}" 
                   class="text-sm text-[#787774] dark:text-[#9B9A97] hover:text-[#37352F] dark:hover:text-[#EFEFED] transition-colors"
                   wire:navigate>
                    ← Volver al Dashboard
                </a>
            </div>
            <p class="text-[#787774] dark:text-[#9B9A97]">
                Gestiona y visualiza todos los usuarios de tu aplicación
            </p>
        </div>

        {{-- Search Bar --}}
        <div class="mb-6">
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por nombre o email..."
                    class="w-full px-4 py-3 pl-10 bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg text-[#37352F] dark:text-[#EFEFED] placeholder-[#9B9A97] focus:outline-none focus:ring-2 focus:ring-[#2383E2] focus:border-transparent"
                >
                <svg class="absolute left-3 top-3.5 w-5 h-5 text-[#9B9A97]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg p-4">
                <div class="text-[#787774] dark:text-[#9B9A97] text-sm mb-1">Total Usuarios</div>
                <div class="text-2xl font-bold text-[#37352F] dark:text-[#EFEFED]">{{ $users->total() }}</div>
            </div>
            <div class="bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg p-4">
                <div class="text-[#787774] dark:text-[#9B9A97] text-sm mb-1">Administradores</div>
                <div class="text-2xl font-bold text-[#37352F] dark:text-[#EFEFED]">{{ \App\Models\User::where('is_admin', true)->count() }}</div>
            </div>
            <div class="bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg p-4">
                <div class="text-[#787774] dark:text-[#9B9A97] text-sm mb-1">Usuarios Regulares</div>
                <div class="text-2xl font-bold text-[#37352F] dark:text-[#EFEFED]">{{ \App\Models\User::where('is_admin', false)->count() }}</div>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="bg-white dark:bg-[#252525] border border-[#E9E9E7] dark:border-[#3E3E3A] rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#F7F7F5] dark:bg-[#1F1F1F] border-b border-[#E9E9E7] dark:border-[#3E3E3A]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#787774] dark:text-[#9B9A97] uppercase tracking-wider cursor-pointer hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] transition-colors"
                                wire:click="sortBy('name')">
                                <div class="flex items-center gap-2">
                                    Usuario
                                    @if($sortField === 'name')
                                        <span class="text-[#2383E2]">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#787774] dark:text-[#9B9A97] uppercase tracking-wider cursor-pointer hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] transition-colors"
                                wire:click="sortBy('email')">
                                <div class="flex items-center gap-2">
                                    Email
                                    @if($sortField === 'email')
                                        <span class="text-[#2383E2]">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#787774] dark:text-[#9B9A97] uppercase tracking-wider">
                                Rol
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#787774] dark:text-[#9B9A97] uppercase tracking-wider cursor-pointer hover:bg-[#EFEFED] dark:hover:bg-[#2A2A2A] transition-colors"
                                wire:click="sortBy('created_at')">
                                <div class="flex items-center gap-2">
                                    Registro
                                    @if($sortField === 'created_at')
                                        <span class="text-[#2383E2]">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E9E9E7] dark:divide-[#3E3E3A]">
                        @forelse($users as $user)
                            <tr class="hover:bg-[#F7F7F5] dark:hover:bg-[#1F1F1F] transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $user->avatar_url }}" 
                                             alt="{{ $user->name }}"
                                             class="w-10 h-10 rounded-full">
                                        <div>
                                            <div class="font-medium text-[#37352F] dark:text-[#EFEFED]">
                                                {{ $user->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-[#787774] dark:text-[#9B9A97]">
                                        {{ $user->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->is_admin)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-[#FFE2DD] text-[#D44C47]">
                                            👑 Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-[#E3E2E0] text-[#787774]">
                                            Usuario
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#787774] dark:text-[#9B9A97]">
                                    {{ $user->created_at->format('d/m/Y') }}
                                    <div class="text-xs text-[#9B9A97]">
                                        {{ $user->created_at->diffForHumans() }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="text-[#9B9A97]">
                                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p class="text-lg font-medium mb-1">No se encontraron usuarios</p>
                                        <p class="text-sm">Intenta con otro término de búsqueda</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-[#E9E9E7] dark:border-[#3E3E3A]">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
