<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestione Clienti</h1>
            <p class="mt-2 text-gray-600">Gestisci i clienti del sistema DTT by Logix</p>
          </div>
          
          <!-- Add Client Button -->
          <CanAccess permission="create.client">
            <button
              @click="openCreateModal"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
              Nuovo Cliente
            </button>
          </CanAccess>
        </div>
      </div>

      <!-- Search and Filters -->
      <div class="mb-6 bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cerca Cliente</label>
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Nome, email o telefono..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              >
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
            <select
              v-model="statusFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="">Tutti gli stati</option>
              <option value="active">Attivo</option>
              <option value="inactive">Inattivo</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ordina per</label>
            <select
              v-model="sortBy"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="name">Nome</option>
              <option value="email">Email</option>
              <option value="created_at">Data Creazione</option>
              <option value="updated_at">Ultimo Aggiornamento</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Clients Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">
            Elenco Clienti ({{ filteredClients.length }})
          </h2>
        </div>
        
        <!-- Loading State -->
        <div v-if="loading" class="p-8 text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
          <p class="mt-2 text-gray-600">Caricamento clienti...</p>
        </div>
        
        <!-- Empty State -->
        <div v-else-if="filteredClients.length === 0" class="p-8 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun cliente trovato</h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ searchQuery ? 'Prova a modificare i criteri di ricerca.' : 'Inizia aggiungendo il primo cliente.' }}
          </p>
          <CanAccess permission="create.client">
            <div class="mt-6">
              <button
                @click="openCreateModal"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Aggiungi Cliente
              </button>
            </div>
          </CanAccess>
        </div>
        
        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Cliente
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Contatti
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Indirizzo
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Stato
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Progetti
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Data Creazione
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Azioni
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="client in paginatedClients"
                :key="client.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                        <span class="text-primary-600 font-medium text-sm">
                          {{ getClientInitials(client.name) }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ client.name }}</div>
                      <div class="text-sm text-gray-500">{{ client.company || 'Privato' }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ client.email }}</div>
                  <div class="text-sm text-gray-500">{{ client.phone || 'N/A' }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">
                    {{ formatAddress(client) }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusClass(client.status)"
                  >
                    {{ getStatusLabel(client.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ client.projects_count || 0 }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(client.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end space-x-2">
                    <button
                      @click="viewClient(client)"
                      class="text-primary-600 hover:text-primary-900 transition-colors"
                      title="Visualizza"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                    
                    <CanAccess permission="edit.client">
                      <button
                        @click="editClient(client)"
                        class="text-indigo-600 hover:text-indigo-900 transition-colors"
                        title="Modifica"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                    </CanAccess>
                    
                    <CanAccess permission="delete.client">
                      <button
                        @click="deleteClient(client)"
                        class="text-red-600 hover:text-red-900 transition-colors"
                        title="Elimina"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </CanAccess>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div v-if="totalPages > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                @click="previousPage"
                :disabled="currentPage === 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Precedente
              </button>
              <button
                @click="nextPage"
                :disabled="currentPage === totalPages"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Successivo
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Mostrando
                  <span class="font-medium">{{ (currentPage - 1) * itemsPerPage + 1 }}</span>
                  a
                  <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, filteredClients.length) }}</span>
                  di
                  <span class="font-medium">{{ filteredClients.length }}</span>
                  risultati
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <button
                    @click="previousPage"
                    :disabled="currentPage === 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  
                  <button
                    v-for="page in visiblePages"
                    :key="page"
                    @click="goToPage(page)"
                    :class="[
                      page === currentPage
                        ? 'z-10 bg-primary-50 border-primary-500 text-primary-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                    ]"
                  >
                    {{ page }}
                  </button>
                  
                  <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Client Modal -->
    <ClientModal
      :show="showModal"
      :client="selectedClient"
      :mode="modalMode"
      @close="closeModal"
      @save="handleSave"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      :show="showDeleteModal"
      title="Elimina Cliente"
      :message="`Sei sicuro di voler eliminare il cliente '${clientToDelete?.name}'? Questa azione non può essere annullata.`"
      confirm-text="Elimina"
      confirm-class="bg-red-600 hover:bg-red-700"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import CanAccess from '@/components/CanAccess.vue'
import ClientModal from '@/components/ClientModal.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'
import { useNotification } from '@/composables/useNotification'

export default {
  name: 'Clients',
  components: {
    CanAccess,
    ClientModal,
    ConfirmationModal
  },
  setup() {
    const router = useRouter()
    const { showNotification } = useNotification()
    
    // State
    const loading = ref(false)
    const clients = ref([])
    const searchQuery = ref('')
    const statusFilter = ref('')
    const sortBy = ref('name')
    const currentPage = ref(1)
    const itemsPerPage = ref(10)
    
    // Modal state
    const showModal = ref(false)
    const modalMode = ref('create') // 'create', 'edit', 'view'
    const selectedClient = ref(null)
    
    // Delete modal state
    const showDeleteModal = ref(false)
    const clientToDelete = ref(null)

    // Computed
    const filteredClients = computed(() => {
      let filtered = [...clients.value]
      
      // Search filter
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(client =>
          client.name.toLowerCase().includes(query) ||
          client.email.toLowerCase().includes(query) ||
          (client.phone && client.phone.includes(query)) ||
          (client.company && client.company.toLowerCase().includes(query))
        )
      }
      
      // Status filter
      if (statusFilter.value) {
        filtered = filtered.filter(client => client.status === statusFilter.value)
      }
      
      // Sort
      filtered.sort((a, b) => {
        const aValue = a[sortBy.value] || ''
        const bValue = b[sortBy.value] || ''
        return aValue.localeCompare(bValue)
      })
      
      return filtered
    })
    
    const totalPages = computed(() => Math.ceil(filteredClients.value.length / itemsPerPage.value))
    
    const paginatedClients = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value
      const end = start + itemsPerPage.value
      return filteredClients.value.slice(start, end)
    })
    
    const visiblePages = computed(() => {
      const pages = []
      const total = totalPages.value
      const current = currentPage.value
      
      if (total <= 7) {
        for (let i = 1; i <= total; i++) {
          pages.push(i)
        }
      } else {
        if (current <= 4) {
          for (let i = 1; i <= 5; i++) {
            pages.push(i)
          }
          pages.push('...')
          pages.push(total)
        } else if (current >= total - 3) {
          pages.push(1)
          pages.push('...')
          for (let i = total - 4; i <= total; i++) {
            pages.push(i)
          }
        } else {
          pages.push(1)
          pages.push('...')
          for (let i = current - 1; i <= current + 1; i++) {
            pages.push(i)
          }
          pages.push('...')
          pages.push(total)
        }
      }
      
      return pages
    })

    // Methods
    const loadClients = async () => {
      loading.value = true
      try {
        // Simulazione API call - sostituire con chiamata reale
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        // Dati di esempio
        clients.value = [
          {
            id: 1,
            name: 'Mario Rossi',
            email: 'mario.rossi@email.com',
            phone: '+39 123 456 7890',
            company: 'Rossi SRL',
            address: 'Via Roma 123',
            city: 'Milano',
            postal_code: '20100',
            country: 'Italia',
            status: 'active',
            projects_count: 3,
            created_at: '2024-01-15T10:30:00Z',
            updated_at: '2024-01-20T14:45:00Z'
          },
          {
            id: 2,
            name: 'Laura Bianchi',
            email: 'laura.bianchi@email.com',
            phone: '+39 987 654 3210',
            company: null,
            address: 'Corso Venezia 45',
            city: 'Roma',
            postal_code: '00100',
            country: 'Italia',
            status: 'active',
            projects_count: 1,
            created_at: '2024-01-10T09:15:00Z',
            updated_at: '2024-01-18T16:20:00Z'
          },
          {
            id: 3,
            name: 'Giuseppe Verdi',
            email: 'giuseppe.verdi@email.com',
            phone: '+39 555 123 4567',
            company: 'Verdi & Associati',
            address: 'Piazza Duomo 1',
            city: 'Firenze',
            postal_code: '50100',
            country: 'Italia',
            status: 'inactive',
            projects_count: 0,
            created_at: '2024-01-05T11:00:00Z',
            updated_at: '2024-01-12T13:30:00Z'
          }
        ]
      } catch (error) {
        console.error('Errore nel caricamento dei clienti:', error)
        showNotification('Errore nel caricamento dei clienti', 'error')
      } finally {
        loading.value = false
      }
    }

    const getClientInitials = (name) => {
      return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2)
    }

    const formatAddress = (client) => {
      const parts = [client.address, client.city, client.postal_code].filter(Boolean)
      return parts.join(', ') || 'N/A'
    }

    const getStatusClass = (status) => {
      return status === 'active'
        ? 'bg-green-100 text-green-800'
        : 'bg-red-100 text-red-800'
    }

    const getStatusLabel = (status) => {
      return status === 'active' ? 'Attivo' : 'Inattivo'
    }

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('it-IT', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }

    // Modal methods
    const openCreateModal = () => {
      selectedClient.value = null
      modalMode.value = 'create'
      showModal.value = true
    }

    const viewClient = (client) => {
      selectedClient.value = client
      modalMode.value = 'view'
      showModal.value = true
    }

    const editClient = (client) => {
      selectedClient.value = client
      modalMode.value = 'edit'
      showModal.value = true
    }

    const closeModal = () => {
      showModal.value = false
      selectedClient.value = null
    }

    const handleSave = async (clientData) => {
      try {
        if (modalMode.value === 'create') {
          // Simulazione creazione
          const newClient = {
            ...clientData,
            id: Date.now(),
            projects_count: 0,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
          }
          clients.value.push(newClient)
          showNotification('Cliente creato con successo', 'success')
        } else if (modalMode.value === 'edit') {
          // Simulazione modifica
          const index = clients.value.findIndex(c => c.id === selectedClient.value.id)
          if (index !== -1) {
            clients.value[index] = {
              ...clients.value[index],
              ...clientData,
              updated_at: new Date().toISOString()
            }
            showNotification('Cliente aggiornato con successo', 'success')
          }
        }
        closeModal()
      } catch (error) {
        console.error('Errore nel salvataggio del cliente:', error)
        showNotification('Errore nel salvataggio del cliente', 'error')
      }
    }

    // Delete methods
    const deleteClient = (client) => {
      clientToDelete.value = client
      showDeleteModal.value = true
    }

    const confirmDelete = async () => {
      try {
        // Simulazione eliminazione
        const index = clients.value.findIndex(c => c.id === clientToDelete.value.id)
        if (index !== -1) {
          clients.value.splice(index, 1)
          showNotification('Cliente eliminato con successo', 'success')
        }
        cancelDelete()
      } catch (error) {
        console.error('Errore nell\'eliminazione del cliente:', error)
        showNotification('Errore nell\'eliminazione del cliente', 'error')
      }
    }

    const cancelDelete = () => {
      showDeleteModal.value = false
      clientToDelete.value = null
    }

    // Pagination methods
    const goToPage = (page) => {
      if (page !== '...' && page >= 1 && page <= totalPages.value) {
        currentPage.value = page
      }
    }

    const previousPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--
      }
    }

    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++
      }
    }

    // Watchers
    watch([searchQuery, statusFilter], () => {
      currentPage.value = 1
    })

    // Lifecycle
    onMounted(() => {
      loadClients()
    })

    return {
      // State
      loading,
      clients,
      searchQuery,
      statusFilter,
      sortBy,
      currentPage,
      itemsPerPage,
      
      // Modal state
      showModal,
      modalMode,
      selectedClient,
      showDeleteModal,
      clientToDelete,
      
      // Computed
      filteredClients,
      totalPages,
      paginatedClients,
      visiblePages,
      
      // Methods
      loadClients,
      getClientInitials,
      formatAddress,
      getStatusClass,
      getStatusLabel,
      formatDate,
      openCreateModal,
      viewClient,
      editClient,
      closeModal,
      handleSave,
      deleteClient,
      confirmDelete,
      cancelDelete,
      goToPage,
      previousPage,
      nextPage
    }
  }
}
</script>