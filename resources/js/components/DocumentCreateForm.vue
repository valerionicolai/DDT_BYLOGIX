<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <!-- Error Display -->
    <div v-if="error" class="rounded-md bg-red-50 p-4">
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

    <!-- Document Basic Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Title -->
      <div class="md:col-span-2">
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
          Titolo Documento *
        </label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
          placeholder="Inserisci il titolo del documento"
        >
      </div>

      <!-- Document Type -->
      <div>
        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
          Tipo Documento *
        </label>
        <select
          id="type"
          v-model="form.type"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
        >
          <option value="">Seleziona tipo</option>
          <option value="entry">Documento di Entrata</option>
          <option value="delivery">Documento di Consegna</option>
          <option value="invoice">Fattura</option>
          <option value="receipt">Ricevuta</option>
        </select>
      </div>

      <!-- Status -->
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
          Stato *
        </label>
        <select
          id="status"
          v-model="form.status"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
        >
          <option value="">Seleziona stato</option>
          <option value="draft">Bozza</option>
          <option value="pending">In Attesa</option>
          <option value="approved">Approvato</option>
          <option value="completed">Completato</option>
          <option value="cancelled">Annullato</option>
        </select>
      </div>
    </div>

    <!-- Client and Project Selection (S2-F02) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Client Selection -->
      <div>
        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
          Cliente *
        </label>
        <select
          id="client_id"
          v-model="form.client_id"
          @change="onClientChange"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
          :disabled="loadingClients"
        >
          <option value="">
            {{ loadingClients ? 'Caricamento clienti...' : 'Seleziona cliente' }}
          </option>
          <option
            v-for="client in clients"
            :key="client.id"
            :value="client.id"
          >
            {{ client.name }} {{ client.company ? `(${client.company})` : '' }}
          </option>
        </select>
      </div>

      <!-- Project Selection (Cascading) -->
      <div>
        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
          Progetto *
        </label>
        <select
          id="project_id"
          v-model="form.project_id"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
          :disabled="!form.client_id || loadingProjects"
        >
          <option value="">
            {{ getProjectSelectText() }}
          </option>
          <option
            v-for="project in availableProjects"
            :key="project.id"
            :value="project.id"
          >
            {{ project.name }}
          </option>
        </select>
      </div>
    </div>

    <!-- Description -->
    <div>
      <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
        Descrizione
      </label>
      <textarea
        id="description"
        v-model="form.description"
        rows="3"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
        placeholder="Descrizione opzionale del documento"
      ></textarea>
    </div>

    <!-- Financial Information -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div>
        <label for="subtotal" class="block text-sm font-medium text-gray-700 mb-2">
          Subtotale (€)
        </label>
        <input
          id="subtotal"
          v-model.number="form.subtotal"
          type="number"
          step="0.01"
          min="0"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
          @input="calculateTotal"
        >
      </div>

      <div>
        <label for="vat_amount" class="block text-sm font-medium text-gray-700 mb-2">
          IVA (€)
        </label>
        <input
          id="vat_amount"
          v-model.number="form.vat_amount"
          type="number"
          step="0.01"
          min="0"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
          @input="calculateTotal"
        >
      </div>

      <div>
        <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-2">
          Totale (€)
        </label>
        <input
          id="total_amount"
          v-model.number="form.total_amount"
          type="number"
          step="0.01"
          min="0"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50"
          readonly
        >
      </div>
    </div>

    <!-- Materials Section -->
    <div class="border-t pt-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">Materiali</h3>
        <button
          type="button"
          @click="addMaterial"
          class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
          <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Aggiungi Materiale
        </button>
      </div>

      <!-- Materials List -->
      <div v-if="form.materials.length === 0" class="text-center py-8 text-gray-500">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <p class="mt-2">Nessun materiale aggiunto</p>
        <p class="text-sm">Clicca "Aggiungi Materiale" per iniziare</p>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="(material, index) in form.materials"
          :key="index"
          class="border border-gray-200 rounded-lg p-4 bg-gray-50"
        >
          <div class="flex justify-between items-start mb-4">
            <h4 class="text-sm font-medium text-gray-900">Materiale {{ index + 1 }}</h4>
            <button
              type="button"
              @click="removeMaterial(index)"
              class="text-red-600 hover:text-red-800"
            >
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Nome *</label>
              <input
                v-model="material.name"
                type="text"
                required
                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-primary-500"
                placeholder="Nome materiale"
              >
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Quantità *</label>
              <input
                v-model.number="material.quantity"
                type="number"
                step="0.01"
                min="0"
                required
                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-primary-500"
                placeholder="0.00"
              >
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Unità</label>
              <input
                v-model="material.unit"
                type="text"
                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-primary-500"
                placeholder="kg, pz, m, etc."
              >
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Prezzo Unitario (€)</label>
              <input
                v-model.number="material.unit_price"
                type="number"
                step="0.01"
                min="0"
                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-primary-500"
                placeholder="0.00"
              >
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Codice Lotto</label>
              <input
                v-model="material.lot_number"
                type="text"
                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-primary-500"
                placeholder="Codice lotto"
              >
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Data Scadenza</label>
              <input
                v-model="material.expiry_date"
                type="date"
                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-primary-500"
              >
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Ubicazione</label>
              <input
                v-model="material.location"
                type="text"
                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-primary-500"
                placeholder="Magazzino, scaffale, etc."
              >
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Note</label>
              <input
                v-model="material.notes"
                type="text"
                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-primary-500"
                placeholder="Note aggiuntive"
              >
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-4 pt-6 border-t">
      <button
        type="button"
        @click="$emit('cancel')"
        class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        :disabled="loading"
      >
        Annulla
      </button>
      
      <button
        type="submit"
        class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
        :disabled="loading || !isFormValid"
      >
        <span v-if="loading" class="flex items-center">
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Creazione...
        </span>
        <span v-else>Crea Documento</span>
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import useApi from '@/composables/useApi'

// Props & Emits
const props = defineProps({
  loading: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['submit', 'cancel'])

// API composables
const { get } = useApi()

// State
const loadingClients = ref(false)
const loadingProjects = ref(false)
const clients = ref([])
const projects = ref([])

// Form data
const form = ref({
  title: '',
  type: '',
  status: 'draft',
  client_id: '',
  project_id: '',
  description: '',
  subtotal: 0,
  vat_amount: 0,
  total_amount: 0,
  materials: []
})

// Computed
const availableProjects = computed(() => {
  if (!form.value.client_id) return []
  return projects.value.filter(project => project.client_id == form.value.client_id)
})

const isFormValid = computed(() => {
  return form.value.title &&
         form.value.type &&
         form.value.status &&
         form.value.client_id &&
         form.value.project_id &&
         form.value.materials.every(material => 
           material.name && 
           material.quantity > 0
         )
})

// Methods
const loadClients = async () => {
  loadingClients.value = true
  try {
    const response = await get('/api/clients')
    clients.value = response.data || []
  } catch (error) {
    console.error('Error loading clients:', error)
  } finally {
    loadingClients.value = false
  }
}

const loadProjects = async () => {
  loadingProjects.value = true
  try {
    const response = await get('/api/projects')
    projects.value = response.data || []
  } catch (error) {
    console.error('Error loading projects:', error)
  } finally {
    loadingProjects.value = false
  }
}

const onClientChange = () => {
  // Reset project selection when client changes
  form.value.project_id = ''
}

const getProjectSelectText = () => {
  if (!form.value.client_id) {
    return 'Seleziona prima un cliente'
  }
  if (loadingProjects.value) {
    return 'Caricamento progetti...'
  }
  if (availableProjects.value.length === 0) {
    return 'Nessun progetto disponibile per questo cliente'
  }
  return 'Seleziona progetto'
}

const calculateTotal = () => {
  const subtotal = parseFloat(form.value.subtotal) || 0
  const vat = parseFloat(form.value.vat_amount) || 0
  form.value.total_amount = subtotal + vat
}

const addMaterial = () => {
  form.value.materials.push({
    name: '',
    quantity: 0,
    unit: '',
    unit_price: 0,
    lot_number: '',
    expiry_date: '',
    location: '',
    notes: ''
  })
}

const removeMaterial = (index) => {
  form.value.materials.splice(index, 1)
}

const handleSubmit = () => {
  if (!isFormValid.value) return
  
  // Clean up form data
  const submitData = {
    ...form.value,
    materials: form.value.materials.filter(material => 
      material.name && material.quantity > 0
    )
  }
  
  emit('submit', submitData)
}

// Watchers
watch(() => form.value.subtotal, calculateTotal)
watch(() => form.value.vat_amount, calculateTotal)

// Lifecycle
onMounted(() => {
  loadClients()
  loadProjects()
})
</script>