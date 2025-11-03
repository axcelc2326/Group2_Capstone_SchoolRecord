<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import Swal from 'sweetalert2'
import axios from 'axios'
import GradeInputModal from '@/Components/Teacher/GradeInputModal.vue'
import SettingsModal from '@/Components/Teacher/StudentSettingsModal.vue'
import IndividualStudentModal from '@/Components/Teacher/StudentSettingsIndividualModal.vue'
import PromoteStudentsModal from '@/Components/Teacher/PromoteStudentsModal.vue'

const props = defineProps({
  students: Object,
  filters: Object,
  message: String,
})

const search = ref(props.filters?.search || '')
const showSettingsModal = ref(false)
const showIndividualModal = ref(false)
const selectedStudent = ref(null)
const showPromoteModal = ref(false)

// 🔍 Search filter
watch(search, (value) => {
  router.get(route('teacher.students'), { search: value }, { preserveState: true, replace: true })
})

// Statistics with safe fallbacks
const totalStudents = computed(() => props.students?.total || 0)
const hasStudents = computed(() => props.students?.data && props.students.data.length > 0)

// 🚀 Modal State
const showGradeModal = ref(false)
const InputGrade = ref(null)

// Update the promoteStudents function in SettingsModal
const promoteStudents = () => {
  showSettingsModal.value = false
  showPromoteModal.value = true
}

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

// Individual student modal functions
const openIndividualModal = (student) => {
  selectedStudent.value = student
  showIndividualModal.value = true
}

const closeIndividualModal = () => {
  showIndividualModal.value = false
  selectedStudent.value = null
}

const openSettingsModal = () => {
  showSettingsModal.value = true
}

const closeSettingsModal = () => {
  showSettingsModal.value = false
}

// Handle the actual promotion
const handlePromoteStudents = async (targetClassId) => {
  showPromoteModal.value = false
  
  try {
    await router.put(route('teacher.students.promote'), {
      target_class_id: targetClassId
    })
    
    // Show success message
    Swal.fire({
      title: 'Success!',
      text: 'Students have been promoted successfully.',
      icon: 'success',
      confirmButtonColor: '#10b981',
      background: '#1f2937',
      color: '#f9fafb',
      customClass: {
        popup: 'rounded-xl border border-gray-700 shadow-lg',
      }
    })
  } catch (error) {
    console.error('Promotion failed:', error)
    Swal.fire({
      title: 'Error!',
      text: 'Failed to promote students.',
      icon: 'error',
      confirmButtonColor: '#ef4444',
      background: '#1f2937',
      color: '#f9fafb',
      customClass: {
        popup: 'rounded-xl border border-gray-700 shadow-lg',
      }
    })
  }
}

// 🔻 Individual student actions (called from modal)
function removeStudent() {
  if (!selectedStudent.value) return
  
  const studentId = selectedStudent.value.id
  closeIndividualModal()
  
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will unapprove the student.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, unapprove',
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
      router.put(route('teacher.students.unapprove', { student: studentId }))
    }
  })
}

function clearGrades() {
  if (!selectedStudent.value) return
  
  const studentId = selectedStudent.value.id
  closeIndividualModal()
  
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

// Bulk actions
function unapproveAll() {
  if (!hasStudents.value) return
  
  showSettingsModal.value = false
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
  if (!hasStudents.value) return
  
  showSettingsModal.value = false
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

          <!-- Settings Button (only show if there are students) -->
          <div v-if="hasStudents" class="relative">
            <button
              @click="openSettingsModal"
              class="group relative inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 border border-white/20"
            >
              <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Settings
            </button>
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
          <h3 class="text-lg font-semibold text-white">My Students</h3>
          <p class="text-sm text-white/70 mt-1">{{ students?.data?.length || 0 }} students shown</p>
        </div>

        <div v-if="hasStudents">
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
                    <div class="text-sm text-white">{{ index + 1 + ((students.current_page || 1) - 1) * (students.per_page || 10) }}</div>
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
                    
                    <!-- Individual Settings Button -->
                    <button
                      @click="openIndividualModal(student)"
                      class="inline-flex items-center px-3 py-1.5 bg-purple-500/20 hover:bg-purple-500/30 text-purple-100 border border-purple-400/30 hover:border-purple-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      Actions
                    </button>
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
                    <p class="text-xs text-white/50">#{{ index + 1 + ((students.current_page || 1) - 1) * (students.per_page || 10) }}</p>
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
                <button
                  @click="openIndividualModal(student)"
                  class="w-full inline-flex justify-center items-center px-3 py-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-100 border border-purple-400/30 hover:border-purple-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  Student Actions
                </button>
              </div>
            </div>
          </div>

          <!-- Pagination (only show if there are students) -->
          <div v-if="students.last_page > 1" class="px-6 py-4 border-t border-white/10 flex justify-between items-center">
            <div class="text-sm text-white/70">
              Showing {{ students.from || 0 }} to {{ students.to || 0 }} of {{ students.total || 0 }} students
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
        <div v-else class="text-center py-16 px-6">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/5 mb-4">
            <svg class="h-10 w-10 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-white/90 mb-2">
            {{ search ? 'No students found' : 'Once the admin assigns you to a class, they’ll appear here' }}
          </h3>
          <p class="text-sm text-white/60 max-w-sm mx-auto">
            {{ search ? 'Try adjusting your search terms or clear the search to see all students.' : 'No students assigned yet. Please wait for the admin to assign you a class.' }}
          </p>
          <button
            v-if="search"
            @click="search = ''"
            class="mt-6 inline-flex items-center px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-100 border border-blue-400/30 hover:border-blue-400/50 rounded-lg transition-all duration-150"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Clear Search
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
  
  <!-- Settings Modal Component -->
  <SettingsModal
    :show="showSettingsModal"
    @close="closeSettingsModal"
    @unapprove-all="unapproveAll"
    @clear-all-grades="clearAllGrades"
    @promote-students="promoteStudents"
  />
  
  <!-- Promote Students Modal -->
  <PromoteStudentsModal
    :show="showPromoteModal"
    @close="showPromoteModal = false"
    @promote="handlePromoteStudents"
  />

  <!-- Individual Student Modal Component -->
  <IndividualStudentModal
    :show="showIndividualModal"
    :student="selectedStudent"
    @close="closeIndividualModal"
    @unapprove-student="removeStudent"
    @clear-student-grades="clearGrades"
  />

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