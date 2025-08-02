import { ref, computed } from 'vue'
import axios from 'axios'

/**
 * Composable for dashboard data management
 */
export function useDashboard() {
  const loading = ref(false)
  const error = ref(null)
  
  // Dashboard statistics
  const stats = ref({
    totalProjects: 0,
    completedProjects: 0,
    activeProjects: 0,
    delayedProjects: 0,
    totalClients: 0,
    totalMaterialTypes: 0
  })

  // Recent projects
  const recentProjects = ref([])
  
  // Recent activity feed
  const recentActivity = ref([])

  // Quick links based on permissions
  const quickLinks = computed(() => [
    {
      name: 'Progetti',
      path: '/projects',
      icon: 'folder',
      permission: 'view.projects',
      description: 'Gestisci i tuoi progetti'
    },
    {
      name: 'Clienti',
      path: '/clients',
      icon: 'users',
      permission: 'view.clients',
      description: 'Gestisci i clienti'
    },
    {
      name: 'Tipi Materiali',
      path: '/material-types',
      icon: 'cube',
      permission: 'view.material-types',
      description: 'Gestisci i tipi di materiali'
    },
    {
      name: 'Report',
      path: '/reports',
      icon: 'chart-bar',
      permission: 'view.reports',
      description: 'Visualizza report e statistiche'
    }
  ])

  /**
   * Fetch dashboard statistics
   */
  const fetchDashboardStats = async () => {
    try {
      loading.value = true
      error.value = null

      // Try to fetch from multiple endpoints as specified in requirements
      const [dashboardResponse, projectsResponse, clientsResponse, materialTypesResponse] = await Promise.allSettled([
        axios.get('/api/dashboard/stats'),
        axios.get('/api/projects/count'),
        axios.get('/api/clients/count'),
        axios.get('/api/material-types/count')
      ])

      // Handle dashboard stats response
      if (dashboardResponse.status === 'fulfilled') {
        stats.value = { ...stats.value, ...dashboardResponse.value.data }
      }

      // Handle individual count responses
      if (projectsResponse.status === 'fulfilled') {
        const projectData = projectsResponse.value.data
        stats.value.totalProjects = projectData.total || projectData.count || 0
        stats.value.completedProjects = projectData.completed || 0
        stats.value.activeProjects = projectData.active || 0
        stats.value.delayedProjects = projectData.delayed || 0
      }

      if (clientsResponse.status === 'fulfilled') {
        stats.value.totalClients = clientsResponse.value.data.count || clientsResponse.value.data.total || 0
      }

      if (materialTypesResponse.status === 'fulfilled') {
        stats.value.totalMaterialTypes = materialTypesResponse.value.data.count || materialTypesResponse.value.data.total || 0
      }

    } catch (err) {
      console.error('Error fetching dashboard stats:', err)
      error.value = 'Errore nel caricamento delle statistiche'
      
      // Fallback to mock data for development
      stats.value = {
        totalProjects: 12,
        completedProjects: 8,
        activeProjects: 3,
        delayedProjects: 1,
        totalClients: 25,
        totalMaterialTypes: 15
      }
    } finally {
      loading.value = false
    }
  }

  /**
   * Fetch recent projects
   */
  const fetchRecentProjects = async () => {
    try {
      const response = await axios.get('/api/projects/recent')
      recentProjects.value = response.data.data || response.data
    } catch (err) {
      console.error('Error fetching recent projects:', err)
      
      // Fallback to mock data
      recentProjects.value = [
        {
          id: 1,
          name: 'Progetto Alpha',
          description: 'Sviluppo applicazione web',
          status: 'In Corso',
          updatedAt: '2 ore fa',
          client: 'Cliente A'
        },
        {
          id: 2,
          name: 'Progetto Beta',
          description: 'Migrazione database',
          status: 'Completato',
          updatedAt: '1 giorno fa',
          client: 'Cliente B'
        },
        {
          id: 3,
          name: 'Progetto Gamma',
          description: 'Ottimizzazione performance',
          status: 'In Ritardo',
          updatedAt: '3 giorni fa',
          client: 'Cliente C'
        }
      ]
    }
  }

  /**
   * Fetch recent activity
   */
  const fetchRecentActivity = async () => {
    try {
      const response = await axios.get('/api/dashboard/activity')
      recentActivity.value = response.data.data || response.data
    } catch (err) {
      console.error('Error fetching recent activity:', err)
      
      // Fallback to mock data
      recentActivity.value = [
        {
          id: 1,
          type: 'project_created',
          message: 'Nuovo progetto "Progetto Delta" creato',
          timestamp: '1 ora fa',
          user: 'Mario Rossi'
        },
        {
          id: 2,
          type: 'project_completed',
          message: 'Progetto "Progetto Beta" completato',
          timestamp: '2 ore fa',
          user: 'Luigi Verdi'
        },
        {
          id: 3,
          type: 'client_added',
          message: 'Nuovo cliente "Azienda XYZ" aggiunto',
          timestamp: '3 ore fa',
          user: 'Anna Bianchi'
        }
      ]
    }
  }

  /**
   * Load all dashboard data
   */
  const loadDashboardData = async () => {
    await Promise.all([
      fetchDashboardStats(),
      fetchRecentProjects(),
      fetchRecentActivity()
    ])
  }

  /**
   * Refresh dashboard data
   */
  const refreshData = async () => {
    await loadDashboardData()
  }

  /**
   * Get status class for project status
   */
  const getStatusClass = (status) => {
    const classes = {
      'Completato': 'bg-green-100 text-green-800',
      'In Corso': 'bg-yellow-100 text-yellow-800',
      'In Ritardo': 'bg-red-100 text-red-800',
      'Pianificato': 'bg-blue-100 text-blue-800',
      'Sospeso': 'bg-gray-100 text-gray-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
  }

  /**
   * Get activity icon based on type
   */
  const getActivityIcon = (type) => {
    const icons = {
      'project_created': 'plus-circle',
      'project_completed': 'check-circle',
      'project_updated': 'pencil',
      'client_added': 'user-plus',
      'material_added': 'cube'
    }
    return icons[type] || 'information-circle'
  }

  return {
    // State
    loading,
    error,
    stats,
    recentProjects,
    recentActivity,
    quickLinks,
    
    // Methods
    loadDashboardData,
    fetchDashboardStats,
    fetchRecentProjects,
    fetchRecentActivity,
    refreshData,
    getStatusClass,
    getActivityIcon
  }
}