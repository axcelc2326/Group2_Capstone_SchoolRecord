<template>
  <!-- Modal Overlay -->
  <Teleport to="body">
    <Transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click="handleBackdropClick"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
          <Transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="opacity-0 scale-95 translate-y-4"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-4"
          >
            <div
              v-if="show"
              ref="modalContent"
              class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg p-8 shadow-2xl border border-gray-200/50 dark:border-gray-700/50 transition-all"
              @click.stop
            >
              <!-- Gradient overlay for visual depth -->
              <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-blue-900/20 dark:to-purple-900/20 pointer-events-none"></div>
              
              <form @submit.prevent="submit" class="relative space-y-6">
                <!-- Header -->
                <div class="text-center space-y-2">
                  <div class="w-12 h-12 mx-auto bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </div>
                  <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit SF5 Record</h2>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Update school form 5 information
                  </p>
                </div>

                <!-- Form Fields -->
                <div class="space-y-4">
                  <!-- Region Dropdown -->
                  <div class="space-y-2">
                    <label for="region" class="text-sm font-medium text-gray-700 dark:text-gray-300">Region</label>
                    <div class="relative group">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                      </div>
                      <select
                        id="region"
                        v-model="form.region"
                        class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-all duration-200 appearance-none"
                        required
                      >
                        <option value="">-- Select Region --</option>
                        <option value="NCR">NCR - National Capital Region</option>
                        <option value="CAR">CAR - Cordillera Administrative Region</option>
                        <option value="Region I">Region I - Ilocos Region</option>
                        <option value="Region II">Region II - Cagayan Valley</option>
                        <option value="Region III">Region III - Central Luzon</option>
                        <option value="Region IV-A">Region IV-A - CALABARZON</option>
                        <option value="MIMAROPA">MIMAROPA Region</option>
                        <option value="Region V">Region V - Bicol Region</option>
                        <option value="Region VI">Region VI - Western Visayas</option>
                        <option value="Region VII">Region VII - Central Visayas</option>
                        <option value="Region VIII">Region VIII - Eastern Visayas</option>
                        <option value="Region IX">Region IX - Zamboanga Peninsula</option>
                        <option value="Region X">Region X - Northern Mindanao</option>
                        <option value="Region XI">Region XI - Davao Region</option>
                        <option value="Region XII">Region XII - SOCCSKSARGEN</option>
                        <option value="Region XIII">Region XIII - Caraga</option>
                        <option value="BARMM">BARMM - Bangsamoro Autonomous Region in Muslim Mindanao</option>
                      </select>
                    </div>
                    <div v-if="form.errors.region" class="text-red-500 text-sm">
                      {{ form.errors.region }}
                    </div>
                  </div>

                  <!-- Division -->
                  <div class="space-y-2">
                    <label for="division" class="text-sm font-medium text-gray-700 dark:text-gray-300">Division</label>
                    <div class="relative group">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                      </div>
                      <input
                        id="division"
                        v-model="form.division"
                        type="text"
                        class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                        placeholder="Enter division"
                      />
                    </div>
                    <div v-if="form.errors.division" class="text-red-500 text-sm">
                      {{ form.errors.division }}
                    </div>
                  </div>

                  <!-- School ID -->
                  <div class="space-y-2">
                    <label for="school_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">School ID</label>
                    <div class="relative group">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                      </div>
                      <input
                        id="school_id"
                        v-model="form.school_id"
                        type="text"
                        class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                        placeholder="Enter school ID"
                      />
                    </div>
                    <div v-if="form.errors.school_id" class="text-red-500 text-sm">
                      {{ form.errors.school_id }}
                    </div>
                  </div>

                  <!-- School Name -->
                  <div class="space-y-2">
                    <label for="school_name" class="text-sm font-medium text-gray-700 dark:text-gray-300">School Name</label>
                    <div class="relative group">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                      </div>
                      <input
                        id="school_name"
                        v-model="form.school_name"
                        type="text"
                        class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                        placeholder="Enter school name"
                      />
                    </div>
                    <div v-if="form.errors.school_name" class="text-red-500 text-sm">
                      {{ form.errors.school_name }}
                    </div>
                  </div>

                  <!-- School Year -->
                  <div class="space-y-2">
                    <label for="school_year" class="text-sm font-medium text-gray-700 dark:text-gray-300">School Year</label>
                    <div class="relative group">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                      </div>
                      <input
                        id="school_year"
                        v-model="form.school_year"
                        type="text"
                        class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                        placeholder="e.g., 2024-2025"
                      />
                    </div>
                    <div v-if="form.errors.school_year" class="text-red-500 text-sm">
                      {{ form.errors.school_year }}
                    </div>
                  </div>

                  <!-- School Head / Chair -->
                  <div class="space-y-2">
                    <label for="school_head_chair" class="text-sm font-medium text-gray-700 dark:text-gray-300">School Head / SCC Chair</label>
                    <div class="relative group">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                      </div>
                      <input
                        id="school_head_chair"
                        v-model="form.school_head_chair"
                        type="text"
                        class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                        placeholder="Enter name"
                      />
                    </div>
                    <div v-if="form.errors.school_head_chair" class="text-red-500 text-sm">
                      {{ form.errors.school_head_chair }}
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 pt-4">
                  <!-- Cancel Button -->
                  <button
                    type="button"
                    @click="handleClose"
                    class="flex-1 py-3 px-6 bg-gray-500/90 hover:bg-gray-600 focus:ring-4 focus:ring-gray-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                  >
                    <div class="flex items-center justify-center space-x-2">
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                      <span>Cancel</span>
                    </div>
                  </button>

                  <!-- Submit Button -->
                  <button
                    type="submit"
                    class="flex-1 py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                    :class="{ 'opacity-75 cursor-not-allowed transform-none shadow-lg': form.processing }"
                    :disabled="form.processing"
                  >
                    <div class="flex items-center justify-center space-x-2">
                      <template v-if="form.processing">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                        <span>Updating...</span>
                      </template>
                      <template v-else>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Update</span>
                      </template>
                    </div>
                  </button>
                </div>

                <!-- Close Button -->
                <button
                  type="button"
                  @click="handleClose"
                  class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </form>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch, ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  show: Boolean,
  record: Object,
})

const emit = defineEmits(['close'])

const modalContent = ref(null)

const form = useForm({
  region: '',
  division: '',
  school_id: '',
  school_name: '',
  school_year: '',
  school_head_chair: '',
})

// Auto-populate when record changes
watch(
  () => props.record,
  (record) => {
    if (record) {
      form.region = record.region
      form.division = record.division
      form.school_id = record.school_id
      form.school_name = record.school_name
      form.school_year = record.school_year
      form.school_head_chair = record.school_head_chair
    }
  },
  { immediate: true }
)

const submit = () => {
  form.put(route('sf5.update', props.record.id), {
    onSuccess: () => handleClose(),
  })
}

const handleClose = () => {
  form.reset()
  emit('close')
}

function handleBackdropClick(event) {
  if (event.target === event.currentTarget) {
    handleClose()
  }
}

// Handle ESC key to close modal
function handleEscape(event) {
  if (event.key === 'Escape') {
    handleClose()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})
</script>