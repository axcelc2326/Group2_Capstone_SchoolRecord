<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref, reactive, watch } from 'vue'

const props = defineProps({
  show: Boolean,          // control modal visibility
  student: Object,
  subjects: Array,
  grades: Object,
  currentQuarter: String,
})

const emit = defineEmits(['close', 'saved'])

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

const submit = () => {
  form.post(route('grades.store'), {
    onSuccess: () => {
      emit('saved') // notify parent
      emit('close') // close modal
    },
  })
}
</script>

<template>
  <!-- Backdrop -->
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <!-- Modal box -->
    <div
      class="bg-gray-900 text-white rounded-lg shadow-lg w-full max-w-3xl p-6 relative"
    >
      <!-- Close Button -->
      <button
        @click="emit('close')"
        class="absolute top-3 right-3 text-gray-400 hover:text-white"
      >
        ✕
      </button>

      <!-- Header -->
      <h2 class="text-xl font-semibold mb-4">
        Grade Input for {{ student.first_name }} {{ student.last_name }}
      </h2>

      <!-- Quarter Dropdown -->
      <div class="mb-4">
        <label class="block text-sm font-medium">Select Quarter</label>
        <select
          v-model="quarter"
          class="mt-1 block w-full rounded border border-gray-400 bg-transparent text-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option v-for="q in quarters" :key="q" :value="q">
            {{ q }} Quarter
          </option>
        </select>
      </div>

      <!-- Grades Form -->
      <form @submit.prevent="submit">
        <h3 class="text-lg font-bold mb-2">{{ quarter }} Grades</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div v-for="subject in subjects" :key="subject.id">
            <label class="block text-sm font-medium">{{ subject.name }}</label>
            <input
              type="number"
              min="60"
              max="100"
              v-model="form.grades[subject.id]"
              class="mt-1 block w-full rounded border border-gray-400 bg-transparent text-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </div>

        <!-- Actions -->
        <div class="text-right mt-6 space-x-2">
          <button
            type="button"
            @click="emit('close')"
            class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
            :disabled="form.processing"
          >
            {{ form.processing ? 'Saving...' : 'Save Grades' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
