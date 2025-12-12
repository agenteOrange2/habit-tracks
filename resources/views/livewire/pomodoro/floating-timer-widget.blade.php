<div 
    wire:ignore
    x-data="{
        dragging: false,
        dragOffset: { x: 0, y: 0 },
        
        startDrag(e) {
            this.dragging = true;
            const rect = this.$refs.widget.getBoundingClientRect();
            this.dragOffset = {
                x: (e.clientX || e.touches[0].clientX) - rect.left,
                y: (e.clientY || e.touches[0].clientY) - rect.top
            };
            document.body.style.userSelect = 'none';
        },
        
        onDrag(e) {
            if (!this.dragging) return;
            
            const clientX = e.clientX || (e.touches && e.touches[0].clientX);
            const clientY = e.clientY || (e.touches && e.touches[0].clientY);
            
            if (!clientX || !clientY) return;
            
            let x = clientX - this.dragOffset.x;
            let y = clientY - this.dragOffset.y;
            
            // Constrain to viewport bounds
            const rect = this.$refs.widget.getBoundingClientRect();
            const maxX = window.innerWidth - rect.width;
            const maxY = window.innerHeight - rect.height;
            
            x = Math.max(0, Math.min(x, maxX));
            y = Math.max(0, Math.min(y, maxY));
            
            $store.pomodoro.updateWidgetPosition(x, y);
        },
        
        stopDrag() {
            if (this.dragging) {
                this.dragging = false;
                document.body.style.userSelect = '';
                document.body.style.cursor = '';
            }
        },
        
        init() {
            // Ensure widget stays in bounds on window resize
            window.addEventListener('resize', () => {
                const x = $store.pomodoro.widgetPosition.x;
                const y = $store.pomodoro.widgetPosition.y;
                const maxX = window.innerWidth - 320;
                const maxY = window.innerHeight - 400;
                
                if (x > maxX || y > maxY) {
                    $store.pomodoro.updateWidgetPosition(
                        Math.min(x, maxX),
                        Math.min(y, maxY)
                    );
                }
            });
        }
    }"
    x-show="$store.pomodoro.widgetVisible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90"
    @mousemove.window="onDrag"
    @mouseup.window="stopDrag"
    @touchmove.window="onDrag"
    @touchend.window="stopDrag"
    class="fixed z-50"
    :style="`left: ${$store.pomodoro.widgetPosition.x}px; top: ${$store.pomodoro.widgetPosition.y}px;`"
    style="display: none;"
>
    <!-- Expanded State -->
    <div 
        x-show="!$store.pomodoro.widgetCollapsed"
        x-ref="widget"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700 transition-opacity duration-200 hover:opacity-100"
        :class="dragging ? 'opacity-100 cursor-grabbing' : 'opacity-70 cursor-grab'"
        style="width: 320px;"
        @mousedown="startDrag"
        @touchstart="startDrag"
    >
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <span class="text-2xl">🍅</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    <span x-text="$store.pomodoro.timerType === 'pomodoro' ? 'Pomodoro' : 
                                  $store.pomodoro.timerType === 'short_break' ? 'Descanso Corto' : 
                                  'Descanso Largo'"></span>
                </span>
            </div>
            <button 
                @click.stop="$store.pomodoro.stopTimer()"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                title="Cerrar"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Timer Display -->
        <div class="p-6 text-center">
            <div class="text-5xl font-bold text-gray-900 dark:text-white mb-4" x-text="$store.pomodoro.getFormattedTime()">
                25:00
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-4">
                <div 
                    class="h-2 rounded-full transition-all duration-1000"
                    :class="$store.pomodoro.timerType === 'pomodoro' ? 'bg-red-500' : 'bg-green-500'"
                    :style="`width: ${$store.pomodoro.getProgress()}%`"
                ></div>
            </div>

            <!-- Controls -->
            <div class="flex gap-2 justify-center">
                <button 
                    x-show="$store.pomodoro.timerState === 'running'"
                    @click.stop="$store.pomodoro.pauseTimer()"
                    class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors text-sm font-medium"
                >
                    ⏸ Pausar
                </button>
                
                <button 
                    x-show="$store.pomodoro.timerState === 'paused'"
                    @click.stop="$store.pomodoro.resumeTimer()"
                    class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors text-sm font-medium"
                >
                    ▶ Reanudar
                </button>
                
                <button 
                    @click.stop="$store.pomodoro.stopTimer()"
                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors text-sm font-medium"
                >
                    ⏹ Detener
                </button>
            </div>
        </div>

        <!-- Statistics -->
        <div class="px-4 pb-4 border-t border-gray-200 dark:border-gray-700 pt-4">
            <div class="grid grid-cols-2 gap-4 text-center text-sm">
                <div>
                    <div class="text-gray-500 dark:text-gray-400">Hoy</div>
                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                        🍅 <span x-text="$store.pomodoro.consecutivePomodoros || 0">0</span>
                    </div>
                </div>
                <div>
                    <div class="text-gray-500 dark:text-gray-400">Ciclo</div>
                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                        <span x-text="`${$store.pomodoro.cycleCount}/4`">0/4</span>
                    </div>
                </div>
            </div>
            
            <!-- Cycle Progress Indicator -->
            <div class="flex justify-center gap-1 mt-3">
                <template x-for="i in 4" :key="i">
                    <div 
                        class="w-3 h-3 rounded-full transition-colors"
                        :class="i <= $store.pomodoro.cycleCount ? 'bg-red-500' : 'bg-gray-300 dark:bg-gray-600'"
                    ></div>
                </template>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-4 pb-3 flex justify-center">
            <button 
                @click.stop="$store.pomodoro.toggleWidgetCollapsed()"
                class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors"
            >
                ▼ Minimizar
            </button>
        </div>
    </div>

    <!-- Minimized State -->
    <div 
        x-show="$store.pomodoro.widgetCollapsed"
        x-ref="widgetMin"
        class="bg-white dark:bg-gray-800 rounded-full shadow-2xl border-2 border-gray-200 dark:border-gray-700 transition-opacity duration-200 hover:opacity-100 cursor-grab"
        :class="dragging ? 'opacity-100 cursor-grabbing' : 'opacity-70'"
        style="width: 120px; height: 120px;"
        @mousedown="startDrag"
        @touchstart="startDrag"
        @click.stop="$store.pomodoro.toggleWidgetCollapsed()"
    >
        <div class="flex flex-col items-center justify-center h-full p-3">
            <span class="text-3xl mb-1">🍅</span>
            <div class="text-xl font-bold text-gray-900 dark:text-white" x-text="$store.pomodoro.getFormattedTime()">
                25:00
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                ▲
            </div>
        </div>
    </div>
</div>
