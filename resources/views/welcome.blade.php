<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habit XP - Gamify Your Life</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Merriweather:ital,wght@0,300;0,700;0,900;1,300&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3 {
            font-family: 'Merriweather', serif;
        }
    </style>
</head>
<body class="bg-white text-[#37352F] antialiased selection:bg-[#cce9ff]">

    <nav class="sticky top-0 bg-white/80 backdrop-blur-md z-50 border-b border-[#E9E9E8] h-14 flex items-center justify-between px-6 md:px-12">
        <div class="flex items-center gap-2">
            <img src="{{ asset('img/habit-xp-logo.png') }}" alt="Habit XP" class="w-6 h-6 rounded">
            <span class="font-semibold tracking-tight">Habit XP</span>
        </div>
        <div class="hidden md:flex items-center gap-6 text-sm font-medium">
            <a href="#features" class="hover:text-gray-600 transition-colors">Características</a>
            <a href="#pricing" class="hover:text-gray-600 transition-colors">Precios</a>
            <div class="h-4 w-px bg-gray-200"></div>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-600 transition-colors">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="hover:text-gray-600 transition-colors">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="bg-black text-white px-3 py-1.5 rounded hover:bg-gray-800 transition-colors">Jugar Gratis</a>
                @endif
            @endauth
        </div>
    </nav>

    <header class="max-w-5xl mx-auto px-6 pt-20 pb-16 text-center">
        <div class="inline-flex items-center gap-2 bg-[#F1F1EF] border border-[#E9E9E8] px-3 py-1 rounded-full text-xs font-medium text-gray-600 mb-6">
            <span class="text-xs">✨</span>
            <span>v2.0: Ahora con Clases RPG y Tienda de Recompensas</span>
        </div>
        
        <h1 class="text-5xl md:text-7xl font-bold text-gray-900 mb-6 leading-tight">
            Tu vida no es una lista.<br>Es una <span class="bg-[#FDECC8] px-2 italic">aventura</span>.
        </h1>
        
        <p class="text-xl text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed font-light">
            <strong>Habit XP</strong> transforma tus tareas aburridas en misiones épicas. Elige tu clase, gana experiencia, sube de nivel y desbloquea recompensas reales.
        </p>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="bg-black text-white px-8 py-3 rounded text-lg font-medium hover:bg-gray-800 transition-all flex items-center justify-center gap-2 shadow-lg shadow-gray-200">
                    <span>⚔️</span> Ir al Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="bg-black text-white px-8 py-3 rounded text-lg font-medium hover:bg-gray-800 transition-all flex items-center justify-center gap-2 shadow-lg shadow-gray-200">
                    <span>⚔️</span> Empezar Aventura
                </a>
                <a href="{{ route('login') }}" class="bg-white text-gray-900 border border-gray-200 px-8 py-3 rounded text-lg font-medium hover:bg-gray-50 transition-all">
                    Iniciar Sesión
                </a>
            @endauth
        </div>
    </header>

    <section class="max-w-4xl mx-auto px-6 mb-24">
        <div class="border border-[#E9E9E8] rounded-xl shadow-[rgba(15,15,15,0.1)_0px_0px_0px_1px,rgba(15,15,15,0.1)_0px_2px_4px] overflow-hidden bg-white">
            <div class="bg-[#F7F7F5] px-4 py-2 border-b border-[#E9E9E8] flex gap-2">
                <div class="w-3 h-3 rounded-full bg-[#FF5F57]"></div>
                <div class="w-3 h-3 rounded-full bg-[#FEBC2E]"></div>
                <div class="w-3 h-3 rounded-full bg-[#28C840]"></div>
            </div>
            
            <div class="p-8 md:p-12">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-16 h-16 rounded-full bg-[#E3E2E0] flex items-center justify-center text-3xl">🧙‍♂️</div>
                    <div>
                        <h3 class="text-2xl font-bold">Arquimago Dev</h3>
                        <div class="flex items-center gap-3 text-sm mt-1">
                            <span class="bg-[#E8DEEE] text-[#9065B0] px-1.5 rounded">Nivel 5</span>
                            <div class="w-32 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="bg-[#9065B0] w-[70%] h-full"></div>
                            </div>
                            <span class="text-gray-400 font-mono">700/1000 XP</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Misiones Diarias</div>
                    
                    <div class="flex items-center justify-between group p-2 hover:bg-[#F1F1EF] rounded cursor-pointer transition-colors border border-transparent hover:border-[#E9E9E8]">
                        <div class="flex items-center gap-3">
                            <div class="w-5 h-5 border-2 border-gray-300 rounded-sm"></div>
                            <span class="font-medium text-gray-700">Sesión de Deep Work (2h)</span>
                        </div>
                        <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-xs bg-[#FFE2DD] text-[#D44C47] px-1.5 py-0.5 rounded font-mono">+150 XP</span>
                            <span class="text-xs bg-[#FDECC8] text-[#CB912F] px-1.5 py-0.5 rounded font-mono">50 Oro</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between group p-2 hover:bg-[#F1F1EF] rounded cursor-pointer transition-colors border border-transparent hover:border-[#E9E9E8]">
                        <div class="flex items-center gap-3">
                            <div class="w-5 h-5 bg-[#2383E2] border-2 border-[#2383E2] rounded-sm flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="font-medium text-gray-400 line-through">Entrenamiento Físico</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400">Completado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="max-w-5xl mx-auto px-6 py-20">
        <div class="mb-12">
            <h2 class="text-3xl font-bold mb-4">Más que un To-Do List</h2>
            <p class="text-gray-500 text-lg">Diseñado con la psicología de los RPGs para mantenerte enganchado a tus metas.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="p-6 border border-[#E9E9E8] rounded-lg hover:shadow-[rgba(15,15,15,0.1)_0px_0px_0px_1px,rgba(15,15,15,0.1)_0px_2px_4px] transition-shadow">
                <div class="text-3xl mb-4">🛡️</div>
                <h3 class="font-bold text-lg mb-2">Sistema de Clases</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    ¿Eres un Guerrero del código o un Mago del diseño? Elige tu rol y obtén bonificaciones específicas para tus hábitos.
                </p>
            </div>
            
            <div class="p-6 border border-[#E9E9E8] rounded-lg hover:shadow-[rgba(15,15,15,0.1)_0px_0px_0px_1px,rgba(15,15,15,0.1)_0px_2px_4px] transition-shadow">
                <div class="text-3xl mb-4">💰</div>
                <h3 class="font-bold text-lg mb-2">Economía de Oro</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Cada tarea completada te da Oro. Úsalo en la tienda para comprar "Pociones de descanso" o recompensas reales que tú configures.
                </p>
            </div>

            <div class="p-6 border border-[#E9E9E8] rounded-lg hover:shadow-[rgba(15,15,15,0.1)_0px_0px_0px_1px,rgba(15,15,15,0.1)_0px_2px_4px] transition-shadow">
                <div class="text-3xl mb-4">🍅</div>
                <h3 class="font-bold text-lg mb-2">Focus Timer</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Integramos Pomodoro. Gana doble XP cuando completas sesiones de enfoque profundo sin distracciones.
                </p>
            </div>
            
            <div class="p-6 border border-[#E9E9E8] rounded-lg hover:shadow-[rgba(15,15,15,0.1)_0px_0px_0px_1px,rgba(15,15,15,0.1)_0px_2px_4px] transition-shadow">
                <div class="text-3xl mb-4">📊</div>
                <h3 class="font-bold text-lg mb-2">Stats & Heatmap</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Visualiza tu consistencia con un mapa de calor estilo GitHub. No rompas la racha y mira cómo suben tus atributos.
                </p>
            </div>

            <div class="p-6 border border-[#E9E9E8] rounded-lg hover:shadow-[rgba(15,15,15,0.1)_0px_0px_0px_1px,rgba(15,15,15,0.1)_0px_2px_4px] transition-shadow">
                <div class="text-3xl mb-4">🎒</div>
                <h3 class="font-bold text-lg mb-2">Inventario</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Colecciona medallas y objetos raros al alcanzar hitos importantes. Tu perfil muestra tus logros al mundo.
                </p>
            </div>

             <div class="p-6 border border-[#E9E9E8] rounded-lg hover:shadow-[rgba(15,15,15,0.1)_0px_0px_0px_1px,rgba(15,15,15,0.1)_0px_2px_4px] transition-shadow bg-[#F7F7F5]">
                <div class="text-3xl mb-4">📱</div>
                <h3 class="font-bold text-lg mb-2">Estética Notion</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Todo el poder de un juego, con la limpieza visual que amas. Sin distracciones, solo productividad pura.
                </p>
            </div>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-6 mb-24">
        <div class="p-6 bg-[#F7F7F5] rounded-lg border border-transparent hover:border-[#E9E9E8] transition-colors flex gap-4">
            <div class="text-4xl">💡</div>
            <div>
                <h3 class="font-bold text-gray-900 mb-2">El problema de los Habit Trackers</h3>
                <p class="text-gray-600 leading-relaxed mb-4">
                    La mayoría de las apps te castigan cuando fallas. <strong>Habit XP</strong> está diseñado para recompensarte cuando triunfas. Tu cerebro libera dopamina con cada "Level Up", creando un ciclo positivo de productividad.
                </p>
            </div>
        </div>
    </section>

    <section id="pricing" class="max-w-4xl mx-auto px-6 py-20 border-t border-[#E9E9E8]">
        <h2 class="text-3xl font-bold mb-8 text-center">Elige tu Dificultad</h2>
        
        <div class="border border-[#E9E9E8] rounded-lg overflow-hidden shadow-sm">
            <div class="grid grid-cols-4 bg-[#F7F7F5] border-b border-[#E9E9E8] text-xs font-semibold text-gray-500 p-3">
                <div class="col-span-2">PLAN</div>
                <div class="text-center">PRECIO</div>
                <div class="text-center">LOOT</div>
            </div>

            <div class="grid grid-cols-4 border-b border-[#E9E9E8] p-4 items-center hover:bg-[#F7F7F5] transition-colors">
                <div class="col-span-2 flex items-center gap-3">
                    <span class="text-xl">🧢</span>
                    <div>
                        <div class="font-bold text-gray-900">Novato (Free)</div>
                        <div class="text-xs text-gray-500">Para empezar tu viaje</div>
                    </div>
                </div>
                <div class="text-center">
                    <span class="bg-[#E3E2E0] px-2 py-1 rounded text-sm font-mono">Gratis</span>
                </div>
                <div class="text-center text-sm text-gray-500">
                    3 Hábitos, Clases Básicas
                </div>
            </div>

            <div class="grid grid-cols-4 p-4 items-center hover:bg-[#F7F7F5] transition-colors bg-blue-50/30">
                <div class="col-span-2 flex items-center gap-3">
                    <span class="text-xl">👑</span>
                    <div>
                        <div class="font-bold text-gray-900">Héroe (Pro)</div>
                        <div class="text-xs text-gray-500">Para jugadores serios</div>
                    </div>
                </div>
                <div class="text-center">
                    <span class="bg-[#D3E5EF] text-[#1880B6] px-2 py-1 rounded text-sm font-mono">$5 / mes</span>
                </div>
                <div class="text-center text-sm text-gray-500">
                    Ilimitado, Tienda, Temas
                </div>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="bg-black text-white px-8 py-3 rounded hover:bg-gray-800 transition-colors font-medium inline-block">
                    Ir al Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="bg-black text-white px-8 py-3 rounded hover:bg-gray-800 transition-colors font-medium inline-block">
                    Unirse a la Beta
                </a>
            @endauth
        </div>
    </section>

    <footer class="py-12 text-center text-sm text-gray-400 border-t border-[#E9E9E8] bg-[#F7F7F5]">
        <p>© 2025 Habit XP. Construido con TALL Stack.</p>
    </footer>

</body>
</html>
