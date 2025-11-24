<!-- Components/RegisterModal.vue -->
<template>
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
              class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg p-8 shadow-2xl border border-gray-200/50 dark:border-gray-700/50 transition-all"
              @click.stop
            >
              <!-- Gradient overlay for visual depth -->
              <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-blue-900/20 dark:to-purple-900/20 pointer-events-none"></div>
              
              <form @submit.prevent="register" class="relative space-y-6">
                <!-- Header -->
                <div class="text-center space-y-2">
                  <div class="w-12 h-12 mx-auto bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                  </div>
                  <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Register Parent & Student</h2>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Parent credentials will be auto-generated</p>
                </div>

                <!-- Student Information Section -->
                <div class="space-y-4">
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Student Information</h3>
                  
                  <!-- Student Name Fields -->
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- First Name -->
                    <div class="space-y-2">
                      <InputLabel for="student_first_name" value="First Name" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                      <TextInput
                        id="student_first_name"
                        v-model="form.student_first_name"
                        type="text"
                        class="block w-full px-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                        placeholder="First name"
                        required
                      />
                      <InputError class="mt-1" :message="form.errors.student_first_name" />
                    </div>

                    <!-- Middle Name -->
                    <div class="space-y-2">
                      <InputLabel for="student_middle_name" value="Middle Name" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                      <TextInput
                        id="student_middle_name"
                        v-model="form.student_middle_name"
                        type="text"
                        class="block w-full px-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                        placeholder="Middle name (optional)"
                      />
                      <InputError class="mt-1" :message="form.errors.student_middle_name" />
                    </div>

                    <!-- Last Name -->
                    <div class="space-y-2">
                      <InputLabel for="student_last_name" value="Last Name" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                      <TextInput
                        id="student_last_name"
                        v-model="form.student_last_name"
                        type="text"
                        class="block w-full px-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                        placeholder="Last name"
                        required
                      />
                      <InputError class="mt-1" :message="form.errors.student_last_name" />
                    </div>
                  </div>

                  <!-- LRN and Gender -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- LRN -->
                    <div class="space-y-2">
                      <InputLabel for="student_lrn" value="LRN (12 digits)" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                      <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                          </svg>
                        </div>
                        <TextInput
                          id="student_lrn"
                          v-model="form.student_lrn"
                          type="text"
                          class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                          placeholder="000000000000"
                          maxlength="12"
                          required
                        />
                      </div>
                      <InputError class="mt-1" :message="form.errors.student_lrn" />
                    </div>

                    <!-- Gender -->
                    <div class="space-y-2">
                      <InputLabel for="student_gender" value="Gender" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                      <select
                        id="student_gender"
                        v-model="form.student_gender"
                        class="block w-full px-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-all duration-200"
                        required
                      >
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                      </select>
                      <InputError class="mt-1" :message="form.errors.student_gender" />
                    </div>
                  </div>

                  <!-- Class Selection -->
                  <div class="space-y-2">
                    <InputLabel for="class_id" value="Class" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                    <div class="relative group">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                      </div>
                      <select 
                        id="class_id"
                        v-model="form.class_id" 
                        class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-all duration-200" 
                        required
                      >
                        <option value="" disabled>Select a class</option>
                        <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                          {{ cls.name }} (Grade {{ cls.grade_level }})
                        </option>
                      </select>
                    </div>
                    <InputError class="mt-1" :message="form.errors.class_id" />
                  </div>
                </div>

                <!-- Info Box -->
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                  <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-700 dark:text-blue-300">
                      <p class="font-medium mb-2">Auto-generated parent credentials:</p>
                      <ul class="space-y-1 text-xs">
                        <li><strong>Parent Name:</strong> Parent [Student Last Name]</li>
                        <li><strong>Email:</strong> [Student LastName]_[Student FirstName]@gmail.com</li>
                        <li><strong>Password:</strong> First 3 letters of LastName + _2025</li>
                        <li class="text-blue-600 dark:text-blue-400 mt-2">
                          Example: Student "Juan Carlos Reyes" →<br>
                          Parent Name: Parent Reyes<br>
                          Email: reyes_juan@gmail.com<br>
                          Password: Rey_2025
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Submit Button -->
                <PrimaryButton
                  class="w-full py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                  :class="{ 'opacity-75 cursor-not-allowed transform-none shadow-lg': form.processing }"
                  :disabled="form.processing"
                >
                  <div class="flex items-center justify-center space-x-2">
                    <template v-if="form.processing">
                      <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                      </svg>
                      <span>Registering...</span>
                    </template>
                    <template v-else>
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                      </svg>
                      <span>Register Parent & Student</span>
                    </template>
                  </div>
                </PrimaryButton>

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
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Swal from 'sweetalert2'

defineProps({
  show: Boolean,
  classes: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close'])

const modalContent = ref(null)

const form = useForm({
  student_first_name: '',
  student_middle_name: '',
  student_last_name: '',
  student_lrn: '',
  student_gender: '',
  class_id: '',
})

const register = () => {
  form.post(route('parents.store'), {
    onSuccess: () => {
      Swal.fire({
        title: 'Success!',
        text: 'Parent and Student have been successfully registered.',
        icon: 'success',
        background: '#1f2937',
        color: '#f9fafb',
        backdrop: 'rgba(0, 0, 0, 0.7)',
        showClass: {
          popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
          popup: 'animate__animated animate__fadeOutUp animate__faster'
        },
        customClass: {
          popup: 'rounded-2xl shadow-2xl border border-gray-600/50 backdrop-blur-lg',
          title: 'text-2xl font-bold text-white mb-2',
          htmlContainer: 'text-gray-300',
          confirmButton: 'py-3 px-6 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:ring-4 focus:ring-green-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
          icon: '!border-none !bg-transparent'
        },
        buttonsStyling: false,
        confirmButtonText: 'OK',
        timer: 4000,
        timerProgressBar: true,
        didOpen: (popup) => {
          popup.addEventListener('mouseenter', Swal.stopTimer)
          popup.addEventListener('mouseleave', Swal.resumeTimer)
        }
      }).then(() => {
        form.reset()
        closeModal()
      })
    },
    onError: () => {
      Swal.fire({
        title: 'Error!',
        text: 'Failed to register. Please check all input fields and try again.',
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
  })
}

function closeModal() {
  emit('close')
}

function handleBackdropClick(event) {
  if (event.target === event.currentTarget) {
    closeModal()
  }
}

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