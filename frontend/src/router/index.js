import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth';
import LoginPage from '../views/LoginPage.vue';
import EventsPage from '../views/EventsPage.vue';

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
  }],
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore();
  if (to.meta.requiresAuth && !auth.token) {
    next({ name: 'Login' });
  } else if (to.name === 'Login' && auth.token) {
    next({ name: 'Events' });
  } else {
    next();
  }
});

export default router
