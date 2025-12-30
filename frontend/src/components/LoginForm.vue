<template>
  <div class="flex flex-col gap-2">
    <h1 class="mx-auto text-xl md:text-2xl mb-2">Login</h1>
    <form @submit.prevent="handleLogin" class="flex flex-col gap-2 text-base md:text-lg">
      <label for="userEmail" class="text-xs md:text-sm font-semibold text-gray-700">Email</label>
      <input
        id="userEmail"
        v-model="email"
        placeholder="John@mail.com"
        class="mb-2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all"
      />
      <label for="userPassword" class="text-xs md:text-sm font-semibold text-gray-700">Password</label>
      <input
        id="userPassword"
        v-model="password"
        type="password"
        placeholder="Password"
        class="mb-2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all"
      />
      <button
        type="button"
        @click="$emit('show-forgot')"
        class="text-base mr-auto text-teal-600 hover:underline"
      >
        Forgot password?
      </button>
      <button class="bg-teal-600 text-white py-2 rounded-lg font-semibold hover:bg-teal-700 transition-colors" type="submit">Login</button>
    </form>
    <p v-if="error">{{ error }}</p>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const error = ref('')
const auth = useAuthStore()
const router = useRouter()

const handleLogin = async () => {
  try {
    await auth.login(email.value, password.value)
    if (auth.user?.role === 'agent') {
      router.push('/agent-dashboard')
    } else {
      router.push('/events')
    }
  } catch (err) {
    console.error(err)
    error.value = 'Login failed'
  }
}
</script>
