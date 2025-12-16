<div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-6 h-full flex flex-col">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-gray-900 dark:text-zinc-50">Mapa de Actividad</h3>

        <div class="flex items-center gap-1 text-[10px] text-gray-400 dark:text-zinc-500">
            <span>Menos</span>
            <div class="w-2.5 h-2.5 bg-gray-100 dark:bg-zinc-700 rounded-sm"></div>
            <div class="w-2.5 h-2.5 bg-[#DBEDDB] dark:bg-green-900 rounded-sm"></div>
            <div class="w-2.5 h-2.5 bg-[#86EFAC] dark:bg-green-700 rounded-sm"></div>
            <div class="w-2.5 h-2.5 bg-[#22C55E] dark:bg-green-600 rounded-sm"></div>
            <span>Más</span>
        </div>
    </div>

    <div class="flex-1 flex items-end overflow-x-auto pb-2">
        <div class="flex gap-1">
            <div class="flex flex-col justify-between text-[9px] text-gray-400 dark:text-zinc-500 mr-2 h-[88px] py-1 font-mono">
                <span>Mon</span>
                <span>Wed</span>
                <span>Fri</span>
            </div>

            <div class="flex gap-1">
                <script>
                    for(let w=0; w<24; w++) {
                        document.write('<div class="flex flex-col gap-1">');
                        for(let d=0; d<7; d++) {
                            // Aleatoriedad para el demo
                            const chance = Math.random();
                            let colorLight = 'bg-gray-100'; // Empty
                            let colorDark = 'dark:bg-zinc-700';
                            if(chance > 0.7) {
                                colorLight = 'bg-[#DBEDDB]'; // Notion Light Green
                                colorDark = 'dark:bg-green-900';
                            }
                            if(chance > 0.85) {
                                colorLight = 'bg-[#86EFAC]'; // Mid Green
                                colorDark = 'dark:bg-green-700';
                            }
                            if(chance > 0.95) {
                                colorLight = 'bg-[#15803d]'; // Dark Green
                                colorDark = 'dark:bg-green-600';
                            }

                            document.write(`<div class="w-3 h-3 rounded-sm ${colorLight} ${colorDark} hover:ring-1 ring-gray-300 dark:ring-zinc-600 transition-all cursor-pointer" title="Actividad"></div>`);
                        }
                        document.write('</div>');
                    }
                </script>
            </div>
        </div>
    </div>
</div>
