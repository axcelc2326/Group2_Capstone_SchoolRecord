<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
  show: Boolean,         // modal visibility
  parent: Object | null, // parent data to edit
})

const emit = defineEmits(['close', 'updated'])

// Password visibility toggle
const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

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

// Handle backdrop click
const handleBackdropClick = (event) => {
  if (event.target === event.currentTarget) {
    emit('close')
  }
}

// Handle ESC key
const handleEscape = (event) => {
  if (event.key === 'Escape') {
    emit('close')
  }
}

// Add event listeners
watch(() => props.show, (show) => {
  if (show) {
    document.addEventListener('keydown', handleEscape)
  } else {
    document.removeEventListener('keydown', handleEscape)
  }
})
</script>

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
              class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg shadow-2xl border border-gray-200/50 dark:border-gray-700/50 transition-all"
              @click.stop
            >
              <!-- Gradient overlay for visual depth -->
              <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-blue-900/20 dark:to-purple-900/20 pointer-events-none z-0"></div>
              
              <!-- Content -->
              <div class="relative z-10 p-8">
                <form @submit.prevent="submit" class="space-y-6">
                  <!-- Header -->
                  <div class="text-center space-y-2">
                    <div class="w-12 h-12 mx-auto bg-gradient-to-br from-orange-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Parent</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Update parent information</p>
                  </div>

                  <!-- Current Information Card -->
                  <div class="relative p-4 rounded-xl bg-gradient-to-r from-gray-50/80 to-gray-100/80 dark:from-gray-800/80 dark:to-gray-700/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-600/50">
                    <div class="flex items-center space-x-2 mb-3">
                      <div class="w-6 h-6 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </div>
                      <h3 class="font-semibold text-gray-900 dark:text-white">Current Information</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                      <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Name:</span>
                        <span class="text-gray-900 dark:text-white">{{ props.parent?.name }}</span>
                      </div>
                      <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Email:</span>
                        <span class="text-gray-900 dark:text-white">{{ props.parent?.email }}</span>
                      </div>
                      <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Password:</span>
                        <span class="text-gray-500 dark:text-gray-400 italic">(hidden for security)</span>
                      </div>
                    </div>
                  </div>

                  <!-- Name Field -->
                  <div class="space-y-2">
                    <label for="name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                    <div class="relative group">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                      </div>
                      <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                        placeholder="Enter full name"
                        required
                      />
                    </div>
                    <span v-if="form.errors.name" class="text-red-500 text-sm flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <span>{{ form.errors.name }}</span>
                    </span>
                  </div>

                  <!-- Email Field -->
                  <div class="space-y-2">
                    <label for="email" class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                    <div class="relative group">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                      </div>
                      <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                        placeholder="Enter email address"
                        required
                      />
                    </div>
                    <span v-if="form.errors.email" class="text-red-500 text-sm flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <span>{{ form.errors.email }}</span>
                    </span>
                  </div>

                  <!-- Password Field -->
                  <div class="space-y-2">
                    <label for="password" class="text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Leave blank to keep current password</p>
                    <div class="relative group">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                      </div>
                      <input
                        id="password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        class="block w-full pl-10 pr-12 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                        placeholder="Enter new password"
                      />
                      <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                      >
                        <svg v-if="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                        </svg>
                        <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                      </button>
                    </div>
                    <span v-if="form.errors.password" class="text-red-500 text-sm flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <span>{{ form.errors.password }}</span>
                    </span>
                  </div>

                  <!-- Password Confirmation Field -->
                  <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                    <div class="relative group">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </div>
                      <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        class="block w-full pl-10 pr-12 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                        placeholder="Confirm new password"
                      />
                      <button
                        type="button"
                        @click="showPasswordConfirmation = !showPasswordConfirmation"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                      >
                        <svg v-if="showPasswordConfirmation" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                        </svg>
                        <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                      </button>
                    </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="flex space-x-3 pt-4">
                    <button
                      type="button"
                      @click="emit('close')"
                      class="flex-1 py-3 px-6 bg-gray-200/80 dark:bg-gray-700/80 hover:bg-gray-300/80 dark:hover:bg-gray-600/80 text-gray-700 dark:text-gray-300 font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200"
                    >
                      Cancel
                    </button>
                    <button
                      type="submit"
                      :disabled="form.processing"
                      class="flex-1 py-3 px-6 bg-gradient-to-r from-orange-600 to-pink-600 hover:from-orange-700 hover:to-pink-700 focus:ring-4 focus:ring-orange-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                      :class="{ 'opacity-75 cursor-not-allowed transform-none shadow-lg': form.processing }"
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
                          <span>Save Changes</span>
                        </template>
                      </div>
                    </button>
                  </div>

                  <!-- Close Button -->
                  <button
                    type="button"
                    @click="emit('close')"
                    class="absolute top-4 right-4 z-20 p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-colors"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </form>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>