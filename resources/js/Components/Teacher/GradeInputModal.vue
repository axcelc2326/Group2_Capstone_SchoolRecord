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
              class="relative w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg p-8 shadow-2xl border border-gray-200/50 dark:border-gray-700/50 transition-all"
              @click.stop
            >
              <!-- Gradient overlay for visual depth -->
              <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-blue-900/20 dark:to-purple-900/20 pointer-events-none"></div>
              
              <form @submit.prevent="submit" class="relative space-y-6">
                <!-- Header -->
                <div class="text-center space-y-2">
                  <div class="w-12 h-12 mx-auto bg-gradient-to-br from-green-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                  </div>
                  <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Grade Input</h2>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Managing grades for {{ student.first_name }} {{ student.last_name }}
                  </p>
                </div>

                <!-- Quarter Selection Field -->
                <div class="space-y-2">
                  <label for="quarter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Quarter</label>
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                    </div>
                    <select
                      id="quarter"
                      v-model="quarter"
                      class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-all duration-200 appearance-none"
                    >
                      <option
                        v-for="q in quarters"
                        :key="q"
                        :value="q"
                        class="text-gray-900 dark:text-white"
                      >
                        {{ q }} Quarter
                      </option>
                    </select>
                  </div>
                </div>

                <!-- Grades Section -->
                <div class="space-y-4">
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                      <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                      </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ quarter }} Grades</h3>
                  </div>

                  <!-- Grades Grid -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="subject in subjects" :key="subject.id" class="space-y-2">
                      <label :for="`grade_${subject.id}`" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ subject.name }}
                      </label>
                      <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400 group-focus-within:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                          </svg>
                        </div>
                        <input
                          :id="`grade_${subject.id}`"
                          type="number"
                          min="60"
                          max="100"
                          v-model="form.grades[subject.id]"
                          class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                          :placeholder="`Enter grade for ${subject.name}`"
                        />
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        Grade range: 75-100
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 pt-4">
                  <!-- Cancel Button -->
                  <button
                    type="button"
                    @click="handleClose"
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
                    class="flex-1 py-3 px-6 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 focus:ring-4 focus:ring-green-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                    :class="{ 'opacity-75 cursor-not-allowed transform-none shadow-lg': form.processing }"
                    :disabled="form.processing"
                  >
                    <div class="flex items-center justify-center space-x-2">
                      <template v-if="form.processing">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                        <span>Saving...</span>
                      </template>
                      <template v-else>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Save Grades</span>
                      </template>
                    </div>
                  </button>
                </div>

                <!-- Close Button -->
                <button
                  type="button"
                  @click="handleClose"
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
import { useForm } from '@inertiajs/vue3'
import { ref, reactive, watch, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  show: Boolean,          // control modal visibility
  student: Object,
  subjects: Array,
  grades: Object,
  currentQuarter: String,
})

const emit = defineEmits(['close', 'saved'])

const modalContent = ref(null)

// Quarters
const quarters = ['Q1', 'Q2', 'Q3', 'Q4']
const quarter = ref(props.currentQuarter)

// Blank grades template
const blankGrades = () =>
  props.subjects.reduce((acc, subject) => {
    acc[subject.id] = ''
    return acc
  }, {})

// Reactive grades for each quarter
const allGrades = reactive({
  Q1: { ...blankGrades(), ...(props.grades?.Q1 ?? {}) },
  Q2: { ...blankGrades(), ...(props.grades?.Q2 ?? {}) },
  Q3: { ...blankGrades(), ...(props.grades?.Q3 ?? {}) },
  Q4: { ...blankGrades(), ...(props.grades?.Q4 ?? {}) },
})

// Inertia form
const form = useForm({
  student_id: props.student.id,
  class_id: props.student.class_id,
  quarter: quarter.value,
  grades: allGrades[quarter.value],
})

// Sync when quarter changes
watch(quarter, (newQuarter) => {
  form.quarter = newQuarter
  form.grades = allGrades[newQuarter]
})

const handleClose = () => {
  emit('close')
  location.reload()
}

function handleBackdropClick(event) {
  if (event.target === event.currentTarget) {
    handleClose()
  }
}

const submit = () => {
  form.post(route('grades.store'), {
    onSuccess: () => {
      emit('saved') // notify parent
      emit('close') // close modal
    },
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