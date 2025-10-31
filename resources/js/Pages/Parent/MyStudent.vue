<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { FileText, Plus } from 'lucide-vue-next'

const { props } = usePage()
const students = ref(props.students)
const classes = ref(props.classes)

// Total students count
const totalStudents = computed(() => students.value.length)
</script>

<template>
  <Head title="Student Management" />

  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-1">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-bold text-white">
              Student Management
            </h2>
            <p class="text-white/70 mt-1">
              Manage and monitor student registrations
            </p>
          </div>
          <FileText class="w-8 h-8 text-white/60" />
        </div>
      </div>
    </template>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      <!-- Header Actions -->
      <div class="flex justify-between items-center">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-2">
          <span class="text-white/80 text-sm">{{ totalStudents }} Students</span>
        </div>
      </div>

      <!-- Mobile Stats (visible on small screens) -->
      <div class="md:hidden">
        <div class="backdrop-blur-md bg-blue-500/10 border border-blue-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-blue-100">{{ totalStudents }}</div>
          <div class="text-sm text-blue-200">Total Students</div>
        </div>
      </div>

      <!-- Students List -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <FileText class="w-5 h-5 text-purple-300" />
            <div>
              <h3 class="text-lg font-semibold text-white">Registered Students</h3>
              <p class="text-sm text-white/70 mt-1">{{ totalStudents }} students registered</p>
            </div>
          </div>
        </div>

        <!-- Desktop View -->
        <div class="hidden md:block">
          <div class="divide-y divide-white/10">
            <div 
              v-for="student in students" 
              :key="student.id"
              class="p-6 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <div class="flex items-center space-x-3 mb-3">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium text-sm">
                      {{ student.first_name.charAt(0) }}{{ student.last_name.charAt(0) }}
                    </div>
                    <div>
                      <h4 class="text-lg font-semibold text-white">{{ student.first_name }} {{ student.middle_name }} {{ student.last_name }}</h4>
                      <div class="flex items-center space-x-4 mt-1">
                        <p class="text-sm text-white/60">
                          LRN: {{ student.lrn }}
                        </p>
                        <p class="text-sm text-white/60">
                          Class: {{ student.class?.name }} (Grade {{ student.class?.grade_level }})
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="ml-4">
                  <span
                    :class="student.approved_by_teacher 
                      ? 'bg-emerald-500/20 text-emerald-100 border-emerald-400/30' 
                      : 'bg-amber-500/20 text-amber-100 border-amber-400/30'"
                    class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium border backdrop-blur-sm"
                  >
                    <span 
                      :class="student.approved_by_teacher ? 'bg-emerald-400' : 'bg-amber-400'"
                      class="w-2 h-2 rounded-full mr-2"
                    ></span>
                    {{ student.approved_by_teacher ? 'Enrolled' : 'Unenrolled' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-white/10">
          <div 
            v-for="student in students" 
            :key="student.id"
            class="p-4 hover:bg-white/5 transition-colors duration-150"
          >
            <div class="flex items-start space-x-3 mb-3">
              <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium text-sm">
                {{ student.first_name.charAt(0) }}{{ student.last_name.charAt(0) }}
              </div>
              <div class="flex-1">
                <h4 class="text-white font-medium mb-1">{{ student.first_name }} {{ student.last_name }}</h4>
                <p class="text-sm text-white/60 mb-2">
                  LRN: {{ student.lrn }}
                </p>
                <p class="text-sm text-white/60 mb-2">
                  {{ student.class?.name }} (Grade {{ student.class?.grade_level }})
                </p>
                <span
                  :class="student.approved_by_teacher 
                    ? 'bg-emerald-500/20 text-emerald-100 border-emerald-400/30' 
                    : 'bg-amber-500/20 text-amber-100 border-amber-400/30'"
                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border backdrop-blur-sm"
                >
                  <span 
                    :class="student.approved_by_teacher ? 'bg-emerald-400' : 'bg-amber-400'"
                    class="w-1.5 h-1.5 rounded-full mr-2"
                  ></span>
                  {{ student.approved_by_teacher ? 'Enrolled' : 'Unenrolled' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="students.length === 0" class="text-center py-8">
          <FileText class="mx-auto h-10 w-10 text-white/40 mb-3" />
          <h3 class="text-sm font-medium text-white/80">
            No students registered yet
          </h3>
          <p class="mt-1 text-sm text-white/60">
            Students will appear here once they are registered.
          </p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
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
button:focus-visible {
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