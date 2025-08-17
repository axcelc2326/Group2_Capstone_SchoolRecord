<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch, ref } from 'vue'

const props = defineProps({
  show: Boolean,
  subject: Object, // subject being edited
})

const emit = defineEmits(['close'])

const form = useForm({
  name: '',
  grade_level: '',
})

// Watch for subject change and pre-fill the form
watch(
  () => props.subject,
  (newSubject) => {
    if (newSubject) {
      form.name = newSubject.name
      form.grade_level = newSubject.grade_level
    }
  },
  { immediate: true }
)

function submit() {
  form.put(route('subjects.update', props.subject.id), {
    onSuccess: () => {
      emit('close')
      form.reset()
    },
  })
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
    <div class="bg-gray-600 rounded-lg shadow-lg w-full max-w-md p-6">
      <h2 class="text-white font-semibold mb-4">Edit Subject</h2>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-white font-medium">Name</label>
          <input v-model="form.name" type="text" class="w-full border rounded px-3 py-2" required />
        </div>

        <div>
          <label class="block text-white font-medium">Grade Level</label>
          <input v-model="form.grade_level" type="number" min="1" max="12" class="w-full border rounded px-3 py-2" required />
        </div>

        <div class="flex justify-end gap-2 mt-4">
          <button type="button" @click="$emit('close')" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
        </div>
      </form>
    </div>
  </div>
</template>
