<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Impostazioni</h1>
        <p class="mt-2 text-gray-600">Configura le preferenze dell'applicazione</p>
      </div>

      <!-- Settings Sections -->
      <div class="space-y-6">
        <!-- Profile Settings -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Profilo Utente</h2>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                <input
                  v-model="settings.profile.name"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input
                  v-model="settings.profile.email"
                  type="email"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ruolo</label>
                <select
                  v-model="settings.profile.role"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="admin">Amministratore</option>
                  <option value="manager">Project Manager</option>
                  <option value="developer">Sviluppatore</option>
                  <option value="viewer">Visualizzatore</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lingua</label>
                <select
                  v-model="settings.profile.language"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="it">Italiano</option>
                  <option value="en">English</option>
                  <option value="es">Español</option>
                  <option value="fr">Français</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Notifiche</h2>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-medium text-gray-900">Notifiche Email</h3>
                  <p class="text-sm text-gray-500">Ricevi aggiornamenti via email</p>
                </div>
                <button
                  @click="settings.notifications.email = !settings.notifications.email"
                  :class="[
                    settings.notifications.email ? 'bg-primary-600' : 'bg-gray-200',
                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2'
                  ]"
                >
                  <span
                    :class="[
                      settings.notifications.email ? 'translate-x-5' : 'translate-x-0',
                      'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                    ]"
                  ></span>
                </button>
              </div>

              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-medium text-gray-900">Notifiche Push</h3>
                  <p class="text-sm text-gray-500">Ricevi notifiche nel browser</p>
                </div>
                <button
                  @click="settings.notifications.push = !settings.notifications.push"
                  :class="[
                    settings.notifications.push ? 'bg-primary-600' : 'bg-gray-200',
                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2'
                  ]"
                >
                  <span
                    :class="[
                      settings.notifications.push ? 'translate-x-5' : 'translate-x-0',
                      'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                    ]"
                  ></span>
                </button>
              </div>

              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-medium text-gray-900">Aggiornamenti Progetti</h3>
                  <p class="text-sm text-gray-500">Notifiche per cambiamenti nei progetti</p>
                </div>
                <button
                  @click="settings.notifications.projects = !settings.notifications.projects"
                  :class="[
                    settings.notifications.projects ? 'bg-primary-600' : 'bg-gray-200',
                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2'
                  ]"
                >
                  <span
                    :class="[
                      settings.notifications.projects ? 'translate-x-5' : 'translate-x-0',
                      'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                    ]"
                  ></span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- System Settings -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Sistema</h2>
          </div>
          <div class="p-6">
            <div class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tema</label>
                <select
                  v-model="settings.system.theme"
                  class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="light">Chiaro</option>
                  <option value="dark">Scuro</option>
                  <option value="auto">Automatico</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fuso Orario</label>
                <select
                  v-model="settings.system.timezone"
                  class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="Europe/Rome">Europa/Roma</option>
                  <option value="Europe/London">Europa/Londra</option>
                  <option value="America/New_York">America/New York</option>
                  <option value="Asia/Tokyo">Asia/Tokyo</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Formato Data</label>
                <select
                  v-model="settings.system.dateFormat"
                  class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                  <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                  <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Security Settings -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Sicurezza</h2>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-medium text-gray-900">Autenticazione a Due Fattori</h3>
                  <p class="text-sm text-gray-500">Aggiungi un livello extra di sicurezza</p>
                </div>
                <button
                  @click="settings.security.twoFactor = !settings.security.twoFactor"
                  :class="[
                    settings.security.twoFactor ? 'bg-primary-600' : 'bg-gray-200',
                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2'
                  ]"
                >
                  <span
                    :class="[
                      settings.security.twoFactor ? 'translate-x-5' : 'translate-x-0',
                      'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                    ]"
                  ></span>
                </button>
              </div>

              <div>
                <button
                  @click="showChangePassword = true"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                  Cambia Password
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end space-x-4">
          <button
            @click="resetSettings"
            class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            Ripristina
          </button>
          <button
            @click="saveSettings"
            class="px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            Salva Modifiche
          </button>
        </div>
      </div>

      <!-- Change Password Modal -->
      <div v-if="showChangePassword" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cambia Password</h3>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Attuale</label>
                <input
                  type="password"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nuova Password</label>
                <input
                  type="password"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Conferma Password</label>
                <input
                  type="password"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
              </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
              <button
                @click="showChangePassword = false"
                class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300"
              >
                Annulla
              </button>
              <button
                @click="changePassword"
                class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500"
              >
                Cambia Password
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'

export default {
  name: 'Settings',
  setup() {
    const showChangePassword = ref(false)
    
    const settings = ref({
      profile: {
        name: 'Admin User',
        email: 'admin@dttbylogix.com',
        role: 'admin',
        language: 'it'
      },
      notifications: {
        email: true,
        push: true,
        projects: true
      },
      system: {
        theme: 'light',
        timezone: 'Europe/Rome',
        dateFormat: 'DD/MM/YYYY'
      },
      security: {
        twoFactor: false
      }
    })

    const originalSettings = ref({})

    const saveSettings = () => {
      // Qui implementeremo il salvataggio delle impostazioni
      console.log('Salvataggio impostazioni:', settings.value)
      
      // Simulazione salvataggio
      alert('Impostazioni salvate con successo!')
      
      // Aggiorna le impostazioni originali
      originalSettings.value = JSON.parse(JSON.stringify(settings.value))
    }

    const resetSettings = () => {
      settings.value = JSON.parse(JSON.stringify(originalSettings.value))
    }

    const changePassword = () => {
      // Qui implementeremo il cambio password
      console.log('Cambio password richiesto')
      showChangePassword.value = false
      alert('Funzionalità in sviluppo')
    }

    const loadSettings = () => {
      // Qui caricheremo le impostazioni dal backend
      originalSettings.value = JSON.parse(JSON.stringify(settings.value))
    }

    onMounted(() => {
      loadSettings()
    })

    return {
      settings,
      showChangePassword,
      saveSettings,
      resetSettings,
      changePassword
    }
  }
}
</script>