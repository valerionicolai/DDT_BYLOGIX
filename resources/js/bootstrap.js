import axios from 'axios';
window.axios = axios;

// Configurazione per supportare i cookie di sessione
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Configurazione base URL per le API - usa l'origine corrente
window.axios.defaults.baseURL = window.location.origin;

// Per Laravel Sanctum, il CSRF token viene gestito automaticamente tramite cookie
// dopo aver chiamato /sanctum/csrf-cookie, non tramite meta tag
