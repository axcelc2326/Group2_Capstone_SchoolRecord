<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  student: Object
});

const quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
</script>

<template>
  <Head title="Grade Progress" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">
        Grade Progress for {{ student.first_name }} {{ student.last_name }}
      </h2>
      <p class="text-sm text-gray-500">Class: {{ student.class.name }}</p>
    </template>

    <div class="py-8 px-8">
      <table class="min-w-full bg-white rounded shadow text-sm">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-2 border">Subject</th>
            <th v-for="q in quarters" :key="q" class="p-2 border">{{ q }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="subject in [...new Set(student.grades.map(g => g.subject.name))]" :key="subject">
            <td class="p-2 border font-medium">{{ subject }}</td>
            <td v-for="q in quarters" :key="q" class="p-2 border text-center">
              {{
                student.grades.find(g => g.subject.name === subject && g.quarter === q)?.grade ?? '—'
              }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>
</template>
