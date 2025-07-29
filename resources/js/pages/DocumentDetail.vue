<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center space-x-4">
            <button
              @click="goBack"
              class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors"
            >
              <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              Torna ai Documenti
            </button>
            <div class="h-6 border-l border-gray-300"></div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">{{ document?.title || 'Dettaglio Documento' }}</h1>
              <p class="text-sm text-gray-500 mt-1">
                Creato il {{ formatDate(document?.created_at) }}
                <span v-if="document?.updated_at !== document?.created_at">
                  • Aggiornato il {{ formatDate(document?.updated_at) }}
                </span>
              </p>
            </div>
          </div>
          
          <!-- Actions -->
          <div class="flex items-center space-x-3">
            <CanAccess permission="edit.document">
              <button
                @click="editDocument"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Modifica
              </button>
            </CanAccess>
            
            <button
              @click="printDocument"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
              </svg>
              Stampa
            </button>
            
            <CanAccess permission="delete.document">
              <button
                @click="deleteDocument"
                class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Elimina
              </button>
            </CanAccess>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">{{ error }}</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Document Content -->
    <div v-else-if="document" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          
          <!-- Document Information Card -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Informazioni Documento</h2>
            </div>
            <div class="px-6 py-4">
              <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getTypeClass(document.type)">
                      {{ getTypeLabel(document.type) }}
                    </span>
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Stato</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getStatusClass(document.status)">
                      {{ getStatusLabel(document.status) }}
                    </span>
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Cliente</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ document.client?.name }}
                    <span v-if="document.client?.company" class="text-gray-500">
                      ({{ document.client.company }})
                    </span>
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Progetto</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ document.project?.name }}</dd>
                </div>
                <div v-if="document.description" class="md:col-span-2">
                  <dt class="text-sm font-medium text-gray-500">Descrizione</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ document.description }}</dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- Materials Section -->
          <div v-if="document.materials && document.materials.length > 0" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <div class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Materiali</h2>
                <span class="text-sm text-gray-500">
                  {{ document.materials.length }} {{ document.materials.length === 1 ? 'materiale' : 'materiali' }}
                </span>
              </div>
            </div>
            <div class="overflow-hidden">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Materiale
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Quantità
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Prezzo Unit.
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Totale
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Dettagli
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(material, index) in document.materials" :key="index" class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ material.name }}</div>
                        <div v-if="material.lot_number" class="text-xs text-gray-500">
                          Lotto: {{ material.lot_number }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ material.quantity }} {{ material.unit }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        €{{ (material.unit_price || 0).toFixed(2) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        €{{ getMaterialTotal(material).toFixed(2) }}
                      </td>
                      <td class="px-6 py-4 text-sm text-gray-500">
                        <div class="space-y-1">
                          <div v-if="material.expiry_date" class="flex items-center">
                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Scad: {{ formatDate(material.expiry_date) }}
                          </div>
                          <div v-if="material.location" class="flex items-center">
                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ material.location }}
                          </div>
                          <div v-if="material.notes" class="flex items-center">
                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            {{ material.notes }}
                          </div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Empty Materials State -->
          <div v-else class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Materiali</h2>
            </div>
            <div class="px-6 py-8 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun materiale</h3>
              <p class="mt-1 text-sm text-gray-500">Questo documento non contiene materiali.</p>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          
          <!-- Financial Summary -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Riepilogo Finanziario</h2>
            </div>
            <div class="px-6 py-4">
              <dl class="space-y-4">
                <div class="flex justify-between">
                  <dt class="text-sm text-gray-500">Subtotale</dt>
                  <dd class="text-sm font-medium text-gray-900">€{{ (document.subtotal || 0).toFixed(2) }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm text-gray-500">IVA</dt>
                  <dd class="text-sm font-medium text-gray-900">€{{ (document.vat_amount || 0).toFixed(2) }}</dd>
                </div>
                <div class="flex justify-between pt-4 border-t border-gray-200">
                  <dt class="text-base font-medium text-gray-900">Totale</dt>
                  <dd class="text-base font-bold text-gray-900">€{{ (document.total_amount || 0).toFixed(2) }}</dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- Document Metadata -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Metadati</h2>
            </div>
            <div class="px-6 py-4">
              <dl class="space-y-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">ID Documento</dt>
                  <dd class="mt-1 text-sm text-gray-900 font-mono">#{{ document.id }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Data Creazione</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDate(document.created_at) }}</dd>
                </div>
                <div v-if="document.updated_at !== document.created_at">
                  <dt class="text-sm font-medium text-gray-500">Ultima Modifica</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDate(document.updated_at) }}</dd>
                </div>
                <div v-if="document.created_by">
                  <dt class="text-sm font-medium text-gray-500">Creato da</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ document.created_by.name }}</dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Azioni Rapide</h2>
            </div>
            <div class="px-6 py-4 space-y-3">
              <button
                @click="duplicateDocument"
                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z" />
                  <path d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5h8a2 2 0 00-2-2H5z" />
                </svg>
                Duplica Documento
              </button>
              
              <button
                @click="exportDocument"
                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Esporta PDF
              </button>
              
              <button
                @click="shareDocument"
                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                </svg>
                Condividi
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import useApi from '@/composables/useApi'
import CanAccess from '@/components/CanAccess.vue'
import useNotifications from '@/composables/useNotifications'
import { formatDate } from '@/utils'

// Router
const route = useRoute()
const router = useRouter()

// API
const { get, delete: deleteApi } = useApi()
const { addNotification } = useNotifications()

// State
const loading = ref(true)
const error = ref(null)
const document = ref(null)

// Computed
const documentId = computed(() => route.params.id)

// Methods
const loadDocument = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await get(`/api/documents/${documentId.value}`)
    document.value = response.data
  } catch (err) {
    error.value = err.response?.data?.message || 'Errore nel caricamento del documento'
    console.error('Error loading document:', err)
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  router.push('/documents')
}

const editDocument = () => {
  router.push(`/documents/${documentId.value}/edit`)
}

const deleteDocument = async () => {
  if (!confirm('Sei sicuro di voler eliminare questo documento? Questa azione non può essere annullata.')) {
    return
  }
  
  try {
    await deleteApi(`/api/documents/${documentId.value}`)
    router.push('/documents')
  } catch (err) {
    error.value = err.response?.data?.message || 'Errore nell\'eliminazione del documento'
  }
}

const duplicateDocument = () => {
  // Navigate to create page with pre-filled data
  router.push({
    path: '/documents/create',
    query: { duplicate: documentId.value }
  })
}

const printDocument = () => {
  window.print()
}

const exportDocument = () => {
  // TODO: Implement PDF export
  console.log('Export document as PDF')
}

const shareDocument = () => {
  // TODO: Implement document sharing
  console.log('Share document')
}

const getMaterialTotal = (material) => {
  const quantity = parseFloat(material.quantity) || 0
  const unitPrice = parseFloat(material.unit_price) || 0
  return quantity * unitPrice
}

// Status and Type helpers
const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
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

const getTypeClass = (type) => {
  const classes = {
    entry: 'bg-blue-100 text-blue-800',
    delivery: 'bg-green-100 text-green-800',
    invoice: 'bg-purple-100 text-purple-800',
    receipt: 'bg-orange-100 text-orange-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const getTypeLabel = (type) => {
  const labels = {
    entry: 'Documento di Entrata',
    delivery: 'Documento di Consegna',
    invoice: 'Fattura',
    receipt: 'Ricevuta'
  }
  return labels[type] || type
}

// Lifecycle
onMounted(() => {
  loadDocument()
})
</script>

<style scoped>
@media print {
  .no-print {
    display: none !important;
  }
  
  .print-only {
    display: block !important;
  }
  
  body {
    background: white !important;
  }
  
  .shadow {
    box-shadow: none !important;
  }
}
</style>