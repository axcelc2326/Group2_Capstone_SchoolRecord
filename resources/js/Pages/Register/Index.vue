<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import RegisterModal from '@/Components/AdminAndTeacher/RegisterModal.vue'
import ManageParentModal from '@/Components/AdminAndTeacher/ManageParentModal.vue'
import ParentActionsModal from '@/Components/AdminAndTeacher/ParentActionsModal.vue'
import { ref, computed } from 'vue'
import { Users, Search, Filter, UserPlus, Settings, MoreVertical } from 'lucide-vue-next'
import Swal from 'sweetalert2'

const props = defineProps({
  parents: Object,
  classes: Array,
  filters: Object,
  totalMadeParents: Number,
})

const showRegisterModal = ref(false)
const showManageModal = ref(false)
const showActionsModal = ref(false)
const selectedParent = ref(null)

const search = ref(props.filters.search || '')
const selectedClass = ref(props.filters.class_id || '')
const hasStudents = ref(props.filters.has_students || '')

// Statistics
const totalParents = computed(() => props.parents?.data?.length || 0)

// Filter & search
const applyFilters = () => {
  router.get(
    route('parents.index'),
    { 
      search: search.value, 
      class_id: selectedClass.value,
      has_students: hasStudents.value 
    },
    { preserveState: true, replace: true }
  )
}

// Clear all filters
const clearFilters = () => {
  search.value = ''
  selectedClass.value = ''
  hasStudents.value = ''
  applyFilters()
}

// Open Manage modal
const openManageModal = (parent) => {
  selectedParent.value = parent
  showManageModal.value = true
}

// Open Actions modal
const openActionsModal = (parent) => {
  selectedParent.value = parent
  showActionsModal.value = true
}

// Handle actions from the unified modal
const handleParentAction = (action) => {
  if (!selectedParent.value) return

  switch (action) {
    case 'toggle-status':
      toggleStatus(selectedParent.value.id)
      break
    case 'delete':
      deleteParent(selectedParent.value.id)
      break
  }
  showActionsModal.value = false
}

// Toggle active/inactive status with modern SweetAlert design
const toggleStatus = (id) => {
  router.put(route('parents.toggle-status', id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        title: 'Status Updated!',
        text: 'The parent account status has been successfully changed.',
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

// Automatically delete parent (no confirmation prompt)
const deleteParent = (parentId) => {
  router.delete(route('parents.destroy', parentId), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        title: 'Deleted!',
        text: 'The parent account has been deleted successfully.',
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
        text: 'Failed to delete parent. Please try again.',
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
  <Head title="Parents Management" />

  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-1">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-bold text-white">
              Parents Management
            </h2>
            <p class="text-white/70 mt-1">
              Manage and oversee parent accounts
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
          <div class="text-2xl font-bold text-white">{{ totalMadeParents }}</div>
          <div class="text-sm text-white/70">Total Created</div>
        </div>
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3">
          <div class="text-2xl font-bold text-white">{{ totalParents }}</div>
          <div class="text-sm text-white/70">Active Parents</div>
        </div>
      </div>

      <!-- Search and Filters Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl p-6">
        <div class="flex items-center space-x-3 mb-6">
          <Search class="w-5 h-5 text-blue-300" />
          <div>
            <h3 class="text-lg font-semibold text-white">Search & Filter</h3>
            <p class="text-sm text-white/70 mt-1">Find and filter parent accounts</p>
          </div>
        </div>

        <!-- First Row: Search Parents and Class -->
        <div class="grid md:grid-cols-2 gap-4 mb-4">
          <!-- Search Input -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-white">
              Search Parents
            </label>
            <div class="relative">
              <input
                v-model="search"
                @keyup.enter="applyFilters"
                type="text"
                placeholder="Search by name or email..."
                class="w-full backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
              />
              <Search class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-white/50" />
            </div>
          </div>
          
          <!-- Class Filter -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-white">
              Class
            </label>
            <select
              v-model="selectedClass"
              @change="applyFilters"
              class="w-full appearance-none backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
            >
              <option value="" class="bg-gray-800 text-white">All Classes</option>
              <option value="null" class="bg-gray-800 text-white">Unregistered Students</option>
              <option 
                v-for="cls in props.classes" 
                :key="cls.id" 
                :value="cls.id"
                class="bg-gray-800 text-white"
              >
                {{ cls.name }} (Grade {{ cls.grade_level }})
              </option>
            </select>
          </div>
        </div>

        <!-- Second Row: Student Status and Actions -->
        <div class="grid md:grid-cols-2 gap-4">
          <!-- Has Students Filter -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-white">
              Student Status
            </label>
            <select
              v-model="hasStudents"
              @change="applyFilters"
              class="w-full appearance-none backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
            >
              <option value="" class="bg-gray-800 text-white">All Parents</option>
              <option value="with_students" class="bg-gray-800 text-white">With Students</option>
              <option value="without_students" class="bg-gray-800 text-white">Without Students</option>
            </select>
          </div>
          
          <!-- Action Buttons -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-white">
              Actions
            </label>
            <div class="flex gap-2">
              <button 
                @click="applyFilters"
                class="flex-1 backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30 flex items-center justify-center gap-2"
              >
                <Filter class="w-4 h-4" />
                Filter
              </button>
              <button
                @click="clearFilters"
                class="flex-1 backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30 flex items-center justify-center gap-2"
              >
                Clear
              </button>
              <button
                @click="showRegisterModal = true"
                class="flex-1 backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30 flex items-center justify-center gap-2"
              >
                <UserPlus class="w-4 h-4" />
                Register
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Parents Table Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <Users class="w-5 h-5 text-purple-300" />
            <div>
              <h3 class="text-lg font-semibold text-white">Parents Directory</h3>
              <p class="text-sm text-white/70 mt-1">{{ props.parents?.data?.length || 0 }} parents available</p>
            </div>
          </div>
        </div>

        <div v-if="props.parents?.data?.length">
          <!-- Desktop Table -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Parent</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Students</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                <tr
                  v-for="parent in props.parents.data"
                  :key="parent.id"
                  class="hover:bg-white/5 transition-colors duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-medium text-sm">
                          {{ parent.name.charAt(0) }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-white">{{ parent.name }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ parent.email }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div v-if="parent.students.length" class="flex flex-wrap gap-1">
                      <span
                        v-for="student in parent.students"
                        :key="student.id"
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium backdrop-blur-sm bg-indigo-500/20 text-indigo-100 border border-indigo-400/30"
                      >
                        {{ student.first_name }} {{ student.middle_name }} {{ student.last_name }}
                      </span>
                    </div>
                    <span v-else class="text-white/50 italic text-sm">No students assigned</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium backdrop-blur-sm border" :class="{
                      'bg-green-500/20 text-green-100 border-green-400/30': parent.status === 'active',
                      'bg-red-500/20 text-red-100 border-red-400/30': parent.status !== 'active'
                    }">
                      <div class="w-1.5 h-1.5 rounded-full mr-1.5" :class="{
                        'bg-green-400': parent.status === 'active',
                        'bg-red-400': parent.status !== 'active'
                      }"></div>
                      {{ parent.status === 'active' ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center space-x-2">
                      <button
                        @click="openManageModal(parent)"
                        class="flex items-center gap-1.5 p-2 rounded-lg backdrop-blur-sm bg-indigo-500/20 border border-indigo-400/30 text-indigo-100 hover:bg-indigo-500/30 transition-all duration-150"
                        title="Manage"
                      >
                        <Settings class="w-4 h-4" />
                        <span class="text-xs">Manage</span>
                      </button>
                      <button
                        @click="openActionsModal(parent)"
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
              v-for="parent in props.parents.data" 
              :key="parent.id"
              class="p-4 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-medium text-sm">
                    {{ parent.name.charAt(0) }}
                  </div>
                  <div>
                    <h4 class="text-white font-medium text-sm">{{ parent.name }}</h4>
                    <p class="text-xs text-white/70">{{ parent.email }}</p>
                  </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium backdrop-blur-sm border" :class="{
                  'bg-green-500/20 text-green-100 border-green-400/30': parent.status === 'active',
                  'bg-red-500/20 text-red-100 border-red-400/30': parent.status !== 'active'
                }">
                  <div class="w-1.5 h-1.5 rounded-full mr-1" :class="{
                    'bg-green-400': parent.status === 'active',
                    'bg-red-400': parent.status !== 'active'
                  }"></div>
                  {{ parent.status === 'active' ? 'Active' : 'Inactive' }}
                </span>
              </div>
              
              <div class="mb-3">
                <p class="text-xs text-white/60 mb-2">Students:</p>
                <div v-if="parent.students.length" class="flex flex-wrap gap-1">
                  <span
                    v-for="student in parent.students"
                    :key="student.id"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium backdrop-blur-sm bg-indigo-500/20 text-indigo-100 border border-indigo-400/30"
                  >
                    {{ student.first_name }} {{ student.last_name }}
                  </span>
                </div>
                <span v-else class="text-white/50 italic text-xs">No students assigned</span>
              </div>
              
              <div class="flex space-x-2 pt-3 border-t border-white/10">
                <button
                  @click="openManageModal(parent)"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-indigo-500/20 border border-indigo-400/30 text-indigo-100 hover:bg-indigo-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <Settings class="w-4 h-4" />
                  Manage
                </button>
                <button
                  @click="openActionsModal(parent)"
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
          <h3 class="text-sm font-medium text-white/80">No parents found</h3>
          <p class="mt-1 text-sm text-white/60">Start by registering your first parent account.</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="props.parents?.links?.length > 3" class="flex justify-center">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-xl p-2">
          <div class="flex space-x-2">
            <Link
              v-for="link in props.parents.links"
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
  <RegisterModal 
    :show="showRegisterModal" 
    @close="showRegisterModal = false"
  />
  <ManageParentModal
    v-if="selectedParent"
    :show="showManageModal"
    :parent="selectedParent"
    :classes="props.classes"
    @close="showManageModal = false"
  />
  <ParentActionsModal
    v-if="selectedParent"
    :show="showActionsModal"
    :parent="selectedParent"
    @close="showActionsModal = false"
    @action="handleParentAction"
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