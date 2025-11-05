<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
  show: Boolean,
  parent: Object,
})

const emit = defineEmits(['close', 'action'])

const activeTab = ref('edit')
const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

// 🧭 Watch for parent prop changes
watch(() => props.parent, (newParent) => {
  if (newParent) {
    form.name = newParent.name || ''
    form.email = newParent.email || ''
    form.password = ''
    form.password_confirmation = ''
    form.clearErrors()
  }
}, { immediate: true })

const parentStatus = computed(() => props.parent?.status || 'inactive')
const isActive = computed(() => parentStatus.value === 'active')

/* ===================================================
   🟢 TOGGLE STATUS (Activate / Deactivate)
=================================================== */
const handleToggleStatus = () => {
  Swal.fire({
    title: `${isActive.value ? 'Deactivate' : 'Activate'} Parent?`,
    text: `This will ${isActive.value ? 'deactivate' : 'activate'} ${props.parent.name}'s account.`,
    icon: 'question',
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
      popup: 'rounded-2xl shadow-2xl border border-blue-500/50 backdrop-blur-lg',
      title: 'text-2xl font-bold text-white mb-2',
      htmlContainer: 'text-gray-300',
      confirmButton: `py-3 px-6 ${isActive.value
        ? 'bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700'
        : 'bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700'} focus:ring-4 focus:ring-blue-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200`,
      cancelButton: 'py-3 px-6 border border-gray-300 dark:border-gray-600 text-gray-300 font-semibold rounded-xl shadow-sm hover:bg-gray-800 hover:shadow-md transform transition-all duration-200 mr-3',
      icon: '!border-none !bg-transparent text-blue-500'
    },
    buttonsStyling: false,
    showCancelButton: true,
    confirmButtonText: `Yes, ${isActive.value ? 'deactivate' : 'activate'}!`,
    cancelButtonText: 'Cancel',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) emit('action', 'toggle-status')
  })
}

/* ===================================================
   ✏️ EDIT PARENT INFO
=================================================== */
const handleEdit = () => {
  if (!form.name.trim() || !form.email.trim()) {
    return Swal.fire({
      title: 'Validation Error',
      text: 'Name and email are required.',
      icon: 'error',
      background: '#1f2937',
      color: '#f9fafb',
      backdrop: 'rgba(0, 0, 0, 0.7)',
      showClass: { popup: 'animate__animated animate__shakeX animate__faster' },
      customClass: {
        popup: 'rounded-2xl shadow-2xl border border-red-500/50 backdrop-blur-lg',
        title: 'text-2xl font-bold text-white mb-2',
        htmlContainer: 'text-gray-300',
        confirmButton: 'py-3 px-6 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:ring-4 focus:ring-red-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200',
        icon: '!border-none !bg-transparent text-red-500'
      },
      buttonsStyling: false,
      confirmButtonText: 'OK'
    })
  }

  if (form.password && form.password !== form.password_confirmation) {
    return Swal.fire({
      title: 'Password Mismatch',
      text: 'Password confirmation does not match.',
      icon: 'error',
      background: '#1f2937',
      color: '#f9fafb',
      backdrop: 'rgba(0, 0, 0, 0.7)',
      showClass: { popup: 'animate__animated animate__shakeX animate__faster' },
      customClass: {
        popup: 'rounded-2xl shadow-2xl border border-red-500/50 backdrop-blur-lg',
        title: 'text-2xl font-bold text-white mb-2',
        htmlContainer: 'text-gray-300',
        confirmButton: 'py-3 px-6 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:ring-4 focus:ring-red-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200',
        icon: '!border-none !bg-transparent text-red-500'
      },
      buttonsStyling: false,
      confirmButtonText: 'OK'
    })
  }

  form.put(route('parents.update', props.parent.id), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        title: 'Updated!',
        text: 'Parent information updated successfully.',
        icon: 'success',
        background: '#1f2937',
        color: '#f9fafb',
        backdrop: 'rgba(0, 0, 0, 0.7)',
        showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' },
        customClass: {
          popup: 'rounded-2xl shadow-2xl border border-green-500/50 backdrop-blur-lg',
          title: 'text-2xl font-bold text-white mb-2',
          htmlContainer: 'text-gray-300',
          confirmButton: 'py-3 px-6 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 focus:ring-4 focus:ring-green-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200',
          icon: '!border-none !bg-transparent text-green-500'
        },
        buttonsStyling: false,
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true,
      })
      emit('close')
    },
    onError: () => {
      Swal.fire({
        title: 'Error!',
        text: 'Failed to update parent info. Please try again.',
        icon: 'error',
        background: '#1f2937',
        color: '#f9fafb',
        backdrop: 'rgba(0, 0, 0, 0.7)',
        showClass: { popup: 'animate__animated animate__shakeX animate__faster' },
        customClass: {
          popup: 'rounded-2xl shadow-2xl border border-red-500/50 backdrop-blur-lg',
          title: 'text-2xl font-bold text-white mb-2',
          htmlContainer: 'text-gray-300',
          confirmButton: 'py-3 px-6 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:ring-4 focus:ring-red-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200',
          icon: '!border-none !bg-transparent text-red-500'
        },
        buttonsStyling: false,
        confirmButtonText: 'OK'
      })
    }
  })
}

/* ===================================================
   🗑️ DELETE PARENT
=================================================== */
const handleDelete = () => {
  Swal.fire({
    title: 'Are you sure?',
    text: `This will permanently delete ${props.parent.name} and all their associated data!`,
    icon: 'warning',
    background: '#1f2937',
    color: '#f9fafb',
    backdrop: 'rgba(0, 0, 0, 0.7)',
    showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' },
    hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' },
    customClass: {
      popup: 'rounded-2xl shadow-2xl border border-yellow-500/50 backdrop-blur-lg',
      title: 'text-2xl font-bold text-white mb-2',
      htmlContainer: 'text-gray-300',
      confirmButton: 'py-3 px-6 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:ring-4 focus:ring-red-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200',
      cancelButton: 'py-3 px-6 border border-gray-300 dark:border-gray-600 text-gray-300 font-semibold rounded-xl shadow-sm hover:bg-gray-800 hover:shadow-md transition-all duration-200 mr-3',
      icon: '!border-none !bg-transparent text-yellow-500'
    },
    buttonsStyling: false,
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) emit('action', 'delete')
  })
}

/* ===================================================
   ❌ CLOSE MODAL
=================================================== */
const closeModal = () => {
  activeTab.value = 'edit'
  form.reset()
  form.clearErrors()
  emit('close')
}
</script>

<template>
  <!-- Modal Backdrop -->
  <div 
    v-if="show" 
    class="fixed inset-0 z-50 overflow-y-auto"
    @click.self="closeModal"
  >
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200/50 dark:border-gray-700/50">
        <!-- Gradient overlay for visual depth -->
        <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-blue-900/20 dark:to-purple-900/20 pointer-events-none z-0"></div>
        
        <!-- Header -->
        <div class="relative z-10 bg-gradient-to-r from-gray-50/80 to-gray-100/80 dark:from-gray-800/80 dark:to-gray-700/80 px-6 py-4 border-b border-gray-200/50 dark:border-gray-600/50">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center text-white font-medium">
                {{ parent?.name?.charAt(0) || 'P' }}
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ parent?.name || 'Parent' }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ parent?.email || '' }}</p>
              </div>
            </div>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors p-2 hover:bg-gray-100/50 dark:hover:bg-gray-800/50 rounded-lg"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          
          <!-- Status Badge -->
          <div class="mt-3">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border" :class="{
              'bg-green-500/20 text-green-600 dark:text-green-400 border-green-400/30': isActive,
              'bg-red-500/20 text-red-600 dark:text-red-400 border-red-400/30': !isActive
            }">
              <div class="w-1.5 h-1.5 rounded-full mr-1.5" :class="{
                'bg-green-500': isActive,
                'bg-red-500': !isActive
              }"></div>
              {{ isActive ? 'Active' : 'Inactive' }}
            </span>
          </div>
        </div>

        <!-- Tab Navigation -->
        <div class="relative z-10 px-6 pt-4">
          <div class="flex space-x-1 bg-gray-100/50 dark:bg-gray-800/50 rounded-lg p-1 backdrop-blur-sm">
            <button
              @click="activeTab = 'edit'"
              class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-150"
              :class="{
                'bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 shadow-sm border border-blue-200/50 dark:border-blue-400/30': activeTab === 'edit',
                'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-gray-700/50': activeTab !== 'edit'
              }"
            >
              <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Edit
            </button>
            <button
              @click="activeTab = 'status'"
              class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-150"
              :class="{
                'bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 shadow-sm border border-blue-200/50 dark:border-blue-400/30': activeTab === 'status',
                'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-gray-700/50': activeTab !== 'status'
              }"
            >
              <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Status
            </button>
            <button
              @click="activeTab = 'delete'"
              class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-150"
              :class="{
                'bg-white dark:bg-gray-700 text-red-600 dark:text-red-400 shadow-sm border border-red-200/50 dark:border-red-400/30': activeTab === 'delete',
                'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-gray-700/50': activeTab !== 'delete'
              }"
            >
              <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Delete
            </button>
          </div>
        </div>

        <!-- Tab Content -->
        <div class="relative z-10 px-6 py-6">
          <!-- Edit Tab -->
          <div v-if="activeTab === 'edit'" class="space-y-6">
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
                <span v-if="form.errors.name" class="text-red-500 text-sm mt-1 block">{{ form.errors.name }}</span>
              </div>
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
                <span v-if="form.errors.email" class="text-red-500 text-sm mt-1 block">{{ form.errors.email }}</span>
              </div>
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
                <span v-if="form.errors.password" class="text-red-500 text-sm mt-1 block">{{ form.errors.password }}</span>
              </div>
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

            <!-- Students Info (Read-only) -->
            <div v-if="parent?.students?.length" class="space-y-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Associated Students</label>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="student in parent.students"
                  :key="student.id"
                  class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border border-indigo-400/30"
                >
                  {{ student.first_name }} {{ student.last_name }}
                </span>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-3 pt-4">
              <button
                @click="closeModal"
                class="flex-1 py-3 px-6 bg-gray-200/80 dark:bg-gray-700/80 hover:bg-gray-300/80 dark:hover:bg-gray-600/80 text-gray-700 dark:text-gray-300 font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200"
              >
                Cancel
              </button>
              <button
                @click="handleEdit"
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
          </div>

          <!-- Status Tab -->
          <div v-if="activeTab === 'status'" class="space-y-4">
            <div class="text-center py-4">
              <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4" :class="{
                'bg-green-500/20 border-2 border-green-400/30': isActive,
                'bg-red-500/20 border-2 border-red-400/30': !isActive
              }">
                <svg class="w-8 h-8" :class="{
                  'text-green-400': isActive,
                  'text-red-400': !isActive
                }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="isActive" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
              </div>
              <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                Parent account is currently {{ isActive ? 'Active' : 'Inactive' }}
              </h4>
              <p class="text-gray-600 dark:text-gray-400 text-sm mb-6">
                {{ isActive 
                  ? 'This parent can access their account and view student information.' 
                  : 'This parent cannot access their account or view student information.' 
                }}
              </p>
              <button
                @click="handleToggleStatus"
                class="px-6 py-3 rounded-xl font-medium transition-all duration-150" :class="{
                  'bg-red-500/20 hover:bg-red-500/30 text-red-600 dark:text-red-400 border border-red-400/30 hover:border-red-400/50': isActive,
                  'bg-green-500/20 hover:bg-green-500/30 text-green-600 dark:text-green-400 border border-green-400/30 hover:border-green-400/50': !isActive
                }"
              >
                {{ isActive ? 'Deactivate Account' : 'Activate Account' }}
              </button>
            </div>
          </div>

          <!-- Delete Tab -->
          <div v-if="activeTab === 'delete'" class="space-y-4">
            <div class="text-center py-4">
              <div class="w-16 h-16 bg-red-500/20 border-2 border-red-400/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.084 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Delete Parent Account</h4>
              <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">
                This action will permanently delete <strong class="text-gray-900 dark:text-white">{{ parent?.name }}</strong> and cannot be undone.
              </p>
              <p class="text-red-600 dark:text-red-400 text-sm mb-6">
                ⚠️ This will also remove all associated student data and access.
              </p>
              <button
                @click="handleDelete"
                class="px-6 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-600 dark:text-red-400 border border-red-400/30 hover:border-red-400/50 rounded-xl font-medium transition-all duration-150"
              >
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete Parent
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>