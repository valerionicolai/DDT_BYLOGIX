import axios from 'axios';
window.axios = axios;

// Configurazione per supportare i cookie di sessione
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Configurazione base URL per le API
window.axios.defaults.baseURL = window.location.origin;

// Configurazione per CSRF token
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
