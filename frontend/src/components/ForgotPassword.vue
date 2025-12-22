<template>
  <div class="flex flex-col gap-2">
    <div class="flex flex-col gap-4 mb-2">
      <h1 class="mx-auto text-xl md:text-2xl">Reset Password</h1>
      <p class="text-xs text-gray-500 text-center">
        Enter your email and we'll send you a reset link.
      </p>
    </div>
    <form @submit.prevent="handleReset" class="flex flex-col gap-2 text-base md:text-lg">
      <input
        id="resetEmail"
        v-model="email"
        type="email"
        required
        placeholder="John@mail.com"
        class="mb-2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all"
      />
      <button
        class="bg-teal-600 text-white py-2 rounded-lg font-semibold hover:bg-teal-700 transition-colors"
        type="submit"
      >
        Send Reset Link
      </button>
      <button
        type="button"
        @click="$emit('show-login')"
        class="text-sm mx-auto text-gray-500 hover:text-teal-600 hover:underline mt-2"
      >
        Back to Login
      </button>
    </form>
    <p v-if="message" class="text-green-600 text-sm text-center mt-2">{{ message }}</p>
    <p v-if="error" class="text-red-600 text-sm text-center mt-2">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '../axios'

const email = ref('')
const message = ref('')
const error = ref('')

const handleReset = async () => {
  error.value = ''
  message.value = ''
  try {
    const response = await axios.post('http://localhost:8000/api/forgot-password', {
      email: email.value,
    })
    message.value = response.data.message
  } catch (err) {
    error.value = err.response?.data?.message || 'Something went wrong'
  }
}
</script>
