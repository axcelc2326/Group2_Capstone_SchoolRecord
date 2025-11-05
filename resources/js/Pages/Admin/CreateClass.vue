<script setup>
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import CreateClassModal from '@/Components/Admin/CreateClassModal.vue'
import EditClassModal from '@/Components/Admin/EditClassModal.vue'
import { ref, watch, computed } from 'vue'
import Swal from 'sweetalert2'
import { Search, GraduationCap, Settings, Plus, Edit, Trash2 } from 'lucide-vue-next'

const page = usePage()
const props = defineProps({
  classes: Object,
  filters: Object,
  teachers: Array,
  totalCount: Number,
})

const classes = ref(props.classes)
const isLoading = ref(false)
const showModal = ref(false) // Create modal visibility

// ✅ Add Edit modal states
const showEditModal = ref(false)
const selectedClass = ref(null)

// Filters
const filters = ref({
  search: props.filters.search || '',
  grade_level: props.filters.grade_level || '',
})

// Statistics
const totalClasses = computed(() => props.totalCount || 0)

// Watch for changes when Inertia reloads
watch(
  () => page.props.classes,
  (newVal) => {
    classes.value = newVal
  }
)

// Delete class
function deleteClass(classItem) {
  Swal.fire({
    title: `Delete "${classItem.name}"?`,
    text: 'This action cannot be undone. All associated data will be permanently removed.',
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
    confirmButtonText: 'Yes, delete it',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      isLoading.value = true
      router.delete(route('classes.destroy', classItem.id), {
        onSuccess: () => {
          isLoading.value = false
          Swal.fire({
            title: 'Class Deleted!',
            text: `"${classItem.name}" has been permanently removed.`,
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
            didOpen: (toast) => {
              toast.addEventListener('mouseenter', Swal.stopTimer)
              toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
          })
          reloadClasses()
        },
        onError: () => {
          isLoading.value = false
          Swal.fire({
            title: 'Deletion Failed!',
            text: 'Failed to delete the class. Please try again.',
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
      })
    }
  })
}

// ✅ Open edit modal
function openEdit(classItem) {
  selectedClass.value = classItem
  showEditModal.value = true
}

// ✅ Reload classes after update/delete
function reloadClasses() {
  router.reload({ only: ['classes'] })
}

// Grade level options
const gradeOptions = [
  { value: 'K1', label: 'Kinder 1' },
  { value: 'K2', label: 'Kinder 2' },
  ...Array.from({ length: 6 }, (_, i) => ({ value: (i + 1).toString(), label: `Grade ${i + 1}` }))
]

// Apply filters
function applyFilters() {
  router.get(route('classes.index'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Class Management" />
  
  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0">
        <h2 class="text-2xl font-bold text-white bg-gradient-to-r from-white to-white/70 bg-clip-text text-transparent">
          🏫 Class Management
        </h2>
        <div class="flex items-center space-x-4">
          <!-- Quick Stats -->
          <div class="hidden md:flex items-center space-x-3 text-sm">
            <div class="backdrop-blur-sm bg-blue-500/20 border border-blue-300/30 px-3 py-1 rounded-full">
              <span class="text-blue-100">{{ totalClasses }} Classes</span>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Top Action Bar -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl p-6 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
          
          <!-- Search and Filter Section -->
          <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-3 flex-1">
            <!-- Search Bar -->
            <div class="relative flex-1 max-w-md">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Search class="h-5 w-5 text-white/60" />
              </div>
              <input
                v-model="filters.search"
                @input="applyFilters"
                type="text"
                placeholder="Search name or section..."
                class="block w-full pl-10 pr-3 py-2.5 border-0 rounded-xl backdrop-blur-sm bg-white/10 border-white/20 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
              />
            </div>
            
            <!-- Grade Level Filter -->
            <div class="relative">
              <select
                v-model="filters.grade_level"
                @change="applyFilters"
                class="appearance-none px-4 py-2.5 pr-10 rounded-xl backdrop-blur-sm bg-white/10 border border-white/20 text-white focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
              >
                <option value="" class="bg-gray-800 text-white">All Levels</option>
                <option v-for="opt in gradeOptions" :key="opt.value" :value="opt.value" class="bg-gray-800 text-white">
                  {{ opt.label }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Add Class Button -->
          <div class="relative">
            <button
              @click="showModal = true"
              class="group relative inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-blue-600 hover:from-emerald-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 border border-white/20"
            >
              <Plus class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" />
              Add Class
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Stats (visible on small screens) -->
      <div class="md:hidden">
        <div class="backdrop-blur-md bg-blue-500/10 border border-blue-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-blue-100">{{ totalClasses }}</div>
          <div class="text-sm text-blue-200">Total Classes</div>
        </div>
      </div>

      <!-- Classes List -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
              <GraduationCap class="w-4 h-4 text-white" />
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">Total Classes</h3>
              <p class="text-sm text-white/70 mt-1">{{ classes.data.length }} classes shown</p>
            </div>
          </div>
        </div>

        <div v-if="classes.data.length > 0">
          <!-- Desktop View -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">#</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Class</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Teacher</th>
                  <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                <tr 
                  v-for="(classItem, index) in classes.data" 
                  :key="classItem.id" 
                  class="hover:bg-white/5 transition-colors duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ index + 1 + (classes.current_page - 1) * classes.per_page }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-12 w-12">
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                          {{ classItem.grade_level }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-white">{{ classItem.name }}</div>
                        <div class="text-xs text-white/60">
                          <span v-if="classItem.grade_level === 'K1'">Kinder 1</span>
                          <span v-else-if="classItem.grade_level === 'K2'">Kinder 2</span>
                          <span v-else>Grade {{ classItem.grade_level }}</span>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ classItem.teacher?.name || 'Unassigned' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                    <button
                      @click="openEdit(classItem)"
                      class="inline-flex items-center px-3 py-1.5 bg-amber-500/20 hover:bg-amber-500/30 text-amber-100 border border-amber-400/30 hover:border-amber-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                    >
                      <Edit class="w-4 h-4 mr-1" />
                      Edit
                    </button>
                    <button
                      @click="deleteClass(classItem)"
                      class="inline-flex items-center px-3 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-100 border border-red-400/30 hover:border-red-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                    >
                      <Trash2 class="w-4 h-4 mr-1" />
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
              v-for="(classItem, index) in classes.data" 
              :key="classItem.id"
              class="p-6 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                    {{ classItem.grade_level }}
                  </div>
                  <div>
                    <h4 class="text-white font-medium">{{ classItem.name }}</h4>
                    <p class="text-sm text-white/70">
                      <span v-if="classItem.grade_level === 'K1'">Kinder 1</span>
                      <span v-else-if="classItem.grade_level === 'K2'">Kinder 2</span>
                      <span v-else>Grade {{ classItem.grade_level }}</span>
                    </p>
                    <p class="text-xs text-white/50">Teacher: {{ classItem.teacher?.name || 'Unassigned' }}</p>
                  </div>
                </div>
              </div>
              <div class="flex space-x-2">
                <button
                  @click="openEdit(classItem)"
                  class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-amber-500/20 hover:bg-amber-500/30 text-amber-100 border border-amber-400/30 hover:border-amber-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                >
                  <Edit class="w-4 h-4 mr-2" />
                  Edit
                </button>
                <button
                  @click="deleteClass(classItem)"
                  class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-100 border border-red-400/30 hover:border-red-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                >
                  <Trash2 class="w-4 h-4 mr-2" />
                  Delete
                </button>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="classes.links && classes.links.length > 3" class="px-6 py-4 border-t border-white/10 flex justify-between items-center">
            <div class="text-sm text-white/70">
              Showing {{ classes.from || 0 }} to {{ classes.to || 0 }} of {{ classes.total || 0 }} classes
            </div>
            <div class="flex space-x-1">
              <template v-for="(link, index) in classes.links" :key="index">
                <span v-if="!link.url" class="px-3 py-2 text-white/40 cursor-not-allowed text-sm" v-html="link.label"></span>
                <Link v-else :href="link.url" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150"
                  :class="link.active ? 'bg-blue-500/30 text-white border border-blue-400/50' : 'bg-white/10 text-white/70 border border-white/20 hover:bg-white/20'" v-html="link.label" />
              </template>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <GraduationCap class="mx-auto h-12 w-12 text-white/40 mb-4" />
          <h3 class="mt-2 text-sm font-medium text-white/80">
            {{ filters.search || filters.grade_level ? 'No classes found' : 'No classes created yet' }}
          </h3>
          <p class="mt-1 text-sm text-white/60">
            {{ filters.search || filters.grade_level ? 'Try adjusting your search terms or filters.' : 'Create your first class to get started.' }}
          </p>
          <button
            v-if="!filters.search && !filters.grade_level"
            @click="showModal = true"
            class="mt-4 inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-blue-600 hover:from-emerald-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200"
          >
            <Plus class="w-4 h-4 mr-2" />
            Create Class
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
  
  <!-- ✅ Use Modal Components -->
  <CreateClassModal 
    :show="showModal" 
    @close="showModal = false" 
  />

  <EditClassModal
    :show="showEditModal"
    :classItem="selectedClass"
    @close="showEditModal = false"
    @updated="reloadClasses"
  />
</template>