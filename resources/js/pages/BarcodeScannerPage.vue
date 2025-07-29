<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Page Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Scanner Codici a Barre</h1>
            <p class="mt-2 text-gray-600">Scansiona codici a barre per identificare documenti e materiali</p>
          </div>
          <router-link
            to="/documents"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Vai ai Documenti
          </router-link>
        </div>
      </div>

      <!-- Scanner Component -->
      <BarcodeScanner 
        @scan-result="handleScanResult"
        @scan-error="handleScanError"
      />

      <!-- Search Results -->
      <div v-if="searchResults.length > 0" class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Risultati Ricerca</h3>
          <div class="space-y-4">
            <div 
              v-for="result in searchResults" 
              :key="result.id"
              class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <h4 class="font-medium text-gray-900">{{ result.title }}</h4>
                  <p class="text-sm text-gray-600 mt-1">{{ result.description }}</p>
                  <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                    <span>Tipo: {{ result.type }}</span>
                    <span>Codice: {{ result.barcode }}</span>
                    <span>Aggiornato: {{ formatDate(result.updated_at) }}</span>
                  </div>
                </div>
                <div class="flex items-center space-x-2 ml-4">
                  <router-link
                    :to="`/documents/${result.id}`"
                    class="inline-flex items-center px-3 py-1 border border-blue-300 text-blue-700 rounded-md text-sm hover:bg-blue-50 transition-colors"
                  >
                    Visualizza
                  </router-link>
                  <button
                    @click="editDocument(result)"
                    class="inline-flex items-center px-3 py-1 border border-gray-300 text-gray-700 rounded-md text-sm hover:bg-gray-50 transition-colors"
                  >
                    Modifica
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- No Results Message -->
      <div v-if="showNoResults" class="mt-8">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
          <div class="flex">
            <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div>
              <h3 class="text-sm font-medium text-yellow-800">Nessun risultato trovato</h3>
              <p class="text-sm text-yellow-700 mt-1">
                Il codice a barre scansionato "{{ lastScannedCode }}" non corrisponde a nessun documento nel sistema.
              </p>
              <div class="mt-3">
                <button
                  @click="createNewDocument"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-800 bg-yellow-100 hover:bg-yellow-200 transition-colors"
                >
                  Crea Nuovo Documento
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-gray-900">Scansioni Totali</h3>
              <p class="text-2xl font-bold text-blue-600">{{ totalScans }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-gray-900">Trovati</h3>
              <p class="text-2xl font-bold text-green-600">{{ successfulScans }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-gray-900">Non Trovati</h3>
              <p class="text-2xl font-bold text-yellow-600">{{ notFoundScans }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import BarcodeScanner from '@/components/BarcodeScanner.vue'
import useApi from '@/composables/useApi'
import useNotifications from '@/composables/useNotifications'
import { formatDate } from '@/utils/formatDate'

export default {
  name: 'BarcodeScannerPage',
  components: {
    BarcodeScanner
  },
  setup() {
    const router = useRouter()
    const { request: apiRequest } = useApi()
    const { addNotification } = useNotifications()

    const searchResults = ref([])
    const showNoResults = ref(false)
    const lastScannedCode = ref('')
    const scanStats = ref({
      total: 0,
      successful: 0,
      notFound: 0
    })

    const totalScans = computed(() => scanStats.value.total)
    const successfulScans = computed(() => scanStats.value.successful)
    const notFoundScans = computed(() => scanStats.value.notFound)

    const handleScanResult = async (scanResult) => {
      lastScannedCode.value = scanResult.text
      scanStats.value.total++
      
      try {
        // Search for documents with this barcode
        const response = await apiRequest({
          method: 'GET',
          url: '/documents/search',
          params: {
            barcode: scanResult.text
          }
        })

        if (response && response.length > 0) {
          searchResults.value = response
          showNoResults.value = false
          scanStats.value.successful++
          
          // If only one result found, redirect automatically to document detail
          if (response.length === 1) {
            addNotification(`Documento trovato! Reindirizzamento al dettaglio...`, 'success')
            setTimeout(() => {
              router.push(`/documents/${response[0].id}`)
            }, 1500) // Small delay to show the notification
          } else {
            addNotification(`Trovati ${response.length} documenti per il codice ${scanResult.text}`, 'success')
          }
        } else {
          searchResults.value = []
          showNoResults.value = true
          scanStats.value.notFound++
          addNotification(`Nessun documento trovato per il codice ${scanResult.text}`, 'warning')
        }
      } catch (error) {
        console.error('Error searching documents:', error)
        searchResults.value = []
        showNoResults.value = true
        scanStats.value.notFound++
        addNotification('Errore durante la ricerca dei documenti', 'error')
      }
    }

    const handleScanError = (error) => {
      console.error('Scan error:', error)
      addNotification('Errore durante la scansione', 'error')
    }

    const editDocument = (document) => {
      // Navigate to edit page or open edit modal
      router.push(`/documents/${document.id}/edit`)
    }

    const createNewDocument = () => {
      // Navigate to create page with pre-filled barcode
      router.push({
        path: '/documents/create',
        query: { barcode: lastScannedCode.value }
      })
    }

    return {
      searchResults,
      showNoResults,
      lastScannedCode,
      totalScans,
      successfulScans,
      notFoundScans,
      handleScanResult,
      handleScanError,
      editDocument,
      createNewDocument,
      formatDate
    }
  }
}
</script>