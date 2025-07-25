<script setup>
import { useForm, router, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  announcement: Object,
})

const form = useForm({
  title: props.announcement.title,
  body: props.announcement.body,
})

const submit = () => {
  form.put(route('announcements.updateAdmin', props.announcement.id))
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded shadow space-y-4">
      <h1 class="text-xl font-bold">✏️ Edit Announcement</h1>

      <input v-model="form.title" class="border p-2 w-full rounded" placeholder="Title" />
      <textarea v-model="form.body" class="border p-2 w-full rounded" placeholder="Body"></textarea>

      <!-- ✅ Fixed Back Button -->
      <Link
        :href="route('announcements.create')"
        class="inline-block text-sm text-gray-600 hover:text-blue-600 underline"
      >
        ← Back to Announcement List
      </Link>

      <button
        @click="submit"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Update
      </button>
    </div>
  </AuthenticatedLayout>
</template>
