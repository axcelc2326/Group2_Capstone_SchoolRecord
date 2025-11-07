<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import EditSubjectModal from '@/Components/Admin/EditSubjectModal.vue'
import CreateSubjectModal from '@/Components/Admin/CreateSubjectModal.vue'
import Swal from 'sweetalert2'
import { BookOpen, Search, Filter, Plus, Edit, Trash2, MoreVertical } from 'lucide-vue-next'

const props = defineProps({
  subjects: Object,
  filters: Object,
})

const showEditModal = ref(false)
const showCreateModal = ref(false)
const selectedSubject = ref(null)

// Filters
const grade_level = ref(props.filters.grade_level || '')

// Statistics
const totalSubjects = computed(() => props.subjects?.total || 0)

function openEdit(subject) {
  selectedSubject.value = subject
  showEditModal.value = true
}

function destroySubject(subject) {
  Swal.fire({
    title: `Delete "${subject.name}"?`,
    text: 'This action cannot be undone. All associated data will be permanently removed.',
    icon: 'warning',
    background: '#1f2937',
    color: '#f9fafb',
    backdrop: 'rgba(0, 0, 0, 0.7)',
    showClass: {
      popup: 'animate__animated animate__fadeInDown animate__faster'
    },
    customClass: {
      popup: 'rounded-2xl shadow-2xl border border-yellow-500/50 backdrop-blur-lg',
      title: 'text-2xl font-bold text-white mb-2',
      htmlContainer: 'text-gray-300',
      confirmButton: 'py-3 px-6 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:ring-4 focus:ring-red-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
      cancelButton: 'py-3 px-6 border border-gray-300 text-gray-300 font-semibold rounded-xl shadow-sm hover:bg-gray-800 hover:shadow-md transform transition-all duration-200 mr-3',
      icon: '!border-none !bg-transparent text-yellow-500'
    },
    buttonsStyling: false,
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('subjects.destroy', subject.id), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            title: 'Subject Deleted!',
            text: `"${subject.name}" has been permanently removed.`,
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
              icon: '!border-none !bg-transparent text-green-500'
            },
            buttonsStyling: false,
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true,
          })
        },
        onError: () => {
          Swal.fire({
            title: 'Deletion Failed!',
            text: 'Failed to delete the subject. Please try again.',
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

function applyFilters() {
  router.get(route('subjects.index'), { grade_level: grade_level.value }, {
    preserveState: true,
    preserveScroll: true,
  })
}

// Grade level options
const gradeOptions = [
  { value: '', label: 'All Grade Levels' },
  { value: 'K1', label: 'Kinder 1' },
  { value: 'K2', label: 'Kinder 2' },
  ...Array.from({ length: 6 }, (_, i) => ({ value: (i + 1).toString(), label: `Grade ${i + 1}` }))
]
</script>

<template>
  <Head title="Subjects Management" />

  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-1">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-bold text-white">
              Subjects Management
            </h2>
            <p class="text-white/70 mt-1">
              Manage and oversee subject curriculum
            </p>
          </div>
          <BookOpen class="w-8 h-8 text-white/60" />
        </div>
      </div>
    </template>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Header Stats -->
      <div class="grid grid-cols-2 gap-4">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3">
          <div class="text-2xl font-bold text-white">{{ totalSubjects }}</div>
          <div class="text-sm text-white/70">Total Subjects</div>
        </div>
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3">
          <div class="text-2xl font-bold text-white">{{ subjects?.data?.length || 0 }}</div>
          <div class="text-sm text-white/70">Currently Showing</div>
        </div>
      </div>

      <!-- Search and Filters Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl p-6">
        <div class="flex items-center space-x-3 mb-6">
          <Search class="w-5 h-5 text-blue-300" />
          <div>
            <h3 class="text-lg font-semibold text-white">Filter Subjects</h3>
            <p class="text-sm text-white/70 mt-1">Filter subjects by grade level</p>
          </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
          <!-- Grade Level Filter -->
          <div class="md:col-span-2 space-y-2">
            <label class="block text-sm font-semibold text-white">
              Grade Level
            </label>
            <select
              v-model="grade_level"
              @change="applyFilters"
              class="w-full appearance-none backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
            >
              <option 
                v-for="opt in gradeOptions" 
                :key="opt.value" 
                :value="opt.value"
                class="bg-gray-800 text-white"
              >
                {{ opt.label }}
              </option>
            </select>
          </div>
          
          <!-- Action Buttons -->
          <div class="md:col-span-1 space-y-2">
            <label class="block text-sm font-semibold text-white">
              Actions
            </label>
            <div class="flex gap-2">
              <button 
                @click="applyFilters"
                class="flex-1 backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30 flex items-center justify-center gap-2"
              >
                <Filter class="w-4 h-4" />
                Apply
              </button>
              <button
                @click="showCreateModal = true"
                class="flex-1 backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30 flex items-center justify-center gap-2"
              >
                <Plus class="w-4 h-4" />
                Add Subject
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Subjects Table Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <BookOpen class="w-5 h-5 text-purple-300" />
            <div>
              <h3 class="text-lg font-semibold text-white">Subjects Directory</h3>
              <p class="text-sm text-white/70 mt-1">{{ subjects?.data?.length || 0 }} subjects available</p>
            </div>
          </div>
        </div>

        <div v-if="subjects?.data?.length">
          <!-- Desktop Table -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Subject</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Grade Level</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                <tr
                  v-for="subject in subjects.data"
                  :key="subject.id"
                  class="hover:bg-white/5 transition-colors duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-medium text-sm">
                          {{ subject.name.charAt(0) }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-white">{{ subject.name }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">
                      <span v-if="subject.grade_level === 'K1'">Kinder 1</span>
                      <span v-else-if="subject.grade_level === 'K2'">Kinder 2</span>
                      <span v-else>Grade {{ subject.grade_level }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center space-x-2">
                      <button
                        @click="openEdit(subject)"
                        class="flex items-center gap-1.5 p-2 rounded-lg backdrop-blur-sm bg-indigo-500/20 border border-indigo-400/30 text-indigo-100 hover:bg-indigo-500/30 transition-all duration-150"
                        title="Edit"
                      >
                        <Edit class="w-4 h-4" />
                        <span class="text-xs">Edit</span>
                      </button>
                      <button
                        @click="destroySubject(subject)"
                        class="flex items-center gap-1.5 p-2 rounded-lg backdrop-blur-sm bg-red-500/20 border border-red-400/30 text-red-100 hover:bg-red-500/30 transition-all duration-150"
                        title="Delete"
                      >
                        <Trash2 class="w-4 h-4" />
                        <span class="text-xs">Delete</span>
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
              v-for="subject in subjects.data" 
              :key="subject.id"
              class="p-4 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-medium text-sm">
                    {{ subject.name.charAt(0) }}
                  </div>
                  <div>
                    <h4 class="text-white font-medium text-sm">{{ subject.name }}</h4>
                    <p class="text-xs text-white/70">
                      <span v-if="subject.grade_level === 'K1'">Kinder 1</span>
                      <span v-else-if="subject.grade_level === 'K2'">Kinder 2</span>
                      <span v-else>Grade {{ subject.grade_level }}</span>
                    </p>
                  </div>
                </div>
              </div>
              
              <div class="flex space-x-2 pt-3 border-t border-white/10">
                <button
                  @click="openEdit(subject)"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-indigo-500/20 border border-indigo-400/30 text-indigo-100 hover:bg-indigo-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <Edit class="w-4 h-4" />
                  Edit
                </button>
                <button
                  @click="destroySubject(subject)"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-red-500/20 border border-red-400/30 text-red-100 hover:bg-red-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <Trash2 class="w-4 h-4" />
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8">
          <BookOpen class="mx-auto h-10 w-10 text-white/40 mb-3" />
          <h3 class="text-sm font-medium text-white/80">
            {{ grade_level ? 'No subjects found' : 'No subjects created yet' }}
          </h3>
          <p class="mt-1 text-sm text-white/60">
            {{ grade_level ? 'Try adjusting your grade level filter.' : 'Start by creating your first subject.' }}
          </p>
          <button
            v-if="!grade_level"
            @click="showCreateModal = true"
            class="mt-4 inline-flex items-center px-4 py-2 backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30"
          >
            <Plus class="w-4 h-4 mr-2" />
            Create Subject
          </button>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="subjects?.links?.length > 3" class="flex justify-center">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-xl p-2">
          <div class="flex space-x-2">
            <Link
              v-for="link in subjects.links"
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
  <CreateSubjectModal 
    :show="showCreateModal" 
    @close="showCreateModal = false"
    @created="router.reload({ only: ['subjects'] })"
  />
  <EditSubjectModal
    v-if="selectedSubject"
    :show="showEditModal"
    :subject="selectedSubject"
    @close="showEditModal = false"
    @updated="router.reload({ only: ['subjects'] })"
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