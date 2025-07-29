import { createRouter, createWebHistory } from 'vue-router'
import { applyRouteMiddleware } from './middleware'

// Import delle pagine
import Dashboard from '@/pages/Dashboard.vue'
import Projects from '@/pages/Projects.vue'
import ProjectCreate from '@/pages/ProjectCreate.vue'
import Clients from '@/pages/Clients.vue'
import Documents from '@/pages/Documents.vue'
import DocumentCreate from '@/pages/DocumentCreate.vue'
import DocumentDetail from '@/pages/DocumentDetail.vue'
import MaterialTypes from '@/pages/MaterialTypes.vue'
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
    component: ProjectCreate,
    meta: {
      title: 'Nuovo Progetto - DTT by Logix',
      requiresAuth: true,
      requiresPermission: 'create.project'
    }
  },
  {
    path: '/clients',
    name: 'Clients',
    component: Clients,
    meta: {
      requiresAuth: true,
      requiresPermission: 'view.client',
      title: 'Clienti'
    }
  },
  {
    path: '/documents',
    name: 'Documents',
    component: Documents,
    meta: {
      requiresAuth: true,
      requiresPermission: 'view.document',
      title: 'Documenti'
    }
  },
  {
    path: '/documents/create',
    name: 'DocumentCreate',
    component: DocumentCreate,
    meta: {
      requiresAuth: true,
      requiresPermission: 'create.document',
      title: 'Nuovo Documento'
    }
  },
  {
    path: '/documents/:id',
    name: 'DocumentDetail',
    component: DocumentDetail,
    meta: {
      requiresAuth: true,
      requiresPermission: 'view.document',
      title: 'Dettaglio Documento'
    }
  },
  {
    path: '/material-types',
    name: 'MaterialTypes',
    component: MaterialTypes,
    meta: {
      requiresAuth: true,
      requiresPermission: 'view.material-types',
      title: 'Gestione Materiali'
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