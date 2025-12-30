import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth';
import LoginPage from '../views/LoginPage.vue';
import EventsPage from '../views/EventsPage.vue';
import AgentDashboard from '../views/AgentDashboard.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
  {
    path: '/',
    name: 'Login',
    component: LoginPage,
  },
  {
    path: '/events',
    name: 'Events',
    component: EventsPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/agent-dashboard',
    name: 'AgentDashboard',
    component: AgentDashboard,
    meta: { requiresAuth: true, role: 'agent' },
  }],
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore();

  if (to.meta.requiresAuth && !auth.token) {
    return next({ name: 'Login' });
  }

  if (to.name === 'Login' && auth.token) {
    if (auth.user?.role === 'agent') {
      return next({ name: 'AgentDashboard' });
    } else {
      return next({ name: 'Events' });
    }
  }

  if (to.meta.role === 'agent' && auth.user?.role !== 'agent') {
    alert("Unauthorized! Only agents can access this area.");
    return next({ name: 'Events' });
  }
  next();
});

export default router
