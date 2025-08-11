<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { ref, computed } from 'vue'
import EditStudentModal from '@/Components/EditStudentModal.vue'
import RegisterStudentModal from '@/Components/RegisterStudentModal.vue'

const { props } = usePage()
const students = ref(props.students)
const classes = ref(props.classes)
const searchQuery = ref('')

const showEditModal = ref(false)
const selectedStudent = ref(null)
const showRegisterModal = ref(false)

// Filter students based on search query
const filteredStudents = computed(() => {
  if (!searchQuery.value) return students.value
  
  return students.value.filter(student => 
    `${student.first_name} ${student.last_name}`
      .toLowerCase()
      .includes(searchQuery.value.toLowerCase()) ||
    student.class?.name?.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

// Statistics
const approvedCount = computed(() => 
  students.value.filter(s => s.approved_by_teacher).length
)
const pendingCount = computed(() => 
  students.value.filter(s => !s.approved_by_teacher).length
)

const editStudent = (student) => {
  selectedStudent.value = student
  showEditModal.value = true
}

const deleteStudent = (id) => {
  Swal.fire({
    title: 'Delete Student?',
    text: 'This action cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!',
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
      router.delete(route('students.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Student deleted successfully',
            background: '#1f2937', // Tailwind slate-900
            color: '#f9fafb',       // Tailwind gray-50
            iconColor: '#22c55e',   // Tailwind green-500
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
              popup: 'rounded-lg border border-gray-700',
              timerProgressBar: 'bg-green-500' // Tailwind green-500
            }
          }).then(() => {
            location.reload() // Refreshes the page
          });
        },
      })
    }
  })
}
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
          <!-- Quick Stats -->
          <div class="hidden md:flex items-center space-x-3 text-sm">
            <div class="backdrop-blur-sm bg-emerald-500/20 border border-emerald-300/30 px-3 py-1 rounded-full">
              <span class="text-emerald-100">{{ approvedCount }} Approved</span>
            </div>
            <div class="backdrop-blur-sm bg-amber-500/20 border border-amber-300/30 px-3 py-1 rounded-full">
              <span class="text-amber-100">{{ pendingCount }} Pending</span>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
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
                v-model="searchQuery"
                type="text"
                placeholder="Search students or classes..."
                class="block w-full pl-10 pr-3 py-2.5 border-0 rounded-xl backdrop-blur-sm bg-white/10 border-white/20 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
              />
            </div>
          </div>

          <!-- Register Button -->
          <button
            @click="showRegisterModal = true"
            class="group relative inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-blue-600 hover:from-emerald-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 border border-white/20"
          >
            <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Register Student
          </button>
        </div>
      </div>

      <!-- Mobile Stats (visible on small screens) -->
      <div class="md:hidden grid grid-cols-2 gap-4">
        <div class="backdrop-blur-md bg-emerald-500/10 border border-emerald-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-emerald-100">{{ approvedCount }}</div>
          <div class="text-sm text-emerald-200">Approved</div>
        </div>
        <div class="backdrop-blur-md bg-amber-500/10 border border-amber-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-amber-100">{{ pendingCount }}</div>
          <div class="text-sm text-amber-200">Pending</div>
        </div>
      </div>

      <!-- Students Table -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <h3 class="text-lg font-semibold text-white">Registered Students</h3>
          <p class="text-sm text-white/70 mt-1">{{ filteredStudents.length }} of {{ students.length }} students</p>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
          <table class="min-w-full divide-y divide-white/10">
            <thead class="bg-white/5">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Student</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Class</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              <tr 
                v-for="student in filteredStudents" 
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
                      <div class="text-sm font-medium text-white">{{ student.first_name }} {{ student.last_name }}</div>
                    </div>
                  </div>
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
                    {{ student.approved_by_teacher ? 'Approved' : 'Pending' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                  <button
                    class="inline-flex items-center px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-100 border border-blue-400/30 hover:border-blue-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                    @click="editStudent(student)"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                  </button>
                  <button
                    class="inline-flex items-center px-3 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-100 border border-red-400/30 hover:border-red-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                    @click="deleteStudent(student.id)"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-white/10">
          <div 
            v-for="student in filteredStudents" 
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
            <div class="mt-4 flex space-x-2">
              <button
                class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-100 border border-blue-400/30 hover:border-blue-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                @click="editStudent(student)"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
              </button>
              <button
                class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-100 border border-red-400/30 hover:border-red-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                @click="deleteStudent(student.id)"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredStudents.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-white/80">
            {{ searchQuery ? 'No students found' : 'No students registered yet' }}
          </h3>
          <p class="mt-1 text-sm text-white/60">
            {{ searchQuery ? 'Try adjusting your search terms.' : 'Get started by registering your first student.' }}
          </p>
          <div class="mt-6" v-if="!searchQuery">
            <button
              @click="showRegisterModal = true"
              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-blue-600 hover:from-emerald-600 hover:to-blue-700 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 border border-white/20"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Register First Student
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- Modals -->
  <EditStudentModal
    :show="showEditModal"
    :student="selectedStudent"
    :classes="classes"
    @close="showEditModal = false"
    @updated="router.reload()"
  />
  <RegisterStudentModal
    :show="showRegisterModal"
    :classes="classes"
    @close="showRegisterModal = false"
    @created="router.reload()"
  />
</template>