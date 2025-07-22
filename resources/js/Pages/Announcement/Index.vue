<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  announcements: Array,
  parentClassIds: Array, // ✅ Updated to array
})
</script>

<template>
  <Head title="Announcements" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">📢 Announcements</h2>
    </template>

    <div class="py-8 max-w-4xl mx-auto space-y-4">

      <div v-if="parentClassIds?.length" class="mb-4 text-sm text-gray-700">
        🎓 You are viewing announcements for your children’s classes 
        (Class IDs: {{ parentClassIds.join(', ') }}) and global announcements.
      </div>

      <div v-if="announcements.length === 0" class="text-center text-gray-500">
        No announcements available.
      </div>

      <div
        v-for="announcement in announcements"
        :key="announcement.id"
        class="bg-white shadow p-6 rounded-lg"
      >
        <h3 class="text-lg font-bold text-gray-800">{{ announcement.title }}</h3>

        <p class="text-sm text-gray-600 mb-1">
          📅 Posted on: {{ new Date(announcement.created_at).toLocaleDateString() }}
        </p>

        <p class="text-sm text-gray-600 mb-1">
          👤 Posted by: {{ announcement.creator.name }}
        </p>

        <p class="text-sm text-gray-600 mb-3">
          <span v-if="announcement.class_id">
            📚 Class-Specific (Class ID: {{ announcement.class_id }})
          </span>
          <span v-else>🌐 Global Announcement</span>
        </p>

        <p class="text-gray-700 whitespace-pre-line">{{ announcement.body }}</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
