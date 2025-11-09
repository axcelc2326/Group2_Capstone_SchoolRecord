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
              class="relative w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg shadow-2xl border border-gray-200/50 dark:border-gray-700/50 transition-all flex flex-col max-h-[90vh]"
              @click.stop
            >
              <!-- Gradient overlay for visual depth -->
              <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-blue-900/20 dark:to-purple-900/20 pointer-events-none"></div>
              
              <!-- Header -->
              <div class="relative px-8 py-6 border-b border-gray-200/50 dark:border-gray-700/50">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                      <BookOpen class="w-6 h-6 text-white" />
                    </div>
                    <div>
                      <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Subjects for {{ classData.name }}
                      </h3>
                      <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Grade Level: 
                        <span class="font-medium">
                          <span v-if="classData.grade_level === 'K1'">Kinder 1</span>
                          <span v-else-if="classData.grade_level === 'K2'">Kinder 2</span>
                          <span v-else>Grade {{ classData.grade_level }}</span>
                        </span>
                        • Section: {{ classData.name }}
                      </p>
                    </div>
                  </div>
                  
                  <!-- Close X Button -->
                  <button
                    type="button"
                    @click="close"
                    class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-colors"
                  >
                    <X class="w-5 h-5" />
                  </button>
                </div>
              </div>

              <!-- Content -->
              <div class="relative flex-1 overflow-y-auto">
                <!-- Loading State -->
                <div v-if="loading" class="flex justify-center items-center py-12">
                  <div class="inline-flex items-center space-x-3">
                    <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                    <span class="text-lg text-gray-600 dark:text-gray-400">Loading subjects...</span>
                  </div>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="text-center py-12">
                  <AlertCircle class="mx-auto h-12 w-12 text-red-400 mb-3" />
                  <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Failed to load subjects</h3>
                  <p class="text-gray-600 dark:text-gray-400 mb-4">{{ error }}</p>
                  <button
                    @click="loadSubjects"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl font-medium"
                  >
                    Try Again
                  </button>
                </div>

                <!-- Subjects List -->
                <div v-else-if="subjects.length > 0" class="p-6">
                  <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div
                      v-for="subject in subjects"
                      :key="subject.id"
                      class="relative bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl p-5 hover:shadow-lg transition-all duration-200 hover:border-blue-300 dark:hover:border-blue-600 hover:-translate-y-0.5"
                    >
                      <!-- Gradient accent -->
                      <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-purple-600 rounded-t-xl"></div>
                      
                      <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                          <h4 class="font-semibold text-gray-900 dark:text-white text-lg mb-1">{{ subject.name }}</h4>
                        </div>
                      </div>
                      
                      <div class="flex items-center justify-between pt-3 border-t border-gray-200/50 dark:border-gray-700/50">
                        <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">
                          Grade: <span class="text-gray-900 dark:text-white">{{ subject.grade_level }}</span>
                        </div>
                        <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                          </svg>
                          Active
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-12">
                  <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600 rounded-2xl flex items-center justify-center">
                    <BookOpen class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                  </div>
                  <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No subjects found</h3>
                  <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                    No subjects are currently available for {{ classData.grade_level === 'K1' ? 'Kinder 1' : 
                    classData.grade_level === 'K2' ? 'Kinder 2' : 'Grade ' + classData.grade_level }}.
                  </p>
                </div>
              </div>

              <!-- Footer -->
              <div class="relative px-8 py-4 bg-white/30 dark:bg-gray-800/30 backdrop-blur-sm border-t border-gray-200/50 dark:border-gray-700/50 rounded-b-2xl">
                <div class="flex items-center justify-between">
                  <div class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-semibold text-gray-900 dark:text-white">{{ subjects.length }}</span> 
                    {{ subjects.length === 1 ? 'subject' : 'subjects' }} found
                  </div>
                  <div class="flex space-x-3">
                    <button
                      @click="close"
                      class="px-6 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white/70 dark:bg-gray-800/70 border border-gray-300/50 dark:border-gray-600/50 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                    >
                      Close
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
import { ref, watch, defineProps, defineEmits, onMounted, onUnmounted } from 'vue'
import { BookOpen, X, AlertCircle } from 'lucide-vue-next'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  classData: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close'])

const subjects = ref([])
const loading = ref(false)
const error = ref(null)
const modalContent = ref(null)

function close() {
  emit('close')
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

// Define loadSubjects
const loadSubjects = async () => {
  if (!props.classData?.id) {
    subjects.value = []
    return
  }
  
  loading.value = true
  error.value = null
  subjects.value = []
  
  try {
    const response = await fetch(`/api/classes/${props.classData.id}/subjects`)
    if (!response.ok) {
      throw new Error('Failed to fetch subjects')
    }
    const data = await response.json()
    subjects.value = data.subjects || []
  } catch (err) {
    error.value = err.message
    console.error('Error loading subjects:', err)
  } finally {
    loading.value = false
  }
}

const resetState = () => {
  subjects.value = []
  loading.value = false
  error.value = null
}

// Watchers
watch(() => props.show, (newVal, oldVal) => {
  if (!newVal && oldVal) {
    resetState()
  }
})

watch(() => ({
  show: props.show,
  classId: props.classData?.id
}), ({ show, classId }, oldValue) => {
  if (show && classId && classId !== oldValue?.classId) {
    loadSubjects()
  }
})

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
  if (props.show && props.classData?.id) {
    loadSubjects()
  }
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})
</script>