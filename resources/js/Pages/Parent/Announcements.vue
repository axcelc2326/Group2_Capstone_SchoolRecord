<script setup>
defineProps({ announcements: Array });
</script>

<template>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">Announcements</h1>
    <div v-for="announcement in announcements" :key="announcement.id" class="p-4 bg-white shadow rounded mb-4">
      <h2 class="font-semibold text-lg">{{ announcement.title }}</h2>
      <p class="text-sm text-gray-600">{{ announcement.body }}</p>
    </div>
  </div>
</template>
