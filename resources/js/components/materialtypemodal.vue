<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
  >
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        aria-hidden="true"
        @click="closeModal"
      ></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <!-- Header -->
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
              {{ isEditing ? 'Modifica Tipo di Materiale' : 'Nuovo Tipo di Materiale' }}
            </h3>
            <button
              @click="closeModal"
              class="rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form id="material-type-form" @submit.prevent="handleSubmit">

                <div class="mt-5 space-y-4">
                  <!-- Name -->
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                      Nome <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      required
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                      :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.name }"
                      placeholder="Es. Cemento Portland"
                    />
                    <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
                  </div>

                  <!-- Description -->
                  <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">
                      Descrizione
                    </label>
                    <textarea
                      id="description"
                      v-model="form.description"
                      rows="3"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                      :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.description }"
                      placeholder="Descrizione dettagliata del materiale..."
                    ></textarea>
                    <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description[0] }}</p>
                  </div>

                  <!-- Category -->
                  <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">
                      Categoria <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative">
                      <select
                        id="category"
                        v-model="form.category"
                        required
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                        :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.category }"
                      >
                        <option value="">Seleziona una categoria</option>
                        <option v-for="category in categories" :key="category" :value="category">
                          {{ category }}
                        </option>
                      </select>
                    </div>
                    <p v-if="errors.category" class="mt-1 text-sm text-red-600">{{ errors.category[0] }}</p>
                  </div>

                  <!-- Unit of Measure -->
                  <div>
                    <label for="unit_of_measure" class="block text-sm font-medium text-gray-700">
                      Unità di Misura <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative">
                      <select
                        id="unit_of_measure"
                        v-model="form.unit_of_measure"
                        required
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                        :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.unit_of_measure }"
                      >
                        <option value="">Seleziona un'unità</option>
                        <option v-for="unit in unitsOfMeasure" :key="unit" :value="unit">
                          {{ unit }}
                        </option>
                      </select>
                    </div>
                    <p v-if="errors.unit_of_measure" class="mt-1 text-sm text-red-600">{{ errors.unit_of_measure[0] }}</p>
                  </div>

                  <!-- Default Price -->
                  <div>
                    <label for="default_price" class="block text-sm font-medium text-gray-700">
                      Prezzo Predefinito (€)
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">€</span>
                      </div>
                      <input
                        id="default_price"
                        v-model="form.default_price"
                        type="number"
                        step="0.01"
                        min="0"
                        class="block w-full pl-7 pr-12 border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                        :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.default_price }"
                        placeholder="0.00"
                      />
                    </div>
                    <p v-if="errors.default_price" class="mt-1 text-sm text-red-600">{{ errors.default_price[0] }}</p>
                  </div>

                  <!-- Status -->
                  <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">
                      Stato <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                      <select
                        id="status"
                        v-model="form.status"
                        required
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                        :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.status }"
                      >
                        <option value="active">Attivo</option>
                        <option value="inactive">Inattivo</option>
                      </select>
                    </div>
                    <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status[0] }}</p>
                  </div>

                  <!-- Properties -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Proprietà Aggiuntive
                    </label>
                    <div class="space-y-2">
                      <div v-for="(property, index) in form.properties" :key="index" class="flex items-center space-x-2">
                        <input
                          v-model="property.key"
                          type="text"
                          placeholder="Chiave"
                          class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                        />
                        <input
                          v-model="property.value"
                          type="text"
                          placeholder="Valore"
                          class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                        />
                        <button
                          type="button"
                          @click="removeProperty(index)"
                          class="p-2 text-red-600 hover:text-red-800"
                        >
                          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </div>
                      <button
                        type="button"
                        @click="addProperty"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                      >
                        <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Aggiungi Proprietà
                      </button>
                    </div>
                    <p v-if="errors.properties" class="mt-1 text-sm text-red-600">{{ errors.properties[0] }}</p>
                  </div>
                </div>

                <!-- Error Message -->
                <div v-if="submitError" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                      </svg>
                    </div>
                    <div class="ml-3">
                      <h3 class="text-sm font-medium text-red-800">
                        Errore durante il salvataggio
                      </h3>
                      <div class="mt-2 text-sm text-red-700">
                        {{ submitError }}
                      </div>
                    </div>
                  </div>
                </div>

          </form>
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            type="submit"
            form="material-type-form"
            :disabled="submitting"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="submitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ submitting ? 'Salvataggio...' : (isEditing ? 'Aggiorna' : 'Crea') }}
          </button>
          <button
            type="button"
            @click="closeModal"
            :disabled="submitting"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Annulla
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  material: {
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
const emit = defineEmits(['close', 'save'])

// Methods
const closeModal = () => {
  emit('close')
}

// State
const submitting = ref(false)
const submitError = ref('')
const errors = ref({})

// Form data
const form = ref({
  name: '',
  description: '',
  category: '',
  unit_of_measure: '',
  default_price: '',
  status: 'active',
  properties: []
})

// Computed
const isEditing = computed(() => {
  return props.material && props.material.id
})

// Watch for material changes
watch(() => props.material, (newMaterial) => {
  if (newMaterial) {
    form.value = {
      name: newMaterial.name || '',
      description: newMaterial.description || '',
      category: newMaterial.category || '',
      unit_of_measure: newMaterial.unit_of_measure || '',
      default_price: newMaterial.default_price || '',
      status: newMaterial.status || 'active',
      properties: newMaterial.properties ? Object.entries(newMaterial.properties).map(([key, value]) => ({ key, value })) : []
    }
  } else {
    resetForm()
  }
  errors.value = {}
  submitError.value = ''
}, { immediate: true })

// Watch for show changes
watch(() => props.show, (show) => {
  if (!show) {
    errors.value = {}
    submitError.value = ''
  }
})

// Methods
const resetForm = () => {
  form.value = {
    name: '',
    description: '',
    category: '',
    unit_of_measure: '',
    default_price: '',
    status: 'active',
    properties: []
  }
}

const addProperty = () => {
  form.value.properties.push({ key: '', value: '' })
}

const removeProperty = (index) => {
  form.value.properties.splice(index, 1)
}

const validateForm = () => {
  const newErrors = {}

  if (!form.value.name?.trim()) {
    newErrors.name = ['Il nome è obbligatorio']
  }

  if (!form.value.category?.trim()) {
    newErrors.category = ['La categoria è obbligatoria']
  }

  if (!form.value.unit_of_measure?.trim()) {
    newErrors.unit_of_measure = ['L\'unità di misura è obbligatoria']
  }

  if (form.value.default_price && (isNaN(form.value.default_price) || parseFloat(form.value.default_price) < 0)) {
    newErrors.default_price = ['Il prezzo deve essere un numero positivo']
  }

  if (!form.value.status) {
    newErrors.status = ['Lo stato è obbligatorio']
  }

  // Validate properties
  const invalidProperties = form.value.properties.some(prop => 
    (prop.key && !prop.value) || (!prop.key && prop.value)
  )
  if (invalidProperties) {
    newErrors.properties = ['Tutte le proprietà devono avere sia chiave che valore']
  }

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  submitting.value = true
  submitError.value = ''

  try {
    // Prepare data
    const data = {
      name: form.value.name.trim(),
      description: form.value.description?.trim() || null,
      category: form.value.category,
      unit_of_measure: form.value.unit_of_measure,
      default_price: form.value.default_price ? parseFloat(form.value.default_price) : null,
      status: form.value.status,
      properties: form.value.properties
        .filter(prop => prop.key && prop.value)
        .reduce((acc, prop) => {
          acc[prop.key] = prop.value
          return acc
        }, {})
    }

    emit('save', data)
  } catch (error) {
    console.error('Error submitting form:', error)
    
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      submitError.value = error.response?.data?.message || 'Si è verificato un errore durante il salvataggio'
    }
  } finally {
    submitting.value = false
  }
}
</script>