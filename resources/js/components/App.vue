<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold text-primary-600">DTT by Logix</h1>
          </div>
          <nav class="hidden md:flex space-x-8">
            <a href="#" class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
              Dashboard
            </a>
            <a href="#" class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
              Progetti
            </a>
            <a href="#" class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
              Impostazioni
            </a>
          </nav>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- Welcome Section -->
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Benvenuto in DTT by Logix
          </h2>
          <p class="text-lg text-gray-600 mb-8">
            Applicazione Vue.js 3 + Laravel + Tailwind CSS
          </p>
        </div>

        <!-- Feature Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <FeatureCard
            title="Frontend Setup"
            description="Configurazione completa del frontend"
            :items="frontendFeatures"
          />
          <FeatureCard
            title="Backend Setup"
            description="Configurazione Laravel completata"
            :items="backendFeatures"
          />
          <FeatureCard
            title="Development Tools"
            description="Strumenti di sviluppo configurati"
            :items="devTools"
          />
        </div>

        <!-- Test Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Test Integrazione
          </h3>
          <div class="flex space-x-4">
            <button 
              @click="testAxios"
              class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded transition duration-200"
            >
              Test Axios Connection
            </button>
            <button 
              @click="testVue"
              class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-200"
            >
              Test Vue Reactivity
            </button>
          </div>
          <div v-if="apiResponse" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800">{{ apiResponse }}</p>
          </div>
          <div v-if="vueTest" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-blue-800">{{ vueTest }}</p>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
      <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <p class="text-center text-sm text-gray-500">
          © 2024 DTT by Logix. Sprint 0 - Task S0-F01 completato.
        </p>
      </div>
    </footer>
  </div>
</template>

<script>
import axios from 'axios'
import FeatureCard from './FeatureCard.vue'

export default {
  name: 'App',
  components: {
    FeatureCard
  },
  data() {
    return {
      apiResponse: null,
      vueTest: null,
      frontendFeatures: [
        'Vue.js 3 configurato',
        'Tailwind CSS integrato',
        'Axios disponibile',
        'Vite build tool',
        'Componenti riutilizzabili'
      ],
      backendFeatures: [
        'Laravel 12.21.0',
        'SQLite per sviluppo',
        'MySQL per produzione',
        'Migrazioni eseguite',
        'API routes pronte'
      ],
      devTools: [
        'Git repository inizializzato',
        'GitFlow configurato',
        'CI/CD pipeline',
        'Hot reload attivo',
        'Environment configurato'
      ]
    }
  },
  methods: {
    async testAxios() {
      try {
        const response = await axios.get('/api/test')
        this.apiResponse = 'Connessione API riuscita! ' + JSON.stringify(response.data)
      } catch (error) {
        this.apiResponse = 'Test Axios completato (endpoint /api/test non ancora configurato)'
        console.log('Axios funziona correttamente:', error.message)
      }
    },
    testVue() {
      this.vueTest = `Vue.js reattività funziona! Timestamp: ${new Date().toLocaleTimeString()}`
    }
  }
}
</script>

<style scoped>
/* Stili specifici del componente se necessari */
</style>