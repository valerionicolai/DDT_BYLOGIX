<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <form @submit.prevent="handleSubmit">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <!-- Header -->
                <div class="mb-4">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ modalTitle }}
                  </h3>
                  <p class="mt-1 text-sm text-gray-500">
                    {{ modalDescription }}
                  </p>
                </div>

                <!-- Form Fields -->
                <div class="space-y-4">
                  <!-- Nome -->
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                      Nome *
                    </label>
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      :disabled="mode === 'view'"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                      placeholder="Nome del tipo di materiale"
                    />
                  </div>

                  <!-- Descrizione -->
                  <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">
                      Descrizione
                    </label>
                    <textarea
                      id="description"
                      v-model="form.description"
                      :disabled="mode === 'view'"
                      rows="3"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                      placeholder="Descrizione del tipo di materiale"
                    ></textarea>
                  </div>

                  <!-- Categoria -->
                  <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">
                      Categoria *
                    </label>
                    <select
                      id="category"
                      v-model="form.category"
                      :disabled="mode === 'view'"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                    >
                      <option value="">Seleziona categoria</option>
                      <option v-for="category in categories" :key="category" :value="category">
                        {{ category }}
                      </option>
                    </select>
                  </div>

                  <!-- Unità di Misura -->
                  <div>
                    <label for="unit_of_measure" class="block text-sm font-medium text-gray-700">
                      Unità di Misura *
                    </label>
                    <select
                      id="unit_of_measure"
                      v-model="form.unit_of_measure"
                      :disabled="mode === 'view'"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                    >
                      <option value="">Seleziona unità</option>
                      <option v-for="unit in unitsOfMeasure" :key="unit" :value="unit">
                        {{ unit }}
                      </option>
                    </select>
                  </div>

                  <!-- Prezzo Unitario -->
                  <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700">
                      Prezzo Unitario (€)
                    </label>
                    <input
                      id="unit_price"
                      v-model.number="form.unit_price"
                      type="number"
                      step="0.01"
                      min="0"
                      :disabled="mode === 'view'"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                      placeholder="0.00"
                    />
                  </div>

                  <!-- Stato -->
                  <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">
                      Stato *
                    </label>
                    <select
                      id="status"
                      v-model="form.status"
                      :disabled="mode === 'view'"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                    >
                      <option value="active">Attivo</option>
                      <option value="inactive">Inattivo</option>
                    </select>
                  </div>

                  <!-- Note -->
                  <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">
                      Note
                    </label>
                    <textarea
                      id="notes"
                      v-model="form.notes"
                      :disabled="mode === 'view'"
                      rows="2"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                      placeholder="Note aggiuntive"
                    ></textarea>
                  </div>

                  <!-- Informazioni aggiuntive per modalità view -->
                  <div v-if="mode === 'view' && materialType">
                    <div class="border-t border-gray-200 pt-4 mt-4">
                      <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-2">
                        <div>
                          <dt class="text-sm font-medium text-gray-500">Data creazione</dt>
                          <dd class="text-sm text-gray-900">
                            {{ formatDate(materialType.created_at) }}
                          </dd>
                        </div>
                        <div>
                          <dt class="text-sm font-medium text-gray-500">Ultimo aggiornamento</dt>
                          <dd class="text-sm text-gray-900">
                            {{ formatDate(materialType.updated_at) }}
                          </dd>
                        </div>
                      </dl>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <template v-if="mode !== 'view'">
              <button
                type="submit"
                :disabled="!isFormValid || loading"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <div v-if="loading" class="spinner mr-2"></div>
                {{ mode === 'create' ? 'Crea' : 'Aggiorna' }}
              </button>
            </template>
            <button
              type="button"
              @click="$emit('close')"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              {{ mode === 'view' ? 'Chiudi' : 'Annulla' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useApi } from '@/composables/useApi'
import { useNotifications } from '@/composables/useNotifications'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  mode: {
    type: String,
    default: 'create',
    validator: (value) => ['create', 'edit', 'view'].includes(value)
  },
  materialType: {
    type: Object,
    default: null
  },
  categories: {
    type: Array,
    default: () => []
  },
  unitsOfMeasure: {
    type: Array,
    default: () => []
  }
})

// Emits
const emit = defineEmits(['close', 'saved'])

// Composables
const { post, put } = useApi()
const { addNotification } = useNotifications()

// State
const loading = ref(false)
const form = ref({
  name: '',
  description: '',
  category: '',
  unit_of_measure: '',
  unit_price: null,
  status: 'active',
  notes: ''
})

// Computed
const modalTitle = computed(() => {
  switch (props.mode) {
    case 'create':
      return 'Nuovo Tipo Materiale'
    case 'edit':
      return 'Modifica Tipo Materiale'
    case 'view':
      return 'Dettagli Tipo Materiale'
    default:
      return 'Tipo Materiale'
  }
})

const modalDescription = computed(() => {
  switch (props.mode) {
    case 'create':
      return 'Crea un nuovo tipo di materiale per il sistema.'
    case 'edit':
      return 'Modifica le informazioni del tipo di materiale.'
    case 'view':
      return 'Visualizza i dettagli del tipo di materiale.'
    default:
      return ''
  }
})

const isFormValid = computed(() => {
  return form.value.name.trim() !== '' &&
         form.value.category !== '' &&
         form.value.unit_of_measure !== '' &&
         form.value.status !== ''
})

// Methods
const resetForm = () => {
  form.value = {
    name: '',
    description: '',
    category: '',
    unit_of_measure: '',
    unit_price: null,
    status: 'active',
    notes: ''
  }
}

const populateForm = () => {
  if (props.materialType) {
    form.value = {
      name: props.materialType.name || '',
      description: props.materialType.description || '',
      category: props.materialType.category || '',
      unit_of_measure: props.materialType.unit_of_measure || '',
      unit_price: props.materialType.unit_price || null,
      status: props.materialType.status || 'active',
      notes: props.materialType.notes || ''
    }
  }
}

const handleSubmit = async () => {
  if (!isFormValid.value || loading.value) return

  try {
    loading.value = true

    const submitData = {
      ...form.value,
      unit_price: form.value.unit_price || 0
    }

    if (props.mode === 'create') {
      await post('/api/material-types', submitData)
      addNotification('Tipo di materiale creato con successo', 'success')
    } else if (props.mode === 'edit') {
      await put(`/api/material-types/${props.materialType.id}`, submitData)
      addNotification('Tipo di materiale aggiornato con successo', 'success')
    }

    emit('saved')
  } catch (error) {
    console.error('Error saving material type:', error)
    const message = props.mode === 'create' 
      ? 'Errore nella creazione del tipo di materiale'
      : 'Errore nell\'aggiornamento del tipo di materiale'
    addNotification(message, 'error')
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('it-IT', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Watchers
watch(() => props.show, (newValue) => {
  if (newValue) {
    if (props.mode === 'create') {
      resetForm()
    } else {
      populateForm()
    }
  }
})

watch(() => props.materialType, () => {
  if (props.show && props.mode !== 'create') {
    populateForm()
  }
})
</script>

<style scoped>
.spinner {
  display: inline-block;
  position: relative;
  width: 16px;
  height: 16px;
}

.spinner::after {
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
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>