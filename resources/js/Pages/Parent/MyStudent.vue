<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const { props } = usePage()
const students = ref(props.students)
const classes = ref(props.classes)

// Total students count
const totalStudents = computed(() => students.value.length)
</script>

<template>
  <Head title="Student Management" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0">
        <h2 class="text-2xl font-bold text-white bg-gradient-to-r from-white to-white/70 bg-clip-text text-transparent">
          Student Management
        </h2>
        <div class="flex items-center space-x-4">
          <!-- Total Students Stats -->
          <div class="hidden md:flex items-center space-x-3 text-sm">
            <div class="backdrop-blur-sm bg-blue-500/20 border border-blue-300/30 px-3 py-1 rounded-full">
              <span class="text-blue-100">{{ totalStudents }} Total Students</span>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

      <!-- Mobile Stats (visible on small screens) -->
      <div class="md:hidden">
        <div class="backdrop-blur-md bg-blue-500/10 border border-blue-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-blue-100">{{ totalStudents }}</div>
          <div class="text-sm text-blue-200">Total Students</div>
        </div>
      </div>

      <!-- Students Table -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <h3 class="text-lg font-semibold text-white">Registered Students</h3>
          <p class="text-sm text-white/70 mt-1">{{ totalStudents }} students registered</p>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
          <table class="min-w-full divide-y divide-white/10">
            <thead class="bg-white/5">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Student</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">LRN</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Class</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              <tr 
                v-for="student in students" 
                :key="student.id" 
                class="hover:bg-white/5 transition-colors duration-150"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-gradient-to-r from-emerald-500 to-blue-600 flex items-center justify-center text-white font-medium text-sm">
                        {{ student.first_name.charAt(0) }}{{ student.last_name.charAt(0) }}
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-white">{{ student.first_name }} {{ student.middle_name }} {{ student.last_name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-white">{{ student.lrn }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-white">{{ student.class?.name }}</div>
                  <div class="text-xs text-white/60">Grade {{ student.class?.grade_level }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="student.approved_by_teacher 
                      ? 'bg-emerald-500/20 text-emerald-100 border-emerald-400/30' 
                      : 'bg-amber-500/20 text-amber-100 border-amber-400/30'"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border backdrop-blur-sm"
                  >
                    <span 
                      :class="student.approved_by_teacher ? 'bg-emerald-400' : 'bg-amber-400'"
                      class="w-1.5 h-1.5 rounded-full mr-2"
                    ></span>
                    {{ student.approved_by_teacher ? 'Enrolled' : 'Unenrolled' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-white/10">
          <div 
            v-for="student in students" 
            :key="student.id"
            class="p-6 hover:bg-white/5 transition-colors duration-150"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center text-white font-medium">
                  {{ student.first_name.charAt(0) }}{{ student.last_name.charAt(0) }}
                </div>
                <div>
                  <h4 class="text-white font-medium">{{ student.first_name }} {{ student.last_name }}</h4>
                  <p class="text-sm text-white/70">{{ student.class?.name }} (Grade {{ student.class?.grade_level }})</p>
                  <span
                    :class="student.approved_by_teacher 
                      ? 'bg-emerald-500/20 text-emerald-100 border-emerald-400/30' 
                      : 'bg-amber-500/20 text-amber-100 border-amber-400/30'"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border backdrop-blur-sm mt-2"
                  >
                    <span 
                      :class="student.approved_by_teacher ? 'bg-emerald-400' : 'bg-amber-400'"
                      class="w-1.5 h-1.5 rounded-full mr-2"
                    ></span>
                    {{ student.approved_by_teacher ? 'Approved' : 'Pending' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="students.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-white/80">No students registered yet</h3>
          <p class="mt-1 text-sm text-white/60">Students will appear here once they are registered.</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>