<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  student: Object,
  subjects: Array,
  grades: Object,
});

const quarters = ['Q1', 'Q2', 'Q3', 'Q4'];

// Get badge color class
const getBadgeColorClass = (grade) => {
  if (!grade || grade === '—') return 'bg-gray-500/20 border-gray-300/30'
  
  const numGrade = parseFloat(grade)
  if (numGrade >= 90) return 'bg-emerald-500/20 border-emerald-300/30'
  if (numGrade >= 80) return 'bg-blue-500/20 border-blue-300/30'
  if (numGrade >= 70) return 'bg-amber-500/20 border-amber-300/30'
  return 'bg-red-500/20 border-red-300/30'
}

// Calculate quarterly averages as whole numbers
const calculateQuarterlyAverage = (quarter) => {
  const quarterGrades = props.grades[quarter]
  if (!quarterGrades) return null
  
  const validGrades = Object.values(quarterGrades).filter(grade => grade && grade !== '—').map(g => parseFloat(g))
  if (validGrades.length === 0) return null
  
  const average = validGrades.reduce((sum, grade) => sum + grade, 0) / validGrades.length
  return Math.round(average) // Round to whole number
}

// Calculate overall GPA as whole number
const calculateOverallGPA = () => {
  const allAverages = quarters.map(q => calculateQuarterlyAverage(q)).filter(avg => avg !== null)
  if (allAverages.length === 0) return null
  
  const gpa = allAverages.reduce((sum, avg) => sum + parseFloat(avg), 0) / allAverages.length
  return Math.round(gpa) // Round to whole number
}

// Format grade display to remove decimals
const formatGrade = (grade) => {
  if (!grade || grade === '—') return '—'
  return Math.round(parseFloat(grade)).toString()
}

// Go back function
const goBack = () => {
  window.history.back()
}
</script>

<template>
  <Head title="Grade Progress" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between space-y-4 lg:space-y-0">
        <!-- Left Section: Student Info -->
        <div class="space-y-3 flex-1">
          <!-- Header Title -->
          <div>
            <h2 class="text-3xl font-bold text-white bg-gradient-to-r from-white via-blue-100 to-purple-100 bg-clip-text text-transparent">
              📊 Academic Progress
            </h2>
            <p class="text-white/70 mt-1">Comprehensive grade overview and performance tracking</p>
          </div>

          <!-- Student Info -->
          <div class="flex items-center space-x-4 mt-2">
            <div class="flex items-center space-x-3">
              <div class="relative">
                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-emerald-500 via-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                  {{ props.student.first_name.charAt(0) }}{{ props.student.last_name.charAt(0) }}
                </div>
              </div>
              <div>
                <div class="text-xl font-semibold text-white">{{ props.student.first_name }} {{ props.student.middle_name }} {{ props.student.last_name }}</div>
                <div class="flex items-center space-x-2 text-sm text-white/70">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  <span>{{ props.student.class.name }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Section: Quick Stats + Back Button -->
        <div class="flex flex-col items-start space-y-3 lg:items-end lg:space-y-4">
          <!-- Quick Stats -->
          <div class="flex flex-wrap items-center gap-3 text-sm pb-4">
            <div class="backdrop-blur-sm bg-blue-500/20 border border-blue-300/30 px-4 py-2 rounded-full flex items-center space-x-2">
              <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              <span class="text-blue-100 font-medium">{{ props.subjects.length }} Subjects</span>
            </div>
            <div class="backdrop-blur-sm bg-purple-500/20 border border-purple-300/30 px-4 py-2 rounded-full flex items-center space-x-2">
              <svg class="w-4 h-4 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span class="text-purple-100 font-medium">4 Quarters</span>
            </div>
            <div v-if="calculateOverallGPA()" class="backdrop-blur-sm bg-emerald-500/20 border border-emerald-300/30 px-4 py-2 rounded-full flex items-center space-x-2">
              <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
              <span class="text-emerald-100 font-medium">GPA: {{ calculateOverallGPA() }}</span>
            </div>
          </div>

          <!-- Back Button -->
          <button
            @click="goBack"
            class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/20 hover:border-white/30 rounded-xl transition-all duration-200 backdrop-blur-sm shadow-lg hover:shadow-xl"
          >
            <svg class="w-4 h-4 mr-2 group-hover:-translate-x-0.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to MyStudents
          </button>
        </div>
      </div>
    </template>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
      <!-- Performance Summary Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl p-8 shadow-2xl">
        <div class="flex items-center space-x-3 mb-6">
          <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
          <div>
            <h3 class="text-xl font-semibold text-white">Quarterly Performance Overview</h3>
            <p class="text-white/70 text-sm">Average grades across all subjects per quarter</p>
          </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="q in quarters" :key="q" class="text-center">
            <div class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 group">
              <div class="text-lg text-white/70 uppercase tracking-wider mb-3 font-medium">{{ q }}</div>
              <div class="flex items-center justify-center space-x-2 mb-2">
                <div class="text-3xl font-bold" :class="(calculateQuarterlyAverage(q))">
                  {{ calculateQuarterlyAverage(q) || '—' }}
                </div>
              </div>
              <div class="text-xs text-white/50 uppercase tracking-wide">Quarter Average</div>
              <div v-if="calculateQuarterlyAverage(q)" class="mt-3 w-full bg-white/10 rounded-full h-2 overflow-hidden">
                <div 
                  class="h-2 rounded-full transition-all duration-1000 bg-gradient-to-r from-blue-400 to-blue-600"
                  :style="`width: ${Math.min((parseFloat(calculateQuarterlyAverage(q)) / 100) * 100, 100)}%`"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Grade Progress Table -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-8 py-6 border-b border-white/10 bg-gradient-to-r from-white/5 to-transparent">
          <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-white">Detailed Academic Performance</h3>
              <p class="text-sm text-white/70 mt-1">Subject-wise quarterly grades with performance indicators</p>
            </div>
          </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="border-b border-white/10 bg-white/5">
                <th class="px-8 py-5 text-left text-xs font-semibold text-white uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Subject</span>
                  </div>
                </th>
                <th v-for="q in quarters" :key="q" class="px-6 py-5 text-center text-xs font-semibold text-white uppercase tracking-wider">
                  <div class="flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ q }}</span>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              <tr 
                v-for="subject in props.subjects" 
                :key="subject.id" 
                class="hover:bg-white/5 transition-colors duration-200"
              >
                <td class="px-8 py-6">
                  <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 h-12 w-12">
                      <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                        {{ subject.name.charAt(0) }}
                      </div>
                    </div>
                    <div>
                      <div class="text-base font-semibold text-white">{{ subject.name }}</div>
                    </div>
                  </div>
                </td>
                <td v-for="q in quarters" :key="q" class="px-6 py-6 text-center">
                  <div class="flex flex-col items-center space-y-2">
                    <span
                      v-if="props.grades[q]?.[subject.id] && props.grades[q][subject.id] !== '—'"
                      :class="[(props.grades[q][subject.id]), 'text-lg font-bold']"
                    >
                      {{ formatGrade(props.grades[q][subject.id]) }}
                    </span>
                    <span 
                      v-else
                      class="text-lg font-bold text-white/30"
                    >
                      —
                    </span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile/Tablet Cards -->
        <div class="lg:hidden">
          <div 
            v-for="subject in props.subjects" 
            :key="subject.id"
            class="p-6 border-b border-white/5 hover:bg-white/5 transition-colors duration-200 last:border-b-0"
          >
            <div class="flex items-center space-x-4 mb-6">
              <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg">
                {{ subject.name.charAt(0) }}
              </div>
              <div>
                <h4 class="text-white font-semibold text-lg">{{ subject.name }}</h4>
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div 
                v-for="q in quarters" 
                :key="q"
                class="text-center p-4 rounded-xl bg-white/5 border border-white/10"
              >
                <div class="text-xs text-white/60 uppercase tracking-wider mb-2 font-medium">{{ q }}</div>
                <div class="flex items-center justify-center space-x-2">
                  <div 
                    v-if="props.grades[q]?.[subject.id] && props.grades[q][subject.id] !== '—'"
                    :class="[(props.grades[q][subject.id]), 'text-xl font-bold']"
                  >
                    {{ formatGrade(props.grades[q][subject.id]) }}
                  </div>
                  <div 
                    v-else
                    class="text-xl font-bold text-white/30"
                  >
                    —
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="props.subjects.length === 0" class="text-center py-16">
          <div class="mx-auto h-16 w-16 rounded-full bg-white/10 flex items-center justify-center mb-4">
            <svg class="h-8 w-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-white/80 mb-2">No subjects available</h3>
          <p class="text-sm text-white/60 max-w-md mx-auto">Subjects will appear here once they are added to the curriculum. Contact your administrator to add subjects.</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.shadow-glow {
  box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
}

/* Progress bar enhancements */
.h-2 {
  position: relative;
  overflow: hidden;
}

.h-2::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% { left: -100%; }
  100% { left: 100%; }
}
</style>