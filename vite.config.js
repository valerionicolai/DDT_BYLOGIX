import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js'
            ],
            refresh: [
                // Livewire components
                'app/Livewire/**/*.php',
                'app/Http/Livewire/**/*.php',
                // Blade templates
                'resources/views/**/*.blade.php',
                'resources/views/livewire/**/*.blade.php',
                // Filament resources
                'app/Filament/**/*.php',
                // JavaScript files
                'resources/js/**/*.js',
            ],
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
    optimizeDeps: {
        include: [
            'alpinejs',
            '@alpinejs/focus',
            '@alpinejs/persist',
            '@alpinejs/collapse',
        ],
    },
    build: {
        // Optimize for Filament/Livewire
        rollupOptions: {
            output: {
                manualChunks: {
                    // Separate Alpine.js into its own chunk for better caching
                    alpine: ['alpinejs', '@alpinejs/focus', '@alpinejs/persist', '@alpinejs/collapse'],
                    // Separate vendor libraries
                    vendor: ['axios'],
                },
            },
        },
        // Optimize chunk size for better performance
        chunkSizeWarningLimit: 1000,
        // Enable source maps for debugging in development
        sourcemap: process.env.NODE_ENV === 'development',
        // Optimize CSS
        cssCodeSplit: true,
        // Enable minification
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: process.env.NODE_ENV === 'production',
                drop_debugger: process.env.NODE_ENV === 'production',
            },
        },
    },
    css: {
        devSourcemap: true,
    },
});
