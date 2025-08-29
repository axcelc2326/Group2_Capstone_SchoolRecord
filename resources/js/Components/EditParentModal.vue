<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
  show: Boolean,         // modal visibility
  parent: Object | null, // parent data to edit
})

const emit = defineEmits(['close', 'updated'])

// Form
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

// Watch for changes in parent prop and populate the form
watch(
  () => props.parent,
  (parent) => {
    if (parent) {
      form.name = parent.name
      form.email = parent.email
      form.password = ''
      form.password_confirmation = ''
    }
  },
  { immediate: true }
)

// Submit update
const submit = () => {
  if (!props.parent) return

  form.put(route('parents.update', props.parent.id), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Parent updated successfully',
        background: '#1f2937',
        color: '#f9fafb',
        iconColor: '#22c55e',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      })
      emit('close')
    },
  })
}
</script>

<template>
  <div v-show="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-96 p-6 relative">
      <h2 class="text-xl font-bold mb-4">Edit Parent</h2>
      <!-- Current Info -->
        <div class="mb-4 p-3 border rounded-lg bg-gray-100 dark:bg-gray-700">
        <h3 class="font-semibold mb-2">Current Information</h3>
        <p><strong>Name:</strong> {{ props.parent?.name }}</p>
        <p><strong>Email:</strong> {{ props.parent?.email }}</p>
        <p><strong>Password:</strong> ******** (hidden for security)</p>
        </div>

      <!-- Name -->
      <div class="mb-3">
        <label class="block mb-1 font-medium">Name</label>
        <input
          v-model="form.name"
          type="text"
          placeholder="Full Name"
          class="w-full px-3 py-2 border rounded-lg"
        />
        <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
      </div>

      <!-- Email -->
      <div class="mb-3">
        <label class="block mb-1 font-medium">Email</label>
        <input
          v-model="form.email"
          type="email"
          placeholder="Email"
          class="w-full px-3 py-2 border rounded-lg"
        />
        <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
      </div>

      <!-- Password -->
      <div class="mb-3">
        <label class="block mb-1 font-medium">Password (leave blank to keep current)</label>
        <input
          v-model="form.password"
          type="password"
          placeholder="New Password"
          class="w-full px-3 py-2 border rounded-lg"
        />
        <span v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</span>
      </div>

      <!-- Password Confirmation -->
      <div class="mb-4">
        <label class="block mb-1 font-medium">Confirm Password</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          placeholder="Confirm Password"
          class="w-full px-3 py-2 border rounded-lg"
        />
      </div>

      <!-- Actions -->
      <div class="flex justify-end space-x-2">
        <button
          @click="$emit('close')"
          class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400"
        >
          Cancel
        </button>
        <button
          @click="submit"
          :disabled="form.processing"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Save
        </button>
      </div>
    </div>
  </div>
</template>
