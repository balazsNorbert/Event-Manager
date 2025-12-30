<template>
  <div class="flex h-screen bg-gray-100 relative">
    <div class="w-1/3 bg-white border-r border-gray-200 flex flex-col">
      <div class="p-4 border-b border-gray-200 bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">Support Queue</h2>
        <p class="text-sm text-gray-500">{{ pendingChats.length }} users waiting</p>
      </div>

      <div class="overflow-y-auto flex-1">
        <div
          v-for="chat in pendingChats"
          :key="chat.id"
          @click="selectChat(chat)"
          :class="[
            'p-4 border-b cursor-pointer hover:bg-blue-50 transition',
            selectedChat?.id === chat.id ? 'bg-blue-50 border-l-4 border-blue-500' : '',
          ]"
        >
          <div class="flex justify-between items-start">
            <p class="font-semibold text-gray-900">{{ chat.name }}</p>
            <span class="text-xs text-gray-400 text-right">{{ formatTime(chat.updated_at) }}</span>
          </div>
        </div>
        <div v-if="pendingChats.length === 0" class="p-10 text-center text-gray-400">
          <p>No pending support requests.</p>
        </div>
      </div>
    </div>

    <div class="flex-1 flex flex-col">
      <template v-if="selectedChat">
        <div class="p-4 border-b border-gray-200 bg-white flex justify-between items-center">
          <div>
            <h3 class="font-bold text-lg">{{ selectedChat.name }}</h3>
            <p class="text-xs text-green-500 flex items-center">
              <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span> Active Session
            </p>
          </div>
        </div>
        <button @click="closeCurrentChat" class="bg-red-500 text-white px-4 py-2 rounded">
          Close Conversation
        </button>
        <div ref="chatContainer" class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50">
          <div
            v-for="msg in selectedChat.messages.slice().reverse()"
            :key="msg.id"
            :class="['flex', msg.sender_type === 'user' ? 'justify-start' : 'justify-end']"
          >
            <div
              :class="[
                'max-w-[70%] p-3 rounded-lg shadow-sm',
                msg.sender_type === 'user' ? 'bg-white text-gray-800' : 'bg-blue-600 text-white',
                msg.sender_type === 'bot' ? 'bg-gray-600 text-gray-800 italic' : '',
              ]"
            >
              <p class="text-xs mb-1 opacity-70 uppercase font-bold">{{ msg.sender_type }}</p>
              <p class="text-xs md:text-sm">{{ msg.message }}</p>
            </div>
          </div>
        </div>

        <div class="p-4 bg-white border-t border-gray-200">
          <div class="flex gap-2">
            <input
              v-model="agentReplyText"
              @keyup.enter="sendReply"
              placeholder="Type your official response..."
              class="flex-1 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
            />
            <button
              @click="sendReply"
              class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition"
            >
              Send Reply
            </button>
          </div>
        </div>
      </template>

      <div v-else class="flex-1 flex items-center justify-center text-gray-400">
        <p>Select a user from the queue to start helping.</p>
      </div>
    </div>
    <button
      @click="handleLogout"
      class="absolute top-6 right-6 flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-200 rounded-xl shadow-sm transition-all duration-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 hover:shadow-md group"
    >
      <LogOut :size="18" class="group-hover:-translate-x-1 transition-transform duration-200" />
      Logout
    </button>
  </div>
</template>
<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import { LogOut } from 'lucide-vue-next'

const pendingChats = ref([])
const selectedChat = ref(null)
const agentReplyText = ref('')
const auth = useAuthStore()
const router = useRouter()
const chatContainer = ref(null)

const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight
    }
  })
}

watch(
  () => selectedChat.value?.messages,
  () => {
    scrollToBottom()
  },
  { deep: true },
)

onMounted(() => {
  scrollToBottom()
})

const fetchPending = async () => {
  try {
    const response = await axios.get('/agent/pending')
    pendingChats.value = response.data

    if (selectedChat.value) {
      const updated = pendingChats.value.find((c) => c.id === selectedChat.value.id)
      if (updated) selectedChat.value = updated
    }
  } catch (error) {
    console.error('Failed to load queue:', error)
  }
}

const selectChat = (chat) => {
  selectedChat.value = chat
}

const sendReply = async () => {
  if (!agentReplyText.value.trim()) return

  try {
    await axios.post('/agent/reply', {
      user_id: selectedChat.value.id,
      message: agentReplyText.value,
    })
    agentReplyText.value = ''
    fetchPending()
  } catch (error) {
    console.error('Error at sending message:', error)
  }
}

const formatTime = (dateStr) => {
  return new Date(dateStr).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

let intervalId = null

onMounted(() => {
  if (auth.user?.role === 'agent') {
    fetchPending()
    intervalId = setInterval(fetchPending, 5000)
  }
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})

const handleLogout = () => {
  auth.logout()
  router.push('/')
}

const closeCurrentChat = async () => {
  if (!selectedChat.value) return

  try {
    await axios.post(`/chats/${selectedChat.value.id}/close`)
    pendingChats.value = pendingChats.value.filter((c) => c.id !== selectedChat.value.id)
    selectedChat.value = null
    alert('Chat closed and moved to archive.')
  } catch (err) {
    console.error('Failed to close chat', err)
  }
}
</script>
