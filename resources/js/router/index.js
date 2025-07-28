import { createRouter, createWebHistory } from 'vue-router'

// Import delle pagine
import Dashboard from '@/pages/Dashboard.vue'
import Projects from '@/pages/Projects.vue'
import Settings from '@/pages/Settings.vue'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
    meta: {
      title: 'Dashboard - DTT by Logix'
    }
  },
  {
    path: '/projects',
    name: 'Projects',
    component: Projects,
    meta: {
      title: 'Progetti - DTT by Logix'
    }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
    meta: {
      title: 'Impostazioni - DTT by Logix'
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    redirect: '/'
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

// Navigation guards
router.beforeEach((to, from, next) => {
  // Aggiorna il titolo della pagina
  if (to.meta.title) {
    document.title = to.meta.title
  }
  next()
})

export default router