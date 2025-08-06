<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  topClasses: {
    type: Array,
    default: () => [],
  },
  summary: {
    type: Object,
    default: () => ({
      total_classes: 0,
      total_students: 0,
      total_teachers: 0,
      overall_average: 0,
    }),
  },
  announcements: {
    type: Array,
    default: () => [],
  },
});
</script>

<template>
  <Head title="Admin Dashboard" />

  <AuthenticatedLayout title="Admin Dashboard">
    <template #header>
      <h2 class="text-xl font-bold text-gray-800">🛠️ Admin Dashboard</h2>
    </template>

    <div class="py-12 max-w-6xl mx-auto space-y-6">

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
          <h4 class="text-sm text-gray-600">Total Students</h4>
          <p class="text-xl font-bold text-gray-800">{{ summary.total_students }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-purple-500">
          <h4 class="text-sm text-gray-600">Total Teachers</h4>
          <p class="text-xl font-bold text-gray-800">{{ summary.total_teachers }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-indigo-500">
          <h4 class="text-sm text-gray-600">Total Classes</h4>
          <p class="text-xl font-bold text-gray-800">{{ summary.total_classes }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-teal-500">
          <h4 class="text-sm text-gray-600">Overall Average</h4>
          <p class="text-xl font-bold text-gray-800">{{ summary.overall_average }}</p>
        </div>
      </div>

      <!-- Top 3 Performing Classes -->
      <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">🏆 Top 3 Performing Classes</h3>
        <ul class="space-y-2">
          <li v-for="(cls, index) in topClasses" :key="cls.id" class="border-b pb-2">
            <span class="font-bold text-gray-800">#{{ index + 1 }}: {{ cls.name }}</span>
            <span class="text-sm text-gray-600"> - Grade {{ cls.grade_level }}</span>
            <p class="text-sm text-gray-500">Average: <span class="font-semibold">{{ cls.average }}</span></p>
          </li>
        </ul>
        <p v-if="topClasses.length === 0" class="text-gray-500">No class data available.</p>
      </div>

      <!-- Latest Announcements -->
      <div class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">📢 Latest Announcements</h3>
        <ul class="space-y-4">
          <li v-for="announcement in announcements" :key="announcement.id">
            <h4 class="font-bold text-gray-800">{{ announcement.title }}</h4>
            <p class="text-sm text-gray-600">{{ announcement.body }}</p>
            <p class="text-xs text-gray-400">
              Posted by <span class="font-medium">{{ announcement.created_by }}</span>
              • {{ new Date(announcement.created_at).toLocaleString() }}
            </p>
          </li>
        </ul>
        <p v-if="announcements.length === 0" class="text-gray-500">No recent announcements.</p>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
