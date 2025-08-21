<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  students: Array,
  pendingCount: Number,
});

// Statistics
const totalStudents = computed(() => props.students.length);

const approve = (studentId) => {
  router.put(route('students.approve', { student: studentId }));
};

const deny = (studentId) => {
  if (
    confirm(
      'Are you sure you want to deny this student? This will remove them from your class.'
    )
  ) {
    router.put(route('students.deny', { student: studentId }));
  }
};
</script>

<template>
  <Head title="Approve Students" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0">
        <h2 class="text-2xl font-bold text-white bg-gradient-to-r from-white to-white/70 bg-clip-text text-transparent">
          Approve Students
        </h2>
        <div class="flex items-center space-x-4">
          <!-- Quick Stats -->
          <div class="hidden md:flex items-center space-x-3 text-sm">
            <div class="backdrop-blur-sm bg-amber-500/20 border border-amber-300/30 px-3 py-1 rounded-full">
              <span class="text-amber-100">{{ pendingCount }} Pending</span>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Mobile Stats (visible on small screens) -->
      <div class="md:hidden grid grid-cols-1 gap-4">
        <div class="backdrop-blur-md bg-amber-500/10 border border-amber-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-amber-100">{{ pendingCount }}</div>
          <div class="text-sm text-amber-200">Pending Approval</div>
        </div>
      </div>

      <!-- Students Table -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <h3 class="text-lg font-semibold text-white">Pending Student Approvals</h3>
          <p class="text-sm text-white/70 mt-1">{{ totalStudents }} students awaiting approval</p>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
          <table class="min-w-full divide-y divide-white/10">
            <thead class="bg-white/5">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Student</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Parent</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Class</th>
                <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              <tr 
                v-for="student in students" 
                :key="student.id" 
                class="hover:bg-white/5 transition-colors duration-150"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-gradient-to-r from-emerald-500 to-blue-600 flex items-center justify-center text-white font-medium text-sm">
                        {{ student.first_name.charAt(0) }}{{ student.last_name.charAt(0) }}
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-white">{{ student.first_name }} {{ student.last_name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-white">{{ student.parent_name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-white">{{ student.class?.name }}</div>
                  <div class="text-xs text-white/60">Grade {{ student.class?.grade_level }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                  <button
                    class="inline-flex items-center px-4 py-2 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-100 border border-emerald-400/30 hover:border-emerald-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                    @click="approve(student.id)"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Approve
                  </button>
                  <button
                    class="inline-flex items-center px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-100 border border-red-400/30 hover:border-red-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                    @click="deny(student.id)"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Deny
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-white/10">
          <div 
            v-for="student in students" 
            :key="student.id"
            class="p-6 hover:bg-white/5 transition-colors duration-150"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center text-white font-medium">
                  {{ student.first_name.charAt(0) }}{{ student.last_name.charAt(0) }}
                </div>
                <div>
                  <h4 class="text-white font-medium">{{ student.first_name }} {{ student.last_name }}</h4>
                  <p class="text-sm text-white/70">Parent: {{ student.parent_name }}</p>
                  <p class="text-sm text-white/70">{{ student.class?.name }} (Grade {{ student.class?.grade_level }})</p>
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border backdrop-blur-sm mt-2 bg-amber-500/20 text-amber-100 border-amber-400/30">
                    <span class="w-1.5 h-1.5 rounded-full mr-2 bg-amber-400"></span>
                    Pending Approval
                  </span>
                </div>
              </div>
            </div>
            <div class="mt-4 flex space-x-2">
              <button
                class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-100 border border-emerald-400/30 hover:border-emerald-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                @click="approve(student.id)"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Approve
              </button>
              <button
                class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-100 border border-red-400/30 hover:border-red-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                @click="deny(student.id)"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Deny
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="students.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-white/80">
            No pending student approvals
          </h3>
          <p class="mt-1 text-sm text-white/60">
            All students have been approved or there are no pending requests.
          </p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>