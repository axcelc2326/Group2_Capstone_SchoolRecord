<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'
import AddTeacherModal from '@/Components/Admin/AddTeacherModal.vue'
import TeacherActionsModal from '@/Components/Admin/TeacherActionsModal.vue'

const props = defineProps({
  teachers: Object,
  filters: Object,
})

const showAddModal = ref(false)
const showActionsModal = ref(false)
const selectedTeacher = ref(null)

// Search state
const search = ref(props.filters.search || '')

// Statistics
const totalTeachers = computed(() => props.teachers?.data?.length || 0)
const totalCreatedTeachers = computed(() => props.teachers?.total || 0)

const searchTeachers = () => {
  router.get(
    route('teachers.index'),
    { search: search.value },
    { preserveState: true, preserveScroll: true }
  )
}

const toggleStatus = (id) => {
  router.put(route('teachers.toggle-status', id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        title: 'Status Updated!',
        text: 'The teacher account status has been successfully changed.',
        icon: 'success',
        background: '#1f2937',
        color: '#f9fafb',
        backdrop: 'rgba(0, 0, 0, 0.7)',
        showClass: {
          popup: 'animate__animated animate__fadeInDown animate__faster',
        },
        customClass: {
          popup: 'rounded-2xl shadow-2xl border border-green-500/50 backdrop-blur-lg',
          title: 'text-2xl font-bold text-white mb-2',
          htmlContainer: 'text-gray-300',
          confirmButton:
            'py-3 px-6 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 focus:ring-4 focus:ring-green-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
          icon: '!border-none !bg-transparent text-green-500',
        },
        buttonsStyling: false,
        confirmButtonText: 'OK',
        timer: 2000,
        timerProgressBar: true,
      })
    },
    onError: () => {
      Swal.fire({
        title: 'Error!',
        text: 'Failed to update status. Please try again.',
        icon: 'error',
        background: '#1f2937',
        color: '#f9fafb',
        backdrop: 'rgba(0, 0, 0, 0.7)',
        showClass: {
          popup: 'animate__animated animate__shakeX animate__faster',
        },
        customClass: {
          popup: 'rounded-2xl shadow-2xl border border-red-500/50 backdrop-blur-lg',
          title: 'text-2xl font-bold text-white mb-2',
          htmlContainer: 'text-gray-300',
          confirmButton:
            'py-3 px-6 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:ring-4 focus:ring-red-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
          icon: '!border-none !bg-transparent text-red-500',
        },
        buttonsStyling: false,
        confirmButtonText: 'Try Again',
      })
    },
  })
}

// ✅ Automatically delete teacher (no confirmation prompt) - same as parents component
const deleteTeacher = (teacherId) => {
  router.delete(route('teachers.destroy', teacherId), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        title: 'Deleted!',
        text: 'The teacher account has been deleted successfully.',
        icon: 'success',
        background: '#1f2937',
        color: '#f9fafb',
        backdrop: 'rgba(0, 0, 0, 0.7)',
        showClass: {
          popup: 'animate__animated animate__fadeInDown animate__faster',
        },
        customClass: {
          popup: 'rounded-2xl shadow-2xl border border-green-500/50 backdrop-blur-lg',
          title: 'text-2xl font-bold text-white mb-2',
          htmlContainer: 'text-gray-300',
          confirmButton:
            'py-3 px-6 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 focus:ring-4 focus:ring-green-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
          icon: '!border-none !bg-transparent text-green-500',
        },
        buttonsStyling: false,
        confirmButtonText: 'OK',
        timer: 2500,
        timerProgressBar: true,
      })
    },
    onError: () => {
      Swal.fire({
        title: 'Error!',
        text: 'Failed to delete teacher. Please try again.',
        icon: 'error',
        background: '#1f2937',
        color: '#f9fafb',
        backdrop: 'rgba(0, 0, 0, 0.7)',
        showClass: {
          popup: 'animate__animated animate__shakeX animate__faster',
        },
        customClass: {
          popup: 'rounded-2xl shadow-2xl border border-red-500/50 backdrop-blur-lg',
          title: 'text-2xl font-bold text-white mb-2',
          htmlContainer: 'text-gray-300',
          confirmButton:
            'py-3 px-6 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:ring-4 focus:ring-red-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
          icon: '!border-none !bg-transparent text-red-500',
        },
        buttonsStyling: false,
        confirmButtonText: 'Try Again',
      })
    },
  })
}

const openActionsModal = (teacher) => {
  selectedTeacher.value = teacher
  showActionsModal.value = true
}

const handleModalAction = (action) => {
  if (selectedTeacher.value) {
    switch (action) {
      case 'toggle-status':
        toggleStatus(selectedTeacher.value.id)
        break
      case 'delete':
        deleteTeacher(selectedTeacher.value.id) // Now uses the automatic delete function
        break
    }
  }
  showActionsModal.value = false
}
</script>

<template>
  <Head title="Teachers Management" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0">
        <h2 class="text-2xl font-bold text-white bg-gradient-to-r from-white to-white/70 bg-clip-text text-transparent">
          Teachers Management
        </h2>
        <div class="flex items-center space-x-4">
          <!-- Quick Stats -->
          <div class="hidden md:flex items-center space-x-3 text-sm">
            <div class="backdrop-blur-sm bg-blue-500/20 border border-blue-300/30 px-3 py-1 rounded-full">
              <span class="text-blue-100">{{ totalCreatedTeachers }} Total Created</span>
            </div>
            <div class="backdrop-blur-sm bg-emerald-500/20 border border-emerald-300/30 px-3 py-1 rounded-full">
              <span class="text-emerald-100">{{ totalTeachers }} Active</span>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Mobile Stats (visible on small screens) -->
      <div class="md:hidden grid grid-cols-2 gap-4">
        <div class="backdrop-blur-md bg-blue-500/10 border border-blue-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-blue-100">{{ totalCreatedTeachers }}</div>
          <div class="text-sm text-blue-200">Total Created</div>
        </div>
        <div class="backdrop-blur-md bg-emerald-500/10 border border-emerald-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-emerald-100">{{ totalTeachers }}</div>
          <div class="text-sm text-emerald-200">Active Teachers</div>
        </div>
      </div>

      <!-- Search and Controls -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
          <!-- Search Controls -->
          <div class="flex flex-col md:flex-row md:items-center gap-4 flex-1">
            <div class="relative">
              <input
                v-model="search"
                @keyup.enter="searchTeachers"
                type="text"
                placeholder="Search teachers..."
                class="w-full md:w-64 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40 transition-all duration-150"
              />
              <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            
            <button 
              @click="searchTeachers"
              class="px-6 py-3 bg-blue-500/20 hover:bg-blue-500/30 text-blue-100 border border-blue-400/30 hover:border-blue-400/50 rounded-xl transition-all duration-150 backdrop-blur-sm"
            >
              <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
              </svg>
              Search
            </button>
          </div>

          <!-- Add Teacher Button -->
          <button
            @click="showAddModal = true"
            class="px-6 py-3 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-100 border border-emerald-400/30 hover:border-emerald-400/50 rounded-xl transition-all duration-150 backdrop-blur-sm"
          >
            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Register Teacher
          </button>
        </div>
      </div>

      <!-- Teachers Table -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <h3 class="text-lg font-semibold text-white">Teachers Directory</h3>
          <p class="text-sm text-white/70 mt-1">Manage and oversee teacher accounts</p>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
          <table class="min-w-full divide-y divide-white/10">
            <thead class="bg-white/5">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Teacher</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Class</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              <tr
                v-for="teacher in props.teachers.data"
                :key="teacher.id"
                class="hover:bg-white/5 transition-colors duration-150"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium text-sm">
                        {{ teacher.name.charAt(0) }}
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-white">{{ teacher.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-white">{{ teacher.email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <template v-if="teacher.class">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-500/20 text-indigo-100 border border-indigo-400/30 backdrop-blur-sm">
                      {{ teacher.class.name }} - Grade {{ teacher.class.grade_level }}
                    </span>
                  </template>
                  <template v-else>
                    <span class="text-white/50 italic text-sm">No class assigned</span>
                  </template>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border" :class="{
                    'bg-green-500/20 text-green-100 border-green-400/30': teacher.status === 'active',
                    'bg-red-500/20 text-red-100 border-red-400/30': teacher.status !== 'active'
                  }">
                    <div class="w-1.5 h-1.5 rounded-full mr-1.5" :class="{
                      'bg-green-400': teacher.status === 'active',
                      'bg-red-400': teacher.status !== 'active'
                    }"></div>
                    {{ teacher.status === 'active' ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                  <button
                    @click="openActionsModal(teacher)"
                    class="inline-flex items-center px-3 py-2 bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-100 border border-indigo-400/30 hover:border-indigo-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
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
            v-for="teacher in props.teachers.data" 
            :key="teacher.id"
            class="p-6 hover:bg-white/5 transition-colors duration-150"
          >
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center text-white font-medium">
                  {{ teacher.name.charAt(0) }}
                </div>
                <div>
                  <h4 class="text-white font-medium">{{ teacher.name }}</h4>
                  <p class="text-sm text-white/70">{{ teacher.email }}</p>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border mt-1" :class="{
                    'bg-green-500/20 text-green-100 border-green-400/30': teacher.status === 'active',
                    'bg-red-500/20 text-red-100 border-red-400/30': teacher.status !== 'active'
                  }">
                    <div class="w-1.5 h-1.5 rounded-full mr-1" :class="{
                      'bg-green-400': teacher.status === 'active',
                      'bg-red-400': teacher.status !== 'active'
                    }"></div>
                    {{ teacher.status === 'active' ? 'Active' : 'Inactive' }}
                  </span>
                </div>
              </div>
            </div>
            
            <!-- Class Assignment -->
            <div class="mb-4">
              <p class="text-sm text-white/60 mb-2">Class Assignment:</p>
              <template v-if="teacher.class">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-500/20 text-indigo-100 border border-indigo-400/30 backdrop-blur-sm">
                  {{ teacher.class.name }} - Grade {{ teacher.class.grade_level }}
                </span>
              </template>
              <template v-else>
                <span class="text-white/50 italic text-sm">No class assigned</span>
              </template>
            </div>
            
            <div class="flex flex-col space-y-2">
              <button
                @click="openActionsModal(teacher)"
                class="flex justify-center items-center px-3 py-2 bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-100 border border-indigo-400/30 hover:border-indigo-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                </svg>
                Actions
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!props.teachers?.data?.length" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-white/80">
            No teachers found
          </h3>
          <p class="mt-1 text-sm text-white/60">
            Start by registering your first teacher account.
          </p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="props.teachers?.links?.length > 3" class="flex justify-center">
        <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl p-2 shadow-2xl">
          <div class="flex space-x-2">
            <Link
              v-for="link in props.teachers.links"
              :key="link.url"
              :href="link.url || ''"
              v-html="link.label"
              class="px-4 py-2 rounded-xl transition-all duration-150"
              :class="{
                'bg-blue-500/30 text-blue-100 border border-blue-400/50 backdrop-blur-sm': link.active,
                'text-white/70 hover:bg-white/10 hover:text-white border border-transparent': !link.active && link.url,
                'text-white/40 cursor-not-allowed': !link.url
              }"
            />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- Modals -->
  <AddTeacherModal
    :show="showAddModal"
    @close="showAddModal = false"
  />

  <TeacherActionsModal 
    :show="showActionsModal" 
    :teacher="selectedTeacher" 
    @close="showActionsModal = false"
    @action="handleModalAction"
  />
</template>