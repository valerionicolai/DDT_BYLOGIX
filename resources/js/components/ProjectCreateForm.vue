<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <!-- Error Alert -->
    <div v-if="error" class="bg-red-50 border border-red-200 rounded-md p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Errore</h3>
          <div class="mt-2 text-sm text-red-700">{{ error }}</div>
        </div>
      </div>
    </div>

    <!-- Basic Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Project Name -->
      <div class="md:col-span-2">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
          Nome Progetto *
        </label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
          placeholder="Inserisci il nome del progetto"
        />
      </div>

      <!-- Client Selection -->
      <div class="md:col-span-2">
        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
          Cliente *
        </label>
        <select
          id="client_id"
          v-model="form.client_id"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
        >
          <option value="">Seleziona un cliente</option>
          <option v-for="client in clients" :key="client.id" :value="client.id">
            {{ client.name }}
          </option>
        </select>
      </div>

      <!-- Description -->
      <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
          Descrizione
        </label>
        <textarea
          id="description"
          v-model="form.description"
          rows="4"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
          placeholder="Descrizione del progetto (opzionale)"
        ></textarea>
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
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
        >
          <option value="draft">Bozza</option>
          <option value="active">Attivo</option>
          <option value="on_hold">In Pausa</option>
          <option value="completed">Completato</option>
          <option value="cancelled">Annullato</option>
        </select>
      </div>

      <!-- Priority -->
      <div>
        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
          Priorità *
        </label>
        <select
          id="priority"
          v-model="form.priority"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
        >
          <option value="low">Bassa</option>
          <option value="medium">Media</option>
          <option value="high">Alta</option>
          <option value="urgent">Urgente</option>
        </select>
      </div>

      <!-- Start Date -->
      <div>
        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
          Data Inizio
        </label>
        <input
          id="start_date"
          v-model="form.start_date"
          type="date"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
        />
      </div>

      <!-- End Date -->
      <div>
        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
          Data Fine
        </label>
        <input
          id="end_date"
          v-model="form.end_date"
          type="date"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
        />
      </div>

      <!-- Budget -->
      <div>
        <label for="budget" class="block text-sm font-medium text-gray-700 mb-2">
          Budget (€)
        </label>
        <input
          id="budget"
          v-model.number="form.budget"
          type="number"
          step="0.01"
          min="0"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
          placeholder="0.00"
        />
      </div>

      <!-- Hourly Rate -->
      <div>
        <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-2">
          Tariffa Oraria (€/h)
        </label>
        <input
          id="hourly_rate"
          v-model.number="form.hourly_rate"
          type="number"
          step="0.01"
          min="0"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
          placeholder="0.00"
        />
      </div>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
      <button
        type="button"
        @click="$emit('cancel')"
        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
        :disabled="loading"
      >
        Annulla
      </button>
      
      <button
        type="submit"
        class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
        :disabled="loading"
      >
        <span v-if="loading" class="flex items-center">
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Creazione...
        </span>
        <span v-else>Crea Progetto</span>
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import useApi from '@/composables/useApi'

// Props
defineProps({
  loading: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: null
  }
})

// Emits
const emit = defineEmits(['submit', 'cancel'])

// Composables
const { get } = useApi()

// State
const clients = ref([])

// Form data
const form = reactive({
  name: '',
  client_id: '',
  description: '',
  status: 'draft',
  priority: 'medium',
  start_date: '',
  end_date: '',
  budget: null,
  hourly_rate: null
})

// Methods
const handleSubmit = () => {
  // Validate dates
  if (form.start_date && form.end_date && form.start_date > form.end_date) {
    alert('La data di inizio non può essere successiva alla data di fine')
    return
  }

  // Clean up form data
  const formData = { ...form }
  
  // Convert empty strings to null for optional fields
  if (!formData.description) formData.description = null
  if (!formData.start_date) formData.start_date = null
  if (!formData.end_date) formData.end_date = null
  if (!formData.budget) formData.budget = null
  if (!formData.hourly_rate) formData.hourly_rate = null

  emit('submit', formData)
}

const loadClients = async () => {
  try {
    const response = await get('/api/clients')
    clients.value = response.data.data || response.data
  } catch (error) {
    console.error('Errore nel caricamento dei clienti:', error)
  }
}

// Lifecycle
onMounted(() => {
  loadClients()
})
</script>