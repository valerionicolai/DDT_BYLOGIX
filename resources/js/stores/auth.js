import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token'))
  const loading = ref(false)
  const error = ref(null)

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isUser = computed(() => user.value?.role === 'user')

  // Actions
  const login = async (credentials) => {
    loading.value = true
    error.value = null
    
    try {
      // Prima ottieni il token CSRF
      await axios.get('/sanctum/csrf-cookie')
      
      // Poi effettua il login
      const response = await axios.post('/api/auth/login', credentials)
      
      if (response.data.success) {
        token.value = response.data.data.token
        user.value = response.data.data.user
        
        // Salva il token nel localStorage se "remember" è true
        if (credentials.remember) {
          localStorage.setItem('auth_token', token.value)
        }
        
        // Configura il token per le richieste future
        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
        
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

  const logout = async () => {
    loading.value = true
    
    try {
      if (token.value) {
        await axios.post('/api/auth/logout')
      }
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      // Pulisci sempre i dati locali
      user.value = null
      token.value = null
      localStorage.removeItem('auth_token')
      delete axios.defaults.headers.common['Authorization']
      loading.value = false
    }
  }

  const fetchUser = async () => {
    if (!token.value) return null
    
    loading.value = true
    
    try {
      const response = await axios.get('/api/auth/user')
      user.value = response.data.data || response.data
      return user.value
    } catch (err) {
      console.error('Fetch user error:', err)
      // Se il token non è valido, effettua il logout
      await logout()
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

  const clearError = () => {
    error.value = null
  }

  // Inizializza l'header Authorization se il token esiste
  if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
  }

  return {
    // State
    user,
    token,
    loading,
    error,
    
    // Getters
    isAuthenticated,
    isAdmin,
    isUser,
    
    // Actions
    login,
    logout,
    fetchUser,
    register,
    clearError
  }
})