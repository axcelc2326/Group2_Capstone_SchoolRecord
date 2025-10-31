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
        v-if="open"
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
              v-if="open"
              ref="modalContent"
              class="relative w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg p-8 shadow-2xl border border-gray-200/50 dark:border-gray-700/50 transition-all"
              @click.stop
            >
              <!-- Gradient overlay for visual depth -->
              <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-blue-900/20 dark:to-purple-900/20 pointer-events-none"></div>
              
              <div class="relative space-y-6">
                <!-- Header -->
                <div class="text-center space-y-2">
                  <div class="w-12 h-12 mx-auto bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                  </div>
                  <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Record Details</h2>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    View comprehensive record information
                  </p>
                </div>

                <!-- Content -->
                <div class="mt-6">
                  <!-- Loading State -->
                  <div v-if="data.loading" class="text-center py-10">
                    <div class="inline-flex items-center space-x-3">
                      <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                      </svg>
                      <span class="text-lg text-gray-600 dark:text-gray-400">Loading...</span>
                    </div>
                  </div>

                  <!-- Record Content -->
                  <div v-else-if="data.record" class="space-y-6">
                    <!-- SF5 Record -->
                    <div v-if="data.type === 'sf5'" class="space-y-6">
                      <!-- Basic Information -->
                      <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-xl p-6 border border-blue-200/50 dark:border-blue-700/50">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                          <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                          Basic Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Class</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ data.record.class?.name }}</p>
                          </div>
                          <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Teacher</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ data.record.teacher?.name }}</p>
                          </div>
                          <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">School Year</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ data.record.school_year }}</p>
                          </div>
                          <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Class Average</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ data.record.class_average }}</p>
                          </div>
                        </div>
                      </div>

                      <!-- Student Statistics -->
                      <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-6 border border-purple-200/50 dark:border-purple-700/50">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                          <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                          </svg>
                          Student Statistics
                        </h4>
                        <div class="grid grid-cols-3 gap-4">
                          <div class="text-center p-4 bg-white/70 dark:bg-gray-800/70 rounded-lg border border-blue-200/30 dark:border-blue-700/30">
                            <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                              </svg>
                            </div>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ data.record.male_count }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 font-medium">Male Students</p>
                          </div>
                          <div class="text-center p-4 bg-white/70 dark:bg-gray-800/70 rounded-lg border border-pink-200/30 dark:border-pink-700/30">
                            <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center">
                              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                              </svg>
                            </div>
                            <p class="text-3xl font-bold text-pink-600 dark:text-pink-400">{{ data.record.female_count }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 font-medium">Female Students</p>
                          </div>
                          <div class="text-center p-4 bg-white/70 dark:bg-gray-800/70 rounded-lg border border-purple-200/30 dark:border-purple-700/30">
                            <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                              </svg>
                            </div>
                            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ data.record.overall_count }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 font-medium">Total Students</p>
                          </div>
                        </div>
                      </div>

                      <!-- Status and Timeline -->
                      <div class="bg-white/50 dark:bg-gray-800/50 rounded-xl p-6 border border-gray-200/50 dark:border-gray-700/50">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                          <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                          </svg>
                          Status & Timeline
                        </h4>
                        
                        <div class="flex items-center justify-center mb-4">
                          <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold"
                            :class="data.record.status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ data.record.status }}
                          </span>
                        </div>

                        <div class="space-y-3">
                          <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium mr-2">Created:</span>
                            <span>{{ new Date(data.record.created_at).toLocaleString() }}</span>
                          </div>
                          <div v-if="data.record.reviewed_at" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium mr-2">Reviewed:</span>
                            <span>{{ new Date(data.record.reviewed_at).toLocaleString() }}</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Honor Record -->
                    <div v-else-if="data.type === 'honor'" class="space-y-6">
                      <!-- Basic Information Grid -->
                      <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-xl p-6 border border-blue-200/50 dark:border-blue-700/50">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                          <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                          Basic Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Class</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ data.record.class?.name }}</p>
                          </div>
                          <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Teacher</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ data.record.teacher?.name }}</p>
                          </div>
                          <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">School Year</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ data.record.school_year }}</p>
                          </div>
                          <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Quarter</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ data.record.quarter }}</p>
                          </div>
                        </div>
                      </div>

                      <!-- Status and Timeline -->
                      <div class="bg-white/50 dark:bg-gray-800/50 rounded-xl p-6 border border-gray-200/50 dark:border-gray-700/50">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                          <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                          </svg>
                          Status & Timeline
                        </h4>
                        
                        <div class="flex items-center justify-center mb-4">
                          <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold"
                            :class="data.record.status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ data.record.status }}
                          </span>
                        </div>

                        <div class="space-y-3">
                          <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium mr-2">Created:</span>
                            <span>{{ new Date(data.record.created_at).toLocaleString() }}</span>
                          </div>
                          <div v-if="data.record.reviewed_at" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium mr-2">Reviewed:</span>
                            <span>{{ new Date(data.record.reviewed_at).toLocaleString() }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- No Details -->
                  <div v-else class="text-center py-10">
                    <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">No details available.</p>
                  </div>
                </div>

                <!-- Close X Button -->
                <button
                  type="button"
                  @click="close"
                  class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  open: { type: Boolean, default: false },
  data: { type: Object, default: () => ({}) } // { type, record, loading }
})

const emit = defineEmits(['update:open'])

const modalContent = ref(null)

function close() {
  emit('update:open', false)
}

function handleBackdropClick(event) {
  if (event.target === event.currentTarget) {
    close()
  }
}

// Handle ESC key to close modal
function handleEscape(event) {
  if (event.key === 'Escape') {
    close()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})
</script>