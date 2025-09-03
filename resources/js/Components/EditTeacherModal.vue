<script setup>
import { watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
  show: Boolean,
  teacher: Object, // teacher being edited
})
const emit = defineEmits(['close'])

const form = useForm({
  name: props.teacher?.name || '',
  email: props.teacher?.email || '',
  password: '',
  password_confirmation: ''
})

// Watch when teacher changes (so modal resets properly)
watch(() => props.teacher, (newTeacher) => {
  if (newTeacher) {
    form.name = newTeacher.name
    form.email = newTeacher.email
    form.password = ''
    form.password_confirmation = ''
  }
})

const submit = () => {
  form.put(route('teachers.update', props.teacher.id), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Teacher updated successfully!',
        background: '#1f2937',
        color: '#f9fafb',
        iconColor: '#22c55e',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      })
      form.reset('password', 'password_confirmation') // reset only password fields
      emit('close')
    }
  })
}
</script>

<template>
  <div v-show="show" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-96 p-6">
      <h2 class="text-xl font-bold mb-4">Edit Teacher</h2>

      <!-- Teacher Info Summary -->
      <div v-if="teacher" class="mb-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm">
        <p><span class="font-semibold">Current Name:</span> {{ teacher.name }}</p>
        <p><span class="font-semibold">Current Email:</span> {{ teacher.email }}</p>
        <p><span class="font-semibold">Current Password:</span> (hidden for security)</p>
      </div>

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

      <!-- Password (optional) -->
      <div class="mb-3">
        <label class="block mb-1 font-medium">Password (leave blank to keep)</label>
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
        <button @click="submit" :disabled="form.processing" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
          Update
        </button>
      </div>
    </div>
  </div>
</template>