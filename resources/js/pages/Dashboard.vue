<template>
  <CanAccess permission="view.dashboard">
    <div class="min-h-screen bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
              <p class="mt-2 text-gray-600">Benvenuto nel sistema DTT by Logix</p>
            </div>
            
            <!-- Refresh Button -->
            <button
              @click="refreshData"
              :disabled="loading"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50"
            >
              <svg 
                class="w-4 h-4 mr-2" 
                :class="{ 'animate-spin': loading }" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Aggiorna
            </button>
          </div>
          
          <!-- Admin Actions - Only visible to admins -->
          <CanAccess requires-admin class="mt-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <h3 class="text-sm font-medium text-blue-800">Azioni Amministratore</h3>
              <div class="mt-2 flex space-x-3">
                <router-link
                  to="/admin/users"
                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200"
                >
                  Gestisci Utenti
                </router-link>
                <router-link
                  to="/admin/settings"
                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200"
                >
                  Impostazioni Sistema
                </router-link>
              </div>
            </div>
          </CanAccess>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Projects Stats -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Progetti Totali</dt>
                    <dd class="text-lg font-medium text-gray-900">
                       <span v-if="loading">...</span>
                       <span v-else>{{ stats.totalProjects }}</span>
                     </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Clients Stats -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Clienti Totali</dt>
                    <dd class="text-lg font-medium text-gray-900">
                       <span v-if="loading">...</span>
                       <span v-else>{{ stats.totalClients }}</span>
                     </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Material Types Stats -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Tipi di Materiale</dt>
                    <dd class="text-lg font-medium text-gray-900">
                       <span v-if="loading">...</span>
                       <span v-else>{{ stats.totalMaterialTypes }}</span>
                     </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Active Projects Stats -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Progetti Attivi</dt>
                    <dd class="text-lg font-medium text-gray-900">
                       <span v-if="loading">...</span>
                       <span v-else>{{ stats.activeProjects }}</span>
                     </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Accesso Rapido</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <CanAccess 
              v-for="link in quickLinks" 
              :key="link.name"
              :permission="link.permission"
            >
              <router-link
                :to="link.path"
                class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-primary-500 rounded-lg shadow hover:shadow-md transition-shadow"
              >
                <div>
                  <span class="rounded-lg inline-flex p-3 ring-4 ring-white bg-primary-500">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </span>
                </div>
                <div class="mt-4">
                  <h3 class="text-lg font-medium text-gray-900">
                    <span class="absolute inset-0" aria-hidden="true" />
                    {{ link.name }}
                  </h3>
                  <p class="mt-2 text-sm text-gray-500">
                    {{ link.description }}
                  </p>
                </div>
                <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true">
                  <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z" />
                  </svg>
                </span>
              </router-link>
            </CanAccess>
          </div>
        </div>

        <!-- Recent Projects and Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Recent Projects -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Progetti Recenti</h3>
                <CanAccess permission="create.project">
                  <router-link
                    to="/projects/create"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                  >
                    Nuovo Progetto
                  </router-link>
                </CanAccess>
              </div>

              <div class="flow-root">
                <ul role="list" class="-my-5 divide-y divide-gray-200">
                  <li v-for="project in recentProjects" :key="project.id" class="py-4">
                    <div class="flex items-center space-x-4">
                      <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                          <span class="text-sm font-medium text-gray-600">{{ project.name.charAt(0) }}</span>
                        </div>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                          {{ project.name }}
                        </p>
                        <p class="text-sm text-gray-500 truncate">
                          {{ project.description }}
                        </p>
                      </div>
                      <div class="flex-shrink-0">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass(project.status)">
                          {{ project.status }}
                        </span>
                      </div>
                      <div class="flex-shrink-0 flex space-x-2">
                        <CanAccess permission="edit.project">
                          <button
                            @click="editProject(project.id)"
                            type="button"
                            class="inline-flex items-center p-1.5 border border-transparent rounded-full shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                          >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                          </button>
                        </CanAccess>
                        <CanAccess permission="delete.project">
                          <button
                            @click="deleteProject(project.id)"
                            type="button"
                            class="inline-flex items-center p-1.5 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                          >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                          </button>
                        </CanAccess>
                        <CanAccess requires-admin>
                          <button
                            @click="advancedSettings(project.id)"
                            type="button"
                            class="inline-flex items-center p-1.5 border border-transparent rounded-full shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                          >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                          </button>
                        </CanAccess>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
              
              <div v-if="recentProjects.length === 0" class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun progetto</h3>
                <p class="mt-1 text-sm text-gray-500">Inizia creando il tuo primo progetto.</p>
                <div class="mt-6">
                  <router-link
                    to="/projects"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                  >
                    Crea Progetto
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Attività Recenti</h3>
              
              <div class="flow-root">
                <ul role="list" class="-mb-8">
                  <li v-for="(activity, index) in recentActivity" :key="activity.id">
                    <div class="relative pb-8">
                      <span v-if="index !== recentActivity.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true" />
                      <div class="relative flex space-x-3">
                        <div>
                          <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white" :class="getActivityIconBg(activity.type)">
                            <component :is="getActivityIcon(activity.type)" class="h-5 w-5 text-white" aria-hidden="true" />
                          </span>
                        </div>
                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                          <div>
                            <p class="text-sm text-gray-500">
                              {{ activity.description }}
                            </p>
                          </div>
                          <div class="text-right text-sm whitespace-nowrap text-gray-500">
                            <time :datetime="activity.timestamp">{{ formatTime(activity.timestamp) }}</time>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </CanAccess>
</template>

<script setup>
import { ref, onMounted, h } from 'vue'
import CanAccess from '@/components/CanAccess.vue'
import { useDashboard } from '@/composables/useDashboard'
import { usePermissions } from '@/composables/usePermissions'

// Use dashboard composable
const {
  stats,
  recentProjects,
  recentActivity,
  quickLinks,
  loading,
  error,
  loadDashboardData,
  refreshData,
  getStatusClass,
  getActivityIcon
} = useDashboard()

const { hasPermission } = usePermissions()

// Additional utility functions
const getStatusLabel = (status) => {
  const labels = {
    'completed': 'Completato',
    'in_progress': 'In Corso',
    'delayed': 'In Ritardo',
    'pending': 'In Attesa'
  }
  return labels[status] || status
}

const getActivityIconBg = (type) => {
  const backgrounds = {
    'project_created': 'bg-green-500',
    'project_completed': 'bg-blue-500',
    'project_updated': 'bg-yellow-500',
    'client_added': 'bg-purple-500',
    'material_added': 'bg-indigo-500'
  }
  return backgrounds[type] || 'bg-gray-500'
}

const formatTime = (timestamp) => {
  // Simple time formatting - you can enhance this
  return timestamp
}

const editProject = (projectId) => {
  console.log('Edit project:', projectId)
  // Navigate to edit page or open modal
  // router.push(`/projects/${projectId}/edit`)
}

const deleteProject = async (projectId) => {
  if (confirm('Sei sicuro di voler eliminare questo progetto?')) {
    try {
      // Implement delete functionality
      console.log('Delete project:', projectId)
      // await api.delete(`/projects/${projectId}`)
      // await fetchDashboardData() // Refresh data
    } catch (error) {
      console.error('Error deleting project:', error)
    }
  }
}

const advancedSettings = (projectId) => {
  console.log('Advanced settings for project:', projectId)
  // Navigate to advanced settings page
  // router.push(`/projects/${projectId}/settings`)
}

// Load data on mount
onMounted(async () => {
  await loadDashboardData()
})
</script>