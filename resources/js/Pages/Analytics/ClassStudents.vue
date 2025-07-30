<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  class_name: String,
  grade_level: String,
  students: {
    type: Array,
    default: () => []
  },
})

const topThree = computed(() => props.students.slice(0, 3))
const remainingStudents = computed(() => props.students.slice(3))

const getRankBadgeClass = (rank) => {
  switch (rank) {
    case 1:
      return 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-white shadow-md border-2 border-yellow-300'
    case 2:
      return 'bg-gradient-to-r from-gray-400 to-gray-500 text-white shadow-md border-2 border-gray-300'
    case 3:
      return 'bg-gradient-to-r from-amber-600 to-amber-700 text-white shadow-md border-2 border-amber-500'
    default:
      return 'bg-white text-blue-600 border-2 border-blue-200'
  }
}

const getPodiumClass = (rank) => {
  switch (rank) {
    case 1:
      return 'border-yellow-300 bg-gradient-to-br from-white to-yellow-50'
    case 2:
      return 'border-gray-300 bg-gradient-to-br from-white to-gray-50'
    case 3:
      return 'border-amber-300 bg-gradient-to-br from-white to-amber-50'
    default:
      return 'border-gray-200 bg-white'
  }
}

const getMedalIcon = (rank) => {
  switch (rank) {
    case 1: return '🥇'
    case 2: return '🥈'
    case 3: return '🥉'
    default: return ''
  }
}

const getGradeColor = (average, rank) => {
  if (rank <= 3) {
    return 'bg-blue-100 text-blue-800'
  }
  
  const numericAverage = parseFloat(average)
  if (numericAverage >= 90) return 'bg-green-100 text-green-800'
  if (numericAverage >= 80) return 'bg-blue-100 text-blue-800'
  if (numericAverage >= 70) return 'bg-yellow-100 text-yellow-800'
  return 'bg-red-100 text-red-800'
}
</script>

<template>
  <Head title="Student Rankings" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">
            Student Rankings
          </h2>
          <p class="text-blue-600 font-medium mt-1">
            {{ class_name }} - Grade {{ grade_level }}
          </p>
        </div>
        
        <!-- Header Actions -->
        <div class="flex space-x-3">
          <button
            @click="$inertia.get(route('analytics.index'))"
            class="inline-flex items-center px-4 py-2 bg-white text-blue-600 border-2 border-blue-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 font-medium shadow-sm"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Analytics
          </button>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto space-y-8">
      <!-- Top 3 Students - Podium Style -->
      <div class="bg-white rounded-2xl shadow-lg border-2 border-blue-100 overflow-hidden" v-if="topThree.length > 0">
        <!-- Section Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
          <div class="flex items-center justify-center">
            <div class="text-3xl mr-3">🏆</div>
            <h3 class="text-2xl font-bold text-white">Top Performers</h3>
          </div>
        </div>
        
        <!-- Podium Cards -->
        <div class="p-8">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div
              v-for="(student, index) in topThree"
              :key="student.id || index"
              class="relative"
            >
              <!-- Podium Card -->
              <div 
                :class="getPodiumClass(index + 1)"
                class="rounded-2xl border-2 p-8 text-center transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl"
              >
                <!-- Crown/Rank Badge -->
                <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                  <div :class="getRankBadgeClass(index + 1)" class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold">
                    {{ index + 1 }}
                  </div>
                </div>
                
                <!-- Medal Icon -->
                <div class="text-5xl mb-6 mt-6">
                  {{ getMedalIcon(index + 1) }}
                </div>
                
                <!-- Student Info -->
                <h4 class="text-xl font-bold text-gray-900 mb-4">
                  {{ student.name || 'No Name' }}
                </h4>
                
                <!-- Average Grade -->
                <div class="bg-white border-2 border-blue-200 rounded-xl p-4 shadow-sm">
                  <p class="text-sm text-blue-600 font-semibold uppercase tracking-wide">Average Grade</p>
                  <p class="text-3xl font-bold text-blue-700 mt-1">{{ student.average || 'N/A' }}</p>
                </div>
                
                <!-- Action Button -->
                <div class="mt-6">
                  <div class="text-sm text-blue-600 font-medium">Excellent Performance!</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Complete Rankings Table -->
      <div class="bg-white rounded-2xl shadow-lg border-2 border-blue-100 overflow-hidden">
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-blue-50 to-white px-8 py-6 border-b-2 border-blue-100">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">Complete Rankings</h3>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-blue-50 border-b-2 border-blue-100">
              <tr>
                <th class="px-8 py-4 text-left text-sm font-bold text-blue-700 uppercase tracking-wider">
                  Rank
                </th>
                <th class="px-8 py-4 text-center text-sm font-bold text-blue-700 uppercase tracking-wider">
                  Student Name
                </th>
                <th class="px-8 py-4 text-center text-sm font-bold text-blue-700 uppercase tracking-wider">
                  Average Grade
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-blue-100">
              <!-- All students -->
              <tr
                v-for="(student, index) in students"
                :key="student.id || index"
                class="hover:bg-blue-50 transition-colors duration-200"
                :class="{
                  'bg-gradient-to-r from-blue-50 to-white': index < 3
                }"
              >
                <td class="px-8 py-6 whitespace-nowrap">
                  <div class="flex items-center">
                    <span 
                      :class="getRankBadgeClass(index + 1)" 
                      class="px-4 py-2 rounded-lg text-sm font-bold min-w-[3rem] text-center"
                    >
                      #{{ index + 1 }}
                    </span>
                    <span class="ml-3 text-xl" v-if="index < 3">{{ getMedalIcon(index + 1) }}</span>
                  </div>
                </td>
                <td class="px-8 py-6 whitespace-nowrap">
                  <div class="text-center font-semibold text-gray-900">
                    {{ student.name || 'No Name' }}
                  </div>
                </td>
                <td class="px-8 py-6 whitespace-nowrap text-center">
                  <span 
                    class="px-4 py-2 inline-flex text-sm font-bold rounded-lg"
                    :class="getGradeColor(student.average, index + 1)"
                  >
                    {{ student.average || 'N/A' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="students.length === 0" class="bg-white rounded-2xl shadow-lg border-2 border-blue-100 p-16 text-center">
        <div class="text-blue-300 text-8xl mb-6">📊</div>
        <h3 class="text-2xl font-bold text-gray-900 mb-4">No Students Found</h3>
        <p class="text-gray-600 mb-8 text-lg">There are no students to display for this class.</p>
        <button 
          class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors"
          @click="$inertia.get(route('students.create'))"
        >
          Add Students
        </button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>