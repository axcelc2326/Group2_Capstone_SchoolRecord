<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  show: Boolean,
})

const emit = defineEmits(['close', 'saved'])

const form = useForm({
  title: '',
  body: '',
})

const submit = () => {
  // use the teacher route
  form.post(route('teacher.announcements.store'), {
    onSuccess: () => {
      form.reset()
      emit('saved')
    },
  })
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
  >
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 relative">
      <!-- Close -->
      <button
        @click="emit('close')"
        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
      >
        ✖
      </button>

      <h1 class="text-xl font-bold mb-4">📢 Post Class Announcement</h1>

      <input
        v-model="form.title"
        class="border p-2 w-full rounded mb-3"
        placeholder="Title"
      />
      <textarea
        v-model="form.body"
        class="border p-2 w-full rounded mb-3"
        placeholder="Body"
      ></textarea>

      <p class="text-sm text-gray-600">
        This will be visible only to your class.
      </p>

      <div class="flex justify-end space-x-2 mt-4">
        <button
          @click="emit('close')"
          class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition"
        >
          Cancel
        </button>
        <button
          @click="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
          :disabled="form.processing"
        >
          Post
        </button>
      </div>
    </div>
  </div>
</template>
