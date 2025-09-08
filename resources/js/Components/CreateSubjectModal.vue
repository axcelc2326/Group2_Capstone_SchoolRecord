<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  show: Boolean,
})

const emit = defineEmits(['close', 'created'])

const form = useForm({
  name: '',
  grade_level: '',
})

function submit() {
  form.post(route('subjects.store'), {
    onSuccess: () => {
      form.reset()
      emit('created') // let parent refresh subjects
      emit('close')   // close modal
    },
  })
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <h2 class="text-lg font-bold mb-4">Add Subject</h2>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Subject Name -->
        <div>
          <label class="block text-sm font-medium">Subject Name</label>
          <input
            v-model="form.name"
            type="text"
            placeholder="Enter subject name"
            class="w-full border rounded px-3 py-2 mt-1"
            required
          />
          <div v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</div>
        </div>

        <!-- Grade Level -->
        <div>
          <label for="gradeLevel" class="block text-sm font-medium">Grade Level</label>
          <select
            id="gradeLevel"
            v-model="form.grade_level"
            class="block w-full pl-3 pr-3 py-2 border rounded mt-1"
            required
          >
            <option disabled value="">-- Select Grade Level --</option>
            <option value="K1">Kinder 1</option>
            <option value="K2">Kinder 2</option>
            <option v-for="n in 6" :key="n" :value="String(n)">
              Grade {{ n }}
            </option>
          </select>
          <div v-if="form.errors.grade_level" class="text-sm text-red-600">{{ form.errors.grade_level }}</div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-2">
          <button
            type="button"
            @click="emit('close')"
            class="px-4 py-2 bg-gray-200 rounded"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            :disabled="form.processing"
          >
            Add
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
