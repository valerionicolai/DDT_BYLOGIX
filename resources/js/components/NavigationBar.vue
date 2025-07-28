<template>
  <nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo e Brand -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-8 w-8 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
              </svg>
            </div>
            <h1 class="ml-3 text-2xl font-bold text-primary-600">DTT by Logix</h1>
          </router-link>
        </div>

        <!-- Navigation Links - Desktop -->
        <div class="hidden md:flex space-x-8">
          <router-link
            v-for="item in navigationItems"
            :key="item.name"
            :to="item.path"
            class="px-3 py-2 rounded-md text-sm font-medium border-b-2 transition-colors duration-200 flex items-center"
            :class="[
              $route.path === item.path
                ? 'text-primary-600 border-primary-600'
                : 'text-gray-500 hover:text-gray-900 border-transparent hover:border-gray-300'
            ]"
          >
            <component :is="item.icon" class="w-5 h-5 mr-2" />
            {{ item.name }}
          </router-link>
        </div>

        <!-- User Menu -->
        <div class="hidden md:flex items-center space-x-4">
          <div class="relative">
            <button
              @click="toggleUserMenu"
              class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                <svg class="h-5 w-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <span class="ml-2 text-gray-700 font-medium">Admin</span>
            </button>
            
            <!-- User Dropdown -->
            <div
              v-show="showUserMenu"
              class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
            >
              <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                Profilo
              </a>
              <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                Logout
              </a>
            </div>
          </div>
        </div>

        <!-- Mobile menu button -->
        <div class="md:hidden">
          <button
            @click="toggleMobileMenu"
            class="text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 p-2"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path v-if="!showMobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <div v-show="showMobileMenu" class="md:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
          <router-link
            v-for="item in navigationItems"
            :key="item.name"
            :to="item.path"
            class="block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200 flex items-center"
            :class="[
              $route.path === item.path
                ? 'text-primary-600 bg-primary-50'
                : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'
            ]"
            @click="closeMobileMenu"
          >
            <component :is="item.icon" class="w-5 h-5 mr-3" />
            {{ item.name }}
          </router-link>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { ref } from 'vue'

// Icone SVG come componenti
const DashboardIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 20 20">
      <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"></path>
    </svg>
  `
}

const ProjectsIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 20 20">
      <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
    </svg>
  `
}

const SettingsIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
    </svg>
  `
}

export default {
  name: 'NavigationBar',
  components: {
    DashboardIcon,
    ProjectsIcon,
    SettingsIcon
  },
  setup() {
    const showMobileMenu = ref(false)
    const showUserMenu = ref(false)

    const navigationItems = [
      {
        name: 'Dashboard',
        path: '/',
        icon: 'DashboardIcon'
      },
      {
        name: 'Progetti',
        path: '/projects',
        icon: 'ProjectsIcon'
      },
      {
        name: 'Impostazioni',
        path: '/settings',
        icon: 'SettingsIcon'
      }
    ]

    const toggleMobileMenu = () => {
      showMobileMenu.value = !showMobileMenu.value
    }

    const closeMobileMenu = () => {
      showMobileMenu.value = false
    }

    const toggleUserMenu = () => {
      showUserMenu.value = !showUserMenu.value
    }

    // Chiudi i menu quando si clicca fuori
    const handleClickOutside = (event) => {
      if (!event.target.closest('.relative')) {
        showUserMenu.value = false
      }
    }

    return {
      navigationItems,
      showMobileMenu,
      showUserMenu,
      toggleMobileMenu,
      closeMobileMenu,
      toggleUserMenu,
      handleClickOutside
    }
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside)
  },
  unmounted() {
    document.removeEventListener('click', this.handleClickOutside)
  }
}
</script>