<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import Swal from 'sweetalert2'
import axios from 'axios'
import { Users, Search, Settings, MoreVertical, BarChart3, Edit, TrendingUp } from 'lucide-vue-next'
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
      title: 'Students Promoted!',
      text: 'Students have been promoted successfully.',
      icon: 'success',
      background: '#1f2937',
      color: '#f9fafb',
      backdrop: 'rgba(0, 0, 0, 0.7)',
      showClass: {
        popup: 'animate__animated animate__fadeInDown animate__faster'
      },
      customClass: {
        popup: 'rounded-2xl shadow-2xl border border-green-500/50 backdrop-blur-lg',
        title: 'text-2xl font-bold text-white mb-2',
        htmlContainer: 'text-gray-300',
        confirmButton: 'py-3 px-6 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 focus:ring-4 focus:ring-green-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
        icon: '!border-none !bg-transparent'
      },
      buttonsStyling: false,
      confirmButtonText: 'OK',
      timer: 3000,
      timerProgressBar: true,
    })
  } catch (error) {
    console.error('Promotion failed:', error)
    Swal.fire({
      title: 'Promotion Failed!',
      text: 'Failed to promote students. Please try again.',
      icon: 'error',
      background: '#1f2937',
      color: '#f9fafb',
      backdrop: 'rgba(0, 0, 0, 0.7)',
      showClass: {
        popup: 'animate__animated animate__shakeX animate__faster'
      },
      customClass: {
        popup: 'rounded-2xl shadow-2xl border border-red-500/50 backdrop-blur-lg',
        title: 'text-2xl font-bold text-white mb-2',
        htmlContainer: 'text-gray-300',
        confirmButton: 'py-3 px-6 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:ring-4 focus:ring-red-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
        icon: '!border-none !bg-transparent text-red-500'
      },
      buttonsStyling: false,
      confirmButtonText: 'Try Again'
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
    background: '#1f2937',
    color: '#f9fafb',
    backdrop: 'rgba(0, 0, 0, 0.7)',
    showClass: {
      popup: 'animate__animated animate__fadeInDown animate__faster'
    },
    hideClass: {
      popup: 'animate__animated animate__fadeOutUp animate__faster'
    },
    customClass: {
      popup: 'rounded-2xl shadow-2xl border border-yellow-500/50 backdrop-blur-lg',
      title: 'text-2xl font-bold text-white mb-2',
      htmlContainer: 'text-gray-300',
      confirmButton: 'py-3 px-6 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:ring-4 focus:ring-red-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
      cancelButton: 'py-3 px-6 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 hover:shadow-md transform transition-all duration-200 mr-3',
      icon: '!border-none !bg-transparent text-yellow-500'
    },
    buttonsStyling: false,
    showCancelButton: true,
    confirmButtonText: 'Yes, unapprove',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(route('teacher.students.unapprove', { student: studentId }), {
        onSuccess: () => {
          Swal.fire({
            title: 'Student Unapproved!',
            text: 'The student has been successfully unapproved.',
            icon: 'success',
            background: '#1f2937',
            color: '#f9fafb',
            showClass: {
              popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            customClass: {
              popup: 'rounded-2xl shadow-2xl border border-green-500/50 backdrop-blur-lg',
              title: 'text-2xl font-bold text-white mb-2',
              htmlContainer: 'text-gray-300',
              confirmButton: 'py-3 px-6 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 focus:ring-4 focus:ring-green-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
              icon: '!border-none !bg-transparent'
            },
            buttonsStyling: false,
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true,
          })
        }
      })
    }
  })
}

function clearGrades() {
  if (!selectedStudent.value) return
  
  const studentId = selectedStudent.value.id
  closeIndividualModal()
  
  Swal.fire({
    title: 'Clear Student Grades?',
    text: 'This will delete all grades for this student.',
    icon: 'warning',
    background: '#1f2937',
    color: '#f9fafb',
    backdrop: 'rgba(0, 0, 0, 0.7)',
    showClass: {
      popup: 'animate__animated animate__fadeInDown animate__faster'
    },
    hideClass: {
      popup: 'animate__animated animate__fadeOutUp animate__faster'
    },
    customClass: {
      popup: 'rounded-2xl shadow-2xl border border-yellow-500/50 backdrop-blur-lg',
      title: 'text-2xl font-bold text-white mb-2',
      htmlContainer: 'text-gray-300',
      confirmButton: 'py-3 px-6 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:ring-4 focus:ring-red-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
      cancelButton: 'py-3 px-6 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 hover:shadow-md transform transition-all duration-200 mr-3',
      icon: '!border-none !bg-transparent text-yellow-500'
    },
    buttonsStyling: false,
    showCancelButton: true,
    confirmButtonText: 'Yes, clear grades',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('teacher.students.clearGrades', { student: studentId }), {
        onSuccess: () => {
          Swal.fire({
            title: 'Grades Cleared!',
            text: 'All grades for this student have been cleared.',
            icon: 'success',
            background: '#1f2937',
            color: '#f9fafb',
            showClass: {
              popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            customClass: {
              popup: 'rounded-2xl shadow-2xl border border-green-500/50 backdrop-blur-lg',
              title: 'text-2xl font-bold text-white mb-2',
              htmlContainer: 'text-gray-300',
              confirmButton: 'py-3 px-6 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 focus:ring-4 focus:ring-green-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
              icon: '!border-none !bg-transparent'
            },
            buttonsStyling: false,
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true,
          })
        }
      })
    }
  })
}

// Bulk actions
function unapproveAll() {
  if (!hasStudents.value) return
  
  showSettingsModal.value = false
  Swal.fire({
    title: 'Unapprove ALL Students?',
    text: 'This will unapprove all students in your class.',
    icon: 'warning',
    background: '#1f2937',
    color: '#f9fafb',
    backdrop: 'rgba(0, 0, 0, 0.7)',
    showClass: {
      popup: 'animate__animated animate__fadeInDown animate__faster'
    },
    hideClass: {
      popup: 'animate__animated animate__fadeOutUp animate__faster'
    },
    customClass: {
      popup: 'rounded-2xl shadow-2xl border border-yellow-500/50 backdrop-blur-lg',
      title: 'text-2xl font-bold text-white mb-2',
      htmlContainer: 'text-gray-300',
      confirmButton: 'py-3 px-6 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:ring-4 focus:ring-red-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
      cancelButton: 'py-3 px-6 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 hover:shadow-md transform transition-all duration-200 mr-3',
      icon: '!border-none !bg-transparent text-yellow-500'
    },
    buttonsStyling: false,
    showCancelButton: true,
    confirmButtonText: 'Yes, unapprove all',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      const studentIds = props.students.data.map((s) => s.id)
      router.put(route('teacher.students.unapproveAll'), {
        student_ids: studentIds,
        onSuccess: () => {
          Swal.fire({
            title: 'All Students Unapproved!',
            text: 'All students have been successfully unapproved.',
            icon: 'success',
            background: '#1f2937',
            color: '#f9fafb',
            showClass: {
              popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            customClass: {
              popup: 'rounded-2xl shadow-2xl border border-green-500/50 backdrop-blur-lg',
              title: 'text-2xl font-bold text-white mb-2',
              htmlContainer: 'text-gray-300',
              confirmButton: 'py-3 px-6 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 focus:ring-4 focus:ring-green-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
              icon: '!border-none !bg-transparent'
            },
            buttonsStyling: false,
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true,
          })
        }
      })
    }
  })
}

function clearAllGrades() {
  if (!hasStudents.value) return
  
  showSettingsModal.value = false
  Swal.fire({
    title: 'Clear ALL Grades?',
    text: 'This will remove all grades for all students shown.',
    icon: 'warning',
    background: '#1f2937',
    color: '#f9fafb',
    backdrop: 'rgba(0, 0, 0, 0.7)',
    showClass: {
      popup: 'animate__animated animate__fadeInDown animate__faster'
    },
    hideClass: {
      popup: 'animate__animated animate__fadeOutUp animate__faster'
    },
    customClass: {
      popup: 'rounded-2xl shadow-2xl border border-yellow-500/50 backdrop-blur-lg',
      title: 'text-2xl font-bold text-white mb-2',
      htmlContainer: 'text-gray-300',
      confirmButton: 'py-3 px-6 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:ring-4 focus:ring-red-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
      cancelButton: 'py-3 px-6 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 hover:shadow-md transform transition-all duration-200 mr-3',
      icon: '!border-none !bg-transparent text-yellow-500'
    },
    buttonsStyling: false,
    showCancelButton: true,
    confirmButtonText: 'Yes, clear all grades',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      const studentIds = props.students.data.map((s) => s.id)
      router.delete(route('teacher.students.clearAllGrades'), {
        data: { student_ids: studentIds },
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            title: 'All Grades Cleared!',
            text: 'All grades for all students have been cleared.',
            icon: 'success',
            background: '#1f2937',
            color: '#f9fafb',
            showClass: {
              popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            customClass: {
              popup: 'rounded-2xl shadow-2xl border border-green-500/50 backdrop-blur-lg',
              title: 'text-2xl font-bold text-white mb-2',
              htmlContainer: 'text-gray-300',
              confirmButton: 'py-3 px-6 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 focus:ring-4 focus:ring-green-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
              icon: '!border-none !bg-transparent'
            },
            buttonsStyling: false,
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true,
          })
        }
      })
    }
  })
}
</script>

<template>
  <Head title="My Students" />
  
  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-1">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-bold text-white">
              My Students
            </h2>
            <p class="text-white/70 mt-1">
              Manage student grades and records
            </p>
          </div>
          <Users class="w-8 h-8 text-white/60" />
        </div>
      </div>
    </template>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Message Alert -->
      <div v-if="message" class="backdrop-blur-md bg-red-500/20 border border-red-300/30 rounded-xl p-4">
        <div class="text-red-100 font-medium">{{ message }}</div>
      </div>

      <!-- Search and Actions Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl p-6">
        <div class="flex items-center space-x-3 mb-6">
          <Search class="w-5 h-5 text-blue-300" />
          <div>
            <h3 class="text-lg font-semibold text-white">Search & Actions</h3>
            <p class="text-sm text-white/70 mt-1">Find students and manage your class</p>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <!-- Search Input -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-white">
              Search Students
            </label>
            <div class="relative">
              <input
                v-model="search"
                type="text"
                placeholder="Search by name..."
                class="w-full backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
              />
              <Search class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-white/50" />
            </div>
          </div>
          
          <!-- Settings Button -->
          <div v-if="hasStudents" class="space-y-2">
            <label class="block text-sm font-semibold text-white">
              Class Management
            </label>
            <button
              @click="openSettingsModal"
              class="w-full backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30 flex items-center justify-center gap-2"
            >
              <Settings class="w-4 h-4" />
              Settings
            </button>
          </div>
        </div>
      </div>

      <!-- Students Table Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <Users class="w-5 h-5 text-purple-300" />
            <div>
              <h3 class="text-lg font-semibold text-white">Students Directory</h3>
              <p class="text-sm text-white/70 mt-1">{{ students?.data?.length || 0 }} students available</p>
            </div>
          </div>
        </div>

        <div v-if="hasStudents">
          <!-- Desktop Table -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">#</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student</th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                <tr
                  v-for="(student, index) in students.data"
                  :key="student.id"
                  class="hover:bg-white/5 transition-colors duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="text-sm text-white">{{ index + 1 + ((students.current_page || 1) - 1) * (students.per_page || 10) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-medium text-sm">
                          {{ student.first_name.charAt(0) }}{{ student.last_name.charAt(0) }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-white">{{ student.first_name }} {{ student.middle_name }} {{ student.last_name }}</div>
                        <div class="text-xs text-white/60">Parent: {{ student.parent_name }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="flex items-center justify-end space-x-2">
                      <Link
                        :href="route('teacher.student.grades', { student: student.id })"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100 hover:bg-green-500/30 transition-all duration-150"
                        title="View Grades"
                      >
                        <BarChart3 class="w-4 h-4" />
                        <span class="text-sm font-medium">View Grades</span>
                      </Link>
                      <button
                        @click="openGradeModal(student.id)"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg backdrop-blur-sm bg-blue-500/20 border border-blue-400/30 text-blue-100 hover:bg-blue-500/30 transition-all duration-150"
                        title="Input Grades"
                      >
                        <Edit class="w-4 h-4" />
                        <span class="text-sm font-medium">Input Grades</span>
                      </button>
                      <button
                        @click="openIndividualModal(student)"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg backdrop-blur-sm bg-indigo-500/20 border border-indigo-400/30 text-indigo-100 hover:bg-indigo-500/30 transition-all duration-150"
                        title="More Actions"
                      >
                        <MoreVertical class="w-4 h-4" />
                        <span class="text-sm font-medium">Actions</span>
                      </button>
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
              class="p-4 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-medium text-sm">
                    {{ student.first_name.charAt(0) }}{{ student.last_name.charAt(0) }}
                  </div>
                  <div>
                    <h4 class="text-white font-medium text-sm">{{ student.first_name }} {{ student.last_name }}</h4>
                    <p class="text-xs text-white/70">Parent: {{ student.parent_name }}</p>
                    <p class="text-xs text-white/50">#{{ index + 1 + ((students.current_page || 1) - 1) * (students.per_page || 10) }}</p>
                  </div>
                </div>
              </div>
              
              <div class="flex space-x-2 pt-3 border-t border-white/10">
                <Link
                  :href="route('teacher.student.grades', { student: student.id })"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100 hover:bg-green-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <BarChart3 class="w-4 h-4" />
                  View
                </Link>
                <button
                  @click="openGradeModal(student.id)"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-blue-500/20 border border-blue-400/30 text-blue-100 hover:bg-blue-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <Edit class="w-4 h-4" />
                  Grade
                </button>
                <button
                  @click="openIndividualModal(student)"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-indigo-500/20 border border-indigo-400/30 text-indigo-100 hover:bg-indigo-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <MoreVertical class="w-4 h-4" />
                  More
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8">
          <Users class="mx-auto h-10 w-10 text-white/40 mb-3" />
          <h3 class="text-sm font-medium text-white/80">
            {{ search ? 'No students found' : 'No students assigned yet' }}
          </h3>
          <p class="mt-1 text-sm text-white/60">
            {{ search ? 'Try adjusting your search terms or clear the search.' : 'Once the admin assigns you to a class, students will appear here.' }}
          </p>
          <button
            v-if="search"
            @click="search = ''"
            class="mt-4 inline-flex items-center px-4 py-2 backdrop-blur-sm bg-blue-500/20 border border-blue-400/30 text-blue-100 hover:bg-blue-500/30 rounded-lg transition-all duration-150"
          >
            Clear Search
          </button>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="students?.links?.length > 3" class="flex justify-center">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-xl p-2">
          <div class="flex space-x-2">
            <Link
              v-for="link in students.links"
              :key="link.url"
              :href="link.url || ''"
              v-html="link.label"
              class="px-4 py-2 rounded-lg transition-all duration-150 text-sm"
              :class="{
                'backdrop-blur-sm bg-blue-500/30 text-blue-100 border border-blue-400/50': link.active,
                'text-white/70 hover:bg-white/10 hover:text-white border border-transparent': !link.active && link.url,
                'text-white/40 cursor-not-allowed': !link.url
              }"
            />
          </div>
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

  <!-- Grade Input Modal -->
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
button:focus-visible,
select:focus-visible,
input:focus-visible {
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