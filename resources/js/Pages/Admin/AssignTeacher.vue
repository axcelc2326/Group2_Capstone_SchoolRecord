<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  teachers: Array,
  classes: Array,
  assignments: Object, // paginated assignments
})

const form = useForm({
  class_id: '',
  teacher_id: '',
})

const submit = () => form.post(route('admin.assign-teacher.submit'))
</script>

<template>
  <AuthenticatedLayout>
    <!-- Assign Form -->
    <div class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow-md">
      <h1 class="text-2xl font-bold mb-6">🧑‍🏫 Assign Teacher to Class</h1>

      <form @submit.prevent="submit" class="space-y-5">
        <!-- Select Class -->
        <div>
          <label class="block mb-1 font-medium">Select Class</label>
          <select v-model="form.class_id" class="w-full border rounded-lg p-2" required>
            <option value="" disabled>Select a class</option>
            <option v-for="cls in props.classes" :key="cls.id" :value="cls.id">
              {{ cls.name }} ({{ 'Grade ' + cls.grade_level ?? 'Grade ?' }}) — 
              {{ cls.teacher?.name ? 'Current: ' + cls.teacher.name : 'No Teacher Assigned' }}
            </option>
          </select>
        </div>

        <!-- Select Teacher -->
        <div>
          <label class="block mb-1 font-medium">Select Teacher</label>
          <select v-model="form.teacher_id" class="w-full border rounded-lg p-2" required>
            <option value="" disabled>Select a teacher</option>
            <option v-for="teacher in props.teachers" :key="teacher.id" :value="teacher.id">
              {{ teacher.name }}
            </option>
          </select>
        </div>

        <!-- Submit Button -->
        <div class="text-right">
          <button
            type="submit"
            class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50"
            :disabled="form.processing"
          >
            {{ form.processing ? 'Assigning...' : 'Assign Teacher' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Assignment Table Section -->
    <div class="max-w-5xl mx-auto mt-12 bg-white rounded-xl shadow-md p-6">
      <h2 class="text-xl font-semibold mb-4">📋 Current Class-Teacher Assignments</h2>

      <div class="overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 text-left">Class Name</th>
              <th class="px-4 py-2 text-left">Grade Level</th>
              <th class="px-4 py-2 text-left">Assigned Teacher</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="cls in props.assignments.data"
              :key="cls.id"
              class="border-t hover:bg-gray-50"
            >
              <td class="px-4 py-2">{{ cls.name }}</td>
              <td class="px-4 py-2">Grade {{ cls.grade_level }}</td>
              <td class="px-4 py-2">
                {{ cls.teacher?.name ?? '— No Teacher Assigned —' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Laravel-style Pagination -->
      <div v-if="props.assignments.links.length > 3" class="flex justify-center space-x-1 mt-6 flex-wrap">
        <template v-for="(link, index) in props.assignments.links" :key="index">
          <span
            v-if="!link.url"
            class="px-3 py-1 text-gray-500 cursor-default"
            v-html="link.label"
          ></span>
          <Link
            v-else
            :href="link.url"
            class="px-3 py-1 rounded"
            :class="link.active ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
            v-html="link.label"
          />
        </template>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
