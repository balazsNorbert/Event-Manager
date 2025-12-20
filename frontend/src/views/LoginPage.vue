<template>
  <div>
    <h1>Login</h1>
    <form @submit.prevent="handleLogin">
      <input v-model="email" placeholder="Email" />
      <input v-model="password" type="password" placeholder="Password" />
      <button type="submit">Login</button>
    </form>
    <p v-if="error">{{ error }}</p>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const error = ref('');
const auth = useAuthStore();
const router = useRouter();

const handleLogin = async () => {
  try {
    await auth.login(email.value, password.value);
    router.push('/events');
  } catch (err) {
    console.error(err);
    error.value = 'Login failed';
  }
};
</script>