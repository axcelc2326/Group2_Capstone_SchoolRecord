<!-- resources/js/Components/RegisterStudentModal.vue -->
<script setup>
import { useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { watch, defineProps, defineEmits } from 'vue'

const props = defineProps({
  show: Boolean,
  classes: Array
})

const emit = defineEmits(['close', 'created'])

const form = useForm({
  first_name: '',
  last_name: '',
  class_id: ''
})

// Reset form whenever modal is opened
watch(() => props.show, (isOpen) => {
  if (isOpen) form.reset()
})

const submit = () => {
  form.post(route('students.store'), {
    onSuccess: () => {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Student registered successfully',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
      }).then(() => {
            location.reload() // Refreshes the page
        });
      emit('created') // tell parent to reload list
      emit('close')
    },
  })
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
      <h3 class="text-lg font-semibold mb-4">Register a Student</h3>
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">First Name</label>
            <input
              v-model="form.first_name"
              type="text"
              class="text-black mt-1 w-full border rounded px-3 py-2"
              required
            />
            <div v-if="form.errors.first_name" class="text-red-500 text-sm">
              {{ form.errors.first_name }}
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium">Last Name</label>
            <input
              v-model="form.last_name"
              type="text"
              class="text-black mt-1 w-full border rounded px-3 py-2"
              required
            />
            <div v-if="form.errors.last_name" class="text-red-500 text-sm">
              {{ form.errors.last_name }}
            </div>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium">Select Class</label>
          <select
            v-model="form.class_id"
            class="text-black mt-1 w-full border rounded px-3 py-2"
            required
          >
            <option value="">Select Class</option>
            <option
              v-for="cls in classes"
              :key="cls.id"
              :value="cls.id"
            >
              {{ cls.name }} (Grade {{ cls.grade_level }})
            </option>
          </select>
          <div v-if="form.errors.class_id" class="text-red-500 text-sm">
            {{ form.errors.class_id }}
          </div>
        </div>

        <div class="flex justify-end space-x-2">
          <button
            type="button"
            @click="emit('close')"
            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
          >
            Register
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
