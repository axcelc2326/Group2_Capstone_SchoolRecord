<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Trophy, Medal, Award, ArrowLeft, Users, School, LineChart } from 'lucide-vue-next'

const props = defineProps({
  grade_level: String,
  students: {
    type: Array,
    default: () => []
  },
  class_names: {
    type: Array,
    default: () => []
  }
})

const topThree = computed(() => props.students.slice(0, 3))
const remainingStudents = computed(() => props.students.slice(3))

const getRankBadgeClass = (rank) => {
  switch (rank) {
    case 1:
      return 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-white border border-yellow-300/50'
    case 2:
      return 'bg-gradient-to-r from-gray-400 to-gray-500 text-white border border-gray-300/50'
    case 3:
      return 'bg-gradient-to-r from-amber-600 to-amber-700 text-white border border-amber-500/50'
    default:
      return 'backdrop-blur-sm bg-white/20 text-white border border-white/30'
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
  if (remarkLower.includes('promoted') || remarkLower.includes('passed') || remarkLower.includes('excellent') || remarkLower.includes('outstanding')) {
    return 'bg-green-500/20 text-green-100 border border-green-400/30'
  }
  if (remarkLower.includes('retained') || remarkLower.includes('failed') || remarkLower.includes('needs improvement')) {
    return 'bg-red-500/20 text-red-100 border border-red-400/30'
  }
  if (remarkLower.includes('progress')) {
    return 'bg-yellow-500/20 text-yellow-100 border border-yellow-400/30'
  }
  return 'bg-blue-500/20 text-blue-100 border border-blue-400/30'
}

const formatAverage = (finalAverage) => {
  if (finalAverage === null || finalAverage === undefined) return 'N/A'
  return typeof finalAverage === 'number' ? Math.round(finalAverage) : finalAverage
}

const getClassBadgeColor = (className) => {
  // Generate consistent color based on class name
  const colors = [
    'bg-purple-500/20 text-purple-100 border border-purple-400/30',
    'bg-indigo-500/20 text-indigo-100 border border-indigo-400/30',
    'bg-pink-500/20 text-pink-100 border border-pink-400/30',
    'bg-teal-500/20 text-teal-100 border border-teal-400/30',
    'bg-orange-500/20 text-orange-100 border border-orange-400/30'
  ]
  
  if (!className) return colors[0]
  
  const index = className.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0) % colors.length
  return colors[index]
}
</script>

<template>
  <Head :title="`Grade ${grade_level} Students - My Classes`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-1">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-bold text-white">
              My Students Ranking
            </h2>
            <p class="text-white/70 mt-1">
              Grade {{ grade_level }} - {{ class_names.join(', ') || 'My Classes' }}
            </p>
          </div>
          <LineChart class="w-8 h-8 text-white/60" />
        </div>
      </div>
    </template>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      <!-- Header Actions -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex flex-wrap items-center gap-3">
          <!-- Student Count -->
          <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-2">
            <span class="text-white/80 text-sm">{{ students.length }} Students</span>
          </div>
          
          <!-- Class Names -->
          <div 
            v-for="className in class_names" 
            :key="className"
            class="backdrop-blur-sm rounded-lg px-3 py-2 border"
            :class="getClassBadgeColor(className)"
          >
            <div class="flex items-center gap-1.5">
              <School class="w-3 h-3" />
              <span class="text-xs font-medium">{{ className }}</span>
            </div>
          </div>
        </div>
        
        <button
          @click="$inertia.get(route('teacher.analytics'))"
          class="backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30 flex items-center gap-2"
        >
          <ArrowLeft class="w-4 h-4" />
          Back to My Analytics
        </button>
      </div>

      <!-- Teacher Stats Summary -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4" v-if="students.length > 0">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white/70 text-sm">Class Average</p>
              <p class="text-2xl font-bold text-white mt-1">
                {{ formatAverage(students.reduce((sum, student) => sum + parseFloat(student.final_average || 0), 0) / students.length) }}%
              </p>
            </div>
            <LineChart class="w-8 h-8 text-blue-300" />
          </div>
        </div>
        
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white/70 text-sm">Promoted</p>
              <p class="text-2xl font-bold text-green-300 mt-1">
                {{ students.filter(s => s.remarks?.toLowerCase().includes('promoted')).length }}
              </p>
            </div>
            <Trophy class="w-8 h-8 text-green-300" />
          </div>
        </div>
        
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white/70 text-sm">In Progress</p>
              <p class="text-2xl font-bold text-yellow-300 mt-1">
                {{ students.filter(s => s.remarks?.toLowerCase().includes('progress')).length }}
              </p>
            </div>
            <Users class="w-8 h-8 text-yellow-300" />
          </div>
        </div>
        
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white/70 text-sm">Retained</p>
              <p class="text-2xl font-bold text-red-300 mt-1">
                {{ students.filter(s => s.remarks?.toLowerCase().includes('retained')).length }}
              </p>
            </div>
            <School class="w-8 h-8 text-red-300" />
          </div>
        </div>
      </div>

      <!-- Top 3 Students - Podium Style -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden" v-if="topThree.length > 0">
        <!-- Section Header -->
        <div class="bg-white/5 border-b border-white/10 px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <Trophy class="w-5 h-5 text-yellow-400 mr-2" />
              <h3 class="text-xl font-semibold text-white">Top Performers in My Classes</h3>
            </div>
            <div class="text-sm text-white/60">
              Grade {{ grade_level }}
            </div>
          </div>
        </div>
        
        <!-- Podium Cards -->
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div
              v-for="(student, index) in topThree"
              :key="student.id || index"
              class="relative"
            >
              <!-- Podium Card -->
              <div 
                :class="getPodiumClass(index + 1)"
                class="rounded-xl border p-6 text-center transition-all duration-200"
              >
                <!-- Crown/Rank Badge -->
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                  <div :class="getRankBadgeClass(index + 1)" class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold">
                    {{ index + 1 }}
                  </div>
                </div>
                
                <!-- Medal Icon -->
                <div class="text-4xl mb-3 mt-4">
                  {{ getMedalIcon(index + 1) }}
                </div>
                
                <!-- Student Info -->
                <h4 class="text-lg font-bold text-white mb-2">
                  {{ student.name || 'No Name' }}
                </h4>

                <!-- Class/Section Badge -->
                <div class="mb-3">
                  <div 
                    class="inline-flex items-center px-2 py-1 rounded-lg backdrop-blur-sm border"
                    :class="getClassBadgeColor(student.class_name)"
                  >
                    <School class="w-3 h-3 mr-1.5" />
                    <span class="text-xs font-medium">{{ student.class_name || 'No Class' }}</span>
                  </div>
                </div>
                
                <!-- Average Grade -->
                <div class="backdrop-blur-sm bg-white/20 border border-white/30 rounded-lg p-3 mb-3">
                  <p class="text-xs text-blue-200 font-semibold uppercase tracking-wide">Final Average</p>
                  <p class="text-2xl font-bold text-white mt-1">{{ formatAverage(student.final_average) }}%</p>
                </div>

                <!-- Remarks -->
                <div class="backdrop-blur-sm rounded-lg p-2" :class="getRemarkColor(student.remarks)">
                  <p class="text-xs uppercase tracking-wide font-semibold opacity-90">Remarks</p>
                  <p class="text-sm font-bold mt-1">{{ student.remarks || 'In Progress' }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Complete Rankings Table -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <Award class="w-5 h-5 text-purple-300" />
              <div>
                <h3 class="text-lg font-semibold text-white">My Students Rankings</h3>
                <p class="text-sm text-white/70 mt-1">{{ students.length }} students across {{ class_names.length }} class{{ class_names.length !== 1 ? 'es' : '' }}</p>
              </div>
            </div>
            <div class="text-sm text-white/60 hidden md:block">
              Sorted by Final Average
            </div>
          </div>
        </div>
        
        <div v-if="students.length > 0">
          <!-- Desktop View -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                    Rank
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                    Student Name
                  </th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                    Class/Section
                  </th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                    Final Average
                  </th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                    Remarks
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                <tr
                  v-for="(student, index) in students"
                  :key="student.id || index"
                  class="hover:bg-white/5 transition-colors duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <span 
                        :class="getRankBadgeClass(index + 1)" 
                        class="px-3 py-1 rounded text-sm font-bold min-w-[2.5rem] text-center backdrop-blur-sm"
                      >
                        #{{ index + 1 }}
                      </span>
                      <span class="ml-2 text-lg" v-if="index < 3">{{ getMedalIcon(index + 1) }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-8 w-8">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-bold text-sm">
                          {{ (student.name || 'N').charAt(0).toUpperCase() }}
                        </div>
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-white">{{ student.name || 'No Name' }}</div>
                        <div class="text-xs text-white/60">My Student</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div 
                      class="inline-flex items-center px-2 py-1 rounded backdrop-blur-sm border"
                      :class="getClassBadgeColor(student.class_name)"
                    >
                      <School class="w-3 h-3 mr-1" />
                      <span class="text-xs font-medium">{{ student.class_name || 'No Class' }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span 
                      class="px-3 py-1 inline-flex text-sm font-bold rounded backdrop-blur-sm"
                      :class="getGradeColor(student.final_average, index + 1)"
                    >
                      {{ formatAverage(student.final_average) }}%
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span 
                      class="px-2 py-1 inline-flex text-xs font-medium rounded backdrop-blur-sm"
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
              class="p-4 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                  <span 
                    :class="getRankBadgeClass(index + 1)" 
                    class="px-2 py-1 rounded text-xs font-bold backdrop-blur-sm"
                  >
                    #{{ index + 1 }}
                  </span>
                  <span class="text-lg" v-if="index < 3">{{ getMedalIcon(index + 1) }}</span>
                  
                  <div class="flex-shrink-0 h-8 w-8 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-bold text-sm">
                    {{ (student.name || 'N').charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <h4 class="text-white text-sm font-medium">{{ student.name || 'No Name' }}</h4>
                    <div class="flex items-center text-xs text-white/70 mt-0.5">
                      <School class="w-3 h-3 mr-1" />
                      {{ student.class_name || 'No Class' }}
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="flex flex-col space-y-2">
                <div class="flex justify-center">
                  <span 
                    class="px-3 py-1 inline-flex text-sm font-bold rounded backdrop-blur-sm"
                    :class="getGradeColor(student.final_average, index + 1)"
                  >
                    Final Average: {{ formatAverage(student.final_average) }}%
                  </span>
                </div>
                
                <div class="flex justify-center">
                  <span 
                    class="px-2 py-1 inline-flex text-xs font-medium rounded backdrop-blur-sm"
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
        <div v-else class="text-center py-8">
          <Users class="mx-auto h-10 w-10 text-white/40 mb-3" />
          <h3 class="text-sm font-medium text-white/80">No Students in Your Classes</h3>
          <p class="mt-1 text-sm text-white/60">You don't have any students assigned to your classes for Grade {{ grade_level }}.</p>
          <button 
            class="mt-4 inline-flex items-center px-3 py-2 bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg transition-all duration-200 border border-white/20 hover:border-white/30"
            @click="$inertia.get(route('teacher.classes.index'))"
          >
            <School class="w-4 h-4 mr-2" />
            Manage Classes
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Glassmorphism base effects */
.backdrop-blur-md {
  backdrop-filter: blur(16px);
}

.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

/* Smooth transitions */
.transition-all {
  transition: all 0.2s ease-in-out;
}

/* Focus states for accessibility */
button:focus-visible {
  outline: 2px solid rgba(59, 130, 246, 0.5);
  outline-offset: 2px;
  border-radius: 0.375rem;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .bg-white\/10 {
    background-color: rgba(255, 255, 255, 0.2);
  }
  
  .border-white\/20 {
    border-color: rgba(255, 255, 255, 0.4);
  }
  
  .text-white\/70 {
    color: rgba(255, 255, 255, 0.9);
  }
}
</style>