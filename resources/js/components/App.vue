<template>
  <div>
    <!-- Loading screen durante l'inizializzazione -->
    <div v-if="authStore.loading && !authStore.user" class="min-h-screen flex items-center justify-center bg-gray-50">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Caricamento...</p>
      </div>
    </div>

    <!-- Layout principale per pagine autenticate -->
    <MainLayout v-else-if="shouldShowMainLayout">
      <router-view />
    </MainLayout>

    <!-- Layout semplice per pagine di autenticazione -->
    <router-view v-else />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import MainLayout from './MainLayout.vue'

const route = useRoute()
const authStore = useAuthStore()

// Determina se mostrare il layout principale
const shouldShowMainLayout = computed(() => {
  // Non mostrare il layout principale per le pagine di autenticazione
  const authPages = ['Login', 'Register']
  return !authPages.includes(route.name)
})

// Inizializza l'autenticazione al caricamento dell'app
onMounted(async () => {
  if (authStore.token && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch (error) {
      console.warn('Failed to initialize auth:', error)
    }
  }
})
</script>

<style scoped>
/* Stili specifici del componente se necessari */
</style>