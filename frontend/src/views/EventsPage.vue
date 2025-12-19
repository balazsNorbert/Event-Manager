<template>
  <div>
    <h1>My Events</h1>
    <ul>
      <li v-for="event in events" :key="event.id">
        {{ event.title }} - {{ event.occurrence }}
        <button @click="deleteEvent(event.id)">Delete</button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../axios';

const events = ref([]);

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