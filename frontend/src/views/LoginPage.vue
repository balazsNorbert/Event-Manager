<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-linear-to-tr from-blue-200 to-cyan-200">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-sm">
      <div v-if="view === 'login'">
        <LoginForm @show-forgot="view = 'forgot'" />
      </div>
      <div v-else>
        <ForgotPassword @show-login="view = 'login'" />
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import LoginForm from '../components/LoginForm.vue'
import ForgotPassword from '../components/ForgotPassword.vue'

const view = ref('login')
</script>
