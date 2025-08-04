import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
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
                'resources/views/filament/**/*.blade.php',
                // Configuration files
                'config/livewire.php',
                'config/filament.php',
            ],
            // Enable HMR for Livewire components
            detectTls: false,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            '~': '/resources',
        },
    },
    optimizeDeps: {
        include: [
            'alpinejs',
            '@alpinejs/focus',
            '@alpinejs/persist',
            '@alpinejs/collapse',
        ],
        exclude: [
            'vue',
        ],
    },
    build: {
        // Optimize for Filament/Livewire
        rollupOptions: {
            output: {
                manualChunks: {
                    // Separate Alpine.js into its own chunk for better caching
                    alpine: ['alpinejs'],
                    // Separate vendor libraries
                    vendor: ['axios'],
                },
            },
        },
        // Enable source maps for development
        sourcemap: process.env.NODE_ENV === 'development',
        // Optimize chunk size
        chunkSizeWarningLimit: 1000,
    },
    server: {
        // Enhanced HMR configuration for Livewire
        hmr: {
            host: 'localhost',
        },
        // Watch additional files for changes
        watch: {
            usePolling: false,
            ignored: [
                '**/node_modules/**',
                '**/vendor/**',
                '**/storage/**',
                '**/bootstrap/cache/**',
            ],
        },
    },
});
