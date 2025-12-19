import axios from 'axios';
import { useAuthStore } from './stores/auth';

axios.defaults.baseURL = '/api';

export function setAuthHeader() {
  const auth = useAuthStore();
  if (auth.token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${auth.token}`;
  } else {
    delete axios.defaults.headers.common['Authorization'];
  }
}

export default axios;