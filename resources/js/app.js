import './bootstrap';
import Alpine from 'alpinejs';

// Import Vue.js components for existing functionality
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './components/App.vue';
import router from './router';

// Configure Alpine.js for Livewire integration
window.Alpine = Alpine;

// Start Alpine.js
Alpine.start();

// Configurazione Axios per CSRF
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

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