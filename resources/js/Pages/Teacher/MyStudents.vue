<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  students: Array,
  message: String
});
</script>

<template>
  <Head title="My Students" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">👩‍🏫 My Students</h2>
    </template>

    <div class="py-6 px-8">
      <div v-if="message" class="text-red-600 font-medium mb-4">{{ message }}</div>

      <div v-if="students.length">
        <table class="min-w-full bg-white rounded shadow">
          <thead>
            <tr class="bg-gray-200 text-left text-sm text-gray-700">
              <th class="px-4 py-2">#</th>
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(student, index) in students" :key="student.id" class="border-t text-sm">
              <td class="px-4 py-2">{{ index + 1 }}</td>
              <td class="px-4 py-2">{{ student.first_name }} {{ student.last_name }}</td>
              <td class="px-4 py-2">
                <Link :href="route('grades.create', { student: student.id })"
                      class="text-blue-600 hover:underline">
                  📝 Input Grades
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="text-center text-gray-500 mt-6">
        📭 No approved students found in your class.
      </div>
    </div>
  </AuthenticatedLayout>
</template>
