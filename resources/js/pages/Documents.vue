<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Documenti</h1>
            <p class="mt-1 text-sm text-gray-500">Gestisci i tuoi documenti</p>
          </div>
          <div class="flex items-center space-x-3">
            <button
              @click="exportCsv"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Esporta CSV
            </button>
            <CanAccess permission="create.documents">
              <button
                @click="createDocument"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nuovo Documento
              </button>
            </CanAccess>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ricerca</label>
            <div class="relative">
              <input
                v-model="filters.search"
                type="text"
                placeholder="Titolo, descrizione o numero..."
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
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Documento</label>
            <select
              v-model="filters.document_type"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="">Tutti i tipi</option>
              <option value="Fattura">Fattura</option>
              <option value="Preventivo">Preventivo</option>
              <option value="Contratto">Contratto</option>
              <option value="Ricevuta">Ricevuta</option>
              <option value="Altro">Altro</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
            <select
              v-model="filters.status"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="">Tutti gli stati</option>
              <option value="draft">Bozza</option>
              <option value="active">Attivo</option>
              <option value="archived">Archiviato</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ordinamento</label>
            <select
              v-model="filters.sort_by"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="created_at">Data Creazione</option>
              <option value="title">Titolo</option>
              <option value="document_date">Data Documento</option>
              <option value="amount">Importo</option>
            </select>
          </div>
        </div>
        
        <div class="mt-4 flex items-center justify-between">
          <button
            @click="resetFilters"
            class="text-sm text-gray-500 hover:text-gray-700"
          >
            Resetta filtri
          </button>
          <div class="flex items-center space-x-2">
            <label class="text-sm text-gray-700">Ordine:</label>
            <button
              @click="toggleSortOrder"
              class="inline-flex items-center text-sm text-primary-600 hover:text-primary-500"
            >
              {{ filters.sort_order === 'asc' ? 'Crescente' : 'Decrescente' }}
              <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="filters.sort_order === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Documents List -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="documents.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun documento trovato</h3>
        <p class="mt-1 text-sm text-gray-500">
          {{ hasActiveFilters ? 'Prova a modificare i criteri di ricerca.' : 'Inizia aggiungendo il primo documento.' }}
        </p>
        <CanAccess permission="create.documents">
          <div class="mt-6">
            <button
              @click="createDocument"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Nuovo Documento
            </button>
          </div>
        </CanAccess>
      </div>

      <!-- Documents Grid -->
      <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="document in documents"
          :key="document.id"
          class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200"
        >
          <div class="p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="h-10 w-10 rounded-lg bg-primary-100 flex items-center justify-center">
                    <svg class="h-6 w-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getDocumentTypeClass(document.document_type)">
                      {{ document.document_type }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getStatusClass(document.status)">
                      {{ getStatusLabel(document.status) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-4">
              <router-link
                :to="`/documents/${document.id}`"
                class="text-lg font-medium text-gray-900 hover:text-primary-600"
              >
                {{ document.title }}
              </router-link>
              <p v-if="document.description" class="mt-1 text-sm text-gray-500 line-clamp-2">
                {{ document.description }}
              </p>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-4 text-sm text-gray-500">
              <div>
                <span class="font-medium">Data:</span>
                {{ formatDate(document.document_date) }}
              </div>
              <div v-if="document.amount">
                <span class="font-medium">Importo:</span>
                {{ formatCurrency(document.amount) }}
              </div>
              <div v-if="document.reference_number">
                <span class="font-medium">Rif:</span>
                <span class="font-mono">{{ document.reference_number }}</span>
              </div>
              <div v-if="document.supplier_name">
                <span class="font-medium">Fornitore:</span>
                {{ document.supplier_name }}
              </div>
            </div>

            <div v-if="document.project || document.client" class="mt-4 text-sm text-gray-500">
              <div v-if="document.project" class="flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <router-link
                  :to="`/projects/${document.project.id}`"
                  class="text-primary-600 hover:text-primary-500"
                >
                  {{ document.project.name }}
                </router-link>
              </div>
              <div v-if="document.client" class="flex items-center mt-1">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <router-link
                  :to="`/clients/${document.client.id}`"
                  class="text-primary-600 hover:text-primary-500"
                >
                  {{ document.client.name }}
                </router-link>
              </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
              <div class="text-xs text-gray-500">
                {{ formatDateTime(document.created_at) }}
              </div>
              <div class="flex items-center space-x-2">
                <router-link
                  :to="`/documents/${document.id}`"
                  class="text-primary-600 hover:text-primary-500 text-sm font-medium"
                >
                  Dettagli
                </router-link>
                <CanAccess permission="edit.documents">
                  <router-link
                    :to="`/documents/${document.id}/edit`"
                    class="text-gray-600 hover:text-gray-500 text-sm"
                  >
                    Modifica
                  </router-link>
                </CanAccess>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > pagination.per_page" class="mt-8 flex items-center justify-between">
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
              Mostrando
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
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import useApi from '@/composables/useApi'
import useNotifications from '@/composables/useNotifications'
import { formatDate } from '@/utils/formatDate'
import debounce from '@/utils/debounce'
import CanAccess from '@/components/CanAccess.vue'

export default {
  name: 'Documents',
  components: {
    CanAccess
  },
  setup() {
    const router = useRouter()
    const { request: apiRequest } = useApi()
    const { addNotification } = useNotifications()
    
    const documents = ref([])
    const loading = ref(true)
    const pagination = ref({
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0,
      from: 0,
      to: 0
    })

    const filters = ref({
      search: '',
      document_type: '',
      status: '',
      sort_by: 'created_at',
      sort_order: 'desc'
    })

    const hasActiveFilters = computed(() => {
      return filters.value.search || filters.value.document_type || filters.value.status
    })

    const visiblePages = computed(() => {
      const current = pagination.value.current_page
      const last = pagination.value.last_page
      const delta = 2
      const range = []
      const rangeWithDots = []

      for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
        range.push(i)
      }

      if (current - delta > 2) {
        rangeWithDots.push(1, '...')
      } else {
        rangeWithDots.push(1)
      }

      rangeWithDots.push(...range)

      if (current + delta < last - 1) {
        rangeWithDots.push('...', last)
      } else if (last > 1) {
        rangeWithDots.push(last)
      }

      return rangeWithDots.filter(page => page !== '...' || rangeWithDots.indexOf(page) === rangeWithDots.lastIndexOf(page))
    })

    const loadDocuments = async (page = 1) => {
      try {
        loading.value = true
        
        const params = {
          page,
          per_page: pagination.value.per_page,
          ...filters.value
        }

        // Remove empty filters
        Object.keys(params).forEach(key => {
          if (params[key] === '' || params[key] === null || params[key] === undefined) {
            delete params[key]
          }
        })

        const response = await apiRequest({
          method: 'GET',
          url: '/api/documents',
          params
        })
        documents.value = response.data
        pagination.value = {
          current_page: response.current_page,
          last_page: response.last_page,
          per_page: response.per_page,
          total: response.total,
          from: response.from,
          to: response.to
        }
        
      } catch (error) {
        console.error('Errore nel caricamento dei documenti:', error)
        addNotification('Errore nel caricamento dei documenti', 'error')
      } finally {
        loading.value = false
      }
    }

    const debouncedLoadDocuments = debounce(() => {
      loadDocuments(1)
    }, 300)

    const resetFilters = () => {
      filters.value = {
        search: '',
        document_type: '',
        status: '',
        sort_by: 'created_at',
        sort_order: 'desc'
      }
    }

    const toggleSortOrder = () => {
      filters.value.sort_order = filters.value.sort_order === 'asc' ? 'desc' : 'asc'
    }

    const createDocument = () => {
      router.push('/documents/create')
    }

    const exportCsv = async () => {
      try {
        const params = { ...filters.value }
        
        // Remove empty filters
        Object.keys(params).forEach(key => {
          if (params[key] === '' || params[key] === null || params[key] === undefined) {
            delete params[key]
          }
        })

        const response = await apiRequest({
          method: 'GET',
          url: '/api/documents/export/csv',
          params,
          responseType: 'blob'
        })
        
        const url = window.URL.createObjectURL(new Blob([response]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `documenti-${new Date().toISOString().split('T')[0]}.csv`)
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(url)
        
        addNotification('Esportazione CSV completata', 'success')
        
      } catch (error) {
        console.error('Errore nell\'esportazione CSV:', error)
        addNotification('Errore nell\'esportazione CSV', 'error')
      }
    }

    const previousPage = () => {
      if (pagination.value.current_page > 1) {
        loadDocuments(pagination.value.current_page - 1)
      }
    }

    const nextPage = () => {
      if (pagination.value.current_page < pagination.value.last_page) {
        loadDocuments(pagination.value.current_page + 1)
      }
    }

    const goToPage = (page) => {
      if (page !== '...' && page !== pagination.value.current_page) {
        loadDocuments(page)
      }
    }

    const getDocumentTypeClass = (type) => {
      const classes = {
        'Fattura': 'bg-green-100 text-green-800',
        'Preventivo': 'bg-blue-100 text-blue-800',
        'Contratto': 'bg-purple-100 text-purple-800',
        'Ricevuta': 'bg-yellow-100 text-yellow-800',
        'Altro': 'bg-gray-100 text-gray-800'
      }
      return classes[type] || classes['Altro']
    }

    const getStatusClass = (status) => {
      const classes = {
        'draft': 'bg-gray-100 text-gray-800',
        'active': 'bg-green-100 text-green-800',
        'archived': 'bg-red-100 text-red-800'
      }
      return classes[status] || classes['draft']
    }

    const getStatusLabel = (status) => {
      const labels = {
        'draft': 'Bozza',
        'active': 'Attivo',
        'archived': 'Archiviato'
      }
      return labels[status] || status
    }

    const formatDateTime = (dateString) => {
      if (!dateString) return 'N/A'
      return new Date(dateString).toLocaleDateString('it-IT')
    }

    const formatCurrency = (amount) => {
      if (!amount) return 'N/A'
      return new Intl.NumberFormat('it-IT', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount)
    }

    // Watch for filter changes
    watch(filters, debouncedLoadDocuments, { deep: true })

    onMounted(() => {
      loadDocuments()
    })

    return {
      documents,
      loading,
      pagination,
      filters,
      hasActiveFilters,
      visiblePages,
      resetFilters,
      toggleSortOrder,
      createDocument,
      exportCsv,
      previousPage,
      nextPage,
      goToPage,
      getDocumentTypeClass,
      getStatusClass,
      getStatusLabel,
      formatDate,
      formatDateTime,
      formatCurrency
    }
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>