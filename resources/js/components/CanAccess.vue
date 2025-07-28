<template>
  <slot v-if="hasAccess" />
  <div v-else-if="showFallback" class="text-gray-500 text-sm italic">
    {{ fallbackMessage }}
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePermissions } from '@/composables/usePermissions'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  // Permesso singolo richiesto
  permission: {
    type: String,
    default: null
  },
  
  // Lista di permessi (tutti richiesti)
  allPermissions: {
    type: Array,
    default: () => []
  },
  
  // Lista di permessi (almeno uno richiesto)
  anyPermissions: {
    type: Array,
    default: () => []
  },
  
  // Richiede ruolo admin
  requiresAdmin: {
    type: Boolean,
    default: false
  },
  
  // Richiede autenticazione
  requiresAuth: {
    type: Boolean,
    default: false
  },
  
  // Mostra messaggio di fallback se non autorizzato
  showFallback: {
    type: Boolean,
    default: false
  },
  
  // Messaggio di fallback personalizzato
  fallbackMessage: {
    type: String,
    default: 'Non hai i permessi per visualizzare questo contenuto.'
  }
})

const authStore = useAuthStore()
const { hasPermission, hasAllPermissions, hasAnyPermission } = usePermissions()

const hasAccess = computed(() => {
  // Se richiede autenticazione e l'utente non è autenticato
  if (props.requiresAuth && !authStore.isAuthenticated) {
    return false
  }
  
  // Se richiede admin e l'utente non è admin
  if (props.requiresAdmin && !authStore.isAdmin) {
    return false
  }
  
  // Se è specificato un permesso singolo
  if (props.permission && !hasPermission(props.permission)) {
    return false
  }
  
  // Se sono specificati permessi multipli (tutti richiesti)
  if (props.allPermissions.length > 0 && !hasAllPermissions(props.allPermissions)) {
    return false
  }
  
  // Se sono specificati permessi multipli (almeno uno richiesto)
  if (props.anyPermissions.length > 0 && !hasAnyPermission(props.anyPermissions)) {
    return false
  }
  
  return true
})
</script>