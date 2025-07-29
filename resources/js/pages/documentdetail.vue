<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center space-x-4">
            <button
              @click="goBack"
              class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              Torna ai Documenti
            </button>
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Dettaglio Documento</h1>
              <p v-if="document" class="mt-1 text-sm text-gray-500">{{ document.title }}</p>
            </div>
          </div>
          
          <div class="flex items-center space-x-3">
            <CanAccess permission="edit.documents">
              <button
                @click="editDocument"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Modifica
              </button>
            </CanAccess>
            
            <button
              @click="downloadDocument"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Download
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-red-50 border border-red-200 rounded-md p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Errore nel caricamento</h3>
            <p class="mt-1 text-sm text-red-700">{{ error }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Document Content -->
    <div v-else-if="document" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          
          <!-- Document Info Card -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Informazioni Documento</h2>
            </div>
            <div class="px-6 py-4">
              <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Titolo</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ document.title }}</dd>
                </div>
                
                <div>
                  <dt class="text-sm font-medium text-gray-500">Tipo Documento</dt>
                  <dd class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getDocumentTypeClass(document.document_type)">
                      {{ document.document_type }}
                    </span>
                  </dd>
                </div>
                
                <div>
                  <dt class="text-sm font-medium text-gray-500">Stato</dt>
                  <dd class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getStatusClass(document.status)">
                      {{ getStatusLabel(document.status) }}
                    </span>
                  </dd>
                </div>
                
                <div>
                  <dt class="text-sm font-medium text-gray-500">Numero Riferimento</dt>
                  <dd class="mt-1 text-sm text-gray-900 font-mono">{{ document.reference_number || 'N/A' }}</dd>
                </div>
                
                <div>
                  <dt class="text-sm font-medium text-gray-500">Data Documento</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDate(document.document_date) }}</dd>
                </div>
                
                <div>
                  <dt class="text-sm font-medium text-gray-500">Data Scadenza</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDate(document.due_date) || 'N/A' }}</dd>
                </div>
                
                <div v-if="document.amount">
                  <dt class="text-sm font-medium text-gray-500">Importo</dt>
                  <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ formatCurrency(document.amount) }}</dd>
                </div>
                
                <div v-if="document.supplier_name">
                  <dt class="text-sm font-medium text-gray-500">Fornitore</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ document.supplier_name }}</dd>
                </div>
              </dl>
              
              <div v-if="document.description" class="mt-6">
                <dt class="text-sm font-medium text-gray-500">Descrizione</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ document.description }}</dd>
              </div>
            </div>
          </div>

          <!-- Project and Client Info -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Progetto e Cliente</h2>
            </div>
            <div class="px-6 py-4">
              <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div v-if="document.project">
                  <dt class="text-sm font-medium text-gray-500">Progetto</dt>
                  <dd class="mt-1">
                    <router-link 
                      :to="`/projects/${document.project.id}`"
                      class="text-sm text-primary-600 hover:text-primary-500"
                    >
                      {{ document.project.name }}
                    </router-link>
                  </dd>
                </div>
                
                <div v-if="document.client">
                  <dt class="text-sm font-medium text-gray-500">Cliente</dt>
                  <dd class="mt-1">
                    <router-link 
                      :to="`/clients/${document.client.id}`"
                      class="text-sm text-primary-600 hover:text-primary-500"
                    >
                      {{ document.client.name }}
                    </router-link>
                  </dd>
                </div>
                
                <div v-if="document.user">
                  <dt class="text-sm font-medium text-gray-500">Creato da</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ document.user.name }}</dd>
                </div>
                
                <div>
                  <dt class="text-sm font-medium text-gray-500">Data Creazione</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(document.created_at) }}</dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- File Information -->
          <div v-if="document.file_path" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">File Allegato</h2>
            </div>
            <div class="px-6 py-4">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900">{{ document.original_filename || 'File allegato' }}</p>
                  <p class="text-sm text-gray-500">{{ document.file_size ? formatFileSize(document.file_size) : '' }}</p>
                </div>
                <div>
                  <button
                    @click="downloadDocument"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                  >
                    Download
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          
          <!-- Barcode Display Component -->
          <BarcodeDisplay
            :value="document.reference_number || document.id.toString()"
            :document-title="document.title"
            format="CODE128"
            :width="2"
            :height="100"
            :display-text="true"
          />

          <!-- Quick Actions -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Azioni Rapide</h2>
            </div>
            <div class="px-6 py-4 space-y-3">
              <CanAccess permission="edit.documents">
                <button
                  @click="editDocument"
                  class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  Modifica Documento
                </button>
              </CanAccess>
              
              <CanAccess permission="delete.documents">
                <button
                  @click="deleteDocument"
                  class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50"
                >
                  <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  Elimina Documento
                </button>
              </CanAccess>
              
              <button
                @click="duplicateDocument"
                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                Duplica Documento
              </button>
            </div>
          </div>

          <!-- Document History -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Cronologia</h2>
            </div>
            <div class="px-6 py-4">
              <div class="flow-root">
                <ul class="-mb-8">
                  <li>
                    <div class="relative pb-8">
                      <div class="relative flex space-x-3">
                        <div>
                          <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                          </span>
                        </div>
                        <div class="min-w-0 flex-1">
                          <div>
                            <div class="text-sm">
                              <span class="font-medium text-gray-900">Documento creato</span>
                            </div>
                            <p class="mt-0.5 text-sm text-gray-500">
                              {{ formatDateTime(document.created_at) }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                  
                  <li v-if="document.updated_at !== document.created_at">
                    <div class="relative">
                      <div class="relative flex space-x-3">
                        <div>
                          <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                          </span>
                        </div>
                        <div class="min-w-0 flex-1">
                          <div>
                            <div class="text-sm">
                              <span class="font-medium text-gray-900">Ultima modifica</span>
                            </div>
                            <p class="mt-0.5 text-sm text-gray-500">
                              {{ formatDateTime(document.updated_at) }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import useApi from '@/composables/useApi'
import useNotifications from '@/composables/useNotifications'
import { formatDate } from '@/utils/formatDate'
import CanAccess from '@/components/CanAccess.vue'
import BarcodeDisplay from '@/components/BarcodeDisplay.vue'

export default {
  name: 'DocumentDetail',
  components: {
    CanAccess,
    BarcodeDisplay
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const { request: apiRequest } = useApi()
    const { addNotification } = useNotifications()
    
    const document = ref(null)
    const loading = ref(true)
    const error = ref(null)

    const loadDocument = async () => {
      try {
        loading.value = true
        error.value = null
        
        const response = await apiRequest({
          method: 'GET',
          url: `/documents/${route.params.id}`
        })
        document.value = response
        
      } catch (err) {
        console.error('Errore nel caricamento del documento:', err)
        error.value = err.response?.data?.message || 'Errore nel caricamento del documento'
        
        if (err.response?.status === 404) {
          router.push('/documents')
        }
      } finally {
        loading.value = false
      }
    }

    const goBack = () => {
      router.push('/documents')
    }

    const editDocument = () => {
      router.push(`/documents/${route.params.id}/edit`)
    }

    const deleteDocument = async () => {
      if (!confirm('Sei sicuro di voler eliminare questo documento?')) {
        return
      }

      try {
        await apiRequest({
          method: 'DELETE',
          url: `/documents/${route.params.id}`
        })
        addNotification('Documento eliminato con successo', 'success')
        router.push('/documents')
      } catch (err) {
        console.error('Errore nell\'eliminazione del documento:', err)
        addNotification('Errore nell\'eliminazione del documento', 'error')
      }
    }

    const duplicateDocument = () => {
      router.push(`/documents/create?duplicate=${route.params.id}`)
    }

    const downloadDocument = async () => {
      if (!document.value?.file_path) {
        addNotification('Nessun file allegato disponibile', 'warning')
        return
      }

      try {
        const response = await apiRequest({
          method: 'GET',
          url: `/documents/${route.params.id}/download`,
          responseType: 'blob'
        })
        
        const url = window.URL.createObjectURL(new Blob([response]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', document.value.original_filename || `documento-${document.value.id}`)
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(url)
        
      } catch (err) {
        console.error('Errore nel download del documento:', err)
        addNotification('Errore nel download del documento', 'error')
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
      return new Date(dateString).toLocaleString('it-IT')
    }

    const formatCurrency = (amount) => {
      if (!amount) return 'N/A'
      return new Intl.NumberFormat('it-IT', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount)
    }

    const formatFileSize = (bytes) => {
      if (!bytes) return 'N/A'
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(1024))
      return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
    }

    onMounted(() => {
      loadDocument()
    })

    return {
      document,
      loading,
      error,
      goBack,
      editDocument,
      deleteDocument,
      duplicateDocument,
      downloadDocument,
      getDocumentTypeClass,
      getStatusClass,
      getStatusLabel,
      formatDate,
      formatDateTime,
      formatCurrency,
      formatFileSize
    }
  }
}
</script>