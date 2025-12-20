<template>
  <div>
    <h1>My Events</h1>
    <ul>
      <li v-for="event in events" :key="event.id">
        {{ event.title }} - {{ event.occurrence }}
        <button @click="deleteEvent(event.id)">Delete</button>
      </li>
    </ul>
    <button @click="handleLogout">Logout</button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../axios';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();
const events = ref([]);

const handleLogout = () => {
  auth.logout();
  router.push('/');
};

const fetchEvents = async () => {
  const res = await api.get('/events');
  events.value = res.data;
};

const deleteEvent = async (id) => {
  await api.delete(`/events/${id}`);
  fetchEvents();
};



onMounted(fetchEvents);
</script>