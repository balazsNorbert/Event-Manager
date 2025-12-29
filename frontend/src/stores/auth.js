import { defineStore } from 'pinia';
import axios from '../axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: JSON.parse(localStorage.getItem('user')) || null,
  }),
  actions: {
    async login(email, password) {
      const res = await axios.post('/login', { email, password });
      this.token = res.data.access_token;
      this.user = res.data.user;
      localStorage.setItem('token', this.token);
      localStorage.setItem('user', JSON.stringify(this.user));
      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    },
    logout() {
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      delete axios.defaults.headers.common['Authorization'];
    }
  }
});