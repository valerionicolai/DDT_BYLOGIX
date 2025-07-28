import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Import delle pagine
import Dashboard from '@/pages/Dashboard.vue'
import Projects from '@/pages/Projects.vue'
import Settings from '@/pages/Settings.vue'
import Login from '@/pages/Login.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: {
      title: 'Login - DTT by Logix',
      requiresGuest: true
    }
  },
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
    meta: {
      title: 'Dashboard - DTT by Logix',
      requiresAuth: true
    }
  },
  {
    path: '/projects',
    name: 'Projects',
    component: Projects,
    meta: {
      title: 'Progetti - DTT by Logix',
      requiresAuth: true
    }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
    meta: {
      title: 'Impostazioni - DTT by Logix',
      requiresAuth: true
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  // Aggiorna il titolo della pagina
  if (to.meta.title) {
    document.title = to.meta.title
  }

  const authStore = useAuthStore()

  // Se c'è un token ma non abbiamo i dati dell'utente, prova a recuperarli
  if (authStore.token && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch (error) {
      console.warn('Failed to fetch user:', error)
    }
  }

  // Controlla se la route richiede autenticazione
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login', query: { redirect: to.fullPath } })
    return
  }

  // Controlla se la route è solo per ospiti (non autenticati)
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next({ name: 'Dashboard' })
    return
  }

  next()
})

export default router