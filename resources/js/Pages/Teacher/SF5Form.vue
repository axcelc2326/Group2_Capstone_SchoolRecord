<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import EditSF5Modal from '@/Components/Teacher/EditSF5Modal.vue'
import { FileText, Download, Edit, Trash2, Plus, Loader } from 'lucide-vue-next'
import Swal from 'sweetalert2'

const props = defineProps({
  classes: Array,
  sf5Records: Array
})

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
const assignedClassId = props.classes.length ? props.classes[0].id : null

const showModal = ref(false)
const selectedRecord = ref(null)
const isSubmitting = ref(false)
const schoolYearInput = ref('')

// School year recommendations
const currentYear = new Date().getFullYear()
const schoolYearOptions = computed(() => {
  const options = []
  for (let i = -2; i <= 2; i++) {
    const year = currentYear + i
    options.push(`${year} - ${year + 1}`)
  }
  return options
})

const setSchoolYear = (schoolYear) => {
  schoolYearInput.value = schoolYear
}

const deleteRecord = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will delete the SF5 record permanently.',
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
      router.delete(`/sf5/${id}`, {
        onSuccess: () => {
          Swal.fire({
            title: 'Deleted!',
            text: 'The SF5 record has been deleted successfully.',
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
            text: 'Failed to delete SF5 record. Please try again.',
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

const editRecord = (record) => {
  selectedRecord.value = record
  showModal.value = true
}

const downloadAgain = (id) => {
  window.open(`/sf5/download-again/${id}`, '_blank')
}

const handleSubmit = (e) => {
  isSubmitting.value = true
  // Form will submit naturally, reset after delay
  setTimeout(() => {
    isSubmitting.value = false
  }, 2000)
}
</script>

<template>
  <Head title="Generate SF5 Report" />
  
  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-1">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-bold text-white">
              SF5 Report Generator
            </h2>
            <p class="text-white/70 mt-1">
              Generate and manage School Form 5 reports
            </p>
          </div>
          <FileText class="w-8 h-8 text-white/60" />
        </div>
      </div>
    </template>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      <!-- Header Stats -->
      <div class="flex justify-between items-center">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-2">
          <span class="text-white/80 text-sm">{{ props.sf5Records?.length || 0 }} Generated Reports</span>
        </div>
      </div>

      <!-- Form Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl p-6">
        <div class="flex items-center space-x-3 mb-6">
          <Plus class="w-5 h-5 text-blue-300" />
          <div>
            <h3 class="text-lg font-semibold text-white">Generate New SF5 Report</h3>
            <p class="text-sm text-white/70 mt-1">Fill out the form below to generate and download a new School Form 5 report</p>
          </div>
        </div>

        <form method="POST" action="/sf5/download" target="_blank" @submit="handleSubmit" class="space-y-6">
          <input type="hidden" name="_token" :value="csrfToken" />
          <input type="hidden" name="class_id" :value="assignedClassId" />

          <div class="grid md:grid-cols-2 gap-6">
            <!-- Region -->
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-white">
                Region
                <span class="text-red-400">*</span>
              </label>
              <select 
                name="region" 
                class="w-full appearance-none backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
                required
              >
                <option value="" disabled class="bg-gray-800 text-white">-- Select Region --</option>
                <option value="NCR" class="bg-gray-800 text-white">NCR - National Capital Region</option>
                <option value="CAR" class="bg-gray-800 text-white">CAR - Cordillera Administrative Region</option>
                <option value="Region I" class="bg-gray-800 text-white">Region I - Ilocos Region</option>
                <option value="Region II" class="bg-gray-800 text-white">Region II - Cagayan Valley</option>
                <option value="Region III" class="bg-gray-800 text-white">Region III - Central Luzon</option>
                <option value="Region IV-A" class="bg-gray-800 text-white">Region IV-A - CALABARZON</option>
                <option value="MIMAROPA" class="bg-gray-800 text-white">MIMAROPA Region</option>
                <option value="Region V" class="bg-gray-800 text-white">Region V - Bicol Region</option>
                <option value="Region VI" class="bg-gray-800 text-white">Region VI - Western Visayas</option>
                <option value="Region VII" selected class="bg-gray-800 text-white">Region VII - Central Visayas</option>
                <option value="Region VIII" class="bg-gray-800 text-white">Region VIII - Eastern Visayas</option>
                <option value="Region IX" class="bg-gray-800 text-white">Region IX - Zamboanga Peninsula</option>
                <option value="Region X" class="bg-gray-800 text-white">Region X - Northern Mindanao</option>
                <option value="Region XI" class="bg-gray-800 text-white">Region XI - Davao Region</option>
                <option value="Region XII" class="bg-gray-800 text-white">Region XII - SOCCSKSARGEN</option>
                <option value="Region XIII" class="bg-gray-800 text-white">Region XIII - Caraga</option>
                <option value="BARMM" class="bg-gray-800 text-white">BARMM - Bangsamoro Autonomous Region</option>
              </select>
            </div>

            <!-- Division -->
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-white">
                Division
                <span class="text-red-400">*</span>
              </label>
              <input
                name="division"
                value="Bohol Division"
                class="w-full backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
                required
              />
            </div>

            <!-- School ID -->
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-white">
                School ID
                <span class="text-red-400">*</span>
              </label>
              <input
                name="school_id"
                class="w-full backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
                required
              />
            </div>

            <!-- School Name -->
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-white">
                School Name
                <span class="text-red-400">*</span>
              </label>
              <input
                name="school_name"
                value="Mater Dei College"
                class="w-full backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
                required
              />
            </div>

            <!-- School Year -->
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-white">
                School Year
                <span class="text-red-400">*</span>
              </label>
              <input
                v-model="schoolYearInput"
                name="school_year"
                placeholder="e.g., 2025 - 2026"
                class="w-full backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
                required
              />
              
              <!-- School Year Recommendations -->
              <div class="mt-2">
                <p class="text-xs text-white/60 mb-2">Quick select:</p>
                <div class="flex gap-2">
                  <button
                    v-for="option in schoolYearOptions"
                    :key="option"
                    type="button"
                    @click="setSchoolYear(option)"
                    class="flex-1 text-xs backdrop-blur-sm bg-white/5 hover:bg-white/10 border border-white/20 text-white px-2 py-2 rounded-lg transition-all duration-150 hover:scale-105 min-w-0"
                  >
                    {{ option }}
                  </button>
                </div>
              </div>
            </div>

            <!-- School Head -->
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-white">
                School Head / SCC Chair
                <span class="text-red-400">*</span>
              </label>
              <input
                name="school_head_chair"
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

      <!-- Generated Reports Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <FileText class="w-5 h-5 text-purple-300" />
            <div>
              <h3 class="text-lg font-semibold text-white">Generated SF5 Reports</h3>
              <p class="text-sm text-white/70 mt-1">{{ props.sf5Records?.length || 0 }} reports available</p>
            </div>
          </div>
        </div>

        <div v-if="props.sf5Records?.length">
          <!-- Desktop View -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">#</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">School Year</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Class</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">School Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Division</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Created</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                <tr 
                  v-for="(record, index) in props.sf5Records" 
                  :key="record.id" 
                  class="hover:bg-white/5 transition-colors duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ index + 1 }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-white">{{ record.school_year }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                          {{ record.class?.grade_level }}
                        </div>
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-white">{{ record.class?.name }}</div>
                        <div class="text-xs text-white/60">{{ record.class?.grade_level }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-white">{{ record.school_name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ record.division }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ new Date(record.created_at).toLocaleDateString() }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center space-x-2">
                      <button
                        @click="editRecord(record)"
                        class="p-2 rounded-lg backdrop-blur-sm bg-blue-500/20 border border-blue-400/30 text-blue-100 hover:bg-blue-500/30 transition-all duration-150"
                        title="Edit"
                      >
                        <Edit class="w-4 h-4" />
                      </button>
                      <button
                        @click="downloadAgain(record.id)"
                        class="p-2 rounded-lg backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100 hover:bg-green-500/30 transition-all duration-150"
                        title="Download"
                      >
                        <Download class="w-4 h-4" />
                      </button>
                      <button
                        @click="deleteRecord(record.id)"
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
              v-for="(record, index) in props.sf5Records" 
              :key="record.id"
              class="p-4 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-2">
                  <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                    {{ record.class?.grade_level }}
                  </div>
                  <div>
                    <h4 class="text-white font-medium text-sm">{{ record.school_year }}</h4>
                    <p class="text-xs text-white/70">{{ record.class?.name }}</p>
                  </div>
                </div>
              </div>
              <div class="space-y-2 mb-3">
                <div class="text-xs text-white/70">School: <span class="text-white">{{ record.school_name }}</span></div>
                <div class="text-xs text-white/70">Division: <span class="text-white">{{ record.division }}</span></div>
                <div class="text-xs text-white/70">Created: <span class="text-white">{{ new Date(record.created_at).toLocaleDateString() }}</span></div>
              </div>
              <div class="flex items-center space-x-2 pt-3 border-t border-white/10">
                <button
                  @click="editRecord(record)"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-blue-500/20 border border-blue-400/30 text-blue-100 hover:bg-blue-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <Edit class="w-4 h-4" />
                  Edit
                </button>
                <button
                  @click="downloadAgain(record.id)"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100 hover:bg-green-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <Download class="w-4 h-4" />
                  Download
                </button>
                <button
                  @click="deleteRecord(record.id)"
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
          <FileText class="mx-auto h-10 w-10 text-white/40 mb-3" />
          <h3 class="text-sm font-medium text-white/80">No SF5 reports generated yet</h3>
          <p class="mt-1 text-sm text-white/60">Start by filling out the form above to generate your first report.</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- Edit Modal -->
  <EditSF5Modal
    v-if="selectedRecord"
    :show="showModal"
    :record="selectedRecord"
    @close="showModal = false"
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