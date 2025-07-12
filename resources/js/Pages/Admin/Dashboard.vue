<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Admin Dashboard" />

  <!-- Pass the title to layout for dynamic navbar -->
  <AuthenticatedLayout title="Admin Dashboard">
    <template #header>
      <h2 class="text-xl font-bold text-gray-800">🛠️ Admin Dashboard</h2>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto space-y-6">
        <div class="bg-white p-6 shadow sm:rounded-lg">
          <h3 class="text-lg font-semibold mb-4">Management Tools</h3>
          <ul class="space-y-3 text-blue-600 list-disc list-inside">
            <li>
              <Link :href="route('classes.create')" class="hover:underline">
                🏫 Create Class
              </Link>
            </li>
            <li>
              <Link :href="route('admin.assign-teacher')" class="hover:underline">
                🧑‍🏫 Assign Teacher to Class
              </Link>
            </li>
            <li>
              <Link :href="route('announcements.create')" class="hover:underline">
                📢 Post Announcement
              </Link>
            </li>
            <li>
              <Link :href="route('analytics.index')" class="hover:underline">
                📊 View Class Analytics
              </Link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
