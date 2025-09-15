<template>
  <!-- Modal Overlay -->
  <Teleport to="body">
    <Transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click="handleBackdropClick"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
          <Transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="opacity-0 scale-95 translate-y-4"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-4"
          >
            <div
              v-if="show"
              ref="modalContent"
              class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg p-8 shadow-2xl border border-gray-200/50 dark:border-gray-700/50 transition-all"
              @click.stop
            >
              <!-- Gradient overlay for visual depth -->
              <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-blue-900/20 dark:to-purple-900/20 pointer-events-none"></div>
              
              <form @submit.prevent="submit" class="relative space-y-6">
                <!-- Header -->
                <div class="text-center space-y-2">
                  <div class="w-12 h-12 mx-auto bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </div>
                  <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Student</h2>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Update student information</p>
                </div>

                <!-- First Name Field -->
                <div class="space-y-2">
                  <label for="first_name" class="text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                    </div>
                    <input
                      id="first_name"
                      v-model="form.first_name"
                      type="text"
                      class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                      placeholder="Enter first name"
                      required
                    />
                  </div>
                  <div v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">
                    {{ form.errors.first_name }}
                  </div>
                </div>

                <!-- Last Name Field -->
                <div class="space-y-2">
                  <label for="last_name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                    </div>
                    <input
                      id="last_name"
                      v-model="form.last_name"
                      type="text"
                      class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                      placeholder="Enter last name"
                      required
                    />
                  </div>
                  <div v-if="form.errors.last_name" class="text-red-500 text-sm mt-1">
                    {{ form.errors.last_name }}
                  </div>
                </div>

                <!-- LRN Field -->
                <div class="space-y-2">
                  <label for="lrn" class="text-sm font-medium text-gray-700 dark:text-gray-300">LRN (Learner Reference Number)</label>
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                    </div>
                    <input
                      id="lrn"
                      v-model="form.lrn"
                      type="text"
                      maxlength="12"
                      pattern="[0-9]{12}"
                      class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                      placeholder="Enter 12-digit LRN"
                      required
                    />
                  </div>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Must be exactly 12 digits</p>
                  <div v-if="form.errors.lrn" class="text-red-500 text-sm mt-1">
                    {{ form.errors.lrn }}
                  </div>
                </div>

                <!-- Gender Field -->
                <div class="space-y-2">
                  <label for="gender" class="text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                    </div>
                    <select
                      id="gender"
                      v-model="form.gender"
                      class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-all duration-200 appearance-none"
                      required
                    >
                      <option value="" disabled class="text-gray-400">-- Select Gender --</option>
                      <option value="male" class="text-gray-900 dark:text-white">Male</option>
                      <option value="female" class="text-gray-900 dark:text-white">Female</option>
                    </select>
                  </div>
                  <div v-if="form.errors.gender" class="text-red-500 text-sm mt-1">
                    {{ form.errors.gender }}
                  </div>
                </div>

                <!-- Class Selection Field -->
                <div class="space-y-2">
                  <label for="class_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Class</label>
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                      </svg>
                    </div>
                    <select
                      id="class_id"
                      v-model="form.class_id"
                      class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-all duration-200 appearance-none"
                      required
                    >
                      <option value="" disabled class="text-gray-400">-- Choose Class --</option>
                      <option
                        v-for="cls in classes"
                        :key="cls.id"
                        :value="cls.id"
                        class="text-gray-900 dark:text-white"
                      >
                        {{ cls.name }} (Grade {{ cls.grade_level }})
                      </option>
                    </select>
                  </div>
                  <div v-if="form.errors.class_id" class="text-red-500 text-sm mt-1">
                    {{ form.errors.class_id }}
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 pt-4">
                  <!-- Cancel Button -->
                  <button
                    type="button"
                    @click="closeModal"
                    class="flex-1 py-3 px-6 bg-gray-500/90 hover:bg-gray-600 focus:ring-4 focus:ring-gray-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                  >
                    <div class="flex items-center justify-center space-x-2">
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                      <span>Cancel</span>
                    </div>
                  </button>
                  
                  <!-- Submit Button -->
                  <button
                    type="submit"
                    class="flex-1 py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                    :class="{ 'opacity-75 cursor-not-allowed transform-none shadow-lg': form.processing }"
                    :disabled="form.processing"
                  >
                    <div class="flex items-center justify-center space-x-2">
                      <template v-if="form.processing">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                        <span>Updating...</span>
                      </template>
                      <template v-else>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Update Student</span>
                      </template>
                    </div>
                  </button>
                </div>

                <!-- Close Button -->
                <button
                  type="button"
                  @click="closeModal"
                  class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </form>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { watch, defineProps, defineEmits } from 'vue'

const props = defineProps({
  show: Boolean,
  student: Object,
  classes: Array
})

const emit = defineEmits(['close', 'updated'])

const modalContent = ref(null)

const form = useForm({
  first_name: '',
  last_name: '',
  lrn: '',
  gender: '',
  class_id: ''
})

// Update form whenever student prop changes
watch(() => props.student, (newStudent) => {
  if (newStudent) {
    form.first_name = newStudent.first_name
    form.last_name = newStudent.last_name
    form.lrn = newStudent.lrn || ''
    form.gender = newStudent.gender || ''
    form.class_id = newStudent.class_id
  }
}, { immediate: true })

const submit = () => {
  if (!props.student || !props.student.parent_id) return;

  form.put(route('parents.students.update', { 
    parent: props.student.parent_id, 
    student: props.student.id 
  }), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Student updated successfully',
        background: '#1f2937',
        color: '#f9fafb',
        iconColor: '#22c55e',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      });
      emit('close');   // close modal
    }
  });
}

function closeModal() {
  emit('close')
}

function handleBackdropClick(event) {
  if (event.target === event.currentTarget) {
    closeModal()
  }
}

// Handle ESC key to close modal
function handleEscape(event) {
  if (event.key === 'Escape') {
    closeModal()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})
</script>