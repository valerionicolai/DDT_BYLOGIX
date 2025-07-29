<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="space-y-6">
        <!-- Page Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Gestione Documenti</h1>
              <p class="mt-2 text-gray-600">
                Gestisci i documenti di entrata del sistema DTT by Logix
              </p>
            </div>
            <div class="flex space-x-3">
              <CanAccess permission="create.document">
                <router-link
                  :to="{ name: 'DocumentCreate' }"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                  <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Nuovo Documento
                </router-link>
              </CanAccess>
            </div>
          </div>
        </div>

        <!-- Filters Card -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filtri di Ricerca</h3>
          </div>
          <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cerca</label>
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Numero documento, fornitore..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select
                  v-model="typeFilter"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="">Tutti i tipi</option>
                  <option value="entry">Entrata</option>
                  <option value="delivery">Consegna</option>
                  <option value="invoice">Fattura</option>
                  <option value="receipt">Ricevuta</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
                <select
                  v-model="statusFilter"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="">Tutti gli stati</option>
                  <option value="draft">Bozza</option>
                  <option value="confirmed">Confermato</option>
                  <option value="received">Ricevuto</option>
                  <option value="cancelled">Annullato</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fornitore</label>
                <input
                  v-model="supplierFilter"
                  type="text"
                  placeholder="Nome fornitore..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ordina per</label>
                <select
                  v-model="sortBy"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="document_date">Data Documento</option>
                  <option value="document_number">Numero Documento</option>
                  <option value="supplier_name">Fornitore</option>
                  <option value="total_amount">Importo</option>
                  <option value="created_at">Data Creazione</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Documents Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div v-if="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>

          <div v-else-if="documents.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun documento trovato</h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ hasFilters ? 'Prova a modificare i criteri di ricerca.' : 'Inizia creando il primo documento.' }}
            </p>
            <CanAccess permission="create.document">
              <div class="mt-6">
                <router-link
                  :to="{ name: 'DocumentCreate' }"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
                >
                  <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Crea Documento
                </router-link>
              </div>
            </CanAccess>
          </div>
          
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Documento
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Fornitore
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Progetto
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
                    Data
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Azioni
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="document in documents"
                  :key="document.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div>
                        <div class="text-sm font-medium text-gray-900">
                          {{ document.document_number }}
                        </div>
                        <div v-if="document.notes" class="text-sm text-gray-500">
                          {{ truncateText(document.notes, 50) }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ document.supplier_name }}</div>
                    <div v-if="document.supplier_vat" class="text-sm text-gray-500">
                      P.IVA: {{ document.supplier_vat }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ document.project?.name || 'N/A' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="getTypeClass(document.document_type)"
                    >
                      {{ getTypeLabel(document.document_type) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="getStatusClass(document.status)"
                    >
                      {{ getStatusLabel(document.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    €{{ formatCurrency(document.total_amount) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(document.document_date) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <button
                        @click="viewDocument(document)"
                        class="text-primary-600 hover:text-primary-900"
                        title="Visualizza dettagli"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                      </button>
                      <CanAccess permission="update.document">
                        <button
                          @click="editDocument(document)"
                          class="text-indigo-600 hover:text-indigo-900"
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
                          @click="confirmDelete(document)"
                          class="text-red-600 hover:text-red-900"
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

          <!-- Pagination -->
          <div v-if="pagination && pagination.last_page > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                @click="previousPage"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Precedente
              </button>
              <button
                @click="nextPage"
                :disabled="pagination.current_page === pagination.last_page"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Successivo
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Mostra
                  <span class="font-medium">{{ pagination.from }}</span>
                  a
                  <span class="font-medium">{{ pagination.to }}</span>
                  di
                  <span class="font-medium">{{ pagination.total }}</span>
                  risultati
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <button
                    @click="previousPage"
                    :disabled="pagination.current_page === 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span class="sr-only">Precedente</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  
                  <button
                    v-for="page in visiblePages"
                    :key="page"
                    @click="goToPage(page)"
                    :class="[
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                      page === pagination.current_page
                        ? 'z-10 bg-primary-50 border-primary-500 text-primary-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                    ]"
                  >
                    {{ page }}
                  </button>
                  
                  <button
                    @click="nextPage"
                    :disabled="pagination.current_page === pagination.last_page"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span class="sr-only">Successivo</span>
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

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Elimina Documento"
      :message="`Sei sicuro di voler eliminare il documento '${documentToDelete?.document_number}'? Questa azione non può essere annullata.`"
      confirm-text="Elimina"
      confirm-class="bg-red-600 hover:bg-red-700"
      @confirm="deleteDocument"
      @cancel="cancelDelete"
    />
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '@/components/MainLayout.vue'
import CanAccess from '@/components/CanAccess.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'
import useApi from '@/composables/useApi'
import useNotifications from '@/composables/useNotifications'
import { formatDate } from '@/utils'

const router = useRouter()
const { get, delete: deleteRequest } = useApi()
const { showSuccess, showError } = useNotifications()

// State
const loading = ref(false)
const documents = ref([])
const pagination = ref(null)

// Filters
const searchQuery = ref('')
const typeFilter = ref('')
const statusFilter = ref('')
const supplierFilter = ref('')
const sortBy = ref('document_date')
const sortOrder = ref('desc')
const currentPage = ref(1)

// Modal state
const showDeleteModal = ref(false)
const documentToDelete = ref(null)

// Computed
const hasFilters = computed(() => {
  return searchQuery.value || typeFilter.value || statusFilter.value || supplierFilter.value
})

const visiblePages = computed(() => {
  if (!pagination.value) return []
  
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages = []
  
  // Always show first page
  if (current > 3) pages.push(1)
  
  // Show pages around current
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  
  // Always show last page
  if (current < last - 2) pages.push(last)
  
  return [...new Set(pages)].sort((a, b) => a - b)
})

// Methods
const loadDocuments = async () => {
  loading.value = true
  try {
    const params = {
      page: currentPage.value,
      per_page: 15,
      sort_by: sortBy.value,
      sort_order: sortOrder.value
    }

    if (searchQuery.value) {
      params.search = searchQuery.value
    }
    if (typeFilter.value) {
      params.type = typeFilter.value
    }
    if (statusFilter.value) {
      params.status = statusFilter.value
    }
    if (supplierFilter.value) {
      params.supplier = supplierFilter.value
    }

    const response = await get('/api/entry-documents', { params })
    
    if (response.success) {
      documents.value = response.data.data
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        per_page: response.data.per_page,
        total: response.data.total,
        from: response.data.from,
        to: response.data.to
      }
    } else {
      showError('Errore nel caricamento dei documenti')
    }
  } catch (error) {
    console.error('Error loading documents:', error)
    showError('Errore nel caricamento dei documenti')
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

const confirmDelete = (document) => {
  documentToDelete.value = document
  showDeleteModal.value = true
}

const cancelDelete = () => {
  documentToDelete.value = null
  showDeleteModal.value = false
}

const deleteDocument = async () => {
  if (!documentToDelete.value) return

  try {
    const response = await deleteRequest(`/api/entry-documents/${documentToDelete.value.id}`)
    
    if (response.success) {
      showSuccess('Documento eliminato con successo')
      await loadDocuments()
    } else {
      showError('Errore nell\'eliminazione del documento')
    }
  } catch (error) {
    console.error('Error deleting document:', error)
    showError('Errore nell\'eliminazione del documento')
  } finally {
    cancelDelete()
  }
}

const canEdit = (document) => {
  return document.status === 'draft' || document.status === 'confirmed'
}

const canDelete = (document) => {
  return document.status === 'draft'
}

// Pagination methods
const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (pagination.value && currentPage.value < pagination.value.last_page) {
    currentPage.value++
  }
}

const goToPage = (page) => {
  currentPage.value = page
}

// Utility methods
const truncateText = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

const formatCurrency = (amount) => {
  if (!amount) return '0,00'
  return new Intl.NumberFormat('it-IT', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
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

const getTypeClass = (type) => {
  const classes = {
    entry: 'bg-blue-100 text-blue-800',
    delivery: 'bg-green-100 text-green-800',
    invoice: 'bg-yellow-100 text-yellow-800',
    receipt: 'bg-purple-100 text-purple-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    draft: 'Bozza',
    confirmed: 'Confermato',
    received: 'Ricevuto',
    cancelled: 'Annullato'
  }
  return labels[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    confirmed: 'bg-blue-100 text-blue-800',
    received: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

// Watchers
watch([searchQuery, typeFilter, statusFilter, supplierFilter, sortBy, sortOrder], () => {
  currentPage.value = 1
  loadDocuments()
}, { debounce: 300 })

watch(currentPage, () => {
  loadDocuments()
})

// Lifecycle
onMounted(() => {
  loadDocuments()
})
</script>