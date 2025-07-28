import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './components/App.vue';
import router from './router';

// Configurazione Axios per CSRF
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Crea l'applicazione Vue
const app = createApp(App);

// Crea e configura Pinia
const pinia = createPinia();

// Usa Pinia e il router
app.use(pinia);
app.use(router);

// Monta l'applicazione
app.mount('#app');