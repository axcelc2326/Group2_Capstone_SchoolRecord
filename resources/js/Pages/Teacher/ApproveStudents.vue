<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
  students: Array,
});

const approve = (studentId) => {
  router.put(route('students.approve', { student: studentId }));
};
</script>

<template>
  <Head title="Approve Students" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">👨‍🏫 Approve Students</h2>
    </template>

    <div class="py-6 max-w-6xl mx-auto">
      <div v-if="students.length" class="space-y-4">
        <div
          v-for="student in students"
          :key="student.id"
          class="p-4 bg-white shadow rounded-lg flex justify-between items-center"
        >
          <div>
            <p class="font-semibold text-lg">{{ student.first_name }} {{ student.last_name }}</p>
            <p class="text-sm text-gray-600">Grade Level: {{ student.class?.grade_level }}</p>
            <p class="text-sm text-gray-600">Class: {{ student.class?.name }}</p>
          </div>
          <button
            @click="approve(student.id)"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
          >
            ✅ Approve
          </button>
        </div>
      </div>

      <div v-else class="text-gray-500 text-center">
        No pending student approvals.
      </div>
    </div>
  </AuthenticatedLayout>
</template>
