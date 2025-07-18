<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    title: '',
    body: '',
});

const submit = () => form.post(route('teacher.announcements.store'));
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-2xl mx-auto space-y-4 p-6 bg-white rounded shadow">
      <h1 class="text-xl font-bold">📢 Post Class Announcement</h1>

      <input v-model="form.title" class="border p-2 w-full rounded" placeholder="Title"/>

      <textarea v-model="form.body" class="border p-2 w-full rounded" placeholder="Body"></textarea>

      <button @click="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Post</button>
    </div>
  </AuthenticatedLayout>
</template>
