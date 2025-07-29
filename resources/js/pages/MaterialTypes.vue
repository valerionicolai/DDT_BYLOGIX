<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="space-y-6">
        <!-- Page Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Gestione Materiali</h1>
              <p class="mt-2 text-gray-600">
                Gestisci i tipi di materiali e visualizza l'inventario
              </p>
            </div>
            <div class="flex space-x-3">
              <CanAccess permission="create.material-type">
                <button
                  @click="openModal('create')"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                  <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Nuovo Tipo Materiale
                </button>
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cerca</label>
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Nome, descrizione..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                <select
                  v-model="categoryFilter"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="">Tutte le categorie</option>
                  <option v-for="category in categories" :key="category" :value="category">
                    {{ category }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
                <select
                  v-model="statusFilter"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="">Tutti gli stati</option>
                  <option value="active">Attivo</option>
                  <option value="inactive">Inattivo</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ordina per</label>
                <select
                  v-model="sortBy"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="name">Nome</option>
                  <option value="category">Categoria</option>
                  <option value="unit_price">Prezzo</option>
                  <option value="created_at">Data creazione</option>
                </select>
              </div>
            </div>
          </div>
        </div>

      <!-- Material Types Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
        </div>

        <div v-else-if="filteredMaterialTypes.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun tipo di materiale</h3>
          <p class="mt-1 text-sm text-gray-500">Inizia creando un nuovo tipo di materiale.</p>
          <div class="mt-6">
            <CanAccess permission="create.material-type">
              <button
                @click="openModal('create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nuovo Tipo Materiale
              </button>
            </CanAccess>
          </div>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Materiale
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Categoria
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Prezzo Unitario
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Unità di Misura
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Stato
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Azioni
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="materialType in paginatedMaterialTypes" :key="materialType.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div>
                      <div class="text-sm font-medium text-gray-900">
                        {{ materialType.name }}
                      </div>
                      <div v-if="materialType.description" class="text-sm text-gray-500">
                        {{ materialType.description }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ materialType.category }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  €{{ (materialType.unit_price || 0).toFixed(2) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ materialType.unit_of_measure }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    materialType.status === 'active' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  ]">
                    {{ materialType.status === 'active' ? 'Attivo' : 'Inattivo' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end space-x-2">
                    <button
                      @click="viewMaterialType(materialType)"
                      class="text-primary-600 hover:text-primary-900"
                      title="Visualizza dettagli"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                    <CanAccess permission="update.material-type">
                      <button
                        @click="openModal('edit', materialType)"
                        class="text-indigo-600 hover:text-indigo-900"
                        title="Modifica"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                    </CanAccess>
                    <CanAccess permission="delete.material-type">
                      <button
                        @click="confirmDelete(materialType)"
                        class="text-red-600 hover:text-red-900"
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
        <div v-if="totalPages > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
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
                Mostra
                <span class="font-medium">{{ (currentPage - 1) * itemsPerPage + 1 }}</span>
                a
                <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, filteredMaterialTypes.length) }}</span>
                di
                <span class="font-medium">{{ filteredMaterialTypes.length }}</span>
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
                  <span class="sr-only">Precedente</span>
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
                
                <button
                  v-for="page in visiblePages"
                  :key="page"
                  @click="currentPage = page"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                    page === currentPage
                      ? 'z-10 bg-primary-50 border-primary-500 text-primary-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                  ]"
                >
                  {{ page }}
                </button>
                
                <button
                  @click="nextPage"
                  :disabled="currentPage === totalPages"
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

    <!-- Material Type Modal -->
    <MaterialTypeModal
      v-if="showModal"
      :show="showModal"
      :mode="modalMode"
      :material-type="selectedMaterialType"
      :categories="categories"
      :units-of-measure="unitsOfMeasure"
      @close="closeModal"
      @saved="handleMaterialTypeSaved"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Elimina Tipo Materiale"
      :message="`Sei sicuro di voler eliminare il tipo di materiale '${materialTypeToDelete?.name}'? Questa azione non può essere annullata.`"
      confirm-text="Elimina"
      confirm-class="bg-red-600 hover:bg-red-700"
      @confirm="deleteMaterialType"
      @cancel="cancelDelete"
    />
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import useApi from '@/composables/useApi'
import useNotifications from '@/composables/useNotifications'
import MainLayout from '@/components/MainLayout.vue'
import CanAccess from '@/components/CanAccess.vue'
import MaterialTypeModal from '@/components/MaterialTypeModal.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'

// Composables
const { get, del } = useApi()
const { addNotification } = useNotifications()

// State
const materialTypes = ref([])
const categories = ref([])
const unitsOfMeasure = ref([])
const loading = ref(true)

// Filters
const searchQuery = ref('')
const categoryFilter = ref('')
const statusFilter = ref('')
const sortBy = ref('name')

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Modal state
const showModal = ref(false)
const modalMode = ref('create')
const selectedMaterialType = ref(null)
const showDeleteModal = ref(false)
const materialTypeToDelete = ref(null)

// Computed
const filteredMaterialTypes = computed(() => {
  let filtered = materialTypes.value

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(mt => 
      mt.name.toLowerCase().includes(query) ||
      (mt.description && mt.description.toLowerCase().includes(query))
    )
  }

  // Category filter
  if (categoryFilter.value) {
    filtered = filtered.filter(mt => mt.category === categoryFilter.value)
  }

  // Status filter
  if (statusFilter.value) {
    filtered = filtered.filter(mt => mt.status === statusFilter.value)
  }

  // Sort
  filtered.sort((a, b) => {
    const aVal = a[sortBy.value]
    const bVal = b[sortBy.value]
    
    if (typeof aVal === 'string') {
      return aVal.localeCompare(bVal)
    }
    
    return aVal - bVal
  })

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredMaterialTypes.value.length / itemsPerPage.value))

const paginatedMaterialTypes = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredMaterialTypes.value.slice(start, end)
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
      pages.push('...', total)
    } else if (current >= total - 3) {
      pages.push(1, '...')
      for (let i = total - 4; i <= total; i++) {
        pages.push(i)
      }
    } else {
      pages.push(1, '...')
      for (let i = current - 1; i <= current + 1; i++) {
        pages.push(i)
      }
      pages.push('...', total)
    }
  }
  
  return pages.filter(page => page !== '...' || pages.indexOf(page) === pages.lastIndexOf(page))
})

// Methods
const loadMaterialTypes = async () => {
  try {
    loading.value = true
    const response = await get('/api/material-types')
    materialTypes.value = response.data || []
  } catch (error) {
    console.error('Error loading material types:', error)
    addNotification('Errore nel caricamento dei tipi di materiali', 'error')
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await get('/api/material-types/categories')
    categories.value = response.data || []
  } catch (error) {
    console.error('Error loading categories:', error)
  }
}

const loadUnitsOfMeasure = async () => {
  try {
    const response = await get('/api/material-types/units-of-measure')
    unitsOfMeasure.value = response.data || []
  } catch (error) {
    console.error('Error loading units of measure:', error)
  }
}

const openModal = (mode, materialType = null) => {
  modalMode.value = mode
  selectedMaterialType.value = materialType
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedMaterialType.value = null
}

const handleMaterialTypeSaved = () => {
  closeModal()
  loadMaterialTypes()
}

const viewMaterialType = (materialType) => {
  openModal('view', materialType)
}

const confirmDelete = (materialType) => {
  materialTypeToDelete.value = materialType
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  materialTypeToDelete.value = null
}

const deleteMaterialType = async () => {
  try {
    await del(`/api/material-types/${materialTypeToDelete.value.id}`)
    addNotification('Tipo di materiale eliminato con successo', 'success')
    loadMaterialTypes()
  } catch (error) {
    console.error('Error deleting material type:', error)
    addNotification('Errore nell\'eliminazione del tipo di materiale', 'error')
  } finally {
    cancelDelete()
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

// Lifecycle
onMounted(() => {
  loadMaterialTypes()
  loadCategories()
  loadUnitsOfMeasure()
})
</script>