<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  classItem: Object,
})

const form = useForm({
  name: props.classItem.name,
  grade_level: props.classItem.grade_level,
})

const updateClass = () => {
  form.put(route('classes.update', props.classItem.id), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Edit Class" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">✏️ Edit Class</h2>
    </template>

    <div class="max-w-xl mx-auto py-8">
      <form @submit.prevent="updateClass" class="space-y-4 bg-white p-6 rounded shadow">
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

        <div class="flex justify-between items-center">
          <Link
            :href="route('classes.create')"
            class="text-sm text-gray-600 hover:underline"
          >
            ← Back to Class List
          </Link>

          <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            :disabled="form.processing"
          >
            Update Class
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
