<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import EditSubjectModal from '@/Components/EditSubjectModal.vue'
import CreateSubjectModal from '@/Components/CreateSubjectModal.vue'
import Swal from 'sweetalert2'
import { BookOpen, Plus, Edit, Trash2 } from 'lucide-vue-next'

const props = defineProps({
  subjects: Object, // Laravel paginator
  filters: Object,  // passed from backend (grade_level)
})

const showEditModal = ref(false)
const showCreateModal = ref(false)
const selectedSubject = ref(null)

// Filters - removed search
const filters = ref({
  grade_level: props.filters.grade_level || '',
})

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
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, delete it',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
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
      router.delete(route('subjects.destroy', subject.id), {
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: `"${subject.name}" has been removed.`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            background: '#1f2937',
            color: '#f9fafb',
            iconColor: '#10b981'
          })
          reloadSubjects()
        },
        onError: () => {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to delete the subject. Please try again.',
            toast: true,
            position: 'top-end',
            background: '#1f2937',
            color: '#f9fafb',
            iconColor: '#ef4444'
          })
        }
      })
    }
  })
}

function reloadSubjects() {
  router.reload({ only: ['subjects'] })
}

function applyFilters() {
  router.get(route('subjects.index'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

// Grade level options
const gradeOptions = [
  { value: 'K1', label: 'Kinder 1' },
  { value: 'K2', label: 'Kinder 2' },
  ...Array.from({ length: 6 }, (_, i) => ({ value: (i + 1).toString(), label: `Grade ${i + 1}` }))
]
</script>

<template>
  <Head title="Subjects" />
  
  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0">
        <h2 class="text-2xl font-bold text-white bg-gradient-to-r from-white to-white/70 bg-clip-text text-transparent">
          📚 Subject Management
        </h2>
        <div class="flex items-center space-x-4">
          <!-- Quick Stats -->
          <div class="hidden md:flex items-center space-x-3 text-sm">
            <div class="backdrop-blur-sm bg-blue-500/20 border border-blue-300/30 px-3 py-1 rounded-full">
              <span class="text-blue-100">{{ totalSubjects }} Subjects</span>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Top Action Bar -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl p-6 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
          
          <!-- Filter Section -->
          <div class="flex items-center space-x-3">
            <!-- Grade Level Filter -->
            <div class="relative">
              <select
                v-model="filters.grade_level"
                @change="applyFilters"
                class="appearance-none w-64 px-4 py-2.5 pr-10 rounded-xl backdrop-blur-sm bg-white/10 border border-white/20 text-white focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
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

          <!-- Add Subject Button -->
          <div class="relative">
            <button
              @click="showCreateModal = true"
              class="group relative inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-blue-600 hover:from-emerald-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 border border-white/20"
            >
              <Plus class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" />
              Add Subject
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Stats (visible on small screens) -->
      <div class="md:hidden">
        <div class="backdrop-blur-md bg-blue-500/10 border border-blue-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-blue-100">{{ totalSubjects }}</div>
          <div class="text-sm text-blue-200">Total Subjects</div>
        </div>
      </div>

      <!-- Subjects List -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
              <BookOpen class="w-4 h-4 text-white" />
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">Total Subjects</h3>
              <p class="text-sm text-white/70 mt-1">{{ subjects.data.length }} subjects shown</p>
            </div>
          </div>
        </div>

        <div v-if="subjects.data.length > 0">
          <!-- Desktop View -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">#</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Subject</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Grade Level</th>
                  <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                <tr 
                  v-for="(subject, index) in subjects.data" 
                  :key="subject.id" 
                  class="hover:bg-white/5 transition-colors duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ index + 1 + (subjects.current_page - 1) * subjects.per_page }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-12 w-12">
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-bold text-lg">
                          {{ subject.name.charAt(0).toUpperCase() }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-white">{{ subject.name }}</div>
                        <div class="text-xs text-white/60">Subject</div>
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
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                    <button
                      @click="openEdit(subject)"
                      class="inline-flex items-center px-3 py-1.5 bg-amber-500/20 hover:bg-amber-500/30 text-amber-100 border border-amber-400/30 hover:border-amber-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                    >
                      <Edit class="w-4 h-4 mr-1" />
                      Edit
                    </button>
                    <button
                      @click="destroySubject(subject)"
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
              v-for="(subject, index) in subjects.data" 
              :key="subject.id"
              class="p-6 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-bold text-lg">
                    {{ subject.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <h4 class="text-white font-medium">{{ subject.name }}</h4>
                    <p class="text-sm text-white/70">
                      <span v-if="subject.grade_level === 'K1'">Kinder 1</span>
                      <span v-else-if="subject.grade_level === 'K2'">Kinder 2</span>
                      <span v-else>Grade {{ subject.grade_level }}</span>
                    </p>
                  </div>
                </div>
              </div>
              <div class="flex space-x-2">
                <button
                  @click="openEdit(subject)"
                  class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-amber-500/20 hover:bg-amber-500/30 text-amber-100 border border-amber-400/30 hover:border-amber-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                >
                  <Edit class="w-4 h-4 mr-2" />
                  Edit
                </button>
                <button
                  @click="destroySubject(subject)"
                  class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-100 border border-red-400/30 hover:border-red-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                >
                  <Trash2 class="w-4 h-4 mr-2" />
                  Delete
                </button>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="subjects.links && subjects.links.length > 3" class="px-6 py-4 border-t border-white/10 flex justify-between items-center">
            <div class="text-sm text-white/70">
              Showing {{ subjects.from || 0 }} to {{ subjects.to || 0 }} of {{ subjects.total || 0 }} subjects
            </div>
            <div class="flex space-x-1">
              <template v-for="(link, index) in subjects.links" :key="index">
                <span v-if="!link.url" class="px-3 py-2 text-white/40 cursor-not-allowed text-sm" v-html="link.label"></span>
                <button v-else @click="router.get(link.url, {}, { preserveState: true })" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150"
                  :class="link.active ? 'bg-blue-500/30 text-white border border-blue-400/50' : 'bg-white/10 text-white/70 border border-white/20 hover:bg-white/20'" v-html="link.label" />
              </template>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <BookOpen class="mx-auto h-12 w-12 text-white/40 mb-4" />
          <h3 class="mt-2 text-sm font-medium text-white/80">
            {{ filters.grade_level ? 'No subjects found' : 'No subjects created yet' }}
          </h3>
          <p class="mt-1 text-sm text-white/60">
            {{ filters.grade_level ? 'Try adjusting your grade level filter.' : 'Create your first subject to get started.' }}
          </p>
          <button
            v-if="!filters.grade_level"
            @click="showCreateModal = true"
            class="mt-4 inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-blue-600 hover:from-emerald-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200"
          >
            <Plus class="w-4 h-4 mr-2" />
            Create Subject
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- Modals -->
  <CreateSubjectModal
    :show="showCreateModal"
    @close="showCreateModal = false"
    @created="reloadSubjects"
  />

  <EditSubjectModal
    :show="showEditModal"
    :subject="selectedSubject"
    @close="showEditModal = false"
    @updated="reloadSubjects"
  />
</template>