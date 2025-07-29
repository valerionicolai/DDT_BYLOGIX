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
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
        <!-- Header -->
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
              {{ modalTitle }}
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

          <!-- Form -->
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Personal Information Section -->
            <div>
              <h4 class="text-md font-medium text-gray-900 mb-4">Informazioni Personali</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nome Completo *
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.name ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                    placeholder="Es. Mario Rossi"
                  >
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>

                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email *
                  </label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.email ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                    placeholder="mario.rossi@email.com"
                  >
                  <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                </div>

                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                    Telefono
                  </label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.phone ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                    placeholder="+39 123 456 7890"
                  >
                  <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</p>
                </div>

                <div>
                  <label for="company" class="block text-sm font-medium text-gray-700 mb-1">
                    Azienda
                  </label>
                  <input
                    id="company"
                    v-model="form.company"
                    type="text"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.company ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                    placeholder="Nome azienda (opzionale)"
                  >
                  <p v-if="errors.company" class="mt-1 text-sm text-red-600">{{ errors.company }}</p>
                </div>
              </div>
            </div>

            <!-- Address Information Section -->
            <div>
              <h4 class="text-md font-medium text-gray-900 mb-4">Indirizzo</h4>
              <div class="grid grid-cols-1 gap-4">
                <div>
                  <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                    Indirizzo
                  </label>
                  <input
                    id="address"
                    v-model="form.address"
                    type="text"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.address ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                    placeholder="Via, numero civico"
                  >
                  <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                      Città
                    </label>
                    <input
                      id="city"
                      v-model="form.city"
                      type="text"
                      :disabled="mode === 'view'"
                      :class="[
                        'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                        errors.city ? 'border-red-300' : 'border-gray-300',
                        mode === 'view' ? 'bg-gray-50' : 'bg-white'
                      ]"
                      placeholder="Milano"
                    >
                    <p v-if="errors.city" class="mt-1 text-sm text-red-600">{{ errors.city }}</p>
                  </div>

                  <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                      CAP
                    </label>
                    <input
                      id="postal_code"
                      v-model="form.postal_code"
                      type="text"
                      :disabled="mode === 'view'"
                      :class="[
                        'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                        errors.postal_code ? 'border-red-300' : 'border-gray-300',
                        mode === 'view' ? 'bg-gray-50' : 'bg-white'
                      ]"
                      placeholder="20100"
                    >
                    <p v-if="errors.postal_code" class="mt-1 text-sm text-red-600">{{ errors.postal_code }}</p>
                  </div>

                  <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                      Paese
                    </label>
                    <select
                      id="country"
                      v-model="form.country"
                      :disabled="mode === 'view'"
                      :class="[
                        'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                        errors.country ? 'border-red-300' : 'border-gray-300',
                        mode === 'view' ? 'bg-gray-50' : 'bg-white'
                      ]"
                    >
                      <option value="">Seleziona paese</option>
                      <option value="Italia">Italia</option>
                      <option value="Francia">Francia</option>
                      <option value="Germania">Germania</option>
                      <option value="Spagna">Spagna</option>
                      <option value="Svizzera">Svizzera</option>
                      <option value="Austria">Austria</option>
                    </select>
                    <p v-if="errors.country" class="mt-1 text-sm text-red-600">{{ errors.country }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Status Section (only for edit mode) -->
            <div v-if="mode === 'edit'">
              <h4 class="text-md font-medium text-gray-900 mb-4">Stato</h4>
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                  Stato Cliente
                </label>
                <select
                  id="status"
                  v-model="form.status"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="active">Attivo</option>
                  <option value="inactive">Inattivo</option>
                </select>
              </div>
            </div>

            <!-- Notes Section -->
            <div v-if="mode !== 'view'">
              <h4 class="text-md font-medium text-gray-900 mb-4">Note</h4>
              <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                  Note Aggiuntive
                </label>
                <textarea
                  id="notes"
                  v-model="form.notes"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                  placeholder="Note opzionali sul cliente..."
                ></textarea>
              </div>
            </div>

            <!-- View Mode Information -->
            <div v-if="mode === 'view' && client">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
                  <span
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="client.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  >
                    {{ client.status === 'active' ? 'Attivo' : 'Inattivo' }}
                  </span>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Progetti Associati</label>
                  <p class="text-sm text-gray-900">{{ client.projects_count || 0 }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Data Creazione</label>
                  <p class="text-sm text-gray-900">{{ formatDate(client.created_at) }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Ultimo Aggiornamento</label>
                  <p class="text-sm text-gray-900">{{ formatDate(client.updated_at) }}</p>
                </div>
              </div>
              
              <div v-if="client.notes" class="pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ client.notes }}</p>
              </div>
            </div>
          </form>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <div v-if="mode === 'view'" class="flex space-x-3">
            <CanAccess permission="edit.client">
              <button
                @click="switchToEditMode"
                type="button"
                class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm"
              >
                Modifica
              </button>
            </CanAccess>
            <button
              @click="closeModal"
              type="button"
              class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm"
            >
              Chiudi
            </button>
          </div>
          
          <div v-else class="flex space-x-3">
            <button
              @click="handleSubmit"
              :disabled="loading"
              type="submit"
              class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed sm:text-sm"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Salvataggio...' : (mode === 'create' ? 'Crea Cliente' : 'Aggiorna Cliente') }}
            </button>
            <button
              @click="closeModal"
              type="button"
              class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm"
            >
              Annulla
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, nextTick } from 'vue'
import CanAccess from '@/components/CanAccess.vue'

export default {
  name: 'ClientModal',
  components: {
    CanAccess
  },
  props: {
    show: {
      type: Boolean,
      default: false
    },
    client: {
      type: Object,
      default: null
    },
    mode: {
      type: String,
      default: 'create', // 'create', 'edit', 'view'
      validator: (value) => ['create', 'edit', 'view'].includes(value)
    }
  },
  emits: ['close', 'save'],
  setup(props, { emit }) {
    // State
    const loading = ref(false)
    const form = ref({
      name: '',
      email: '',
      phone: '',
      company: '',
      address: '',
      city: '',
      postal_code: '',
      country: 'Italia',
      status: 'active',
      notes: ''
    })
    const errors = ref({})

    // Computed
    const modalTitle = computed(() => {
      switch (props.mode) {
        case 'create':
          return 'Nuovo Cliente'
        case 'edit':
          return 'Modifica Cliente'
        case 'view':
          return 'Dettagli Cliente'
        default:
          return 'Cliente'
      }
    })

    // Methods
    const resetForm = () => {
      form.value = {
        name: '',
        email: '',
        phone: '',
        company: '',
        address: '',
        city: '',
        postal_code: '',
        country: 'Italia',
        status: 'active',
        notes: ''
      }
      errors.value = {}
    }

    const populateForm = () => {
      if (props.client) {
        form.value = {
          name: props.client.name || '',
          email: props.client.email || '',
          phone: props.client.phone || '',
          company: props.client.company || '',
          address: props.client.address || '',
          city: props.client.city || '',
          postal_code: props.client.postal_code || '',
          country: props.client.country || 'Italia',
          status: props.client.status || 'active',
          notes: props.client.notes || ''
        }
      }
    }

    const validateForm = () => {
      errors.value = {}
      
      // Required fields
      if (!form.value.name.trim()) {
        errors.value.name = 'Il nome è obbligatorio'
      }
      
      if (!form.value.email.trim()) {
        errors.value.email = 'L\'email è obbligatoria'
      } else if (!isValidEmail(form.value.email)) {
        errors.value.email = 'Inserisci un\'email valida'
      }
      
      // Optional validations
      if (form.value.phone && !isValidPhone(form.value.phone)) {
        errors.value.phone = 'Inserisci un numero di telefono valido'
      }
      
      if (form.value.postal_code && !isValidPostalCode(form.value.postal_code)) {
        errors.value.postal_code = 'Inserisci un CAP valido'
      }
      
      return Object.keys(errors.value).length === 0
    }

    const isValidEmail = (email) => {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return emailRegex.test(email)
    }

    const isValidPhone = (phone) => {
      const phoneRegex = /^[\+]?[0-9\s\-\(\)]{8,}$/
      return phoneRegex.test(phone)
    }

    const isValidPostalCode = (postalCode) => {
      const postalCodeRegex = /^[0-9]{5}$/
      return postalCodeRegex.test(postalCode)
    }

    const handleSubmit = async () => {
      if (!validateForm()) {
        return
      }
      
      loading.value = true
      
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        emit('save', { ...form.value })
      } catch (error) {
        console.error('Errore nel salvataggio:', error)
      } finally {
        loading.value = false
      }
    }

    const closeModal = () => {
      emit('close')
    }

    const switchToEditMode = () => {
      emit('close')
      nextTick(() => {
        emit('save', { ...props.client, _mode: 'edit' })
      })
    }

    const formatDate = (dateString) => {
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

    watch(() => props.client, () => {
      if (props.show && props.mode !== 'create') {
        populateForm()
      }
    })

    return {
      // State
      loading,
      form,
      errors,
      
      // Computed
      modalTitle,
      
      // Methods
      resetForm,
      populateForm,
      validateForm,
      handleSubmit,
      closeModal,
      switchToEditMode,
      formatDate
    }
  }
}
</script>