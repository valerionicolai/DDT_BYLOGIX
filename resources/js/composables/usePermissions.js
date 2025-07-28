import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

/**
 * Composable per la gestione dei permessi utente
 */
export function usePermissions() {
  const authStore = useAuthStore()

  // Permessi base
  const canViewDashboard = computed(() => authStore.isAuthenticated)
  const canViewProjects = computed(() => authStore.isAuthenticated)
  const canCreateProject = computed(() => authStore.isAuthenticated)
  const canEditProject = computed(() => authStore.isAuthenticated)
  const canDeleteProject = computed(() => authStore.isAdmin)
  
  // Permessi per la gestione clienti
  const canViewClients = computed(() => authStore.isAuthenticated)
  const canCreateClient = computed(() => authStore.isAuthenticated)
  const canEditClient = computed(() => authStore.isAuthenticated)
  const canDeleteClient = computed(() => authStore.isAdmin)
  
  // Permessi per la gestione tipi materiali
  const canViewMaterialTypes = computed(() => authStore.isAuthenticated)
  const canCreateMaterialType = computed(() => authStore.isAdmin)
  const canEditMaterialType = computed(() => authStore.isAdmin)
  const canDeleteMaterialType = computed(() => authStore.isAdmin)
  
  // Permessi per le impostazioni
  const canViewSettings = computed(() => authStore.isAuthenticated)
  const canManageUsers = computed(() => authStore.isAdmin)
  const canViewSystemSettings = computed(() => authStore.isAdmin)
  
  // Permessi per i report
  const canViewReports = computed(() => authStore.isAuthenticated)
  const canExportReports = computed(() => authStore.isAuthenticated)
  const canViewAdvancedReports = computed(() => authStore.isAdmin)

  /**
   * Verifica se l'utente ha un permesso specifico
   * @param {string} permission - Nome del permesso da verificare
   * @returns {boolean}
   */
  const hasPermission = (permission) => {
    const permissions = {
      'view.dashboard': canViewDashboard.value,
      'view.projects': canViewProjects.value,
      'create.project': canCreateProject.value,
      'edit.project': canEditProject.value,
      'delete.project': canDeleteProject.value,
      'view.clients': canViewClients.value,
      'create.client': canCreateClient.value,
      'edit.client': canEditClient.value,
      'delete.client': canDeleteClient.value,
      'view.material-types': canViewMaterialTypes.value,
      'create.material-type': canCreateMaterialType.value,
      'edit.material-type': canEditMaterialType.value,
      'delete.material-type': canDeleteMaterialType.value,
      'view.settings': canViewSettings.value,
      'manage.users': canManageUsers.value,
      'view.system-settings': canViewSystemSettings.value,
      'view.reports': canViewReports.value,
      'export.reports': canExportReports.value,
      'view.advanced-reports': canViewAdvancedReports.value,
    }

    return permissions[permission] || false
  }

  /**
   * Verifica se l'utente ha tutti i permessi specificati
   * @param {string[]} permissions - Array di permessi da verificare
   * @returns {boolean}
   */
  const hasAllPermissions = (permissions) => {
    return permissions.every(permission => hasPermission(permission))
  }

  /**
   * Verifica se l'utente ha almeno uno dei permessi specificati
   * @param {string[]} permissions - Array di permessi da verificare
   * @returns {boolean}
   */
  const hasAnyPermission = (permissions) => {
    return permissions.some(permission => hasPermission(permission))
  }

  return {
    // Permessi specifici
    canViewDashboard,
    canViewProjects,
    canCreateProject,
    canEditProject,
    canDeleteProject,
    canViewClients,
    canCreateClient,
    canEditClient,
    canDeleteClient,
    canViewMaterialTypes,
    canCreateMaterialType,
    canEditMaterialType,
    canDeleteMaterialType,
    canViewSettings,
    canManageUsers,
    canViewSystemSettings,
    canViewReports,
    canExportReports,
    canViewAdvancedReports,
    
    // Metodi di utilità
    hasPermission,
    hasAllPermissions,
    hasAnyPermission
  }
}