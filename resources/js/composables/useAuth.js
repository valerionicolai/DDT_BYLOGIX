import { ref, computed } from 'vue'

export default function useAuth() {
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token'))
  
  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isManager = computed(() => ['admin', 'manager'].includes(user.value?.role))

  const login = async (credentials) => {
    try {
      // Qui implementeremo la logica di login
      console.log('Login attempt:', credentials)
      
      // Simulazione per ora
      user.value = {
        id: 1,
        name: 'Admin User',
        email: credentials.email,
        role: 'admin'
      }
      token.value = 'fake-jwt-token'
      localStorage.setItem('auth_token', token.value)
      
      return user.value
    } catch (error) {
      throw new Error('Credenziali non valide')
    }
  }

  const logout = () => {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
  }

  const checkAuth = async () => {
    if (token.value) {
      try {
        // Qui verificheremo il token con il backend
        user.value = {
          id: 1,
          name: 'Admin User',
          email: 'admin@dttbylogix.com',
          role: 'admin'
        }
      } catch (error) {
        logout()
      }
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    isAdmin,
    isManager,
    login,
    logout,
    checkAuth
  }
}