<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  teachers: Array,
  classes: Array,
})

const form = useForm({
  class_id: '',
  teacher_id: '',
})

const submit = () => form.post(route('admin.assign-teacher.submit'))
</script>

<template>
  <AuthenticatedLayout>
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

        <!-- Submit -->
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
  </AuthenticatedLayout>
</template>
