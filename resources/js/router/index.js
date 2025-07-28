import { createRouter, createWebHistory } from 'vue-router'
import { applyRouteMiddleware } from './middleware'

// Import delle pagine
import Dashboard from '@/pages/Dashboard.vue'
import Projects from '@/pages/Projects.vue'
import Settings from '@/pages/Settings.vue'
import Login from '@/pages/Login.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: {
      title: 'Login - DTT by Logix',
      requiresGuest: true
    }
  },
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
    meta: {
      title: 'Dashboard - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'view.dashboard'
    }
  },
  {
    path: '/projects',
    name: 'Projects',
    component: Projects,
    meta: {
      title: 'Progetti - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'view.projects'
    }
  },
  {
    path: '/projects/create',
    name: 'ProjectCreate',
    component: () => import('@/pages/ProjectCreate.vue'),
    meta: {
      title: 'Nuovo Progetto - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'create.project'
    }
  },
  {
    path: '/projects/:id/edit',
    name: 'ProjectEdit',
    component: () => import('@/pages/ProjectEdit.vue'),
    meta: {
      title: 'Modifica Progetto - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'edit.project'
    }
  },
  {
    path: '/clients',
    name: 'Clients',
    component: () => import('@/pages/Clients.vue'),
    meta: {
      title: 'Clienti - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'view.clients'
    }
  },
  {
    path: '/clients/create',
    name: 'ClientCreate',
    component: () => import('@/pages/ClientCreate.vue'),
    meta: {
      title: 'Nuovo Cliente - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'create.client'
    }
  },
  {
    path: '/material-types',
    name: 'MaterialTypes',
    component: () => import('@/pages/MaterialTypes.vue'),
    meta: {
      title: 'Tipi di Materiale - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'view.material-types'
    }
  },
  {
    path: '/material-types/create',
    name: 'MaterialTypeCreate',
    component: () => import('@/pages/MaterialTypeCreate.vue'),
    meta: {
      title: 'Nuovo Tipo di Materiale - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'create.material-type'
    }
  },
  {
    path: '/reports',
    name: 'Reports',
    component: () => import('@/pages/Reports.vue'),
    meta: {
      title: 'Report - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'view.reports'
    }
  },
  {
    path: '/reports/advanced',
    name: 'AdvancedReports',
    component: () => import('@/pages/AdvancedReports.vue'),
    meta: {
      title: 'Report Avanzati - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'view.advanced-reports'
    }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
    meta: {
      title: 'Impostazioni - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'view.settings'
    }
  },
  {
    path: '/settings/users',
    name: 'UserManagement',
    component: () => import('@/pages/UserManagement.vue'),
    meta: {
      title: 'Gestione Utenti - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'manage.users'
    }
  },
  {
    path: '/settings/system',
    name: 'SystemSettings',
    component: () => import('@/pages/SystemSettings.vue'),
    meta: {
      title: 'Impostazioni Sistema - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'view.system-settings'
    }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('@/pages/Profile.vue'),
    meta: {
      title: 'Profilo - DTT by Logix',
      requiresAuth: true
    }
  },
  {
    path: '/unauthorized',
    name: 'Unauthorized',
    component: () => import('@/pages/Unauthorized.vue'),
    meta: {
      title: 'Accesso Negato - DTT by Logix',
      requiresAuth: true
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/pages/NotFound.vue'),
    meta: {
      title: 'Pagina Non Trovata - DTT by Logix'
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Applica il middleware globale
router.beforeEach(applyRouteMiddleware)

// Gestione degli errori di navigazione
router.onError((error) => {
  console.error('Router error:', error)
  
  // Reindirizza a una pagina di errore se necessario
  if (error.message.includes('Loading chunk')) {
    window.location.reload()
  }
})

export default router