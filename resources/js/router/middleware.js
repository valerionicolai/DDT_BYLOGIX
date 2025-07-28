import { useAuthStore } from '@/stores/auth'
import { usePermissions } from '@/composables/usePermissions'

/**
 * Middleware per la gestione dell'autenticazione nelle route
 */
export const authMiddleware = {
  /**
   * Verifica se l'utente è autenticato
   */
  requiresAuth: async (to, from, next) => {
    const authStore = useAuthStore()
    
    // Se c'è un token ma non abbiamo i dati dell'utente, prova a recuperarli
    if (authStore.token && !authStore.user) {
      try {
        await authStore.fetchUser()
      } catch (error) {
        console.warn('Failed to fetch user:', error)
        // Se il fetch fallisce, reindirizza al login
        return next({ 
          name: 'Login', 
          query: { redirect: to.fullPath, error: 'session_expired' } 
        })
      }
    }

    if (!authStore.isAuthenticated) {
      return next({ 
        name: 'Login', 
        query: { redirect: to.fullPath } 
      })
    }

    next()
  },

  /**
   * Verifica che l'utente NON sia autenticato (per pagine come login/register)
   */
  requiresGuest: (to, from, next) => {
    const authStore = useAuthStore()
    
    if (authStore.isAuthenticated) {
      return next({ name: 'Dashboard' })
    }

    next()
  },

  /**
   * Verifica che l'utente sia un admin
   */
  requiresAdmin: async (to, from, next) => {
    const authStore = useAuthStore()
    
    // Prima verifica l'autenticazione
    await authMiddleware.requiresAuth(to, from, (result) => {
      if (result && typeof result === 'object') {
        // Se requiresAuth ha restituito un redirect, seguilo
        return next(result)
      }
    })

    if (!authStore.isAdmin) {
      return next({ 
        name: 'Dashboard', 
        query: { error: 'insufficient_permissions' } 
      })
    }

    next()
  },

  /**
   * Verifica permessi specifici
   */
  requiresPermission: (permission) => {
    return async (to, from, next) => {
      const authStore = useAuthStore()
      const { hasPermission } = usePermissions()
      
      // Prima verifica l'autenticazione
      await authMiddleware.requiresAuth(to, from, (result) => {
        if (result && typeof result === 'object') {
          // Se requiresAuth ha restituito un redirect, seguilo
          return next(result)
        }
      })

      if (!hasPermission(permission)) {
        return next({ 
          name: 'Dashboard', 
          query: { error: 'insufficient_permissions' } 
        })
      }

      next()
    }
  },

  /**
   * Verifica permessi multipli (tutti richiesti)
   */
  requiresAllPermissions: (permissions) => {
    return async (to, from, next) => {
      const authStore = useAuthStore()
      const { hasAllPermissions } = usePermissions()
      
      // Prima verifica l'autenticazione
      await authMiddleware.requiresAuth(to, from, (result) => {
        if (result && typeof result === 'object') {
          return next(result)
        }
      })

      if (!hasAllPermissions(permissions)) {
        return next({ 
          name: 'Dashboard', 
          query: { error: 'insufficient_permissions' } 
        })
      }

      next()
    }
  },

  /**
   * Verifica permessi multipli (almeno uno richiesto)
   */
  requiresAnyPermission: (permissions) => {
    return async (to, from, next) => {
      const authStore = useAuthStore()
      const { hasAnyPermission } = usePermissions()
      
      // Prima verifica l'autenticazione
      await authMiddleware.requiresAuth(to, from, (result) => {
        if (result && typeof result === 'object') {
          return next(result)
        }
      })

      if (!hasAnyPermission(permissions)) {
        return next({ 
          name: 'Dashboard', 
          query: { error: 'insufficient_permissions' } 
        })
      }

      next()
    }
  }
}

/**
 * Applica i middleware a una route in base ai suoi meta
 */
export const applyRouteMiddleware = async (to, from, next) => {
  const meta = to.meta || {}
  
  // Aggiorna il titolo della pagina
  if (meta.title) {
    document.title = meta.title
  }

  try {
    // Applica i middleware in ordine di priorità
    if (meta.requiresGuest) {
      return authMiddleware.requiresGuest(to, from, next)
    }

    if (meta.requiresAdmin) {
      return await authMiddleware.requiresAdmin(to, from, next)
    }

    if (meta.requiresPermission) {
      return await authMiddleware.requiresPermission(meta.requiresPermission)(to, from, next)
    }

    if (meta.requiresAllPermissions) {
      return await authMiddleware.requiresAllPermissions(meta.requiresAllPermissions)(to, from, next)
    }

    if (meta.requiresAnyPermission) {
      return await authMiddleware.requiresAnyPermission(meta.requiresAnyPermission)(to, from, next)
    }

    if (meta.requiresAuth) {
      return await authMiddleware.requiresAuth(to, from, next)
    }

    // Se non ci sono middleware specifici, continua
    next()
  } catch (error) {
    console.error('Route middleware error:', error)
    next({ 
      name: 'Dashboard', 
      query: { error: 'middleware_error' } 
    })
  }
}