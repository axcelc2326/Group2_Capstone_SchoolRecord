<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Parent Dashboard" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-bold text-gray-800">👪 Parent Dashboard</h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white p-6 shadow sm:rounded-lg">
          <h3 class="text-lg font-semibold mb-4">Quick Access</h3>
          <ul class="space-y-3 text-blue-600">
            <li>
              <Link :href="route('students.create')" class="hover:underline">
                ➕ Register My Child
              </Link>
            </li>
            <li>
              <Link :href="route('parent.grades')" class="hover:underline">
                📚 View My Child's Grades
              </Link>
            </li>
            <li>
              <Link :href="route('announcements.index')" class="hover:underline">
                📢 View Announcements
              </Link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
