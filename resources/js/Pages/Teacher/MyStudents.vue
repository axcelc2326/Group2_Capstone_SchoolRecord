<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import Swal from 'sweetalert2'
import axios from 'axios'
import GradeInputModal from '@/Components/GradeInputModal.vue'

const props = defineProps({
  students: Object,
  filters: Object,
  message: String,
})

const search = ref(props.filters?.search || '')
const showSettingsDropdown = ref(false)

// 🔍 Search filter
watch(search, (value) => {
  router.get(route('teacher.students'), { search: value }, { preserveState: true, replace: true })
})

// Statistics
const totalStudents = computed(() => props.students.total || 0)

// 🚀 Modal State
const showGradeModal = ref(false)
const InputGrade = ref(null)

// 🔹 Fetch data for a student and open modal
const openGradeModal = async (studentId) => {
  try {
    const res = await axios.get(route('grades.InputGrade', studentId))
    InputGrade.value = res.data
    showGradeModal.value = true
  } catch (err) {
    console.error('Error loading grade data:', err)
  }
}

// 🔻 Existing student actions
function removeStudent(studentId) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will unapprove the student.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, unapprove',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#ef4444', // red-500
    cancelButtonColor: '#6b7280', // gray-500
    background: '#1f2937', // slate-800
    color: '#f9fafb',       // gray-50
    iconColor: '#ef4444',   // red-500
    backdrop: 'rgba(0,0,0,0.8)',
    customClass: {
      popup: 'rounded-xl border border-gray-700 shadow-lg',
      title: 'text-lg font-semibold',
      htmlContainer: 'text-gray-300',
      confirmButton: 'px-4 py-2 rounded-md font-medium',
      cancelButton: 'px-4 py-2 rounded-md font-medium',
    }
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(route('teacher.students.unapprove', { student: studentId }))
    }
  })
}

function clearGrades(studentId) {
  Swal.fire({
    title: 'Clear student grades?',
    text: 'This will delete all grades for this student.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, clear',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    background: '#1f2937',
    color: '#f9fafb',
    iconColor: '#ef4444',
    backdrop: 'rgba(0,0,0,0.8)',
    customClass: {
      popup: 'rounded-xl border border-gray-700 shadow-lg',
      title: 'text-lg font-semibold',
      htmlContainer: 'text-gray-300',
      confirmButton: 'px-4 py-2 rounded-md font-medium',
      cancelButton: 'px-4 py-2 rounded-md font-medium',
    }
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('teacher.students.clearGrades', { student: studentId }))
    }
  })
}

function unapproveAll() {
  Swal.fire({
    title: 'Unapprove ALL students?',
    text: 'This will unapprove all students in your class.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, unapprove all',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    background: '#1f2937',
    color: '#f9fafb',
    iconColor: '#ef4444',
    backdrop: 'rgba(0,0,0,0.8)',
    customClass: {
      popup: 'rounded-xl border border-gray-700 shadow-lg',
      title: 'text-lg font-semibold',
      htmlContainer: 'text-gray-300',
      confirmButton: 'px-4 py-2 rounded-md font-medium',
      cancelButton: 'px-4 py-2 rounded-md font-medium',
    }
  }).then((result) => {
    if (result.isConfirmed) {
      const studentIds = props.students.data.map((s) => s.id)
      router.put(route('teacher.students.unapproveAll'), {
        student_ids: studentIds,
      })
    }
  })
}

function clearAllGrades() {
  Swal.fire({
    title: 'Clear ALL grades?',
    text: 'This will remove all grades for all students shown.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, clear all grades',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    background: '#1f2937',
    color: '#f9fafb',
    iconColor: '#ef4444',
    backdrop: 'rgba(0,0,0,0.8)',
    customClass: {
      popup: 'rounded-xl border border-gray-700 shadow-lg',
      title: 'text-lg font-semibold',
      htmlContainer: 'text-gray-300',
      confirmButton: 'px-4 py-2 rounded-md font-medium',
      cancelButton: 'px-4 py-2 rounded-md font-medium',
    }
  }).then((result) => {
    if (result.isConfirmed) {
      const studentIds = props.students.data.map((s) => s.id)
      router.delete(route('teacher.students.clearAllGrades'), {
        data: { student_ids: studentIds },
        preserveScroll: true,
      })
    }
  })
}
</script>

<template>
  <Head title="My Students" />
  
  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0">
        <h2 class="text-2xl font-bold text-white bg-gradient-to-r from-white to-white/70 bg-clip-text text-transparent">
          👩‍🏫 My Students
        </h2>
        <div class="flex items-center space-x-4">
          <!-- Quick Stats -->
          <div class="hidden md:flex items-center space-x-3 text-sm">
            <div class="backdrop-blur-sm bg-blue-500/20 border border-blue-300/30 px-3 py-1 rounded-full">
              <span class="text-blue-100">{{ totalStudents }} Students</span>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Message Alert -->
      <div v-if="message" class="backdrop-blur-md bg-red-500/10 border border-red-300/30 rounded-2xl p-4 shadow-xl">
        <div class="text-red-100 font-medium">{{ message }}</div>
      </div>

      <!-- Top Action Bar -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl p-6 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
          
          <!-- Search Bar -->
          <div class="flex-1 max-w-md">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <input
                v-model="search"
                type="text"
                placeholder="Search students by name..."
                class="block w-full pl-10 pr-3 py-2.5 border-0 rounded-xl backdrop-blur-sm bg-white/10 border-white/20 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
              />
            </div>
          </div>

          <!-- Settings Dropdown -->
          <div class="relative z-50">
            <button
              @click="showSettingsDropdown = !showSettingsDropdown"
              class="group relative inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 border border-white/20"
            >
              <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Settings
            </button>
            
            <!-- Dropdown Menu -->
            <div 
              v-if="showSettingsDropdown"
              class="absolute right-0 mt-2 w-56 bg-slate-800 border border-gray-600 rounded-xl shadow-2xl z-[99999]"
              @click.away="showSettingsDropdown = false"
            >
              <div class="p-2">
                <button
                  @click="unapproveAll(); showSettingsDropdown = false"
                  class="w-full text-left px-4 py-3 text-sm text-red-100 hover:bg-red-500/20 rounded-lg transition-colors duration-150 flex items-center"
                >
                  <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  Unapprove All Students
                </button>
                <button
                  @click="clearAllGrades(); showSettingsDropdown = false"
                  class="w-full text-left px-4 py-3 text-sm text-yellow-100 hover:bg-yellow-500/20 rounded-lg transition-colors duration-150 flex items-center"
                >
                  <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  Clear All Grades
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

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
          <h3 class="text-lg font-semibold text-white">My Approved Students</h3>
          <p class="text-sm text-white/70 mt-1">{{ students.data.length }} students shown</p>
        </div>

        <div v-if="students.data.length">
          <!-- Desktop Table -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">#</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Student</th>
                  <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                <tr 
                  v-for="(student, index) in students.data" 
                  :key="student.id" 
                  class="hover:bg-white/5 transition-colors duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ index + 1 + (students.current_page - 1) * students.per_page }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-emerald-500 to-blue-600 flex items-center justify-center text-white font-medium text-sm">
                          {{ student.first_name.charAt(0) }}{{ student.last_name.charAt(0) }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-white">{{ student.first_name }} {{ student.last_name }}</div>
                        <div class="text-xs text-white/60">Parent: {{ student.parent_name }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                    <Link
                      :href="route('teacher.student.grades', { student: student.id })"
                      class="inline-flex items-center px-3 py-1.5 bg-green-500/20 hover:bg-green-500/30 text-green-100 border border-green-400/30 hover:border-green-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                      </svg>
                      View Grades
                    </Link>
                    <button
                      @click="openGradeModal(student.id)"
                      class="inline-flex items-center px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-100 border border-blue-400/30 hover:border-blue-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                      Input Grades
                    </button>
                    
                    <!-- Individual Settings Dropdown -->
                    <div class="relative inline-block">
                      <button
                        @click="student.showDropdown = !student.showDropdown"
                        class="inline-flex items-center px-3 py-1.5 bg-purple-500/20 hover:bg-purple-500/30 text-purple-100 border border-purple-400/30 hover:border-purple-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                      </button>
                      
                      <div 
                        v-if="student.showDropdown"
                        class="absolute right-0 mt-1 w-44 bg-slate-800 border border-gray-600 rounded-lg shadow-2xl z-[9999]"
                      >
                        <div class="p-1">
                          <button
                            @click="removeStudent(student.id); student.showDropdown = false"
                            class="w-full text-left px-3 py-2 text-xs text-red-100 hover:bg-red-500/20 rounded transition-colors duration-150 flex items-center"
                          >
                            <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Unapprove
                          </button>
                          <button
                            @click="clearGrades(student.id); student.showDropdown = false"
                            class="w-full text-left px-3 py-2 text-xs text-yellow-100 hover:bg-yellow-500/20 rounded transition-colors duration-150 flex items-center"
                          >
                            <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Clear Grades
                          </button>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Cards -->
          <div class="md:hidden divide-y divide-white/10">
            <div 
              v-for="(student, index) in students.data" 
              :key="student.id"
              class="p-6 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center text-white font-medium">
                    {{ student.first_name.charAt(0) }}{{ student.last_name.charAt(0) }}
                  </div>
                  <div>
                    <h4 class="text-white font-medium">{{ student.first_name }} {{ student.last_name }}</h4>
                    <p class="text-sm text-white/70">Parent: {{ student.parent_name }}</p>
                    <p class="text-xs text-white/50">#{{ index + 1 + (students.current_page - 1) * students.per_page }}</p>
                  </div>
                </div>
              </div>
              <div class="space-y-2">
                <Link
                  :href="route('teacher.student.grades', { student: student.id })"
                  class="w-full inline-flex justify-center items-center px-3 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-100 border border-green-400/30 hover:border-green-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                  View Grades
                </Link>
                <button
                  @click="openGradeModal(student.id)"
                  class="w-full inline-flex justify-center items-center px-3 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-100 border border-blue-400/30 hover:border-blue-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  Input Grades
                </button>
                <div class="grid grid-cols-2 gap-2">
                  <button
                    @click="removeStudent(student.id)"
                    class="inline-flex justify-center items-center px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-100 border border-red-400/30 hover:border-red-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm text-sm"
                  >
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Unapprove
                  </button>
                  <button
                    @click="clearGrades(student.id)"
                    class="inline-flex justify-center items-center px-3 py-2 bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-100 border border-yellow-400/30 hover:border-yellow-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm text-sm"
                  >
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Clear Grades
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-white/10 flex justify-between items-center">
            <div class="text-sm text-white/70">
              Showing {{ students.from }} to {{ students.to }} of {{ students.total }} students
            </div>
            <div class="flex space-x-1">
              <button
                v-for="page in students.last_page"
                :key="page"
                @click="router.get(route('teacher.students'), { page, search }, { preserveState: true })"
                :class="{
                  'px-3 py-1 rounded-lg bg-blue-500/30 text-white border border-blue-400/50': page === students.current_page,
                  'px-3 py-1 rounded-lg bg-white/10 text-white/70 border border-white/20 hover:bg-white/20': page !== students.current_page,
                }"
                class="text-sm transition-all duration-150"
              >
                {{ page }}
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-white/80">
            {{ search ? 'No students found' : 'No approved students found' }}
          </h3>
          <p class="mt-1 text-sm text-white/60">
            {{ search ? 'Try adjusting your search terms.' : 'Students need to be approved before they appear here.' }}
          </p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- 📌 Grade Input Modal -->
  <GradeInputModal
    v-if="InputGrade"
    :show="showGradeModal"
    :student="InputGrade.student"
    :subjects="InputGrade.subjects"
    :grades="InputGrade.grades"
    :currentQuarter="InputGrade.currentQuarter"
    @close="showGradeModal = false"
    @saved="showGradeModal = false"
  />
</template>