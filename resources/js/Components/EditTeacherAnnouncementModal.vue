<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

const props = defineProps({
  show: Boolean,
  announcement: Object,
  updateRoute: String,
})

const emit = defineEmits(['close', 'updated'])

const form = useForm({
  title: '',
  body: '',
})

// 👀 Watch props.announcement and sync form values
watch(
  () => props.announcement,
  (newAnnouncement) => {
    if (newAnnouncement) {
      form.title = newAnnouncement.title
      form.body = newAnnouncement.body
    }
  },
  { immediate: true }
)

const submit = () => {
  form.put(route(props.updateRoute, props.announcement.id), {
    onSuccess: () => {
      emit('updated')
      emit('close')
    },
  })
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
  >
    <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 space-y-4">
      <h2 class="text-lg font-bold">✏️ Edit Announcement</h2>

      <input
        v-model="form.title"
        class="border p-2 w-full rounded"
        placeholder="Title"
      />
      <textarea
        v-model="form.body"
        class="border p-2 w-full rounded"
        placeholder="Body"
      ></textarea>

      <div class="flex justify-end space-x-2">
        <button
          @click="$emit('close')"
          class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300"
        >
          Cancel
        </button>
        <button
          @click="submit"
          class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
          :disabled="form.processing"
        >
          Update
        </button>
      </div>
    </div>
  </div>
</template>
