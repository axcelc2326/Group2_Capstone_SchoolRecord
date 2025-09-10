<template>
  <div 
    v-if="show"
    class="fixed inset-0 z-[9999] flex items-center justify-center"
    @click.self="$emit('close')"
  >
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
    
    <!-- Modal Content -->
    <div class="relative bg-slate-800 border border-gray-600 rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
          <div class="p-2 bg-purple-500/20 rounded-lg">
            <svg class="w-6 h-6 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-white">Student Settings</h3>
        </div>
        <button
          @click="$emit('close')"
          class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors duration-150"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Settings Options -->
      <div class="space-y-3">
        <!-- Unapprove All -->
        <button
          @click="$emit('unapprove-all')"
          class="w-full flex items-center px-4 py-4 text-left text-red-100 bg-red-500/10 hover:bg-red-500/20 border border-red-400/30 hover:border-red-400/50 rounded-xl transition-all duration-150 group"
        >
          <div class="flex-shrink-0 p-2 bg-red-500/20 rounded-lg mr-4 group-hover:bg-red-500/30 transition-colors">
            <svg class="w-5 h-5 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
            </svg>
          </div>
          <div>
            <div class="font-medium">Unapprove All Students</div>
            <div class="text-sm text-red-200/70">Remove approval status from all students</div>
          </div>
        </button>

        <!-- Clear All Grades -->
        <button
          @click="$emit('clear-all-grades')"
          class="w-full flex items-center px-4 py-4 text-left text-yellow-100 bg-yellow-500/10 hover:bg-yellow-500/20 border border-yellow-400/30 hover:border-yellow-400/50 rounded-xl transition-all duration-150 group"
        >
          <div class="flex-shrink-0 p-2 bg-yellow-500/20 rounded-lg mr-4 group-hover:bg-yellow-500/30 transition-colors">
            <svg class="w-5 h-5 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </div>
          <div>
            <div class="font-medium">Clear All Grades</div>
            <div class="text-sm text-yellow-200/70">Delete all grades for all students</div>
          </div>
        </button>
      </div>

      <!-- Footer -->
      <div class="mt-6 pt-4 border-t border-gray-600">
        <p class="text-xs text-gray-400 text-center">
          These actions cannot be undone. Please proceed with caution.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false,
  }
})

defineEmits(['close', 'unapprove-all', 'clear-all-grades'])
</script>