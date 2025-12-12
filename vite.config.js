import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    // Split vendor code into separate chunk
                    'vendor': ['lucide'],
                }
            }
        },
        // Increase chunk size warning limit
        chunkSizeWarningLimit: 600,
        // Enable minification with esbuild (faster and included by default)
        minify: 'esbuild',
        target: 'es2015'
    }
});