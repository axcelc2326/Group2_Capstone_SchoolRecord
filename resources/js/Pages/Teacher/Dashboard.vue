<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Teacher Dashboard" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-bold text-gray-800">📘 Teacher Dashboard</h2>
    </template>

    <div class="py-12">
      <div class="max-w-5xl mx-auto space-y-6">
        <div class="bg-white p-6 shadow sm:rounded-lg">
          <h3 class="text-lg font-semibold mb-4">Teaching Tools</h3>
          <ul class="space-y-3 text-blue-600">
            <li>
              <Link :href="route('teacher.students')" class="hover:underline">
                👨‍🏫 View My Students
              </Link>
            </li>
            <li>
              <Link :href="route('students.approval.list')" class="hover:underline">
                ✅ Approve Student Registrations
              </Link>
            </li>
            <li>
              <Link :href="route('sf5.download', { class: 1 })" class="hover:underline">
                🧾 Download SF5 (Select Class)
              </Link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
