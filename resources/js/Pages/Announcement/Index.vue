<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  announcements: Array
})

const page = usePage()
const user = computed(() => page.props.auth.user)
</script>

<template>
  <Head title="Announcements" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">📢 Announcements</h2>
    </template>

    <div class="py-8 max-w-4xl mx-auto space-y-4">
      <div
        v-if="announcements.length === 0"
        class="text-center text-gray-500"
      >
        No announcements available.
      </div>

      <div
        v-for="announcement in announcements"
        :key="announcement.id"
        class="bg-white shadow p-6 rounded-lg"
      >
        <h3 class="text-lg font-bold text-gray-800">{{ announcement.title }}</h3>
        <p class="text-sm text-gray-600 mb-2">
          Posted on: {{ new Date(announcement.created_at).toLocaleDateString() }}
        </p>
        <p class="text-gray-700">{{ announcement.content }}</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
