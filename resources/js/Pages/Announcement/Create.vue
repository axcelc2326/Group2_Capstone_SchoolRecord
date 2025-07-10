<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  classes: Array,
});

const form = useForm({
  title: '',
  body: '',
  class_id: '',
});

const submit = () => form.post(route('announcements.store'));
</script>

<template>
<AuthenticatedLayout>
  <div class="max-w-2xl mx-auto p-6 bg-white rounded-2xl shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📢 Post Announcement</h1>

    <form @submit.prevent="submit" class="space-y-5">
      <!-- Title -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input
          v-model="form.title"
          type="text"
          placeholder="Enter title"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        />
      </div>

      <!-- Body -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
        <textarea
          v-model="form.body"
          rows="4"
          placeholder="Write your announcement here..."
          class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        ></textarea>
      </div>

      <!-- Submit Button -->
      <div class="text-right">
        <button
          type="submit"
          class="inline-flex items-center px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200 shadow-sm disabled:opacity-50"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Posting...' : 'Post Announcement' }}
        </button>
      </div>
    </form>
  </div>
</AuthenticatedLayout>
</template>
