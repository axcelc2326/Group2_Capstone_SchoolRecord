<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const form = useForm({
  name: '',
  grade_level: '',
})

function submit() {
  form.post(route('classes.store'))
}
</script>

<template>
  <Head title="Create Class" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">🏫 Create a New Class</h2>
    </template>

    <div class="max-w-xl mx-auto py-8">
      <form @submit.prevent="submit" class="space-y-4 bg-white p-6 rounded shadow">
        <div>
          <label class="block font-medium text-gray-700">Class Name</label>
          <input
            v-model="form.name"
            type="text"
            class="mt-1 block w-full border rounded p-2"
          />
          <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
            {{ form.errors.name }}
          </div>
        </div>

        <div>
          <label class="block font-medium text-gray-700">Grade Level</label>
          <input
            v-model="form.grade_level"
            type="number"
            min="1"
            max="12"
            class="mt-1 block w-full border rounded p-2"
          />
          <div v-if="form.errors.grade_level" class="text-red-600 text-sm mt-1">
            {{ form.errors.grade_level }}
          </div>
        </div>

        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Create Class
        </button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
