<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, reactive, watch } from 'vue';

const props = defineProps({
  student: Object,
  subjects: Array,
  grades: Object,
  currentQuarter: String,
});

// 🌟 Quarters available
const quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
const quarter = ref(props.currentQuarter);

// 🌟 Create blank grades structure
const blankGrades = () =>
  props.subjects.reduce((acc, subject) => {
    acc[subject.id] = '';
    return acc;
  }, {});

// 🌟 Reactive grade states per quarter (pre-fill from props.grades)
const allGrades = reactive({
  Q1: { ...blankGrades(), ...(props.grades.Q1 ?? {}) },
  Q2: { ...blankGrades(), ...(props.grades.Q2 ?? {}) },
  Q3: { ...blankGrades(), ...(props.grades.Q3 ?? {}) },
  Q4: { ...blankGrades(), ...(props.grades.Q4 ?? {}) },
});

// 🌟 Inertia form
const form = useForm({
  student_id: props.student.id,
  class_id: props.student.class_id,
  quarter: quarter.value,
  grades: allGrades[quarter.value],
});

// 🔄 Sync form data when quarter changes
watch(quarter, (newQuarter) => {
  form.quarter = newQuarter;
  form.grades = allGrades[newQuarter];
});

const submit = () => {
  form.post(route('grades.store'), {
    onSuccess: () => {
      // Optional: Notify user
    },
  });
};
</script>

<template>
  <Head title="Enter Grades" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">
        Grade Input for {{ student.first_name }} {{ student.last_name }}
      </h2>
    </template>

    <div class="py-8">
      <div class="max-w-4xl mx-auto bg-transparent p-6 rounded shadow space-y-6">
        <!-- 🔻 Quarter Dropdown -->
        <div>
          <label class="block text-sm font-medium text-white">Select Quarter</label>
          <select
            v-model="quarter"
            class="mt-1 block w-full rounded border border-gray-400 bg-transparent text-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option v-for="q in quarters" :key="q" :value="q">
              {{ q }} Quarter
            </option>
          </select>
        </div>

        <!-- 🔢 Subject Grades -->
        <form @submit.prevent="submit">
          <h3 class="text-lg font-bold mb-2">{{ quarter }} Grades</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="subject in subjects" :key="subject.id">
              <label class="block text-sm font-medium text-white">
                {{ subject.name }}
              </label>
              <input
                type="number"
                min="60"
                max="100"
                v-model="form.grades[subject.id]"
                class="mt-1 block w-full rounded border border-gray-400 bg-transparent text-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <div class="text-right mt-6">
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
  </AuthenticatedLayout>
</template>

