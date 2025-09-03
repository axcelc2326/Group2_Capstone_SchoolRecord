<script setup>
import { useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
  show: Boolean,
})
const emit = defineEmits(['close'])

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const submit = () => {
  form.post(route('teachers.store'), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Teacher registered successfully!',
        background: '#1f2937',
        color: '#f9fafb',
        iconColor: '#22c55e',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      })
      form.reset()
      emit('close')
    }
  })
}
</script>

<template>
  <div v-show="show" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-96 p-6">
      <h2 class="text-xl font-bold mb-4">Register Teacher</h2>

      <!-- Name -->
      <div class="mb-3">
        <label class="block mb-1 font-medium">Full Name</label>
        <input v-model="form.name" type="text" class="w-full px-3 py-2 border rounded-lg" />
        <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
      </div>

      <!-- Email -->
      <div class="mb-3">
        <label class="block mb-1 font-medium">Email</label>
        <input v-model="form.email" type="email" class="w-full px-3 py-2 border rounded-lg" />
        <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
      </div>

      <!-- Password -->
      <div class="mb-3">
        <label class="block mb-1 font-medium">Password</label>
        <input v-model="form.password" type="password" class="w-full px-3 py-2 border rounded-lg" />
        <span v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</span>
      </div>

      <!-- Confirm Password -->
      <div class="mb-4">
        <label class="block mb-1 font-medium">Confirm Password</label>
        <input v-model="form.password_confirmation" type="password" class="w-full px-3 py-2 border rounded-lg" />
      </div>

      <!-- Actions -->
      <div class="flex justify-end space-x-2">
        <button @click="$emit('close')" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
          Cancel
        </button>
        <button @click="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Save
        </button>
      </div>
    </div>
  </div>
</template>
