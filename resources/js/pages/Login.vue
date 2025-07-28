<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div>
        <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
          <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Accedi al tuo account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          DTT by Logix - Sistema di gestione progetti
        </p>
      </div>

      <!-- Form -->
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="rounded-md shadow-sm -space-y-px">
          <!-- Email -->
          <div>
            <label for="email" class="sr-only">Email</label>
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
              autocomplete="email"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-300': errors.email }"
              placeholder="Indirizzo email"
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="sr-only">Password</label>
            <input
              id="password"
              v-model="form.password"
              name="password"
              type="password"
              autocomplete="current-password"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-300': errors.password }"
              placeholder="Password"
            />
            <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
          </div>
        </div>

        <!-- Remember me -->
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember-me"
              v-model="form.remember"
              name="remember-me"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="remember-me" class="ml-2 block text-sm text-gray-900">
              Ricordami
            </label>
          </div>

          <div class="text-sm">
            <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
              Password dimenticata?
            </a>
          </div>
        </div>

        <!-- Error message -->
        <div v-if="authStore.error" class="rounded-md bg-red-50 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">
                Errore di autenticazione
              </h3>
              <div class="mt-2 text-sm text-red-700">
                {{ authStore.error }}
              </div>
            </div>
          </div>
        </div>

        <!-- Submit button -->
        <div>
          <button
            type="submit"
            :disabled="authStore.loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg v-if="!authStore.loading" class="h-5 w-5 text-blue-500 group-hover:text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
              </svg>
              <svg v-else class="animate-spin h-5 w-5 text-blue-500" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ authStore.loading ? 'Accesso in corso...' : 'Accedi' }}
          </button>
        </div>

        <!-- Register link -->
        <div class="text-center">
          <p class="text-sm text-gray-600">
            Non hai un account?
            <router-link to="/register" class="font-medium text-blue-600 hover:text-blue-500">
              Registrati qui
            </router-link>
          </p>
        </div>
      </form>

      <!-- Demo credentials -->
      <div class="mt-6 p-4 bg-blue-50 rounded-md">
        <h3 class="text-sm font-medium text-blue-800 mb-2">Credenziali di test:</h3>
        <div class="text-xs text-blue-700 space-y-1">
          <p><strong>Admin:</strong> admin@example.com / password</p>
          <p><strong>User:</strong> user@example.com / password</p>
        </div>
        <div class="mt-2 flex space-x-2">
          <button
            @click="fillAdminCredentials"
            class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700"
          >
            Usa Admin
          </button>
          <button
            @click="fillUserCredentials"
            class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700"
          >
            Usa User
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Form data
const form = reactive({
  email: '',
  password: '',
  remember: false
})

// Form errors
const errors = ref({})

// Clear errors when store error changes
const clearErrors = () => {
  errors.value = {}
  authStore.clearError()
}

// Validate form
const validateForm = () => {
  const newErrors = {}

  if (!form.email) {
    newErrors.email = 'L\'email è richiesta'
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    newErrors.email = 'L\'email non è valida'
  }

  if (!form.password) {
    newErrors.password = 'La password è richiesta'
  } else if (form.password.length < 6) {
    newErrors.password = 'La password deve essere di almeno 6 caratteri'
  }

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

// Handle login
const handleLogin = async () => {
  clearErrors()

  if (!validateForm()) {
    return
  }

  try {
    await authStore.login({
      email: form.email,
      password: form.password,
      remember: form.remember
    })

    // Redirect to dashboard on successful login
    router.push('/')
  } catch (error) {
    // Error is handled by the store
    console.error('Login failed:', error)
  }
}

// Demo credentials helpers
const fillAdminCredentials = () => {
  form.email = 'admin@example.com'
  form.password = 'password'
  clearErrors()
}

const fillUserCredentials = () => {
  form.email = 'user@example.com'
  form.password = 'password'
  clearErrors()
}

// Redirect if already authenticated
onMounted(async () => {
  if (authStore.isAuthenticated) {
    router.push('/')
  }
})
</script>