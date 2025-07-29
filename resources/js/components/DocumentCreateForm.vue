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
        <div>
          <h3 class="text-lg font-medium text-gray-900">Materiali</h3>
          <p class="text-sm text-gray-500 mt-1">
            {{ form.materials.length }} {{ form.materials.length === 1 ? 'materiale' : 'materiali' }} 
            {{ form.materials.length > 0 ? `- Totale: €${materialsTotalAmount.toFixed(2)}` : '' }}
          </p>
        </div>
        <div class="flex space-x-2">
          <button
            v-if="form.materials.length > 0"
            type="button"
            @click="clearAllMaterials"
            class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            Rimuovi Tutti
          </button>
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
      </div>

      <!-- Empty State -->
      <div v-if="form.materials.length === 0" class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun materiale aggiunto</h3>
        <p class="mt-1 text-sm text-gray-500">Inizia aggiungendo il primo materiale al documento</p>
        <div class="mt-6">
          <button
            type="button"
            @click="addMaterial"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Aggiungi Primo Materiale
          </button>
        </div>
      </div>

      <!-- Materials List -->
      <div v-else class="space-y-4">
        <TransitionGroup name="material" tag="div" class="space-y-4">
          <div
            v-for="(material, index) in form.materials"
            :key="material.id || index"
            class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm hover:shadow-md transition-shadow duration-200"
          >
            <!-- Material Header -->
            <div class="flex justify-between items-center mb-4">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-primary-600 font-medium text-sm">{{ index + 1 }}</span>
                  </div>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-900">
                    {{ material.name || `Materiale ${index + 1}` }}
                  </h4>
                  <p v-if="getMaterialTotal(material) > 0" class="text-xs text-gray-500">
                    Totale riga: €{{ getMaterialTotal(material).toFixed(2) }}
                  </p>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <button
                  v-if="index > 0"
                  type="button"
                  @click="moveMaterialUp(index)"
                  class="text-gray-400 hover:text-gray-600 transition-colors"
                  title="Sposta su"
                >
                  <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
                <button
                  v-if="index < form.materials.length - 1"
                  type="button"
                  @click="moveMaterialDown(index)"
                  class="text-gray-400 hover:text-gray-600 transition-colors"
                  title="Sposta giù"
                >
                  <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
                <button
                  type="button"
                  @click="duplicateMaterial(index)"
                  class="text-blue-600 hover:text-blue-800 transition-colors"
                  title="Duplica"
                >
                  <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z" />
                    <path d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5h8a2 2 0 00-2-2H5z" />
                  </svg>
                </button>
                <button
                  type="button"
                  @click="removeMaterial(index)"
                  class="text-red-600 hover:text-red-800 transition-colors"
                  title="Rimuovi"
                >
                  <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Material Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">
                  Nome Materiale *
                  <span v-if="!material.name" class="text-red-500">Campo obbligatorio</span>
                </label>
                <input
                  v-model="material.name"
                  type="text"
                  required
                  class="w-full px-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
                  :class="material.name ? 'border-gray-300' : 'border-red-300 bg-red-50'"
                  placeholder="Inserisci nome materiale"
                  @blur="validateMaterial(material, index)"
                >
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                  Quantità *
                  <span v-if="!material.quantity || material.quantity <= 0" class="text-red-500">Richiesta</span>
                </label>
                <input
                  v-model.number="material.quantity"
                  type="number"
                  step="0.01"
                  min="0"
                  required
                  class="w-full px-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
                  :class="material.quantity > 0 ? 'border-gray-300' : 'border-red-300 bg-red-50'"
                  placeholder="0.00"
                  @input="calculateMaterialTotal(material)"
                >
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Unità di Misura</label>
                <select
                  v-model="material.unit"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                  <option value="">Seleziona unità</option>
                  <option value="kg">Chilogrammi (kg)</option>
                  <option value="g">Grammi (g)</option>
                  <option value="l">Litri (l)</option>
                  <option value="ml">Millilitri (ml)</option>
                  <option value="m">Metri (m)</option>
                  <option value="cm">Centimetri (cm)</option>
                  <option value="m²">Metri quadri (m²)</option>
                  <option value="m³">Metri cubi (m³)</option>
                  <option value="pz">Pezzi (pz)</option>
                  <option value="conf">Confezioni (conf)</option>
                  <option value="scatole">Scatole</option>
                  <option value="bancali">Bancali</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Prezzo Unitario (€)</label>
                <input
                  v-model.number="material.unit_price"
                  type="number"
                  step="0.01"
                  min="0"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                  placeholder="0.00"
                  @input="calculateMaterialTotal(material)"
                >
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Codice/Lotto</label>
                <input
                  v-model="material.lot_number"
                  type="text"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                  placeholder="Codice identificativo"
                >
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Data Scadenza</label>
                <input
                  v-model="material.expiry_date"
                  type="date"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                  :min="new Date().toISOString().split('T')[0]"
                >
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Ubicazione</label>
                <input
                  v-model="material.location"
                  type="text"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                  placeholder="Magazzino, scaffale..."
                >
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Note</label>
                <input
                  v-model="material.notes"
                  type="text"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                  placeholder="Note aggiuntive"
                >
              </div>
            </div>

            <!-- Material Summary -->
            <div v-if="getMaterialTotal(material) > 0" class="mt-4 p-3 bg-gray-50 rounded-md">
              <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600">
                  {{ material.quantity }} {{ material.unit }} × €{{ (material.unit_price || 0).toFixed(2) }}
                </span>
                <span class="font-medium text-gray-900">
                  Totale: €{{ getMaterialTotal(material).toFixed(2) }}
                </span>
              </div>
            </div>
          </div>
        </TransitionGroup>

        <!-- Quick Add Material Button -->
        <div class="flex justify-center pt-4">
          <button
            type="button"
            @click="addMaterial"
            class="inline-flex items-center px-4 py-2 border border-dashed border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Aggiungi Altro Materiale
          </button>
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

// Computed property for materials total
const materialsTotalAmount = computed(() => {
  return form.value.materials.reduce((total, material) => {
    return total + getMaterialTotal(material)
  }, 0)
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

const calculateTotals = () => {
  // Calculate materials total and update form totals
  const materialsTotal = materialsTotalAmount.value
  form.value.subtotal = materialsTotal
  calculateTotal()
}

// Material management methods
const addMaterial = () => {
  const newMaterial = {
    id: Date.now(), // Unique ID for transitions
    name: '',
    quantity: 1,
    unit: '',
    unit_price: 0,
    lot_number: '',
    expiry_date: '',
    location: '',
    notes: ''
  }
  form.value.materials.push(newMaterial)
  calculateTotals()
}

const removeMaterial = (index) => {
  if (form.value.materials.length > 0) {
    form.value.materials.splice(index, 1)
    calculateTotals()
  }
}

const clearAllMaterials = () => {
  if (confirm('Sei sicuro di voler rimuovere tutti i materiali?')) {
    form.value.materials = []
    calculateTotals()
  }
}

const duplicateMaterial = (index) => {
  const originalMaterial = form.value.materials[index]
  const duplicatedMaterial = {
    ...originalMaterial,
    id: Date.now(), // New unique ID
    name: `${originalMaterial.name} (Copia)`
  }
  form.value.materials.splice(index + 1, 0, duplicatedMaterial)
  calculateTotals()
}

const moveMaterialUp = (index) => {
  if (index > 0) {
    const material = form.value.materials.splice(index, 1)[0]
    form.value.materials.splice(index - 1, 0, material)
  }
}

const moveMaterialDown = (index) => {
  if (index < form.value.materials.length - 1) {
    const material = form.value.materials.splice(index, 1)[0]
    form.value.materials.splice(index + 1, 0, material)
  }
}

const getMaterialTotal = (material) => {
  const quantity = parseFloat(material.quantity) || 0
  const unitPrice = parseFloat(material.unit_price) || 0
  return quantity * unitPrice
}

const calculateMaterialTotal = (material) => {
  // Trigger reactivity and recalculate totals
  calculateTotals()
}

const validateMaterial = (material, index) => {
  // Basic validation for material fields
  if (!material.name) {
    console.warn(`Material ${index + 1}: Nome richiesto`)
  }
  if (!material.quantity || material.quantity <= 0) {
    console.warn(`Material ${index + 1}: Quantità richiesta`)
  }
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

<style scoped>
/* Material transition animations */
.material-enter-active,
.material-leave-active {
  transition: all 0.3s ease;
}

.material-enter-from {
  opacity: 0;
  transform: translateY(-20px) scale(0.95);
}

.material-leave-to {
  opacity: 0;
  transform: translateY(-20px) scale(0.95);
}

.material-move {
  transition: transform 0.3s ease;
}

/* Hover effects for material cards */
.material-card {
  transition: all 0.2s ease;
}

.material-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Focus states for better accessibility */
.form-input:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  border-color: #3b82f6;
}

/* Button hover animations */
.btn-hover {
  transition: all 0.2s ease;
}

.btn-hover:hover {
  transform: translateY(-1px);
}

/* Loading state for buttons */
.btn-loading {
  position: relative;
  color: transparent;
}

.btn-loading::after {
  content: '';
  position: absolute;
  width: 16px;
  height: 16px;
  top: 50%;
  left: 50%;
  margin-left: -8px;
  margin-top: -8px;
  border: 2px solid #ffffff;
  border-radius: 50%;
  border-top-color: transparent;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .material-card {
    margin-bottom: 1rem;
  }
  
  .material-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .material-actions {
    align-self: flex-end;
  }
}
</style>