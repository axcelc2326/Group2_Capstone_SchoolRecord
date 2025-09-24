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
        class="fixed inset-0 z-50 overflow-hidden"
        @click="handleBackdropClick"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <!-- Slider Container -->
        <div class="fixed inset-y-0 right-0 flex max-w-full pl-16">
          <Transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="transform translate-x-full"
            enter-to-class="transform translate-x-0"
            leave-active-class="duration-200 ease-in"
            leave-from-class="transform translate-x-0"
            leave-to-class="transform translate-x-full"
          >
            <div
              v-if="show"
              ref="modalContent"
              class="w-screen max-w-xl transform overflow-hidden bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg shadow-2xl border-l border-gray-200/50 dark:border-gray-700/50 transition-all"
              @click.stop
            >
              <!-- Gradient overlay for visual depth -->
              <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-blue-900/20 dark:to-purple-900/20 pointer-events-none"></div>
              
              <div class="relative h-full flex flex-col">
                <!-- Enhanced Header -->
                <div class="relative px-8 py-6 border-b border-gray-200/50 dark:border-gray-700/50">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                      <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                          </svg>
                        </div>
                        <!-- Decorative ring -->
                        <div class="absolute -inset-1 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl opacity-20 animate-pulse"></div>
                      </div>
                      <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Class</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                          Update class information
                        </p>
                        <div v-if="teacherName" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 mt-2">
                          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                          </svg>
                          Teacher: {{ teacherName }}
                        </div>
                      </div>
                    </div>
                    
                    <!-- Close Button -->
                    <button
                      type="button"
                      @click="handleClose"
                      class="p-2.5 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 rounded-xl hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-all duration-200 hover:scale-105"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Form Content -->
                <div class="flex-1 px-8 py-6 overflow-y-auto">
                  <form @submit.prevent="updateClass" class="space-y-6">
                    <!-- Class Name Field -->
                    <div class="space-y-2">
                      <label for="className" class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span>Class Name</span>
                        <span class="text-red-500">*</span>
                      </label>
                      <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400 group-focus-within:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                          </svg>
                        </div>
                        <input
                          id="className"
                          v-model="form.name"
                          type="text"
                          class="block w-full pl-10 pr-3 py-3.5 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                          :class="{ 'border-red-500 dark:border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.name }"
                          placeholder="e.g., Rose Section, Diamond Class, Eagles Team"
                        />
                      </div>
                      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Choose a unique and memorable name for your class
                      </p>
                      <div v-if="form.errors.name" class="text-red-500 text-sm flex items-center space-x-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span>{{ form.errors.name }}</span>
                      </div>
                    </div>

                    <!-- Grade Level Field -->
                    <div class="space-y-2">
                      <label for="gradeLevel" class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                        <span>Grade Level</span>
                        <span class="text-red-500">*</span>
                      </label>
                      <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                          </svg>
                        </div>
                        <select
                          id="gradeLevel"
                          v-model="form.grade_level"
                          class="block w-full pl-10 pr-10 py-3.5 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-800 dark:text-white transition-all duration-200 appearance-none bg-white"
                          :class="{ 'border-red-500 dark:border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.grade_level }"
                        >
                          <option disabled value="" class="text-gray-500 dark:text-gray-400">-- Select Grade Level --</option>
                          <option v-for="opt in gradeOptions" :key="opt.value" :value="opt.value" class="text-gray-900 dark:text-white">
                            {{ opt.label }}
                          </option>
                        </select>
                        <!-- Custom dropdown arrow -->
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                          </svg>
                        </div>
                      </div>
                      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Select the appropriate grade level for your students
                      </p>
                      <div v-if="form.errors.grade_level" class="text-red-500 text-sm flex items-center space-x-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span>{{ form.errors.grade_level }}</span>
                      </div>
                    </div>

                    <!-- General Error Message (for duplicate class names) -->
                    <div v-if="duplicateError" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                      <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                          <h4 class="text-sm font-medium text-red-800 dark:text-red-200">Class Name Already Exists</h4>
                          <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                            A class with this name already exists. Please choose a different name.
                          </p>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>

                <!-- Enhanced Footer with Action Buttons -->
                <div class="relative px-8 py-6 border-t border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-gray-50/50 to-orange-50/50 dark:from-gray-800/50 dark:to-orange-900/50">
                  <div class="flex space-x-4">
                    <!-- Cancel Button -->
                    <button
                      type="button"
                      @click="handleClose"
                      class="flex-1 py-3.5 px-6 bg-white/80 hover:bg-gray-100 border-2 border-gray-300 hover:border-gray-400 focus:ring-4 focus:ring-gray-500/20 text-gray-700 font-semibold rounded-xl shadow-sm hover:shadow-md transform hover:-translate-y-0.5 transition-all duration-200"
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
                      @click="updateClass"
                      class="flex-1 py-3.5 px-6 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 focus:ring-4 focus:ring-orange-500/30 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-75 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-lg"
                      :disabled="form.processing || !form.name || !form.grade_level"
                    >
                      <div class="flex items-center justify-center space-x-2">
                        <template v-if="form.processing">
                          <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                          </svg>
                          <span>Updating Class...</span>
                        </template>
                        <template v-else>
                          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                          </svg>
                          <span>Save Changes</span>
                        </template>
                      </div>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch, computed, ref, onMounted, onUnmounted } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  show: Boolean,
  classItem: Object, // should include teacher relation
})

const emit = defineEmits(['close', 'updated'])

const modalContent = ref(null)

// Form setup
const form = useForm({
  name: '',
  grade_level: '',
})

// Populate form when classItem changes
watch(
  () => props.classItem,
  (newVal) => {
    if (newVal) {
      form.name = newVal.name
      form.grade_level = newVal.grade_level
    }
  },
  { immediate: true }
)

// Check for duplicate class name error
const duplicateError = computed(() => {
  return form.errors.name && form.errors.name.toLowerCase().includes('already') ||
         form.errors.name && form.errors.name.toLowerCase().includes('taken') ||
         form.errors.name && form.errors.name.toLowerCase().includes('exists') ||
         form.errors.name && form.errors.name.toLowerCase().includes('duplicate')
})

// Grade level options
const gradeOptions = [
  { value: 'K1', label: 'Kinder 1' },
  { value: 'K2', label: 'Kinder 2' },
  ...Array.from({ length: 6 }, (_, i) => ({
    value: (i + 1).toString(),
    label: `Grade ${i + 1}`,
  })),
]

// Computed teacher name
const teacherName = computed(() => props.classItem?.teacher?.name || 'Unassigned')

const handleClose = () => {
  form.reset()
  form.clearErrors()
  emit('close')
}

function handleBackdropClick(event) {
  if (event.target === event.currentTarget) {
    handleClose()
  }
}

// Submit update
function updateClass() {
  form.put(route('classes.update', props.classItem.id), {
    onSuccess: () => {
      Swal.fire({
        title: 'Success!',
        text: 'Class updated successfully.',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
      })
      form.reset()
      form.clearErrors()
      emit('updated') // refresh parent
      emit('close')
    },
    onError: (errors) => {
      // Handle specific duplicate error
      if (errors.name && (errors.name.includes('already') || errors.name.includes('taken') || errors.name.includes('exists'))) {
        // Error will be shown via duplicateError computed property
        console.log('Duplicate class name detected')
      }
      
      // Show general error for other validation issues
      if (Object.keys(errors).length > 0) {
        Swal.fire({
          title: 'Validation Error',
          text: 'Please check the form and fix any errors.',
          icon: 'error',
          toast: true,
          position: 'top-end',
          timer: 3000,
          showConfirmButton: false
        })
      }
    }
  })
}

// Handle ESC key to close modal
function handleEscape(event) {
  if (event.key === 'Escape') {
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