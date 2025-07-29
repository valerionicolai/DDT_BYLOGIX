<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestione Documenti</h1>
            <p class="mt-2 text-gray-600">Gestisci i documenti di entrata del sistema DTT by Logix</p>
          </div>
          
          <!-- Add Document Button -->
          <CanAccess permission="create.document">
            <router-link
              :to="{ name: 'DocumentCreate' }"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
              Nuovo Documento
            </router-link>
          </CanAccess>
        </div>
      </div>

      <!-- Search and Filters -->
      <div class="mb-6 bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cerca Documento</label>
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Titolo o descrizione..."
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
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
            <select
              v-model="typeFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="">Tutti i tipi</option>
              <option value="entry">Entrata</option>
              <option value="delivery">Consegna</option>
              <option value="invoice">Fattura</option>
              <option value="receipt">Ricevuta</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
            <select
              v-model="statusFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="">Tutti gli stati</option>
              <option value="draft">Bozza</option>
              <option value="pending">In Attesa</option>
              <option value="approved">Approvato</option>
              <option value="completed">Completato</option>
              <option value="cancelled">Annullato</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ordina per</label>
            <select
              v-model="sortBy"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="created_at">Data Creazione</option>
              <option value="title">Titolo</option>
              <option value="total_amount">Importo</option>
              <option value="updated_at">Ultimo Aggiornamento</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Documents Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">
            Elenco Documenti ({{ filteredDocuments.length }})
          </h2>
        </div>
        
        <!-- Loading State -->
        <div v-if="loading" class="p-8 text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
          <p class="mt-2 text-gray-600">Caricamento documenti...</p>
        </div>
        
        <!-- Empty State -->
        <div v-else-if="filteredDocuments.length === 0" class="p-8 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun documento trovato</h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ searchQuery ? 'Prova a modificare i criteri di ricerca.' : 'Inizia creando il primo documento.' }}
          </p>
          <CanAccess permission="create.document">
            <div class="mt-6">
              <router-link
                :to="{ name: 'DocumentCreate' }"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Crea Documento
              </router-link>
            </div>
          </CanAccess>
        </div>
        
        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Documento
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Cliente/Progetto
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Tipo
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Stato
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Importo
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
                v-for="document in paginatedDocuments"
                :key="document.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-6 py-4">
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ document.title }}</div>
                    <div class="text-sm text-gray-500">{{ document.description || 'Nessuna descrizione' }}</div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">{{ document.client?.name || 'N/A' }}</div>
                  <div class="text-sm text-gray-500">{{ document.project?.name || 'N/A' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getTypeClass(document.type)"
                  >
                    {{ getTypeLabel(document.type) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusClass(document.status)"
                  >
                    {{ getStatusLabel(document.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  € {{ formatCurrency(document.total_amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(document.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end space-x-2">
                    <button
                      @click="viewDocument(document)"
                      class="text-primary-600 hover:text-primary-900 transition-colors"
                      title="Visualizza"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                    
                    <CanAccess permission="edit.document">
                      <button
                        @click="editDocument(document)"
                        class="text-indigo-600 hover:text-indigo-900 transition-colors"
                        title="Modifica"
                        :disabled="!canEdit(document)"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                    </CanAccess>
                    
                    <CanAccess permission="delete.document">
                      <button
                        @click="deleteDocument(document)"
                        class="text-red-600 hover:text-red-900 transition-colors"
                        title="Elimina"
                        :disabled="!canDelete(document)"
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
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import CanAccess from '@/components/CanAccess.vue'
import useApi from '@/composables/useApi'
import useNotifications from '@/composables/useNotifications'
import { formatDate } from '@/utils'

const router = useRouter()
const { get, delete: deleteApi } = useApi()
const { showNotification } = useNotifications()

// State
const loading = ref(false)
const documents = ref([])
const searchQuery = ref('')
const typeFilter = ref('')
const statusFilter = ref('')
const sortBy = ref('created_at')

// Computed
const filteredDocuments = computed(() => {
  let filtered = documents.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(doc => 
      doc.title.toLowerCase().includes(query) ||
      (doc.description && doc.description.toLowerCase().includes(query))
    )
  }

  if (typeFilter.value) {
    filtered = filtered.filter(doc => doc.type === typeFilter.value)
  }

  if (statusFilter.value) {
    filtered = filtered.filter(doc => doc.status === statusFilter.value)
  }

  // Sort
  filtered.sort((a, b) => {
    const aVal = a[sortBy.value]
    const bVal = b[sortBy.value]
    
    if (sortBy.value === 'created_at' || sortBy.value === 'updated_at') {
      return new Date(bVal) - new Date(aVal)
    }
    
    return aVal > bVal ? 1 : -1
  })

  return filtered
})

const paginatedDocuments = computed(() => {
  // For now, return all filtered documents
  // In a real app, you'd implement pagination here
  return filteredDocuments.value
})

// Methods
const loadDocuments = async () => {
  loading.value = true
  try {
    const response = await get('/api/documents')
    documents.value = response.data || []
  } catch (error) {
    console.error('Error loading documents:', error)
    addNotification({
      type: 'error',
      title: 'Errore',
      message: 'Errore durante il caricamento dei documenti'
    })
  } finally {
    loading.value = false
  }
}

const viewDocument = (document) => {
  router.push({ name: 'DocumentDetail', params: { id: document.id } })
}

const editDocument = (document) => {
  router.push({ name: 'DocumentEdit', params: { id: document.id } })
}

const deleteDocument = async (document) => {
  if (!confirm(`Sei sicuro di voler eliminare il documento "${document.title}"?`)) {
    return
  }

  try {
    await deleteApi(`/api/documents/${document.id}`)
    documents.value = documents.value.filter(d => d.id !== document.id)
    
    addNotification({
      type: 'success',
      title: 'Documento eliminato',
      message: 'Il documento è stato eliminato con successo'
    })
  } catch (error) {
    addNotification({
      type: 'error',
      title: 'Errore',
      message: 'Errore durante l\'eliminazione del documento'
    })
  }
}

const canEdit = (document) => {
  return ['draft', 'pending'].includes(document.status)
}

const canDelete = (document) => {
  return ['draft'].includes(document.status)
}

const getTypeClass = (type) => {
  const classes = {
    entry: 'bg-blue-100 text-blue-800',
    delivery: 'bg-green-100 text-green-800',
    invoice: 'bg-yellow-100 text-yellow-800',
    receipt: 'bg-purple-100 text-purple-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const getTypeLabel = (type) => {
  const labels = {
    entry: 'Entrata',
    delivery: 'Consegna',
    invoice: 'Fattura',
    receipt: 'Ricevuta'
  }
  return labels[type] || type
}

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    draft: 'Bozza',
    pending: 'In Attesa',
    approved: 'Approvato',
    completed: 'Completato',
    cancelled: 'Annullato'
  }
  return labels[status] || status
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('it-IT', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount || 0)
}

// Lifecycle
onMounted(() => {
  loadDocuments()
})
</script>