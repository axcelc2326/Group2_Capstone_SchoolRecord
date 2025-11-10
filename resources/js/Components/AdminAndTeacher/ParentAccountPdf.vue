<template>
  <div 
    v-if="show" 
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
  >
    <div class="bg-gray-900 border border-white/20 rounded-2xl shadow-xl w-full max-w-md p-6 relative backdrop-blur-md">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
          <FileText class="w-6 h-6 text-green-400" />
          <h2 class="text-xl font-semibold text-white">
            Generate Filtered PDF
          </h2>
        </div>
        <button 
          @click="closeModal"
          class="text-white/60 hover:text-white transition-colors duration-200 p-1 rounded-lg hover:bg-white/10"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="downloadPDF" class="space-y-4">
        <!-- Class Filter -->
        <div>
          <label for="class_id" class="block text-white text-sm font-medium mb-2">
            Select Class
          </label>
          <select 
            id="class_id"
            v-model="filters.class_id" 
            class="block w-full pl-3 pr-10 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-800 dark:text-white transition-all duration-200 appearance-none"
          >
            <option value="" class="bg-gray-800 text-white">All Classes</option>
            <option 
              v-for="cls in classes" 
              :key="cls.id" 
              :value="cls.id"
              class="bg-gray-800 text-white"
            >
              {{ cls.name }} (Grade {{ cls.grade_level }})
            </option>
          </select>
        </div>

        <!-- Info Text -->
        <div class="bg-blue-500/10 border border-blue-400/30 rounded-lg p-3">
          <p class="text-sm text-blue-200">
            This will generate a PDF report of all parent accounts for the selected class.
          </p>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-3 pt-4">
          <button 
            type="button"
            @click="closeModal"
            class="px-4 py-2 rounded-lg text-white/80 bg-white/10 hover:bg-white/20 border border-white/20 transition-all duration-200 font-medium"
          >
            Cancel
          </button>

          <button 
            type="submit"
            class="px-4 py-2 rounded-lg text-white bg-green-600 hover:bg-green-700 transition-all duration-200 font-medium flex items-center gap-2"
          >
            <FileText class="w-4 h-4" />
            Download PDF
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { FileText } from 'lucide-vue-next'

const props = defineProps({
  show: Boolean,
  classes: Array,
})

const emit = defineEmits(['close'])

const filters = ref({
  class_id: ''
})

// Generate filtered PDF
const downloadPDF = () => {
  const params = new URLSearchParams({
    class_id: filters.value.class_id || '',
  }).toString()

  // Open new tab for PDF download
  window.open(route('parent-accounts.downloadFiltered') + '?' + params, '_blank')
  closeModal()
}

// Close modal and reset filters
const closeModal = () => {
  filters.value = { class_id: '' }
  emit('close')
}

// Reset filters when modal opens/closes
watch(() => props.show, (newVal) => {
  if (!newVal) {
    filters.value = { class_id: '' }
  }
})
</script>