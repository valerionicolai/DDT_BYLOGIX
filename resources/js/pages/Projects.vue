<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Progetti</h1>
          <p class="mt-2 text-gray-600">Gestisci tutti i tuoi progetti</p>
        </div>
        <button
          @click="showCreateModal = true"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
          </svg>
          Nuovo Progetto
        </button>
      </div>

      <!-- Filters -->
      <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cerca progetti..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
          >
        </div>
        <select
          v-model="statusFilter"
          class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
        >
          <option value="">Tutti gli stati</option>
          <option value="Pianificato">Pianificato</option>
          <option value="In Corso">In Corso</option>
          <option value="Completato">Completato</option>
          <option value="In Ritardo">In Ritardo</option>
        </select>
      </div>

      <!-- Projects Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="project in filteredProjects"
          :key="project.id"
          class="bg-white rounded-lg shadow hover:shadow-md transition-shadow cursor-pointer"
          @click="selectProject(project)"
        >
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center">
                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                  <span class="text-primary-600 font-semibold">{{ project.name.charAt(0) }}</span>
                </div>
                <h3 class="ml-3 text-lg font-semibold text-gray-900">{{ project.name }}</h3>
              </div>
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="getStatusClass(project.status)"
              >
                {{ project.status }}
              </span>
            </div>
            
            <p class="text-gray-600 mb-4">{{ project.description }}</p>
            
            <div class="space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Progresso</span>
                <span class="font-medium">{{ project.progress }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="bg-primary-600 h-2 rounded-full transition-all duration-300"
                  :style="{ width: project.progress + '%' }"
                ></div>
              </div>
            </div>
            
            <div class="mt-4 flex justify-between items-center text-sm text-gray-500">
              <span>Creato: {{ project.createdAt }}</span>
              <span>Scadenza: {{ project.dueDate }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredProjects.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">
          {{ searchQuery || statusFilter ? 'Nessun progetto trovato' : 'Nessun progetto' }}
        </h3>
        <p class="mt-1 text-sm text-gray-500">
          {{ searchQuery || statusFilter ? 'Prova a modificare i filtri di ricerca.' : 'Inizia creando il tuo primo progetto.' }}
        </p>
        <div class="mt-6" v-if="!searchQuery && !statusFilter">
          <button
            @click="showCreateModal = true"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            Crea Progetto
          </button>
        </div>
      </div>

      <!-- Create Project Modal (placeholder) -->
      <div v-if="showCreateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900">Nuovo Progetto</h3>
            <div class="mt-2 px-7 py-3">
              <p class="text-sm text-gray-500">
                Funzionalità in sviluppo. Sarà implementata nei prossimi sprint.
              </p>
            </div>
            <div class="items-center px-4 py-3">
              <button
                @click="showCreateModal = false"
                class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300"
              >
                Chiudi
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'

export default {
  name: 'Projects',
  setup() {
    const projects = ref([])
    const searchQuery = ref('')
    const statusFilter = ref('')
    const showCreateModal = ref(false)

    const filteredProjects = computed(() => {
      let filtered = projects.value

      if (searchQuery.value) {
        filtered = filtered.filter(project =>
          project.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          project.description.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
      }

      if (statusFilter.value) {
        filtered = filtered.filter(project => project.status === statusFilter.value)
      }

      return filtered
    })

    const getStatusClass = (status) => {
      const classes = {
        'Completato': 'bg-green-100 text-green-800',
        'In Corso': 'bg-yellow-100 text-yellow-800',
        'In Ritardo': 'bg-red-100 text-red-800',
        'Pianificato': 'bg-blue-100 text-blue-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const selectProject = (project) => {
      console.log('Progetto selezionato:', project)
      // Qui implementeremo la navigazione al dettaglio del progetto
    }

    const loadProjects = async () => {
      // Simulazione dati per ora
      projects.value = [
        {
          id: 1,
          name: 'Progetto Alpha',
          description: 'Sviluppo di una nuova applicazione web per la gestione clienti',
          status: 'In Corso',
          progress: 65,
          createdAt: '15 Gen 2024',
          dueDate: '30 Mar 2024'
        },
        {
          id: 2,
          name: 'Progetto Beta',
          description: 'Migrazione del database legacy verso una nuova architettura',
          status: 'Completato',
          progress: 100,
          createdAt: '01 Gen 2024',
          dueDate: '28 Feb 2024'
        },
        {
          id: 3,
          name: 'Progetto Gamma',
          description: 'Ottimizzazione delle performance del sistema esistente',
          status: 'In Ritardo',
          progress: 30,
          createdAt: '10 Dic 2023',
          dueDate: '15 Feb 2024'
        },
        {
          id: 4,
          name: 'Progetto Delta',
          description: 'Implementazione di nuove funzionalità di reporting',
          status: 'Pianificato',
          progress: 0,
          createdAt: '20 Feb 2024',
          dueDate: '15 Mag 2024'
        },
        {
          id: 5,
          name: 'Progetto Epsilon',
          description: 'Refactoring del codice frontend per migliorare la manutenibilità',
          status: 'In Corso',
          progress: 45,
          createdAt: '05 Feb 2024',
          dueDate: '20 Apr 2024'
        }
      ]
    }

    onMounted(() => {
      loadProjects()
    })

    return {
      projects,
      searchQuery,
      statusFilter,
      showCreateModal,
      filteredProjects,
      getStatusClass,
      selectProject
    }
  }
}
</script>