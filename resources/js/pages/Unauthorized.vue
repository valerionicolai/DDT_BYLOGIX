<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div class="text-center">
        <div class="mx-auto h-24 w-24 text-red-500">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
          </svg>
        </div>
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          Accesso Negato
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Non hai i permessi necessari per accedere a questa pagina.
        </p>
        <div class="mt-4 text-xs text-gray-500">
          <p v-if="errorType === 'insufficient_permissions'">
            I tuoi permessi attuali non sono sufficienti per visualizzare questa risorsa.
          </p>
          <p v-else-if="errorType === 'session_expired'">
            La tua sessione è scaduta. Effettua nuovamente il login.
          </p>
          <p v-else>
            Si è verificato un errore durante la verifica dei permessi.
          </p>
        </div>
      </div>
      
      <div class="space-y-4">
        <button
          @click="goBack"
          class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Torna Indietro
        </button>
        
        <button
          @click="goToDashboard"
          class="group relative w-full flex justify-center py-2 px-4 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Vai alla Dashboard
        </button>
        
        <button
          v-if="errorType === 'session_expired'"
          @click="logout"
          class="group relative w-full flex justify-center py-2 px-4 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
          Effettua Logout
        </button>
      </div>
      
      <div class="text-center">
        <p class="text-xs text-gray-500">
          Se ritieni che questo sia un errore, contatta l'amministratore del sistema.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const errorType = computed(() => route.query.error || 'unknown')

const goBack = () => {
  if (window.history.length > 1) {
    router.go(-1)
  } else {
    router.push({ name: 'Dashboard' })
  }
}

const goToDashboard = () => {
  router.push({ name: 'Dashboard' })
}

const logout = async () => {
  try {
    await authStore.logout()
    router.push({ name: 'Login' })
  } catch (error) {
    console.error('Logout error:', error)
    // Forza il logout locale anche se la richiesta fallisce
    router.push({ name: 'Login' })
  }
}

onMounted(() => {
  // Log dell'errore per debugging
  console.warn('Unauthorized access attempt:', {
    route: route.fullPath,
    error: errorType.value,
    user: authStore.user,
    timestamp: new Date().toISOString()
  })
})
</script>