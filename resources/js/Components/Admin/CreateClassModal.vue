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
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                  </div>
                  <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Class</h2>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Build a comprehensive learning environment
                  </p>
                </div>

                <!-- Grade Level Field -->
                <div class="space-y-2">
                  <label for="gradeLevel" class="text-sm font-medium text-gray-700 dark:text-gray-300">Grade Level *</label>
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400 group-focus-within:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                      </svg>
                    </div>
                    <select
                      id="gradeLevel"
                      v-model="form.grade_level"
                      class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-800 dark:text-white transition-all duration-200 appearance-none"
                      :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.grade_level,
                        'border-gray-300 dark:border-gray-600': !form.errors.grade_level
                      }"
                      required
                    >
                      <option disabled value="" class="text-gray-500 dark:text-gray-400">-- Select Grade Level --</option>
                      <option value="K1" class="text-gray-900 dark:text-white">Kinder 1</option>
                      <option value="K2" class="text-gray-900 dark:text-white">Kinder 2</option>
                      <option v-for="n in 6" :key="n" :value="String(n)" class="text-gray-900 dark:text-white">
                        Grade {{ n }}
                      </option>
                    </select>
                  </div>
                  <div v-if="form.errors.grade_level" class="text-red-500 text-sm flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <span>{{ form.errors.grade_level }}</span>
                  </div>
                </div>

                <!-- Class Name Field -->
                <div class="space-y-2">
                  <label for="className" class="text-sm font-medium text-gray-700 dark:text-gray-300">Class Name *</label>
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                      </svg>
                    </div>
                    <input
                      id="className"
                      v-model="form.name"
                      type="text"
                      class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                      :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.name,
                        'border-gray-300 dark:border-gray-600': !form.errors.name
                      }"
                      placeholder="e.g., Rose Section, Diamond Class, Eagles Team"
                      maxlength="255"
                      required
                    />
                  </div>
                  <div v-if="form.errors.name" class="text-red-500 text-sm flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <span>{{ form.errors.name }}</span>
                  </div>
                  <div v-if="!form.errors.name && form.name.length > 0" class="text-xs text-gray-500 dark:text-gray-400">
                    {{ form.name.length }}/255 characters
                  </div>
                </div>

                <!-- Warning message for duplicate class names -->
                <div v-if="duplicateError" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                  <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <div class="text-sm text-red-700 dark:text-red-300">
                      <p class="font-medium">Class Name Already Exists</p>
                      <p>A class with this name already exists. Please choose a different name.</p>
                    </div>
                  </div>
                </div>

                <!-- Info message when form is valid -->
                <div v-if="form.name && form.grade_level && !form.processing && !duplicateError" class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                  <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-700 dark:text-blue-300">
                      <p class="font-medium">Ready to Create:</p>
                      <p>Class "{{ form.name }}" for {{ getGradeLevelDisplay(form.grade_level) }}</p>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 pt-4">
                  <!-- Cancel Button -->
                  <button
                    type="button"
                    @click="handleClose"
                    :disabled="form.processing"
                    class="flex-1 py-3 px-6 bg-gray-500/90 hover:bg-gray-600 focus:ring-4 focus:ring-gray-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                    :class="{ 'opacity-50 cursor-not-allowed transform-none': form.processing }"
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
                    :class="{ 'opacity-75 cursor-not-allowed transform-none shadow-lg': form.processing || !isFormValid }"
                    :disabled="form.processing || !isFormValid"
                  >
                    <div class="flex items-center justify-center space-x-2">
                      <template v-if="form.processing">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                        <span>Creating...</span>
                      </template>
                      <template v-else>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Create Class</span>
                      </template>
                    </div>
                  </button>
                </div>

                <!-- Close Button -->
                <button
                  type="button"
                  @click="handleClose"
                  :disabled="form.processing"
                  class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-colors"
                  :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
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
import { useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'created'])

const modalContent = ref(null)

const form = useForm({
  name: '',
  grade_level: '',
  description: '',
  subject_focus: '',
})

// Computed property to check if form is valid
const isFormValid = computed(() => {
  return form.name.trim().length > 0 && 
         form.grade_level.length > 0 && 
         form.name.trim().length <= 255
})

// Check for duplicate class name error
const duplicateError = computed(() => {
  return form.errors.name && (
    form.errors.name.toLowerCase().includes('already') ||
    form.errors.name.toLowerCase().includes('taken') ||
    form.errors.name.toLowerCase().includes('exists') ||
    form.errors.name.toLowerCase().includes('duplicate')
  )
})

// Helper function to display grade level in a user-friendly format
const getGradeLevelDisplay = (gradeLevel) => {
  const gradeLevelMap = {
    'K1': 'Kinder 1',
    'K2': 'Kinder 2',
    '1': 'Grade 1',
    '2': 'Grade 2',
    '3': 'Grade 3',
    '4': 'Grade 4',
    '5': 'Grade 5',
    '6': 'Grade 6'
  }
  return gradeLevelMap[gradeLevel] || gradeLevel
}

const handleClose = () => {
  if (!form.processing) {
    form.reset()
    form.clearErrors()
    emit('close')
  }
}

const handleBackdropClick = (event) => {
  if (event.target === event.currentTarget && !form.processing) {
    handleClose()
  }
}

const submit = () => {
  // Clear any existing errors before submitting
  form.clearErrors()
  
  // Trim the name before submitting
  form.name = form.name.trim()
  
  // Limit description to 500 characters
  if (form.description && form.description.length > 500) {
    form.description = form.description.substring(0, 500)
  }

  form.post(route('classes.store'), {
    onSuccess: () => {
      Swal.fire({
        title: 'Class Created!',
        text: 'Class has been created successfully.',
        icon: 'success',
        background: '#1f2937',
        color: '#f9fafb',
        backdrop: 'rgba(0, 0, 0, 0.7)',
        showClass: {
          popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        customClass: {
          popup: 'rounded-2xl shadow-2xl border border-green-500/50 backdrop-blur-lg',
          title: 'text-2xl font-bold text-white mb-2',
          htmlContainer: 'text-gray-300',
          confirmButton: 'py-3 px-6 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 focus:ring-4 focus:ring-green-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
          icon: '!border-none !bg-transparent'
        },
        buttonsStyling: false,
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      }).then(() => {
        form.reset()
        form.clearErrors()
        emit('close')
        emit('created') // Emit created event
      })
    },
    onError: (errors) => {
      // Errors are automatically handled by Inertia and will appear in form.errors
      console.log('Validation errors:', errors)
      
      // Show specific error for duplicate class names
      if (duplicateError.value) {
        Swal.fire({
          title: 'Class Name Exists',
          text: 'A class with this name already exists. Please choose a different name.',
          icon: 'warning',
          background: '#1f2937',
          color: '#f9fafb',
          backdrop: 'rgba(0, 0, 0, 0.7)',
          showClass: {
            popup: 'animate__animated animate__shakeX animate__faster'
          },
          customClass: {
            popup: 'rounded-2xl shadow-2xl border border-yellow-500/50 backdrop-blur-lg',
            title: 'text-2xl font-bold text-white mb-2',
            htmlContainer: 'text-gray-300',
            confirmButton: 'py-3 px-6 bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 focus:ring-4 focus:ring-yellow-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
            icon: '!border-none !bg-transparent text-yellow-500'
          },
          buttonsStyling: false,
          confirmButtonText: 'OK'
        })
      } 
      // Show general error for other validation issues
      else if (Object.keys(errors).length > 0) {
        Swal.fire({
          title: 'Validation Error',
          text: 'Please check the form and fix any errors.',
          icon: 'error',
          background: '#1f2937',
          color: '#f9fafb',
          backdrop: 'rgba(0, 0, 0, 0.7)',
          showClass: {
            popup: 'animate__animated animate__shakeX animate__faster'
          },
          customClass: {
            popup: 'rounded-2xl shadow-2xl border border-red-500/50 backdrop-blur-lg',
            title: 'text-2xl font-bold text-white mb-2',
            htmlContainer: 'text-gray-300',
            confirmButton: 'py-3 px-6 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:ring-4 focus:ring-red-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
            icon: '!border-none !bg-transparent text-red-500'
          },
          buttonsStyling: false,
          confirmButtonText: 'Try Again'
        })
      }
    }
  })
}

// Handle ESC key to close modal
const handleEscape = (event) => {
  if (event.key === 'Escape' && !form.processing) {
    handleClose()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})
</script>