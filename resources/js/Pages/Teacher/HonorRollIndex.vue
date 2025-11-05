<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import EditHonorRollModal from '@/Components/Teacher/EditHonorRollModal.vue'
import { Award, Download, Edit, Trash2, Plus, Loader } from 'lucide-vue-next'
import Swal from 'sweetalert2'

// ✅ Props from controller
const props = defineProps({
  classes: Array,
  records: Array,
})

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

// ✅ Reactive form for creating
const form = ref({
  class_id: '',
  school_year: '',
  quarter: '',
  principal_name: '',
})

// ✅ Modal state for editing
const showEdit = ref(false)
const selectedRecord = ref(null)
const isSubmitting = ref(false)

// ✅ Auto-select teacher's class if only one
onMounted(() => {
  if (props.classes.length === 1) {
    form.value.class_id = props.classes[0].id
  }
})

// ✅ Create + download PDF immediately
async function submitForm() {
  isSubmitting.value = true
  const formData = new FormData()
  formData.append('class_id', form.value.class_id)
  formData.append('school_year', form.value.school_year)
  formData.append('quarter', form.value.quarter)
  formData.append('principal_name', form.value.principal_name)

  const url = route('honorlist.generate')
  const newWindow = window.open('', '_blank')

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
      },
      body: formData,
    })

    if (!response.ok) throw new Error('Failed to generate PDF')

    const blob = await response.blob()
    const pdfUrl = URL.createObjectURL(blob)
    newWindow.location.href = pdfUrl
    router.reload({ only: ['records'] })
  } catch (error) {
    alert('Failed to generate honor roll. Please check input data.')
    console.error(error)
  } finally {
    isSubmitting.value = false
  }
}

// ✅ Delete record
function deleteRecord(id) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will delete the honor roll record permanently.',
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
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('honor-rolls.destroy', id), {
        onSuccess: () => {
          Swal.fire({
            title: 'Deleted!',
            text: 'The honor roll record has been deleted successfully.',
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
            title: 'Error!',
            text: 'Failed to delete honor roll record. Please try again.',
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
        },
      })
    }
  })
}

// ✅ Open modal for editing
function openEdit(record) {
  selectedRecord.value = record
  showEdit.value = true
}

// ✅ Refresh after update
function refreshRecords() {
  router.reload({ only: ['records'] })
}
</script>

<template>
  <Head title="Honor Roll Generator" />
  
  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-1">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-bold text-white">
              Honor Roll Generator
            </h2>
            <p class="text-white/70 mt-1">
              Generate and manage honor roll certificates
            </p>
          </div>
          <Award class="w-8 h-8 text-white/60" />
        </div>
      </div>
    </template>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      <!-- Header Stats -->
      <div class="flex justify-between items-center">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-2">
          <span class="text-white/80 text-sm">{{ props.records?.length || 0 }} Generated Honor Lists</span>
        </div>
      </div>

      <!-- Form Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl p-6">
        <div class="flex items-center space-x-3 mb-6">
          <Plus class="w-5 h-5 text-blue-300" />
          <div>
            <h3 class="text-lg font-semibold text-white">Generate New Honor List</h3>
            <p class="text-sm text-white/70 mt-1">Fill out the form below to generate and download a new honor roll certificate</p>
          </div>
        </div>

        <form @submit.prevent="submitForm" class="space-y-6">
          <div class="grid md:grid-cols-2 gap-6">
            <!-- Class -->
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-white">
                Class
                <span class="text-red-400">*</span>
              </label>
              <select
                v-model="form.class_id"
                class="w-full appearance-none backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
                required
                :disabled="props.classes.length === 1"
              >
                <option value="" disabled class="bg-gray-800 text-white">-- Select Class --</option>
                <option
                  v-for="cls in props.classes"
                  :key="cls.id"
                  :value="cls.id"
                  class="bg-gray-800 text-white"
                >
                  Grade {{ cls.grade_level }} - {{ cls.name }}
                </option>
              </select>
            </div>

            <!-- Quarter -->
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-white">
                Quarter
                <span class="text-red-400">*</span>
              </label>
              <select
                v-model="form.quarter"
                class="w-full appearance-none backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
                required
              >
                <option value="" disabled class="bg-gray-800 text-white">-- Select Quarter --</option>
                <option class="bg-gray-800 text-white">1st Quarter</option>
                <option class="bg-gray-800 text-white">2nd Quarter</option>
                <option class="bg-gray-800 text-white">3rd Quarter</option>
                <option class="bg-gray-800 text-white">4th Quarter</option>
              </select>
            </div>

            <!-- School Year -->
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-white">
                School Year
                <span class="text-red-400">*</span>
              </label>
              <input
                v-model="form.school_year"
                placeholder="e.g., 2025 - 2026"
                class="w-full backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
                required
              />
            </div>

            <!-- Principal Name -->
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-white">
                Principal Name
                <span class="text-red-400">*</span>
              </label>
              <input
                v-model="form.principal_name"
                placeholder="e.g., Mrs. Fina Pangan"
                class="w-full backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
                required
              />
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end pt-4">
            <button
              type="submit"
              :disabled="isSubmitting"
              class="backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <Download class="w-4 h-4" />
              <span v-if="!isSubmitting">Generate PDF</span>
              <span v-else class="flex items-center gap-2">
                <Loader class="w-4 h-4 animate-spin" />
                Generating...
              </span>
            </button>
          </div>
        </form>
      </div>

      <!-- Generated Honor Lists Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <Award class="w-5 h-5 text-purple-300" />
            <div>
              <h3 class="text-lg font-semibold text-white">Generated Honor Lists</h3>
              <p class="text-sm text-white/70 mt-1">{{ props.records?.length || 0 }} honor lists available</p>
            </div>
          </div>
        </div>

        <div v-if="props.records?.length">
          <!-- Desktop View -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">#</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Class</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Quarter</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">School Year</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Principal</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                <tr 
                  v-for="(rec, index) in props.records" 
                  :key="rec.id" 
                  class="hover:bg-white/5 transition-colors duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ index + 1 }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                          {{ rec.class?.grade_level }}
                        </div>
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-white">{{ rec.class?.name }}</div>
                        <div class="text-xs text-white/60">Grade {{ rec.class?.grade_level }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ rec.quarter }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ rec.school_year }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-white">{{ rec.principal_name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center space-x-2">
                      <button
                        @click="openEdit(rec)"
                        class="p-2 rounded-lg backdrop-blur-sm bg-blue-500/20 border border-blue-400/30 text-blue-100 hover:bg-blue-500/30 transition-all duration-150"
                        title="Edit"
                      >
                        <Edit class="w-4 h-4" />
                      </button>
                      <a
                        :href="route('honorlist.download', rec.id)"
                        target="_blank"
                        class="p-2 rounded-lg backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100 hover:bg-green-500/30 transition-all duration-150"
                        title="Download"
                      >
                        <Download class="w-4 h-4" />
                      </a>
                      <button
                        @click="deleteRecord(rec.id)"
                        class="p-2 rounded-lg backdrop-blur-sm bg-red-500/20 border border-red-400/30 text-red-100 hover:bg-red-500/30 transition-all duration-150"
                        title="Delete"
                      >
                        <Trash2 class="w-4 h-4" />
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
              v-for="(rec, index) in props.records" 
              :key="rec.id"
              class="p-4 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-2">
                  <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                    {{ rec.class?.grade_level }}
                  </div>
                  <div>
                    <h4 class="text-white font-medium text-sm">{{ rec.school_year }}</h4>
                    <p class="text-xs text-white/70">{{ rec.class?.name }}</p>
                  </div>
                </div>
              </div>
              <div class="space-y-2 mb-3">
                <div class="text-xs text-white/70">Quarter: <span class="text-white">{{ rec.quarter }}</span></div>
                <div class="text-xs text-white/70">Principal: <span class="text-white">{{ rec.principal_name }}</span></div>
              </div>
              <div class="flex items-center space-x-2 pt-3 border-t border-white/10">
                <button
                  @click="openEdit(rec)"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-blue-500/20 border border-blue-400/30 text-blue-100 hover:bg-blue-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <Edit class="w-4 h-4" />
                  Edit
                </button>
                <a
                  :href="route('honorlist.download', rec.id)"
                  target="_blank"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100 hover:bg-green-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <Download class="w-4 h-4" />
                  Download
                </a>
                <button
                  @click="deleteRecord(rec.id)"
                  class="py-2 px-3 rounded-lg backdrop-blur-sm bg-red-500/20 border border-red-400/30 text-red-100 hover:bg-red-500/30 transition-all duration-150 text-sm"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8">
          <Award class="mx-auto h-10 w-10 text-white/40 mb-3" />
          <h3 class="text-sm font-medium text-white/80">No honor lists generated yet</h3>
          <p class="mt-1 text-sm text-white/60">Start by filling out the form above to generate your first honor list.</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- 🧩 Edit Modal -->
  <EditHonorRollModal
    :show="showEdit"
    :classes="classes"
    :record="selectedRecord"
    @close="showEdit = false"
    @updated="refreshRecords"
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