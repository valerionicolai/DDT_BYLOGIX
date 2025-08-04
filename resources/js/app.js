import './bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist';
import collapse from '@alpinejs/collapse';

// Import Vue.js components for existing functionality
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './components/App.vue';
import router from './router';

// Register Alpine.js plugins
Alpine.plugin(focus);
Alpine.plugin(persist);
Alpine.plugin(collapse);

// Configure Alpine.js for Livewire integration
window.Alpine = Alpine;

// Enhanced Alpine.js configuration for Filament/Livewire
document.addEventListener('alpine:init', () => {
    // Global Alpine.js data and methods for Filament/Livewire integration
    Alpine.data('filamentApp', () => ({
        // Global state management
        theme: Alpine.$persist('light').as('theme'),
        sidebarOpen: Alpine.$persist(false).as('sidebarOpen'),
        
        // Theme toggle functionality
        toggleTheme() {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
        },
        
        // Sidebar toggle functionality
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        },
        
        // Initialize theme
        init() {
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
        }
    }));
    
    // Global utilities for Livewire components
    Alpine.magic('clipboard', () => {
        return (text) => {
            navigator.clipboard.writeText(text).then(() => {
                // You can add a toast notification here
                console.log('Copied to clipboard:', text);
            });
        };
    });
    
    // Global directive for auto-focus
    Alpine.directive('auto-focus', (el, { expression }, { evaluate }) => {
        if (evaluate(expression)) {
            setTimeout(() => el.focus(), 100);
        }
    });
});

// Start Alpine.js
Alpine.start();

// Enhanced Axios configuration for CSRF and Livewire
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add Livewire CSRF token support
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Initialize Vue.js app only if the #app element exists (for existing Vue components)
const appElement = document.getElementById('app');
if (appElement) {
    // Crea l'applicazione Vue
    const app = createApp(App);

    // Crea e configura Pinia
    const pinia = createPinia();

    // Usa Pinia e il router
    app.use(pinia);
    app.use(router);

    // Monta l'applicazione
    app.mount('#app');
}

// Hot Module Replacement (HMR) support for Livewire components
if (import.meta.hot) {
    import.meta.hot.accept();
    
    // Reload Livewire components on file changes
    import.meta.hot.on('livewire:component-updated', (data) => {
        console.log('Livewire component updated:', data);
        // Optionally trigger a Livewire refresh
        if (window.Livewire) {
            window.Livewire.restart();
        }
    });
}