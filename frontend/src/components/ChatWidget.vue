<template>
  <div class="fixed bottom-6 right-6 ml-6 z-50 flex flex-col items-end">
    <div
      v-if="isOpen"
      class="mb-4 w-60 md:w-80 h-100 bg-white rounded-2xl shadow-2xl border border-gray-100 flex flex-col overflow-hidden transition-all duration-300"
    >
      <div class="p-4 bg-teal-600 text-white flex justify-between items-center shadow-md">
        <div class="flex items-center gap-2">
          <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
          <span class="font-semibold">Help Desk AI</span>
        </div>
        <button @click="isOpen = false" class="hover:bg-teal-700 p-1 rounded-lg transition-colors">
          <X :size="20"/>
        </button>
      </div>
      <div class="flex-1 p-4 overflow-y-auto bg-gray-50 flex flex-col gap-3">
        <div
          v-for="(msg, idx) in messages"
          :key="idx"
          :class="[
            'max-w-4/5 p-3 rounded-2xl text-sm shadow-sm',
            msg.sender_type === 'user'
              ? 'bg-teal-500 text-white self-end rounded-br-none'
              : 'bg-white text-gray-700 self-start rounded-bl-none',
          ]"
        >
          {{ msg.message }}
        </div>
      </div>
      <div class="p-4 border-t border-gray-100 bg-white flex gap-2">
        <input
          v-model="newMessage"
          @keyup.enter="sendMessage"
          type="text"
          placeholder="Type a message..."
          class="flex-1 text-sm focus:outline-none"
        />
        <button @click="sendMessage" class="text-teal-600 hover:scale-110 transition-transform">
          <Send :size="20" />
        </button>
      </div>
    </div>
    <button
      @click="isOpen = !isOpen"
      class="w-12 md:w-14 h-12 md:h-14 bg-teal-600 rounded-full flex items-center justify-center text-white shadow-lg hover:bg-teal-700 hover:scale-110 transition-all"
    >
      <MessageCircle v-if="!isOpen" class="w-6 h-6 md:w-7 md:h-7" />
      <X v-else class="w-6 h-6 md:w-7 md:h-7" />
    </button>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import axios from '../axios'
import { MessageCircle, X, Send } from 'lucide-vue-next'

const isOpen = ref(false)
const newMessage = ref('')
const messages = ref([
  { message: "Hello! I'm your AI assistant. How can I help you today?", sender_type: 'bot' },
])

const sendMessage = async () => {
  if (!newMessage.value.trim()) return
  messages.value.push({
    message: newMessage.value,
    sender_type: 'user',
  })

  const textToSend = newMessage.value
  newMessage.value = ''

  try {
    const response = await axios.post('/chat/send', { message: textToSend })

    messages.value.push({
      message: response.data.reply,
      sender_type: 'bot',
    })
  } catch (error) {
    console.error('Error at sending message:', error)
  }
}
</script>
