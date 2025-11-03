<template>
  <div 
    v-if="show"
    class="fixed inset-0 z-[10000] flex items-center justify-center"
    @click.self="$emit('close')"
  >
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
    
    <!-- Modal Content -->
    <div class="relative bg-slate-800 border border-gray-600 rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
          <div class="p-2 bg-green-500/20 rounded-lg">
            <svg class="w-6 h-6 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-white">Promote Students</h3>
        </div>
        <button
          @click="handleClose"
          class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors duration-150"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-400 mx-auto mb-4"></div>
        <p class="text-gray-300">Loading available classes...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-500/20 mb-4">
          <svg class="w-8 h-8 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <h4 class="text-lg font-medium text-white mb-2">Unable to Load Classes</h4>
        <p class="text-gray-400 text-sm mb-4">
          {{ error }}
        </p>
        <button
          @click="fetchAvailableClasses"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-150 flex items-center space-x-2 mx-auto"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span>Try Again</span>
        </button>
      </div>

      <!-- Empty State -->
      <div v-else-if="availableClasses.length === 0" class="text-center py-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-500/20 mb-4">
          <svg class="w-8 h-8 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <h4 class="text-lg font-medium text-white mb-2">No Classes Available</h4>
        <p class="text-gray-400 text-sm mb-4">
          There are no other classes available for promotion. Please contact the administrator to create additional classes.
        </p>
        <button
          @click="handleClose"
          class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-150"
        >
          Close
        </button>
      </div>

      <!-- Classes List -->
      <div v-else class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-300 mb-2">
            Select Target Class
          </label>
          <select
            v-model="selectedClassId"
            class="w-full bg-slate-700 border border-gray-600 rounded-lg px-3 py-2.5 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-150"
          >
            <option value="" disabled>Choose a class...</option>
            <option 
              v-for="classItem in availableClasses" 
              :key="classItem.id" 
              :value="classItem.id"
            >
              {{ classItem.display_text || `${classItem.name} (Grade ${classItem.grade_level})` }}
            </option>
          </select>
          
          <!-- Selection Info -->
          <div v-if="selectedClassId" class="mt-2 text-xs text-green-400">
            ✓ Class selected
          </div>
          <div v-else class="mt-2 text-xs text-gray-400">
            Please select a target class
          </div>
        </div>

        <!-- Promotion Info -->
        <div class="bg-blue-500/10 border border-blue-400/30 rounded-lg p-4">
          <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-blue-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-blue-200">
              <p class="font-medium mb-1">What happens when promoting:</p>
              <ul class="list-disc list-inside space-y-1 text-blue-200/80">
                <li>Students with grade remarks will be moved to the selected class</li>
                <li>Students will be automatically approved in the new class</li>
                <li>All existing grades and grade remarks will be cleared</li>
                <li>This action cannot be undone</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div v-if="availableClasses.length > 0" class="mt-6 flex justify-end space-x-3">
        <button
          @click="handleClose"
          class="px-4 py-2 text-gray-300 hover:text-white border border-gray-600 hover:border-gray-500 rounded-lg transition-colors duration-150"
        >
          Cancel
        </button>
        <button
          @click="confirmPromotion"
          :disabled="!selectedClassId || promoting"
          :class="{
            'bg-green-600 hover:bg-green-700 cursor-pointer': selectedClassId && !promoting,
            'bg-green-600/50 cursor-not-allowed': !selectedClassId || promoting
          }"
          class="px-4 py-2 text-white rounded-lg transition-colors duration-150 flex items-center space-x-2 min-w-24 justify-center"
        >
          <svg v-if="promoting" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <span>{{ promoting ? 'Promoting...' : 'Promote Students' }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  }
})

const emit = defineEmits(['close', 'promote'])

const selectedClassId = ref('')
const availableClasses = ref([])
const loading = ref(false)
const promoting = ref(false)
const error = ref('')

// Fetch available classes when modal opens
watch(() => props.show, async (newVal) => {
  if (newVal) {
    await fetchAvailableClasses()
  } else {
    // Reset when modal closes
    resetModal()
  }
})

const resetModal = () => {
  selectedClassId.value = ''
  availableClasses.value = []
  loading.value = false
  promoting.value = false
  error.value = ''
}

const fetchAvailableClasses = async () => {
  loading.value = true
  error.value = ''
  try {
    console.log('Fetching available classes...')
    const response = await axios.get(route('teacher.classes.available'))
    console.log('Classes received:', response.data)
    availableClasses.value = response.data
  } catch (error) {
    console.error('Error fetching classes:', error)
    error.value = 'Failed to load available classes. Please try again.'
    availableClasses.value = []
  } finally {
    loading.value = false
  }
}

const handleClose = () => {
  resetModal()
  emit('close')
}

const confirmPromotion = async () => {
  if (!selectedClassId.value) return
  
  promoting.value = true
  try {
    console.log('Promoting to class:', selectedClassId.value)
    emit('promote', selectedClassId.value)
    // Don't reset selection here - let parent handle the close after promotion
  } catch (error) {
    console.error('Error in promotion:', error)
  } finally {
    promoting.value = false
  }
}
</script>