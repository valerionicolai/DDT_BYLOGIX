import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token'))
  const loading = ref(false)
  const error = ref(null)
  const sessionTimeout = ref(null)
  const lastActivity = ref(Date.now())

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isUser = computed(() => user.value?.role === 'user')
  const userRole = computed(() => user.value?.role || null)
  const userName = computed(() => user.value?.name || '')
  const userEmail = computed(() => user.value?.email || '')

  // Session management
  const SESSION_TIMEOUT = 30 * 60 * 1000 // 30 minuti
  const WARNING_TIMEOUT = 5 * 60 * 1000 // 5 minuti prima della scadenza

  // Actions
  const login = async (credentials) => {
    loading.value = true
    error.value = null
    
    try {
      // Prima ottieni il token CSRF per le richieste stateful
      await axios.get('/sanctum/csrf-cookie')
      
      // Poi effettua il login
      const response = await axios.post('/api/auth/login', credentials)
      
      if (response.data.success) {
        user.value = response.data.data.user
        
        // Gestisci il token in base al tipo di autenticazione
        if (response.data.data.token_type === 'Session') {
          // Per le sessioni stateful, non memorizzare il token
          token.value = null
          localStorage.removeItem('auth_token')
          delete axios.defaults.headers.common['Authorization']
        } else {
          // Per l'autenticazione con token
          token.value = response.data.data.token
          
          if (credentials.remember) {
            localStorage.setItem('auth_token', token.value)
            axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
          } else {
            localStorage.removeItem('auth_token')
          }
        }
        
        // Avvia il monitoraggio della sessione
        startSessionMonitoring()
        updateLastActivity()
        
        return response.data
      } else {
        throw new Error(response.data.message || 'Login fallito')
      }
    } catch (err) {
      console.error('Login error:', err)
      error.value = err.response?.data?.message || err.message || 'Errore durante il login'
      throw err
    } finally {
      loading.value = false
    }
  }

  const logout = async (reason = 'user_action') => {
    loading.value = true
    
    try {
      if (token.value || isAuthenticated.value) {
        // Per le sessioni stateful, chiama sempre logout anche senza token
        await axios.post('/api/auth/logout')
      }
    } catch (err) {
      console.error('Logout error:', err)
      // Non bloccare il logout locale anche se il server fallisce
    } finally {
      // Pulisci sempre i dati locali
      user.value = null
      token.value = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_remember')
      delete axios.defaults.headers.common['Authorization']
      
      // Ferma il monitoraggio della sessione
      stopSessionMonitoring()
      
      loading.value = false
      
      // Log del motivo del logout per debugging
      console.info('User logged out:', reason)
    }
  }

  const fetchUser = async () => {
    // Per le sessioni stateful, prova sempre a recuperare l'utente
    // anche senza token nel localStorage
    loading.value = true
    
    try {
      const response = await axios.get('/api/auth/user')
      
      if (response.data.success) {
        user.value = response.data.data.user
        updateLastActivity()
        
        // Se non abbiamo un token ma l'utente è autenticato via sessione,
        // avvia comunque il monitoraggio
        if (!sessionTimeout.value) {
          startSessionMonitoring()
        }
        
        return response.data
      }
    } catch (err) {
      console.error('Fetch user error:', err)
      // Se il token non è valido o la sessione è scaduta, effettua il logout
      if (err.response?.status === 401) {
        user.value = null
        token.value = null
        localStorage.removeItem('auth_token')
        localStorage.removeItem('auth_remember')
        delete axios.defaults.headers.common['Authorization']
      }
      throw err
    } finally {
      loading.value = false
    }
  }

  const register = async (userData) => {
    loading.value = true
    error.value = null
    
    try {
      // Prima ottieni il token CSRF
      await axios.get('/sanctum/csrf-cookie')
      
      const response = await axios.post('/api/auth/register', userData)
      
      if (response.data.success) {
        token.value = response.data.data.token
        user.value = response.data.data.user
        
        // Salva il token nel localStorage
        localStorage.setItem('auth_token', token.value)
        
        // Configura il token per le richieste future
        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
        
        // Avvia il monitoraggio della sessione
        startSessionMonitoring()
        updateLastActivity()
        
        return response.data
      } else {
        throw new Error(response.data.message || 'Registrazione fallita')
      }
    } catch (err) {
      console.error('Register error:', err)
      error.value = err.response?.data?.message || err.message || 'Errore durante la registrazione'
      throw err
    } finally {
      loading.value = false
    }
  }

  const refreshToken = async () => {
    if (!token.value) return false
    
    try {
      const response = await axios.post('/api/auth/refresh')
      
      if (response.data.success) {
        user.value = response.data.data.user
        
        // Gestisci il token in base al tipo di autenticazione
        if (response.data.data.token_type === 'Session') {
          // Per le sessioni stateful, non aggiornare il token
          token.value = null
          localStorage.removeItem('auth_token')
          delete axios.defaults.headers.common['Authorization']
        } else {
          // Per l'autenticazione con token
          token.value = response.data.data.token
          localStorage.setItem('auth_token', token.value)
          axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
        }
        
        updateLastActivity()
        return true
      }
    } catch (err) {
      console.error('Token refresh error:', err)
      await logout('token_refresh_failed')
    }
    
    return false
  }

  const updateLastActivity = () => {
    lastActivity.value = Date.now()
  }

  const startSessionMonitoring = () => {
    stopSessionMonitoring() // Ferma eventuali timer esistenti
    
    sessionTimeout.value = setInterval(() => {
      const now = Date.now()
      const timeSinceLastActivity = now - lastActivity.value
      
      if (timeSinceLastActivity >= SESSION_TIMEOUT) {
        // Sessione scaduta
        logout('session_timeout')
        
        // Mostra notifica all'utente
        if (window.confirm('La tua sessione è scaduta. Vuoi effettuare nuovamente il login?')) {
          window.location.href = '/login'
        }
      } else if (timeSinceLastActivity >= SESSION_TIMEOUT - WARNING_TIMEOUT) {
        // Avviso di scadenza imminente
        const remainingTime = Math.ceil((SESSION_TIMEOUT - timeSinceLastActivity) / 1000 / 60)
        console.warn(`Sessione in scadenza tra ${remainingTime} minuti`)
        
        // Qui potresti mostrare una notifica all'utente
        // o tentare un refresh automatico del token
      }
    }, 60000) // Controlla ogni minuto
  }

  const stopSessionMonitoring = () => {
    if (sessionTimeout.value) {
      clearInterval(sessionTimeout.value)
      sessionTimeout.value = null
    }
  }

  const checkPermission = (permission) => {
    if (!isAuthenticated.value) return false
    
    // Implementa la logica dei permessi in base al ruolo
    const rolePermissions = {
      admin: [
        'view.dashboard', 'view.projects', 'create.project', 'edit.project', 'delete.project',
        'view.clients', 'create.client', 'edit.client', 'delete.client',
        'view.material-types', 'create.material-type', 'edit.material-type', 'delete.material-type',
        'view.settings', 'manage.users', 'view.system-settings',
        'view.reports', 'export.reports', 'view.advanced-reports'
      ],
      user: [
        'view.dashboard', 'view.projects', 'create.project', 'edit.project',
        'view.clients', 'create.client', 'edit.client',
        'view.material-types',
        'view.settings',
        'view.reports', 'export.reports'
      ]
    }
    
    const userPermissions = rolePermissions[userRole.value] || []
    return userPermissions.includes(permission)
  }

  const clearError = () => {
    error.value = null
  }

  // Inizializza l'header Authorization solo se il token esiste e remember è attivo
  if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    
    // Avvia il monitoraggio della sessione se l'utente è già autenticato
    if (user.value) {
      startSessionMonitoring()
    }
  }

  // Inizializzazione dell'autenticazione
  const initializeAuth = async () => {
    const savedToken = localStorage.getItem('auth_token');
    const savedRemember = localStorage.getItem('auth_remember') === 'true';
    
    if (savedToken && savedRemember) {
      token.value = savedToken;
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
    }
    
    // Prova sempre a recuperare i dati utente per le sessioni stateful
    try {
      await fetchUser()
    } catch (err) {
      // Se fallisce, l'utente non è autenticato
      console.debug('No active session found')
    }
  }

  // Inizializza l'autenticazione all'avvio
  if (!user.value) {
    initializeAuth()
  }

  // Intercetta le richieste per aggiornare l'ultima attività
  axios.interceptors.request.use((config) => {
    if (isAuthenticated.value) {
      updateLastActivity()
    }
    return config
  })

  // Intercetta le risposte per gestire errori di autenticazione
  axios.interceptors.response.use(
    (response) => response,
    async (error) => {
      if (error.response?.status === 401 && isAuthenticated.value) {
        // Token scaduto o non valido
        await logout('unauthorized_response')
        
        // Reindirizza al login se non siamo già lì
        if (window.location.pathname !== '/login') {
          window.location.href = '/login?error=session_expired'
        }
      }
      return Promise.reject(error)
    }
  )

  return {
    // State
    user,
    token,
    loading,
    error,
    lastActivity,
    
    // Getters
    isAuthenticated,
    isAdmin,
    isUser,
    userRole,
    userName,
    userEmail,
    
    // Actions
    login,
    logout,
    fetchUser,
    register,
    refreshToken,
    updateLastActivity,
    checkPermission,
    clearError
  }
})