<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Trophy, Medal, Award, ArrowLeft, Users } from 'lucide-vue-next'

const props = defineProps({
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
      return 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-white shadow-md border-2 border-yellow-300/50'
    case 2:
      return 'bg-gradient-to-r from-gray-400 to-gray-500 text-white shadow-md border-2 border-gray-300/50'
    case 3:
      return 'bg-gradient-to-r from-amber-600 to-amber-700 text-white shadow-md border-2 border-amber-500/50'
    default:
      return 'backdrop-blur-sm bg-white/20 text-white border-2 border-white/30'
  }
}

const getPodiumClass = (rank) => {
  switch (rank) {
    case 1:
      return 'border-yellow-300/50 bg-gradient-to-br from-yellow-500/20 to-yellow-600/10 backdrop-blur-md'
    case 2:
      return 'border-gray-300/50 bg-gradient-to-br from-gray-500/20 to-gray-600/10 backdrop-blur-md'
    case 3:
      return 'border-amber-300/50 bg-gradient-to-br from-amber-500/20 to-amber-600/10 backdrop-blur-md'
    default:
      return 'border-white/20 backdrop-blur-md bg-white/10'
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

const getGradeColor = (finalAverage, rank) => {
  if (rank <= 3) {
    return 'bg-blue-500/20 text-blue-100 border border-blue-400/30'
  }
  
  const numericAverage = parseFloat(finalAverage)
  if (numericAverage >= 90) return 'bg-green-500/20 text-green-100 border border-green-400/30'
  if (numericAverage >= 80) return 'bg-blue-500/20 text-blue-100 border border-blue-400/30'
  if (numericAverage >= 70) return 'bg-yellow-500/20 text-yellow-100 border border-yellow-400/30'
  return 'bg-red-500/20 text-red-100 border border-red-400/30'
}

const getRemarkColor = (remark) => {
  const remarkLower = (remark || '').toLowerCase()
  if (remarkLower.includes('passed') || remarkLower.includes('excellent') || remarkLower.includes('outstanding')) {
    return 'bg-green-500/20 text-green-100 border border-green-400/30'
  }
  if (remarkLower.includes('failed') || remarkLower.includes('needs improvement')) {
    return 'bg-red-500/20 text-red-100 border border-red-400/30'
  }
  if (remarkLower.includes('progress')) {
    return 'bg-yellow-500/20 text-yellow-100 border border-yellow-400/30'
  }
  return 'bg-blue-500/20 text-blue-100 border border-blue-400/30'
}

const formatAverage = (finalAverage) => {
  if (finalAverage === null || finalAverage === undefined) return 'N/A'
  return typeof finalAverage === 'number' ? finalAverage.toFixed(2) : finalAverage
}
</script>

<template>
  <Head title="Student Rankings" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0">
        <div>
          <h2 class="text-2xl font-bold text-white bg-gradient-to-r from-white to-white/70 bg-clip-text text-transparent">
            🏆 Student Rankings
          </h2>
          <p class="text-blue-200 font-medium mt-1">
            Grade {{ grade_level }} Students
          </p>
        </div>
        
        <!-- Header Actions -->
        <div class="flex items-center space-x-4">
          <!-- Quick Stats -->
          <div class="hidden md:flex items-center space-x-3 text-sm">
            <div class="backdrop-blur-sm bg-blue-500/20 border border-blue-300/30 px-3 py-1 rounded-full">
              <span class="text-blue-100">{{ students.length }} Students</span>
            </div>
          </div>
          
          <button
            @click="$inertia.get(route('analytics.index'))"
            class="group relative inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-purple-500 to-blue-600 hover:from-purple-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 border border-white/20"
          >
            <ArrowLeft class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-200" />
            Back to Analytics
          </button>
        </div>
      </div>
    </template>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      <!-- Mobile Stats (visible on small screens) -->
      <div class="md:hidden">
        <div class="backdrop-blur-md bg-blue-500/10 border border-blue-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-blue-100">{{ students.length }}</div>
          <div class="text-sm text-blue-200">Total Students</div>
        </div>
      </div>

      <!-- Top 3 Students - Podium Style -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden" v-if="topThree.length > 0">
        <!-- Section Header -->
        <div class="bg-gradient-to-r from-purple-500/30 to-blue-600/30 backdrop-blur-sm border-b border-white/10 px-8 py-6">
          <div class="flex items-center justify-center">
            <div class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center mr-3">
              <Trophy class="w-4 h-4 text-white" />
            </div>
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
                <h4 class="text-xl font-bold text-white mb-4">
                  {{ student.name || 'No Name' }}
                </h4>
                
                <!-- Average Grade -->
                <div class="backdrop-blur-sm bg-white/20 border border-white/30 rounded-xl p-4 shadow-sm mb-4">
                  <p class="text-sm text-blue-200 font-semibold uppercase tracking-wide">Final Average</p>
                  <p class="text-3xl font-bold text-white mt-1">{{ formatAverage(student.final_average) }}</p>
                </div>

                <!-- Remarks -->
                <div class="backdrop-blur-sm rounded-xl p-3 shadow-sm" :class="getRemarkColor(student.remarks)">
                  <p class="text-xs uppercase tracking-wide font-semibold opacity-90">Remarks</p>
                  <p class="text-sm font-bold mt-1">{{ student.remarks || 'In Progress' }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Complete Rankings Table -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
              <Award class="w-4 h-4 text-white" />
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">Complete Rankings</h3>
              <p class="text-sm text-white/70 mt-1">{{ students.length }} students ranked by final average</p>
            </div>
          </div>
        </div>
        
        <div v-if="students.length > 0">
          <!-- Desktop View -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-8 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                    Rank
                  </th>
                  <th class="px-8 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                    Student Name
                  </th>
                  <th class="px-8 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                    Final Average
                  </th>
                  <th class="px-8 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                    Remarks
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                <tr
                  v-for="(student, index) in students"
                  :key="student.id || index"
                  class="hover:bg-white/5 transition-colors duration-200"
                  :class="{
                    'bg-gradient-to-r from-white/5 to-transparent': index < 3
                  }"
                >
                  <td class="px-8 py-6 whitespace-nowrap">
                    <div class="flex items-center">
                      <span 
                        :class="getRankBadgeClass(index + 1)" 
                        class="px-4 py-2 rounded-lg text-sm font-bold min-w-[3rem] text-center backdrop-blur-sm"
                      >
                        #{{ index + 1 }}
                      </span>
                      <span class="ml-3 text-xl" v-if="index < 3">{{ getMedalIcon(index + 1) }}</span>
                    </div>
                  </td>
                  <td class="px-8 py-6 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-bold">
                          {{ (student.name || 'N').charAt(0).toUpperCase() }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-white">{{ student.name || 'No Name' }}</div>
                        <div class="text-xs text-white/60">Student</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-8 py-6 whitespace-nowrap text-center">
                    <span 
                      class="px-4 py-2 inline-flex text-sm font-bold rounded-lg backdrop-blur-sm"
                      :class="getGradeColor(student.final_average, index + 1)"
                    >
                      {{ formatAverage(student.final_average) }}
                    </span>
                  </td>
                  <td class="px-8 py-6 whitespace-nowrap text-center">
                    <span 
                      class="px-3 py-1 inline-flex text-xs font-medium rounded-lg backdrop-blur-sm"
                      :class="getRemarkColor(student.remarks)"
                    >
                      {{ student.remarks || 'In Progress' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Cards -->
          <div class="md:hidden divide-y divide-white/10">
            <div 
              v-for="(student, index) in students"
              :key="student.id || index"
              class="p-6 hover:bg-white/5 transition-colors duration-150"
              :class="{
                'bg-gradient-to-r from-white/5 to-transparent': index < 3
              }"
            >
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                  <span 
                    :class="getRankBadgeClass(index + 1)" 
                    class="px-3 py-1 rounded-lg text-sm font-bold backdrop-blur-sm"
                  >
                    #{{ index + 1 }}
                  </span>
                  <span class="text-xl" v-if="index < 3">{{ getMedalIcon(index + 1) }}</span>
                  
                  <div class="flex-shrink-0 h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-bold">
                    {{ (student.name || 'N').charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <h4 class="text-white font-medium">{{ student.name || 'No Name' }}</h4>
                    <p class="text-sm text-white/70">Student</p>
                  </div>
                </div>
              </div>
              
              <div class="flex flex-col space-y-3">
                <div class="flex justify-center">
                  <span 
                    class="px-4 py-2 inline-flex text-sm font-bold rounded-lg backdrop-blur-sm"
                    :class="getGradeColor(student.final_average, index + 1)"
                  >
                    Final Average: {{ formatAverage(student.final_average) }}
                  </span>
                </div>
                
                <div class="flex justify-center">
                  <span 
                    class="px-3 py-1 inline-flex text-xs font-medium rounded-lg backdrop-blur-sm"
                    :class="getRemarkColor(student.remarks)"
                  >
                    {{ student.remarks || 'In Progress' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <Users class="mx-auto h-12 w-12 text-white/40 mb-4" />
          <h3 class="mt-2 text-sm font-medium text-white/80">No Students Found</h3>
          <p class="mt-1 text-sm text-white/60">There are no students to display for this grade level.</p>
          <button 
            class="mt-4 inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-blue-600 hover:from-emerald-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200"
            @click="$inertia.get(route('students.create'))"
          >
            <Users class="w-4 h-4 mr-2" />
            Add Students
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>