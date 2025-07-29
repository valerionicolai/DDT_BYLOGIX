<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Nuovo Documento</h1>
            <p class="mt-2 text-gray-600">Crea un nuovo documento di entrata con materiali associati</p>
          </div>
          
          <!-- Back Button -->
          <button
            @click="goBack"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Indietro
          </button>
        </div>
      </div>

      <!-- Form Container -->
      <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">
            Informazioni Documento
          </h2>
          <p class="mt-1 text-sm text-gray-600">
            Compila tutti i campi richiesti per creare il documento
          </p>
        </div>
        
        <!-- Document Create Form -->
        <div class="p-6">
          <DocumentCreateForm
            @submit="handleSubmit"
            @cancel="goBack"
            :loading="loading"
            :error="error"
          />
        </div>
      </div>

      <!-- Success Modal -->
      <div
        v-if="showSuccessModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        @click="closeSuccessModal"
      >
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
              <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Documento Creato!</h3>
            <div class="mt-2 px-7 py-3">
              <p class="text-sm text-gray-500">
                Il documento è stato creato con successo. Vuoi crearne un altro o tornare alla lista?
              </p>
            </div>
            <div class="items-center px-4 py-3 space-y-2">
              <button
                @click="viewDocument"
                class="w-full px-4 py-2 bg-primary-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500"
              >
                Visualizza Documento
              </button>
              <button
                @click="createAnother"
                class="w-full px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
              >
                Crea Altro Documento
              </button>
              <button
                @click="goToDocuments"
                class="w-full px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
              >
                Vai alla Lista Documenti
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import DocumentCreateForm from '@/components/DocumentCreateForm.vue'
import useApi from '@/composables/useApi'
import useNotifications from '@/composables/useNotifications'

const router = useRouter()
const { post } = useApi()
const { addNotification } = useNotifications()

// State
const loading = ref(false)
const error = ref(null)
const showSuccessModal = ref(false)
const createdDocument = ref(null)

// Methods
const handleSubmit = async (formData) => {
  loading.value = true
  error.value = null
  
  try {
    const response = await post('/api/documents', formData)
    createdDocument.value = response.data
    showSuccessModal.value = true
    
    addNotification({
      type: 'success',
      title: 'Documento creato',
      message: 'Il documento è stato creato con successo'
    })
  } catch (err) {
    error.value = err.response?.data?.message || 'Errore durante la creazione del documento'
    addNotification({
      type: 'error',
      title: 'Errore',
      message: error.value
    })
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  router.go(-1)
}

const goToDocuments = () => {
  router.push({ name: 'Documents' })
}

const viewDocument = () => {
  if (createdDocument.value) {
    router.push({ name: 'DocumentDetail', params: { id: createdDocument.value.id } })
  }
}

const createAnother = () => {
  showSuccessModal.value = false
  createdDocument.value = null
  // Il form si resetterà automaticamente
}

const closeSuccessModal = () => {
  showSuccessModal.value = false
}
</script>