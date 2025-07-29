<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Progetti</h1>
            <p class="mt-1 text-sm text-gray-500">Gestisci i tuoi progetti</p>
          </div>
          <CanAccess permission="create.project">
            <button
              @click="openCreateModal"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Nuovo Progetto
            </button>
          </CanAccess>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search -->
          <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cerca</label>
            <input
              id="search"
              v-model="filters.search"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              placeholder="Nome o descrizione..."
            >
          </div>

          <!-- Status Filter -->
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
            <select
              id="status"
              v-model="filters.status"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="">Tutti gli stati</option>
              <option value="draft">Bozza</option>
              <option value="active">Attivo</option>
              <option value="completed">Completato</option>
              <option value="cancelled">Annullato</option>
              <option value="on_hold">In Pausa</option>
            </select>
          </div>

          <!-- Priority Filter -->
          <div>
            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priorità</label>
            <select
              id="priority"
              v-model="filters.priority"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="">Tutte le priorità</option>
              <option value="low">Bassa</option>
              <option value="medium">Media</option>
              <option value="high">Alta</option>
              <option value="urgent">Urgente</option>
            </select>
          </div>

          <!-- Client Filter -->
          <div>
            <label for="client" class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
            <select
              id="client"
              v-model="filters.client_id"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="">Tutti i clienti</option>
              <option v-for="client in clients" :key="client.id" :value="client.id">
                {{ client.name }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Projects Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
        <p class="mt-2 text-sm text-gray-500">Caricamento progetti...</p>
      </div>

      <div v-else-if="filteredProjects.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="project in filteredProjects"
          :key="project.id"
          class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200"
        >
          <!-- Project Card Header -->
          <div class="p-6">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ project.name }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ project.description }}</p>
              </div>
              <div class="flex space-x-1 ml-4">
                <CanAccess permission="view.project">
                  <button
                    @click="viewProject(project)"
                    class="p-1 text-gray-400 hover:text-gray-600"
                    title="Visualizza"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </CanAccess>
                <CanAccess permission="edit.project">
                  <button
                    @click="editProject(project)"
                    class="p-1 text-gray-400 hover:text-blue-600"
                    title="Modifica"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                </CanAccess>
                <CanAccess permission="delete.project">
                  <button
                    @click="confirmDeleteProject(project)"
                    class="p-1 text-gray-400 hover:text-red-600"
                    title="Elimina"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </CanAccess>
              </div>
            </div>

            <!-- Status and Priority -->
            <div class="flex items-center space-x-4 mb-4">
              <span
                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                :class="getStatusClass(project.status)"
              >
                {{ getStatusLabel(project.status) }}
              </span>
              <span
                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                :class="getPriorityClass(project.priority)"
              >
                {{ getPriorityLabel(project.priority) }}
              </span>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4">
              <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>Progresso</span>
                <span>{{ project.progress_percentage || 0 }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="bg-primary-600 h-2 rounded-full transition-all duration-300"
                  :style="{ width: (project.progress_percentage || 0) + '%' }"
                ></div>
              </div>
            </div>

            <!-- Dates -->
            <div class="text-sm text-gray-500 space-y-1">
              <div class="flex justify-between">
                <span>Creato:</span>
                <span>{{ formatDate(project.created_at) }}</span>
              </div>
              <div v-if="project.deadline" class="flex justify-between">
                <span>Deadline:</span>
                <span :class="{ 'text-red-600': isOverdue(project.deadline) }">
                  {{ formatDate(project.deadline) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun progetto trovato</h3>
        <p class="mt-1 text-sm text-gray-500">Inizia creando un nuovo progetto.</p>
        <div class="mt-6">
          <CanAccess permission="create.project">
            <button
              @click="openCreateModal"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Nuovo Progetto
            </button>
          </CanAccess>
        </div>
      </div>
    </div>

    <!-- Project Modal -->
    <ProjectModal
      :show="showProjectModal"
      :project="selectedProject"
      :mode="modalMode"
      @close="closeProjectModal"
      @save="handleProjectSave"
    />

    <!-- Confirmation Modal -->
    <ConfirmationModal
      :show="showConfirmModal"
      :title="'Elimina Progetto'"
      :message="`Sei sicuro di voler eliminare il progetto '${projectToDelete?.name}'? Questa azione non può essere annullata.`"
      @confirm="deleteProject"
      @cancel="closeConfirmModal"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import CanAccess from '@/components/CanAccess.vue'
import ProjectModal from '@/components/ProjectModal.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'
import { useNotification } from '@/composables/useNotification'

export default {
  name: 'Projects',
  components: {
    CanAccess,
    ProjectModal,
    ConfirmationModal
  },
  setup() {
    const { showNotification } = useNotification()
    
    // State
    const loading = ref(false)
    const projects = ref([])
    const clients = ref([])
    const filters = ref({
      search: '',
      status: '',
      priority: '',
      client_id: ''
    })
    
    // Modal state
    const showProjectModal = ref(false)
    const showConfirmModal = ref(false)
    const selectedProject = ref(null)
    const projectToDelete = ref(null)
    const modalMode = ref('create') // 'create', 'edit', 'view'

    // Computed
    const filteredProjects = computed(() => {
      let filtered = projects.value

      // Search filter
      if (filters.value.search) {
        const searchTerm = filters.value.search.toLowerCase()
        filtered = filtered.filter(project =>
          project.name.toLowerCase().includes(searchTerm) ||
          (project.description && project.description.toLowerCase().includes(searchTerm))
        )
      }

      // Status filter
      if (filters.value.status) {
        filtered = filtered.filter(project => project.status === filters.value.status)
      }

      // Priority filter
      if (filters.value.priority) {
        filtered = filtered.filter(project => project.priority === filters.value.priority)
      }

      // Client filter
      if (filters.value.client_id) {
        filtered = filtered.filter(project => project.client_id == filters.value.client_id)
      }

      return filtered
    })

    // Methods
    const loadProjects = async () => {
      loading.value = true
      try {
        // Simulate API call - replace with actual API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        projects.value = [
          {
            id: 1,
            name: 'Sviluppo E-commerce',
            description: 'Piattaforma e-commerce completa con gestione prodotti e pagamenti',
            client_id: 1,
            user_id: 1,
            status: 'active',
            priority: 'high',
            start_date: '2024-01-15',
            end_date: '2024-06-30',
            deadline: '2024-07-15',
            budget: 25000.00,
            estimated_cost: 22000.00,
            actual_cost: 18500.00,
            progress_percentage: 75,
            notes: 'Progetto in corso, buoni progressi',
            created_at: '2024-01-10T10:00:00Z',
            updated_at: '2024-01-20T15:30:00Z'
          },
          {
            id: 2,
            name: 'App Mobile Fitness',
            description: 'Applicazione mobile per il tracking degli allenamenti',
            client_id: 2,
            user_id: 2,
            status: 'draft',
            priority: 'medium',
            start_date: '2024-02-01',
            end_date: '2024-08-31',
            deadline: '2024-09-15',
            budget: 35000.00,
            estimated_cost: 32000.00,
            actual_cost: null,
            progress_percentage: 25,
            notes: 'In fase di pianificazione',
            created_at: '2024-01-25T09:15:00Z',
            updated_at: '2024-01-25T09:15:00Z'
          },
          {
            id: 3,
            name: 'Sistema CRM',
            description: 'Customer Relationship Management per gestione clienti',
            client_id: 3,
            user_id: 1,
            status: 'completed',
            priority: 'urgent',
            start_date: '2023-10-01',
            end_date: '2024-01-31',
            deadline: '2024-02-15',
            budget: 45000.00,
            estimated_cost: 40000.00,
            actual_cost: 42500.00,
            progress_percentage: 100,
            notes: 'Progetto completato con successo',
            created_at: '2023-09-20T14:20:00Z',
            updated_at: '2024-02-01T16:45:00Z'
          },
          {
            id: 4,
            name: 'Sito Web Aziendale',
            description: 'Nuovo sito web responsive con CMS',
            client_id: 4,
            user_id: 3,
            status: 'on_hold',
            priority: 'low',
            start_date: '2024-03-01',
            end_date: '2024-05-31',
            deadline: '2024-06-30',
            budget: 15000.00,
            estimated_cost: 14000.00,
            actual_cost: 5000.00,
            progress_percentage: 40,
            notes: 'In attesa di feedback dal cliente',
            created_at: '2024-02-15T11:30:00Z',
            updated_at: '2024-03-10T13:20:00Z'
          }
        ]
      } catch (error) {
        console.error('Errore nel caricamento dei progetti:', error)
        showNotification('Errore nel caricamento dei progetti', 'error')
      } finally {
        loading.value = false
      }
    }

    const loadClients = async () => {
      try {
        // Simulate API call - replace with actual API call
        clients.value = [
          { id: 1, name: 'Mario Rossi' },
          { id: 2, name: 'Giulia Bianchi' },
          { id: 3, name: 'Luca Verdi' },
          { id: 4, name: 'Anna Neri' }
        ]
      } catch (error) {
        console.error('Errore nel caricamento dei clienti:', error)
      }
    }

    const openCreateModal = () => {
      selectedProject.value = null
      modalMode.value = 'create'
      showProjectModal.value = true
    }

    const viewProject = (project) => {
      selectedProject.value = project
      modalMode.value = 'view'
      showProjectModal.value = true
    }

    const editProject = (project) => {
      selectedProject.value = project
      modalMode.value = 'edit'
      showProjectModal.value = true
    }

    const confirmDeleteProject = (project) => {
      projectToDelete.value = project
      showConfirmModal.value = true
    }

    const deleteProject = async () => {
      if (!projectToDelete.value) return

      try {
        // Simulate API call - replace with actual API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        // Remove project from local array
        const index = projects.value.findIndex(p => p.id === projectToDelete.value.id)
        if (index > -1) {
          projects.value.splice(index, 1)
        }
        
        showNotification('Progetto eliminato con successo', 'success')
      } catch (error) {
        console.error('Errore nell\'eliminazione del progetto:', error)
        showNotification('Errore nell\'eliminazione del progetto', 'error')
      } finally {
        closeConfirmModal()
      }
    }

    const closeProjectModal = () => {
      showProjectModal.value = false
      selectedProject.value = null
      modalMode.value = 'create'
    }

    const closeConfirmModal = () => {
      showConfirmModal.value = false
      projectToDelete.value = null
    }

    const handleProjectSave = async (projectData) => {
      try {
        if (projectData._mode === 'edit') {
          // Switch to edit mode
          selectedProject.value = projectData
          modalMode.value = 'edit'
          showProjectModal.value = true
          return
        }

        // Simulate API call - replace with actual API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        if (modalMode.value === 'create') {
          // Add new project
          const newProject = {
            ...projectData,
            id: Date.now(), // Temporary ID
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
          }
          projects.value.unshift(newProject)
        } else if (modalMode.value === 'edit') {
          // Update existing project
          const index = projects.value.findIndex(p => p.id === selectedProject.value.id)
          if (index > -1) {
            projects.value[index] = {
              ...projects.value[index],
              ...projectData,
              updated_at: new Date().toISOString()
            }
          }
        }
        
        closeProjectModal()
      } catch (error) {
        console.error('Errore nel salvataggio del progetto:', error)
        showNotification('Errore nel salvataggio del progetto', 'error')
      }
    }

    const formatDate = (dateString) => {
      if (!dateString) return '-'
      return new Date(dateString).toLocaleDateString('it-IT', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }

    const isOverdue = (deadline) => {
      if (!deadline) return false
      return new Date(deadline) < new Date()
    }

    const getStatusClass = (status) => {
      const classes = {
        'draft': 'bg-gray-100 text-gray-800',
        'active': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
        'on_hold': 'bg-yellow-100 text-yellow-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const getStatusLabel = (status) => {
      const labels = {
        'draft': 'Bozza',
        'active': 'Attivo',
        'completed': 'Completato',
        'cancelled': 'Annullato',
        'on_hold': 'In Pausa'
      }
      return labels[status] || status
    }

    const getPriorityClass = (priority) => {
      const classes = {
        'low': 'bg-green-100 text-green-800',
        'medium': 'bg-yellow-100 text-yellow-800',
        'high': 'bg-orange-100 text-orange-800',
        'urgent': 'bg-red-100 text-red-800'
      }
      return classes[priority] || 'bg-gray-100 text-gray-800'
    }

    const getPriorityLabel = (priority) => {
      const labels = {
        'low': 'Bassa',
        'medium': 'Media',
        'high': 'Alta',
        'urgent': 'Urgente'
      }
      return labels[priority] || priority
    }

    // Watchers
    watch(filters, () => {
      // Debounce search if needed
    }, { deep: true })

    // Load data on mount
    onMounted(() => {
      loadProjects()
      loadClients()
    })

    return {
      loading,
      projects,
      clients,
      filters,
      filteredProjects,
      showProjectModal,
      showConfirmModal,
      selectedProject,
      projectToDelete,
      modalMode,
      openCreateModal,
      viewProject,
      editProject,
      confirmDeleteProject,
      deleteProject,
      closeProjectModal,
      closeConfirmModal,
      handleProjectSave,
      formatDate,
      isOverdue,
      getStatusClass,
      getStatusLabel,
      getPriorityClass,
      getPriorityLabel
    }
  }
}
</script>