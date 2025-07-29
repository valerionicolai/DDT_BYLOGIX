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
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
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
            <!-- Basic Information Section -->
            <div>
              <h4 class="text-md font-medium text-gray-900 mb-4">Informazioni di Base</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nome Progetto *
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
                    placeholder="Es. Sviluppo Applicazione Web"
                  >
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>

                <div class="md:col-span-2">
                  <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Descrizione
                  </label>
                  <textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.description ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                    placeholder="Descrizione dettagliata del progetto..."
                  ></textarea>
                  <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                </div>

                <div>
                  <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Cliente *
                  </label>
                  <select
                    id="client_id"
                    v-model="form.client_id"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.client_id ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                  >
                    <option value="">Seleziona cliente</option>
                    <option v-for="client in clients" :key="client.id" :value="client.id">
                      {{ client.name }}
                    </option>
                  </select>
                  <p v-if="errors.client_id" class="mt-1 text-sm text-red-600">{{ errors.client_id }}</p>
                </div>

                <div>
                  <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Project Manager *
                  </label>
                  <select
                    id="user_id"
                    v-model="form.user_id"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.user_id ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                  >
                    <option value="">Seleziona project manager</option>
                    <option v-for="user in users" :key="user.id" :value="user.id">
                      {{ user.name }}
                    </option>
                  </select>
                  <p v-if="errors.user_id" class="mt-1 text-sm text-red-600">{{ errors.user_id }}</p>
                </div>
              </div>
            </div>

            <!-- Status and Priority Section -->
            <div>
              <h4 class="text-md font-medium text-gray-900 mb-4">Stato e Priorità</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                    Stato
                  </label>
                  <select
                    id="status"
                    v-model="form.status"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.status ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                  >
                    <option value="draft">Bozza</option>
                    <option value="active">Attivo</option>
                    <option value="completed">Completato</option>
                    <option value="cancelled">Annullato</option>
                    <option value="on_hold">In Pausa</option>
                  </select>
                  <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</p>
                </div>

                <div>
                  <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                    Priorità
                  </label>
                  <select
                    id="priority"
                    v-model="form.priority"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.priority ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                  >
                    <option value="low">Bassa</option>
                    <option value="medium">Media</option>
                    <option value="high">Alta</option>
                    <option value="urgent">Urgente</option>
                  </select>
                  <p v-if="errors.priority" class="mt-1 text-sm text-red-600">{{ errors.priority }}</p>
                </div>

                <div>
                  <label for="progress_percentage" class="block text-sm font-medium text-gray-700 mb-1">
                    Progresso (%)
                  </label>
                  <input
                    id="progress_percentage"
                    v-model.number="form.progress_percentage"
                    type="number"
                    min="0"
                    max="100"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.progress_percentage ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                    placeholder="0"
                  >
                  <p v-if="errors.progress_percentage" class="mt-1 text-sm text-red-600">{{ errors.progress_percentage }}</p>
                </div>
              </div>
            </div>

            <!-- Dates Section -->
            <div>
              <h4 class="text-md font-medium text-gray-900 mb-4">Date</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Data Inizio
                  </label>
                  <input
                    id="start_date"
                    v-model="form.start_date"
                    type="date"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.start_date ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                  >
                  <p v-if="errors.start_date" class="mt-1 text-sm text-red-600">{{ errors.start_date }}</p>
                </div>

                <div>
                  <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Data Fine
                  </label>
                  <input
                    id="end_date"
                    v-model="form.end_date"
                    type="date"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.end_date ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                  >
                  <p v-if="errors.end_date" class="mt-1 text-sm text-red-600">{{ errors.end_date }}</p>
                </div>

                <div>
                  <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">
                    Deadline
                  </label>
                  <input
                    id="deadline"
                    v-model="form.deadline"
                    type="date"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.deadline ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                  >
                  <p v-if="errors.deadline" class="mt-1 text-sm text-red-600">{{ errors.deadline }}</p>
                </div>
              </div>
            </div>

            <!-- Budget Section -->
            <div>
              <h4 class="text-md font-medium text-gray-900 mb-4">Budget</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label for="budget" class="block text-sm font-medium text-gray-700 mb-1">
                    Budget (€)
                  </label>
                  <input
                    id="budget"
                    v-model.number="form.budget"
                    type="number"
                    step="0.01"
                    min="0"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.budget ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                    placeholder="0.00"
                  >
                  <p v-if="errors.budget" class="mt-1 text-sm text-red-600">{{ errors.budget }}</p>
                </div>

                <div>
                  <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-1">
                    Costo Stimato (€)
                  </label>
                  <input
                    id="estimated_cost"
                    v-model.number="form.estimated_cost"
                    type="number"
                    step="0.01"
                    min="0"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.estimated_cost ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                    placeholder="0.00"
                  >
                  <p v-if="errors.estimated_cost" class="mt-1 text-sm text-red-600">{{ errors.estimated_cost }}</p>
                </div>

                <div v-if="mode === 'edit' || mode === 'view'">
                  <label for="actual_cost" class="block text-sm font-medium text-gray-700 mb-1">
                    Costo Effettivo (€)
                  </label>
                  <input
                    id="actual_cost"
                    v-model.number="form.actual_cost"
                    type="number"
                    step="0.01"
                    min="0"
                    :disabled="mode === 'view'"
                    :class="[
                      'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                      errors.actual_cost ? 'border-red-300' : 'border-gray-300',
                      mode === 'view' ? 'bg-gray-50' : 'bg-white'
                    ]"
                    placeholder="0.00"
                  >
                  <p v-if="errors.actual_cost" class="mt-1 text-sm text-red-600">{{ errors.actual_cost }}</p>
                </div>
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
                  placeholder="Note opzionali sul progetto..."
                ></textarea>
              </div>
            </div>

            <!-- View Mode Information -->
            <div v-if="mode === 'view' && project">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
                  <span
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusClass(project.status)"
                  >
                    {{ getStatusLabel(project.status) }}
                  </span>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Priorità</label>
                  <span
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getPriorityClass(project.priority)"
                  >
                    {{ getPriorityLabel(project.priority) }}
                  </span>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Data Creazione</label>
                  <p class="text-sm text-gray-900">{{ formatDate(project.created_at) }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Ultimo Aggiornamento</label>
                  <p class="text-sm text-gray-900">{{ formatDate(project.updated_at) }}</p>
                </div>
              </div>
              
              <div v-if="project.notes" class="pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ project.notes }}</p>
              </div>
            </div>
          </form>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <div v-if="mode === 'view'" class="flex space-x-3">
            <CanAccess permission="edit.project">
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
              {{ loading ? 'Salvataggio...' : (mode === 'create' ? 'Crea Progetto' : 'Aggiorna Progetto') }}
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
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import CanAccess from '@/components/CanAccess.vue'
import { useNotification } from '@/composables/useNotification'

export default {
  name: 'ProjectModal',
  components: {
    CanAccess
  },
  props: {
    show: {
      type: Boolean,
      default: false
    },
    project: {
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
    const { showNotification } = useNotification()
    
    // State
    const loading = ref(false)
    const clients = ref([])
    const users = ref([])
    const form = ref({
      name: '',
      description: '',
      client_id: '',
      user_id: '',
      status: 'draft',
      priority: 'medium',
      start_date: '',
      end_date: '',
      deadline: '',
      budget: null,
      estimated_cost: null,
      actual_cost: null,
      progress_percentage: 0,
      notes: ''
    })
    const errors = ref({})

    // Computed
    const modalTitle = computed(() => {
      switch (props.mode) {
        case 'create':
          return 'Nuovo Progetto'
        case 'edit':
          return 'Modifica Progetto'
        case 'view':
          return 'Dettagli Progetto'
        default:
          return 'Progetto'
      }
    })

    // Methods
    const resetForm = () => {
      form.value = {
        name: '',
        description: '',
        client_id: '',
        user_id: '',
        status: 'draft',
        priority: 'medium',
        start_date: '',
        end_date: '',
        deadline: '',
        budget: null,
        estimated_cost: null,
        actual_cost: null,
        progress_percentage: 0,
        notes: ''
      }
      errors.value = {}
    }

    const populateForm = () => {
      if (props.project) {
        form.value = {
          name: props.project.name || '',
          description: props.project.description || '',
          client_id: props.project.client_id || '',
          user_id: props.project.user_id || '',
          status: props.project.status || 'draft',
          priority: props.project.priority || 'medium',
          start_date: props.project.start_date || '',
          end_date: props.project.end_date || '',
          deadline: props.project.deadline || '',
          budget: props.project.budget || null,
          estimated_cost: props.project.estimated_cost || null,
          actual_cost: props.project.actual_cost || null,
          progress_percentage: props.project.progress_percentage || 0,
          notes: props.project.notes || ''
        }
      }
    }

    const validateForm = () => {
      errors.value = {}
      
      // Required fields
      if (!form.value.name.trim()) {
        errors.value.name = 'Il nome del progetto è obbligatorio'
      }
      
      if (!form.value.client_id) {
        errors.value.client_id = 'Il cliente è obbligatorio'
      }
      
      if (!form.value.user_id) {
        errors.value.user_id = 'Il project manager è obbligatorio'
      }
      
      // Date validations
      if (form.value.start_date && form.value.end_date) {
        if (new Date(form.value.start_date) > new Date(form.value.end_date)) {
          errors.value.end_date = 'La data di fine deve essere successiva alla data di inizio'
        }
      }
      
      if (form.value.deadline && form.value.end_date) {
        if (new Date(form.value.deadline) < new Date(form.value.end_date)) {
          errors.value.deadline = 'La deadline deve essere successiva o uguale alla data di fine'
        }
      }
      
      // Budget validations
      if (form.value.budget && form.value.budget < 0) {
        errors.value.budget = 'Il budget deve essere un valore positivo'
      }
      
      if (form.value.estimated_cost && form.value.estimated_cost < 0) {
        errors.value.estimated_cost = 'Il costo stimato deve essere un valore positivo'
      }
      
      if (form.value.actual_cost && form.value.actual_cost < 0) {
        errors.value.actual_cost = 'Il costo effettivo deve essere un valore positivo'
      }
      
      // Progress validation
      if (form.value.progress_percentage < 0 || form.value.progress_percentage > 100) {
        errors.value.progress_percentage = 'Il progresso deve essere compreso tra 0 e 100'
      }
      
      return Object.keys(errors.value).length === 0
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
        showNotification(
          props.mode === 'create' ? 'Progetto creato con successo' : 'Progetto aggiornato con successo',
          'success'
        )
      } catch (error) {
        console.error('Errore nel salvataggio:', error)
        showNotification('Errore nel salvataggio del progetto', 'error')
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
        emit('save', { ...props.project, _mode: 'edit' })
      })
    }

    const loadClients = async () => {
      // Simulate API call - replace with actual API call
      clients.value = [
        { id: 1, name: 'Mario Rossi' },
        { id: 2, name: 'Giulia Bianchi' },
        { id: 3, name: 'Luca Verdi' },
        { id: 4, name: 'Anna Neri' }
      ]
    }

    const loadUsers = async () => {
      // Simulate API call - replace with actual API call
      users.value = [
        { id: 1, name: 'Admin User' },
        { id: 2, name: 'Project Manager 1' },
        { id: 3, name: 'Project Manager 2' }
      ]
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

    const getStatusClass = (status) => {
      const classes = {
        'draft': 'bg-gray-100 text-gray-800',
        'active': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
        'on_hold': 'bg-yellow-100 text-yellow-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const getStatusLabel = (status) => {
      const labels = {
        'draft': 'Bozza',
        'active': 'Attivo',
        'completed': 'Completato',
        'cancelled': 'Annullato',
        'on_hold': 'In Pausa'
      }
      return labels[status] || status
    }

    const getPriorityClass = (priority) => {
      const classes = {
        'low': 'bg-green-100 text-green-800',
        'medium': 'bg-yellow-100 text-yellow-800',
        'high': 'bg-orange-100 text-orange-800',
        'urgent': 'bg-red-100 text-red-800'
      }
      return classes[priority] || 'bg-gray-100 text-gray-800'
    }

    const getPriorityLabel = (priority) => {
      const labels = {
        'low': 'Bassa',
        'medium': 'Media',
        'high': 'Alta',
        'urgent': 'Urgente'
      }
      return labels[priority] || priority
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

    watch(() => props.project, () => {
      if (props.show && props.mode !== 'create') {
        populateForm()
      }
    })

    // Load data on mount
    onMounted(() => {
      loadClients()
      loadUsers()
    })

    return {
      loading,
      clients,
      users,
      form,
      errors,
      modalTitle,
      resetForm,
      populateForm,
      validateForm,
      handleSubmit,
      closeModal,
      switchToEditMode,
      formatDate,
      getStatusClass,
      getStatusLabel,
      getPriorityClass,
      getPriorityLabel
    }
  }
}
</script>