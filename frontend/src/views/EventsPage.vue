<template>
  <div
    class="min-h-screen flex flex-col gap-12 items-center justify-center p-6 bg-linear-to-tr from-blue-200 to-cyan-200"
  >
    <h1 class="font-bold text-2xl md:text-3xl">My Events</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl">
      <div class="h-full">
        <button
          v-if="!isCreating"
          @click="isCreating = true"
          class="w-full h-full flex flex-col items-center justify-center border-2 border-dashed border-gray-400 rounded-2xl p-6 text-gray-500 hover:border-teal-500 hover:bg-white hover:text-teal-600 transition-all group"
        >
          <Plus :size="40" class="group-hover:scale-110 transition-transform" />
          <span class="text-lg font-semibold mt-2">Create New Event</span>
        </button>
        <div
          v-else
          class="w-full h-full bg-white border-2 border-teal-500 rounded-2xl p-5 shadow-lg shadow-teal-100 flex flex-col gap-3"
        >
          <h3 class="font-bold text-teal-600">New Event</h3>
          <input
            v-model="newEvent.title"
            type="text"
            placeholder="Title"
            class="w-full p-2 text-sm border border-gray-200 rounded-lg focus:outline-teal-500"
          />
          <input
            v-model="newEvent.occurrence"
            type="datetime-local"
            class="w-full p-2 text-sm border border-gray-200 rounded-lg focus:outline-teal-500"
          />
          <textarea
            v-model="newEvent.description"
            placeholder="Description"
            class="w-full p-2 text-sm border border-gray-200 rounded-lg focus:outline-teal-500"
          ></textarea>
          <div class="flex gap-2 pt-2">
            <button
              @click="resetForm"
              class="flex-1 text-xs bg-gray-200 text-gray-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300"
            >
              Cancel
            </button>
            <button
              @click="handleCreate"
              class="flex-1 bg-teal-600 text-white text-xs px-4 py-2 rounded-lg font-bold"
            >
              Create
            </button>
          </div>
        </div>
      </div>
      <div
        v-for="event in events"
        :key="event.id"
        class="bg-white rounded-xl shadow-md p-5 border border-gray-100 flex flex-col justify-between hover:shadow-lg transition-shadow"
      >
        <div>
          <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-2">{{ event.title }}</h2>
          <h3 class="flex items-center gap-2 text-sm font-medium text-teal-600 mb-3">
            <CalendarDays />
            {{ formatTime(event.occurrence) }}
          </h3>
          <div v-if="editingId === event.id">
            <textarea
              v-model="editValue"
              class="w-full p-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none text-sm text-gray-600 bg-teal-50/30"
              rows="4"
            ></textarea>
            <div class="flex gap-2 mt-2 mb-3 w-full">
              <button
                @click="saveEdit(event)"
                class="text-xs bg-teal-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-teal-700"
              >
                Save
              </button>
              <button
                @click="cancelEdit"
                class="text-xs bg-gray-200 text-gray-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300"
              >
                Cancel
              </button>
            </div>
          </div>
          <p v-else class="text-gray-600 text-sm line-clamp-4">
            {{ event.description || 'No description provided.' }}
          </p>
          <div class="flex w-full">
            <button
              v-if="editingId !== event.id"
              @click="startEdit(event)"
              class="ml-auto mb-2 w-fit text-gray-400 hover:text-teal-600 p-1 rounded-full hover:bg-teal-50 transition-colors"
              title="Edit description"
            >
              <Pencil :size="18" />
            </button>
          </div>
        </div>
        <div
          v-if="showDeleteModal"
          class="fixed inset-0 z-10 flex items-center justify-center p-4 backdrop-blur-xs"
        >
          <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl transform transition-all">
            <div class="flex flex-col items-center text-center">
              <p class="text-gray-500 mb-6">Do you really want to delete?</p>
              <div class="flex gap-3 w-full">
                <button
                  @click="cancelDelete"
                  class="flex-1 py-2.5 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300"
                >
                  Cancel
                </button>
                <button
                  @click="handlePermanentDelete"
                  class="flex-1 py-2.5 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 shadow-lg shadow-red-200"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
        <button
          @click="confirmDelete(event)"
          class="mt-auto w-full py-2 bg-red-50 text-red-600 font-semibold rounded-lg hover:bg-red-600 hover:text-white transition-colors duration-200"
        >
          Delete Event
        </button>
      </div>
    </div>
    <button
      @click="handleLogout"
      class="absolute top-6 right-6 flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-200 rounded-xl shadow-sm transition-all duration-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 hover:shadow-md group"
    >
      <LogOut :size="18" class="group-hover:-translate-x-1 transition-transform duration-200" />
      Logout
    </button>
    <ChatWidget />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../axios'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import { Pencil, CalendarDays, Plus, LogOut } from 'lucide-vue-next'
import ChatWidget from '../components/ChatWidget.vue'

const auth = useAuthStore()
const router = useRouter()
const events = ref([])
const editingId = ref(null)
const editValue = ref('')
const showDeleteModal = ref(false)
const eventToDelete = ref(null)
const isCreating = ref(false)
const newEvent = ref({ title: '', occurrence: '', description: '' })

const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleTimeString('en-US', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const handleLogout = () => {
  auth.logout()
  router.push('/')
}

const fetchEvents = async () => {
  const res = await api.get('/events')
  events.value = res.data
}

onMounted(fetchEvents)

const confirmDelete = (event) => {
  eventToDelete.value = event
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  eventToDelete.value = null
}

const handlePermanentDelete = async () => {
  if (!eventToDelete.value) return
  try {
    await api.delete(`/events/${eventToDelete.value.id}`)
    events.value = events.value.filter((e) => e.id !== eventToDelete.value.id)
    showDeleteModal.value = false
  } catch (error) {
    alert(error.response?.data?.message || 'Error deleting event')
  }
}

const startEdit = (event) => {
  editingId.value = event.id
  editValue.value = event.description || ''
}

const cancelEdit = () => {
  editingId.value = null
}

const saveEdit = async (event) => {
  try {
    await api.put(`/events/${event.id}`, {
      description: editValue.value,
    })
    event.description = editValue.value
    editingId.value = null
  } catch (error) {
    alert(error.response?.data?.message || 'Error saving description')
  }
}

const handleCreate = async () => {
  try {
    await api.post('/events', newEvent.value)
    await fetchEvents()
    resetForm()
  } catch (error) {
    alert(error.response?.data?.message || 'Error creating event')
  }
}

const resetForm = () => {
  isCreating.value = false
  newEvent.value = { title: '', occurrence: '', description: '' }
}
</script>
