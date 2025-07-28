<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-gray-600">Benvenuto nel sistema DTT by Logix</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-primary-100 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Progetti Totali</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.totalProjects }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Progetti Completati</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.completedProjects }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">In Corso</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.activeProjects }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">In Ritardo</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.delayedProjects }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Projects -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">Progetti Recenti</h2>
        </div>
        <div class="p-6">
          <div class="space-y-4">
            <div
              v-for="project in recentProjects"
              :key="project.id"
              class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                    <span class="text-primary-600 font-semibold">{{ project.name.charAt(0) }}</span>
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="text-sm font-medium text-gray-900">{{ project.name }}</h3>
                  <p class="text-sm text-gray-500">{{ project.description }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getStatusClass(project.status)"
                >
                  {{ project.status }}
                </span>
                <span class="text-sm text-gray-500">{{ project.updatedAt }}</span>
              </div>
            </div>
          </div>
          
          <div v-if="recentProjects.length === 0" class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun progetto</h3>
            <p class="mt-1 text-sm text-gray-500">Inizia creando il tuo primo progetto.</p>
            <div class="mt-6">
              <router-link
                to="/projects"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                Crea Progetto
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'

export default {
  name: 'Dashboard',
  setup() {
    const stats = ref({
      totalProjects: 0,
      completedProjects: 0,
      activeProjects: 0,
      delayedProjects: 0
    })

    const recentProjects = ref([])

    const getStatusClass = (status) => {
      const classes = {
        'Completato': 'bg-green-100 text-green-800',
        'In Corso': 'bg-yellow-100 text-yellow-800',
        'In Ritardo': 'bg-red-100 text-red-800',
        'Pianificato': 'bg-blue-100 text-blue-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const loadDashboardData = async () => {
      // Simulazione dati per ora
      stats.value = {
        totalProjects: 12,
        completedProjects: 8,
        activeProjects: 3,
        delayedProjects: 1
      }

      recentProjects.value = [
        {
          id: 1,
          name: 'Progetto Alpha',
          description: 'Sviluppo applicazione web',
          status: 'In Corso',
          updatedAt: '2 ore fa'
        },
        {
          id: 2,
          name: 'Progetto Beta',
          description: 'Migrazione database',
          status: 'Completato',
          updatedAt: '1 giorno fa'
        },
        {
          id: 3,
          name: 'Progetto Gamma',
          description: 'Ottimizzazione performance',
          status: 'In Ritardo',
          updatedAt: '3 giorni fa'
        }
      ]
    }

    onMounted(() => {
      loadDashboardData()
    })

    return {
      stats,
      recentProjects,
      getStatusClass
    }
  }
}
</script>