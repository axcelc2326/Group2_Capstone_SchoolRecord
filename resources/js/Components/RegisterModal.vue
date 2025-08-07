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
              class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg p-8 shadow-2xl border border-gray-200/50 dark:border-gray-700/50 transition-all"
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
                  <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create an account</h2>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Join us to get started</p>
                </div>

                <!-- Name Field -->
                <div class="space-y-2">
                  <InputLabel for="name" value="Full Name" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                    </div>
                    <TextInput
                      id="name"
                      v-model="form.name"
                      type="text"
                      class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                      placeholder="Enter your full name"
                      autocomplete="name"
                      required
                    />
                  </div>
                  <InputError class="mt-1" :message="form.errors.name" />
                </div>

                <!-- Email Field -->
                <div class="space-y-2">
                  <InputLabel for="email" value="Email address" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                      </svg>
                    </div>
                    <TextInput
                      id="email"
                      v-model="form.email"
                      type="email"
                      class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                      placeholder="Enter your email"
                      autocomplete="email"
                      required
                    />
                  </div>
                  <InputError class="mt-1" :message="form.errors.email" />
                </div>

                <!-- Password Field with Strength Meter -->
                <div class="space-y-2">
                  <div class="flex justify-between items-center">
                    <InputLabel for="password" value="Password" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                    <span class="text-xs" :class="passwordStrengthClass">{{ passwordStrengthText }}</span>
                  </div>
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                      </svg>
                    </div>
                    <TextInput
                      id="password"
                      v-model="form.password"
                      :type="showPassword ? 'text' : 'password'"
                      @input="checkPasswordStrength"
                      class="block w-full pl-10 pr-12 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                      placeholder="Create a password"
                      autocomplete="new-password"
                      required
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
                  <!-- Password Strength Meter -->
                  <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                    <div 
                      class="h-1.5 rounded-full transition-all duration-300" 
                      :class="passwordStrengthBarClass"
                      :style="{ width: passwordStrength + '%' }"
                    ></div>
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400">
                    <p v-if="form.password.length > 0">Password should contain:</p>
                    <ul class="list-disc list-inside space-y-1 mt-1">
                      <li :class="{'text-green-500': hasMinLength}">At least 8 characters</li>
                      <li :class="{'text-green-500': hasUppercase}">One uppercase letter</li>
                      <li :class="{'text-green-500': hasLowercase}">One lowercase letter</li>
                      <li :class="{'text-green-500': hasNumber}">One number</li>
                      <li :class="{'text-green-500': hasSpecialChar}">One special character</li>
                    </ul>
                  </div>
                  <InputError class="mt-1" :message="form.errors.password" />
                </div>

                <!-- Confirm Password Field -->
                <div class="space-y-2">
                  <InputLabel for="password_confirmation" value="Confirm Password" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                      </svg>
                    </div>
                    <TextInput
                      id="password_confirmation"
                      v-model="form.password_confirmation"
                      :type="showConfirmPassword ? 'text' : 'password'"
                      class="block w-full pl-10 pr-12 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                      placeholder="Confirm your password"
                      autocomplete="new-password"
                      required
                    />
                    <button
                      type="button"
                      @click="showConfirmPassword = !showConfirmPassword"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                    >
                      <svg v-if="showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                      </svg>
                      <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                  </div>
                  <InputError class="mt-1" :message="form.errors.password_confirmation" />
                </div>

                <!-- Terms Checkbox -->
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="terms"
                      v-model="form.terms"
                      type="checkbox"
                      class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-800 transition-colors"
                      required
                    >
                  </div>
                  <div class="ml-3 text-sm">
                    <label for="terms" class="font-medium text-gray-700 dark:text-gray-300">
                      I agree to the <a href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">Terms of Service</a> and <a href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">Privacy Policy</a>
                    </label>
                  </div>
                </div>

                <!-- Submit Button -->
                <PrimaryButton
                  class="w-full py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                  :class="{ 'opacity-75 cursor-not-allowed transform-none shadow-lg': form.processing }"
                  :disabled="form.processing || passwordStrength < 60"
                >
                  <div class="flex items-center justify-center space-x-2">
                    <template v-if="form.processing">
                      <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                      </svg>
                      <span>Creating account...</span>
                    </template>
                    <template v-else>
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                      </svg>
                      <span>Create Account</span>
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
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

defineProps(['show'])
const emit = defineEmits(['close'])

const showPassword = ref(false)
const showConfirmPassword = ref(false)
const modalContent = ref(null)

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
})

// Password strength indicators
const passwordStrength = ref(0)
const hasMinLength = computed(() => form.password.length >= 8)
const hasUppercase = computed(() => /[A-Z]/.test(form.password))
const hasLowercase = computed(() => /[a-z]/.test(form.password))
const hasNumber = computed(() => /[0-9]/.test(form.password))
const hasSpecialChar = computed(() => /[^A-Za-z0-9]/.test(form.password))

const passwordStrengthText = computed(() => {
  if (passwordStrength.value < 20) return 'Very Weak'
  if (passwordStrength.value < 40) return 'Weak'
  if (passwordStrength.value < 60) return 'Fair'
  if (passwordStrength.value < 80) return 'Good'
  return 'Strong'
})

const passwordStrengthClass = computed(() => {
  if (passwordStrength.value < 20) return 'text-red-500'
  if (passwordStrength.value < 40) return 'text-orange-500'
  if (passwordStrength.value < 60) return 'text-yellow-500'
  if (passwordStrength.value < 80) return 'text-blue-500'
  return 'text-green-500'
})

const passwordStrengthBarClass = computed(() => {
  if (passwordStrength.value < 20) return 'bg-red-500'
  if (passwordStrength.value < 40) return 'bg-orange-500'
  if (passwordStrength.value < 60) return 'bg-yellow-500'
  if (passwordStrength.value < 80) return 'bg-blue-500'
  return 'bg-green-500'
})

function checkPasswordStrength() {
  let strength = 0
  
  // Length check
  if (form.password.length >= 8) strength += 20
  if (form.password.length >= 12) strength += 10
  
  // Character type checks
  if (hasUppercase.value) strength += 15
  if (hasLowercase.value) strength += 15
  if (hasNumber.value) strength += 15
  if (hasSpecialChar.value) strength += 25
  
  // Bonus for multiple character types
  const typesCount = [hasUppercase, hasLowercase, hasNumber, hasSpecialChar]
    .filter(Boolean).length
  if (typesCount >= 3) strength += 10
  
  // Cap at 100
  passwordStrength.value = Math.min(strength, 100)
}

function register() {
  form.post(route('register'), {
    onSuccess: () => {
      closeModal()
      form.reset()
    },
    onError: () => {
      form.reset('password', 'password_confirmation')
    },
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