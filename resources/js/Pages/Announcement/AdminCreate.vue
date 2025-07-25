<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, watch } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  announcements: Object, // paginated
})

const form = useForm({
  title: '',
  body: '',
})

const submit = () => {
  form.post(route('announcements.store'), {
    onSuccess: () => form.reset(),
  })
}

const deleteAnnouncement = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will delete the announcement permanently.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('announcements.destroyAdmin', id))
    }
  })
}
</script>

<template>
  <Head title="Create Announcement" />

  <AuthenticatedLayout>
    <div class="max-w-2xl mx-auto space-y-4 p-6 bg-white rounded shadow">
      <h1 class="text-xl font-bold">📢 Admin Create Global Announcement</h1>

      <input v-model="form.title" class="border p-2 w-full rounded" placeholder="Title" />

      <textarea v-model="form.body" class="border p-2 w-full rounded" placeholder="Body"></textarea>

      <p class="text-sm text-gray-600">This will be visible to all parents.</p>

      <button @click="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
        Post
      </button>
    </div>

    <!-- ✅ Announcement List -->
    <div class="max-w-3xl mx-auto mt-8 space-y-4">
      <h2 class="text-lg font-semibold">📄 Posted Announcements</h2>

      <div v-for="announcement in props.announcements.data" :key="announcement.id" class="border p-4 rounded shadow-sm">
        <h3 class="text-md font-bold">{{ announcement.title }}</h3>
        <p class="text-gray-700 mb-2">{{ announcement.body }}</p>
        <p class="text-xs text-gray-400">Posted: {{ new Date(announcement.created_at).toLocaleString() }}</p>

        <!-- Actions -->
        <div class="mt-2 space-x-2">
          <button class="text-blue-600 hover:underline" @click="router.visit(route('announcements.editAdmin', announcement.id))">
            ✏️ Edit
          </button>
          <button class="text-red-600 hover:underline" @click="deleteAnnouncement(announcement.id)">
            🗑️ Delete
          </button>
        </div>
      </div>

      <!-- ✅ Pagination -->
      <div class="flex justify-center mt-6 space-x-2">
        <template v-for="(link, index) in announcements.links" :key="index">
          <button
            v-if="link.url"
            :class="[
              'px-3 py-1 rounded',
              link.active ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300',
            ]"
            @click="router.visit(link.url)"
            v-html="link.label"
          />
        </template>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
