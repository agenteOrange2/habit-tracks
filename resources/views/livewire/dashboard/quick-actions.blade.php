<div class="bg-white p-4 sm:p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 dark:border-neutral-700 dark:bg-gray-800">
    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Acciones Rápidas</h3>
    
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        @foreach($actions as $action)
            @php
                $colorClasses = match($action['color']) {
                    'blue' => 'bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 hover:border-blue-200 dark:hover:border-blue-800 text-blue-700 dark:text-blue-300',
                    'red' => 'bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 hover:border-red-200 dark:hover:border-red-800 text-red-700 dark:text-red-300',
                    'purple' => 'bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 hover:border-purple-200 dark:hover:border-purple-800 text-purple-700 dark:text-purple-300',
                    'green' => 'bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 hover:border-green-200 dark:hover:border-green-800 text-green-700 dark:text-green-300',
                    default => 'bg-gray-50 dark:bg-gray-900/20 hover:bg-gray-100 dark:hover:bg-gray-900/30 hover:border-gray-200 dark:hover:border-gray-800 text-gray-700 dark:text-gray-300',
                };
            @endphp
            <a 
                href="{{ route($action['route']) }}" 
                class="flex flex-col items-center justify-center p-4 rounded-lg border-2 border-transparent {{ $colorClasses }} transition-all duration-200 group"
            >
                <div class="text-3xl sm:text-4xl mb-2 group-hover:scale-110 transition-transform duration-200">
                    {{ $action['icon'] }}
                </div>
                <span class="text-xs sm:text-sm font-medium text-center">
                    {{ $action['title'] }}
                </span>
            </a>
        @endforeach
    </div>
</div>
