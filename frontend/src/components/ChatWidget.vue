<template>
  <div class="fixed bottom-6 right-6 ml-6 z-50 flex flex-col items-end">
    <div
      v-if="isOpen"
      class="mb-4 w-72 md:w-80 h-100 bg-white rounded-2xl shadow-2xl border border-gray-100 flex flex-col overflow-hidden transition-all duration-300 relative"
    >
      <div class="p-4 bg-teal-600 text-white flex justify-between items-center shadow-md">
        <div class="flex items-center gap-2">
          <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
          <span class="font-semibold">Help Desk AI</span>
        </div>
        <button @click="isOpen = false" class="hover:bg-teal-700 p-1 rounded-lg transition-colors">
          <X :size="20" />
        </button>
      </div>
      <div
        ref="chatContainer"
        class="flex-1 p-4 overflow-y-auto bg-gray-50 flex flex-col gap-3 relative"
      >
        <div
          v-for="(msg, idx) in messages"
          :key="idx"
          :class="[
            'max-w-4/5 p-3 rounded-2xl text-sm shadow-sm',
            msg.sender_type === 'user' ? 'bg-teal-500 text-white self-end rounded-br-none' : '',
            msg.sender_type === 'bot' ? 'bg-white text-gray-700 self-start rounded-bl-none' : '',
            msg.sender_type === 'agent' ? 'bg-blue-600 text-white self-start rounded-bl-none' : '',
          ]"
        >
          <p v-if="msg.sender_type === 'agent'" class="text-xs mb-1 opacity-70 uppercase font-bold">
            Support Agent
          </p>
          <p class="text-xs md:text-sm">{{ msg.message }}</p>
        </div>
        <div v-if="messages.length > 1" class="flex justify-center py-2">
          <button
            @click="showDeleteConfirm = true"
            class="text-xs text-gray-400 hover:text-red-500 transition-colors flex items-center gap-1"
          >
            <Trash2 :size="14" /> Clear conversation
          </button>
        </div>
      </div>
      <div
        v-if="showDeleteConfirm"
        class="absolute inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/10 backdrop-blur-xs"
      >
        <div class="bg-white rounded-2xl p-5 w-full shadow-2xl border border-gray-100">
          <div class="flex flex-col items-center text-center">
            <p class="text-gray-600 text-sm mb-4 font-medium">Do you really want to delete?</p>
            <div class="flex gap-2 w-full">
              <button
                @click="showDeleteConfirm = false"
                class="flex-1 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200"
              >
                Cancel
              </button>
              <button
                @click="handlePermanentDelete"
                class="flex-1 py-2 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 shadow-md shadow-red-200"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="p-4 border-t border-gray-100 bg-white flex items-center gap-2">
        <input
          v-model="newMessage"
          type="text"
          placeholder="Type your message here..."
          @keyup.enter="sendMessage"
          class="w-full p-3 focus:outline-none"
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
import { ref, watch, nextTick, onMounted } from 'vue'
import axios from '../axios'
import { MessageCircle, X, Send, Trash2 } from 'lucide-vue-next'

const isOpen = ref(false)
const newMessage = ref('')
const messages = ref([])
const chatContainer = ref(null)
const showDeleteConfirm = ref(false)

const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight
    }
  })
}

watch(
  messages,
  () => {
    scrollToBottom()
  },
  { deep: true },
)

const fetchMessages = async () => {
  try {
    const response = await axios.get('/chat/get')
    if (response.data.length > messages.value.length || response.data.length === 0) {
      if (response.data.length === 0) {
        messages.value = [
          {
            message: "Hello! I'm your AI assistant. How can I help you today?",
            sender_type: 'bot',
          },
        ]
      } else {
        messages.value = response.data
      }
      scrollToBottom()
    }
  } catch (error) {
    console.error('Error loading messages:', error)
  }
}

onMounted(() => {
  fetchMessages()
  setInterval(fetchMessages, 5000)
  scrollToBottom()
})

const sendMessage = async () => {
  if (!newMessage.value.trim()) return
  messages.value.push({
    message: newMessage.value,
    sender_type: 'user',
  })

  const textToSend = newMessage.value
  newMessage.value = ''

  try {
    await axios.post('/chat/send', { message: textToSend })

    await fetchMessages()
  } catch (error) {
    console.error('Error at sending message:', error)
  }
}

const handlePermanentDelete = async () => {
  showDeleteConfirm.value = false
  try {
    await axios.post('/chat/clear')

    messages.value = [
      { message: "Hello! I'm your AI assistant. How can I help you today?", sender_type: 'bot' },
    ]

    console.log('Chat history cleared successfully')
  } catch (error) {
    console.error('Failed to delete chat history:', error)
    alert('Could not clear chat. Please try again.')
  }
}
</script>
