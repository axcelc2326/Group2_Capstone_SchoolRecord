<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  students: Array,
});

const quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
</script>

<template>
  <Head title="View Grades" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">📊 Your Child's Grades</h2>
    </template>

    <div class="py-6 px-6">
      <div class="max-w-6xl mx-auto space-y-8">
        <div
          v-for="student in students"
          :key="student.id"
          class="bg-white p-6 rounded shadow border"
        >
          <!-- Student Info -->
          <h3 class="text-lg font-bold mb-2 text-blue-700">
            👦 {{ student.first_name }} {{ student.last_name }}
          </h3>

          <p class="text-sm text-gray-600 mb-4">
            Class: <span class="font-medium">{{ student.class?.name ?? 'N/A' }}</span><br />
            Grade Level: <span class="font-medium">{{ student.class?.grade_level ?? 'N/A' }}</span>
          </p>

          <!-- Grade Table -->
          <div v-if="student.grades && student.grades.length > 0">
            <table class="w-full text-left border">
              <thead>
                <tr class="bg-gray-100">
                  <th class="p-2 border">Subject</th>
                  <th v-for="q in quarters" :key="q" class="p-2 border text-center">{{ q }}</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="subject in [...new Set(student.grades.map(g => g.subject.name))]"
                  :key="subject"
                >
                  <td class="p-2 border">{{ subject }}</td>
                  <td
                    v-for="q in quarters"
                    :key="q"
                    class="p-2 border text-center"
                  >
                    {{
                      student.grades.find(
                        g => g.subject.name === subject && g.quarter === q
                      )?.grade ?? '—'
                    }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-else class="text-gray-500 italic">
            No grades recorded yet for this student.
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
