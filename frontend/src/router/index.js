import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Login from '@/views/login/Login.vue'
import Register from '@/views/register/Register.vue'
import Dashboard from '@/views/dashboard/Dashboard.vue'
import DashboardStats from '@/views/dashboardStats/DashboardStats.vue'
import TravelOrderForm from '@/views/travelOrderForm/TravelOrderForm.vue'

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/stats',
    name: 'DashboardStats',
    component: DashboardStats,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/travel-order/new',
    name: 'NewTravelOrder',
    component: TravelOrderForm,
    meta: { requiresAuth: true }
  },
  {
    path: '/travel-order/:id/edit',
    name: 'EditTravelOrder',
    component: TravelOrderForm,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const isAuthenticated = authStore.isAuthenticated

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  } else if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next('/dashboard')
  } else if (to.meta.requiresGuest && isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router 