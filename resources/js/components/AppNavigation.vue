<template>
  <nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <router-link to="/dashboard" class="text-xl font-bold text-primary-600">
              DTT by Logix
            </router-link>
          </div>

          <!-- Navigation Links -->
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <!-- Dashboard - Always visible to authenticated users -->
            <CanAccess requires-auth>
              <router-link
                to="/dashboard"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                active-class="border-primary-500 text-gray-900"
              >
                Dashboard
              </router-link>
            </CanAccess>

            <!-- Projects - Requires view.projects permission -->
            <CanAccess permission="view.projects">
              <router-link
                to="/projects"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                active-class="border-primary-500 text-gray-900"
              >
                Progetti
              </router-link>
            </CanAccess>

            <!-- Clients - Requires view.clients permission -->
            <CanAccess permission="view.clients">
              <router-link
                to="/clients"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                active-class="border-primary-500 text-gray-900"
              >
                Clienti
              </router-link>
            </CanAccess>

            <!-- Documents - Requires view.documents permission -->
            <CanAccess permission="view.documents">
              <router-link
                to="/documents"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                active-class="border-primary-500 text-gray-900"
              >
                Documenti
              </router-link>
            </CanAccess>

            <!-- Material Types - Requires view.material-types permission -->
            <CanAccess permission="view.material-types">
              <router-link
                to="/material-types"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                active-class="border-primary-500 text-gray-900"
              >
                Materiali
              </router-link>
            </CanAccess>

            <!-- Reports - Requires view.reports permission -->
            <CanAccess permission="view.reports">
              <router-link
                to="/reports"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                active-class="border-primary-500 text-gray-900"
              >
                Report
              </router-link>
            </CanAccess>

            <!-- Admin Section - Only for admins -->
            <CanAccess requires-admin>
              <div class="relative" ref="adminDropdown">
                <button
                  @click="toggleAdminMenu"
                  class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center"
                >
                  Amministrazione
                  <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
                
                <div
                  v-show="showAdminMenu"
                  class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                >
                  <div class="py-1">
                    <router-link
                      to="/admin/users"
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      @click="showAdminMenu = false"
                    >
                      Gestione Utenti
                    </router-link>
                    <router-link
                      to="/admin/settings"
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      @click="showAdminMenu = false"
                    >
                      Impostazioni Sistema
                    </router-link>
                  </div>
                </div>
              </div>
            </CanAccess>
          </div>
        </div>

        <!-- User Menu -->
        <div class="hidden sm:ml-6 sm:flex sm:items-center">
          <CanAccess requires-auth>
            <div class="relative" ref="userDropdown">
              <button
                @click="toggleUserMenu"
                class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                <span class="sr-only">Apri menu utente</span>
                <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                  <span class="text-primary-600 font-medium text-sm">
                    {{ userInitials }}
                  </span>
                </div>
              </button>

              <div
                v-show="showUserMenu"
                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
              >
                <div class="py-1">
                  <div class="px-4 py-2 text-sm text-gray-500 border-b border-gray-100">
                    {{ authStore.user?.name || 'Utente' }}
                    <div class="text-xs text-gray-400">
                      {{ authStore.user?.email }}
                    </div>
                  </div>
                  
                  <router-link
                    to="/profile"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="showUserMenu = false"
                  >
                    Profilo
                  </router-link>
                  
                  <CanAccess permission="view.settings">
                    <router-link
                      to="/settings"
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      @click="showUserMenu = false"
                    >
                      Impostazioni
                    </router-link>
                  </CanAccess>
                  
                  <button
                    @click="handleLogout"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Esci
                  </button>
                </div>
              </div>
            </div>
          </CanAccess>

          <!-- Login button for guests -->
          <CanAccess requires-guest>
            <router-link
              to="/login"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
            >
              Accedi
            </router-link>
          </CanAccess>
        </div>

        <!-- Mobile menu button -->
        <div class="sm:hidden flex items-center">
          <button
            @click="toggleMobileMenu"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100"
          >
            <span class="sr-only">Apri menu principale</span>
            <svg
              v-if="!showMobileMenu"
              class="block h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg
              v-else
              class="block h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div v-show="showMobileMenu" class="sm:hidden">
      <div class="pt-2 pb-3 space-y-1">
        <CanAccess requires-auth>
          <router-link
            to="/dashboard"
            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300"
            active-class="border-primary-500 text-primary-700 bg-primary-50"
            @click="showMobileMenu = false"
          >
            Dashboard
          </router-link>
        </CanAccess>

        <CanAccess permission="view.projects">
          <router-link
            to="/projects"
            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300"
            active-class="border-primary-500 text-primary-700 bg-primary-50"
            @click="showMobileMenu = false"
          >
            Progetti
          </router-link>
        </CanAccess>

        <CanAccess permission="view.clients">
          <router-link
            to="/clients"
            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300"
            active-class="border-primary-500 text-primary-700 bg-primary-50"
            @click="showMobileMenu = false"
          >
            Clienti
          </router-link>
        </CanAccess>

        <CanAccess permission="view.documents">
          <router-link
            to="/documents"
            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300"
            active-class="border-primary-500 text-primary-700 bg-primary-50"
            @click="showMobileMenu = false"
          >
            Documenti
          </router-link>
        </CanAccess>

        <CanAccess requires-admin>
          <div class="border-t border-gray-200 pt-2">
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
              Amministrazione
            </div>
            <router-link
              to="/admin/users"
              class="block pl-6 pr-4 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50"
              @click="showMobileMenu = false"
            >
              Gestione Utenti
            </router-link>
            <router-link
              to="/admin/settings"
              class="block pl-6 pr-4 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50"
              @click="showMobileMenu = false"
            >
              Impostazioni Sistema
            </router-link>
          </div>
        </CanAccess>
      </div>
    </div>
  </nav>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import CanAccess from '@/components/CanAccess.vue'

export default {
  name: 'AppNavigation',
  components: {
    CanAccess
  },
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    
    const showUserMenu = ref(false)
    const showAdminMenu = ref(false)
    const showMobileMenu = ref(false)
    const userDropdown = ref(null)
    const adminDropdown = ref(null)

    const userInitials = computed(() => {
      if (!authStore.user?.name) return 'U'
      return authStore.user.name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2)
    })

    const toggleUserMenu = () => {
      showUserMenu.value = !showUserMenu.value
      showAdminMenu.value = false
    }

    const toggleAdminMenu = () => {
      showAdminMenu.value = !showAdminMenu.value
      showUserMenu.value = false
    }

    const toggleMobileMenu = () => {
      showMobileMenu.value = !showMobileMenu.value
    }

    const handleLogout = async () => {
      try {
        await authStore.logout()
        router.push('/login')
      } catch (error) {
        console.error('Errore durante il logout:', error)
      }
    }

    const handleClickOutside = (event) => {
      if (userDropdown.value && !userDropdown.value.contains(event.target)) {
        showUserMenu.value = false
      }
      if (adminDropdown.value && !adminDropdown.value.contains(event.target)) {
        showAdminMenu.value = false
      }
    }

    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
    })

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
    })

    return {
      authStore,
      userInitials,
      showUserMenu,
      showAdminMenu,
      showMobileMenu,
      userDropdown,
      adminDropdown,
      toggleUserMenu,
      toggleAdminMenu,
      toggleMobileMenu,
      handleLogout
    }
  }
}
</script>