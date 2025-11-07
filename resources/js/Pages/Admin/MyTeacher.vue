<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'
import AddTeacherModal from '@/Components/Admin/AddTeacherModal.vue'
import TeacherActionsModal from '@/Components/Admin/TeacherActionsModal.vue'
import { Users, Search, Filter, UserPlus, Settings, MoreVertical } from 'lucide-vue-next'

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

const openActionsModal = (teacher) => {
  selectedTeacher.value = teacher
  showActionsModal.value = true
}

const handleModalAction = (action) => {
  if (!selectedTeacher.value) return

  switch (action) {
    case 'toggle-status':
      toggleStatus(selectedTeacher.value.id)
      break
    case 'delete':
      deleteTeacher(selectedTeacher.value.id)
      break
  }
  showActionsModal.value = false
}

// Toggle active/inactive status with modern SweetAlert design
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

// Automatically delete teacher (no confirmation prompt)
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
</script>

<template>
  <Head title="Teachers Management" />

  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-1">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-bold text-white">
              Teachers Management
            </h2>
            <p class="text-white/70 mt-1">
              Manage and oversee teacher accounts
            </p>
          </div>
          <Users class="w-8 h-8 text-white/60" />
        </div>
      </div>
    </template>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Header Stats -->
      <div class="grid grid-cols-2 gap-4">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3">
          <div class="text-2xl font-bold text-white">{{ totalCreatedTeachers }}</div>
          <div class="text-sm text-white/70">Total Created</div>
        </div>
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3">
          <div class="text-2xl font-bold text-white">{{ totalTeachers }}</div>
          <div class="text-sm text-white/70">Active Teachers</div>
        </div>
      </div>

      <!-- Search and Filters Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl p-6">
        <div class="flex items-center space-x-3 mb-6">
          <Search class="w-5 h-5 text-blue-300" />
          <div>
            <h3 class="text-lg font-semibold text-white">Search & Filter</h3>
            <p class="text-sm text-white/70 mt-1">Find and filter teacher accounts</p>
          </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
          <!-- Search Input -->
          <div class="md:col-span-2 space-y-2">
            <label class="block text-sm font-semibold text-white">
              Search Teachers
            </label>
            <div class="relative">
              <input
                v-model="search"
                @keyup.enter="searchTeachers"
                type="text"
                placeholder="Search by name or email..."
                class="w-full backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
              />
              <Search class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-white/50" />
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="md:col-span-1 space-y-2">
            <label class="block text-sm font-semibold text-white">
              Actions
            </label>
            <div class="flex gap-2">
              <button 
                @click="searchTeachers"
                class="flex-1 backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30 flex items-center justify-center gap-2"
              >
                <Filter class="w-4 h-4" />
                Search
              </button>
              <button
                @click="showAddModal = true"
                class="flex-1 backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30 flex items-center justify-center gap-2"
              >
                <UserPlus class="w-4 h-4" />
                Register
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Teachers Table Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <Users class="w-5 h-5 text-purple-300" />
            <div>
              <h3 class="text-lg font-semibold text-white">Teachers Directory</h3>
              <p class="text-sm text-white/70 mt-1">{{ props.teachers?.data?.length || 0 }} teachers available</p>
            </div>
          </div>
        </div>

        <div v-if="props.teachers?.data?.length">
          <!-- Desktop Table -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Teacher</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Class</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Actions</th>
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
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-medium text-sm">
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
                  <td class="px-6 py-4">
                    <template v-if="teacher.class">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium backdrop-blur-sm bg-indigo-500/20 text-indigo-100 border border-indigo-400/30">
                        {{ teacher.class.name }} (Grade {{ teacher.class.grade_level }})
                      </span>
                    </template>
                    <span v-else class="text-white/50 italic text-sm">No class assigned</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium backdrop-blur-sm border" :class="{
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
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center space-x-2">
                      <button
                        @click="openActionsModal(teacher)"
                        class="flex items-center gap-1.5 p-2 rounded-lg backdrop-blur-sm bg-blue-500/20 border border-blue-400/30 text-blue-100 hover:bg-blue-500/30 transition-all duration-150"
                        title="More Actions"
                      >
                        <MoreVertical class="w-4 h-4" />
                        <span class="text-xs">Actions</span>
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
              v-for="teacher in props.teachers.data" 
              :key="teacher.id"
              class="p-4 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-medium text-sm">
                    {{ teacher.name.charAt(0) }}
                  </div>
                  <div>
                    <h4 class="text-white font-medium text-sm">{{ teacher.name }}</h4>
                    <p class="text-xs text-white/70">{{ teacher.email }}</p>
                  </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium backdrop-blur-sm border" :class="{
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
              
              <div class="mb-3">
                <p class="text-xs text-white/60 mb-2">Class Assignment:</p>
                <template v-if="teacher.class">
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium backdrop-blur-sm bg-indigo-500/20 text-indigo-100 border border-indigo-400/30">
                    {{ teacher.class.name }} (Grade {{ teacher.class.grade_level }})
                  </span>
                </template>
                <span v-else class="text-white/50 italic text-xs">No class assigned</span>
              </div>
              
              <div class="flex space-x-2 pt-3 border-t border-white/10">
                <button
                  @click="openActionsModal(teacher)"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-blue-500/20 border border-blue-400/30 text-blue-100 hover:bg-blue-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <MoreVertical class="w-4 h-4" />
                  Actions
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8">
          <Users class="mx-auto h-10 w-10 text-white/40 mb-3" />
          <h3 class="text-sm font-medium text-white/80">No teachers found</h3>
          <p class="mt-1 text-sm text-white/60">Start by registering your first teacher account.</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="props.teachers?.links?.length > 3" class="flex justify-center">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-xl p-2">
          <div class="flex space-x-2">
            <Link
              v-for="link in props.teachers.links"
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

  <!-- Modals -->
  <AddTeacherModal 
    :show="showAddModal" 
    @close="showAddModal = false"
  />
  <TeacherActionsModal
    v-if="selectedTeacher"
    :show="showActionsModal"
    :teacher="selectedTeacher"
    @close="showActionsModal = false"
    @action="handleModalAction"
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