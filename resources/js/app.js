import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';

// Configurazione Axios per CSRF
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Crea l'applicazione Vue
const app = createApp(App);

// Usa il router
app.use(router);

// Monta l'applicazione
app.mount('#app');