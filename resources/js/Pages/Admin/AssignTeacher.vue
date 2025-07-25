<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  teachers: Array,
  classes: Array,
  assignments: Object, // paginated assignments
})

const form = useForm({
  class_id: '',
  teacher_id: '',
})

const isSubmitting = ref(false)

const submit = async () => {
  isSubmitting.value = true
  try {
    await form.post(route('admin.assign-teacher.submit'))
  } finally {
    isSubmitting.value = false
  }
}

// Computed properties for better data handling
const availableClasses = computed(() => 
  props.classes?.map(cls => ({
    ...cls,
    displayName: `${cls.name} (Grade ${cls.grade_level || '?'})`,
    status: cls.teacher?.name ? `Currently: ${cls.teacher.name}` : 'Unassigned'
  })) || []
)

const availableTeachers = computed(() => props.teachers || [])

const hasAssignments = computed(() => 
  props.assignments?.data && props.assignments.data.length > 0
)
</script>

<template>
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header -->
        <div class="text-center">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Teacher Assignment Portal</h1>
          <p class="text-gray-600">Manage class-teacher assignments efficiently</p>
        </div>

        <!-- Assignment Form Card -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
          <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white flex items-center gap-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
              Assign Teacher to Class
            </h2>
          </div>

          <form @submit.prevent="submit" class="p-6 space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
              <!-- Select Class -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                  Select Class
                  <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <select 
                    v-model="form.class_id" 
                    class="w-full appearance-none bg-white border-2 border-gray-200 rounded-xl px-4 py-3 pr-10 text-gray-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200"
                    required
                  >
                    <option value="" disabled>Choose a class...</option>
                    <option 
                      v-for="cls in availableClasses" 
                      :key="cls.id" 
                      :value="cls.id"
                      class="py-2"
                    >
                      {{ cls.displayName }} — {{ cls.status }}
                    </option>
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Select Teacher -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                  Select Teacher
                  <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <select 
                    v-model="form.teacher_id" 
                    class="w-full appearance-none bg-white border-2 border-gray-200 rounded-xl px-4 py-3 pr-10 text-gray-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200"
                    required
                  >
                    <option value="" disabled>Choose a teacher...</option>
                    <option 
                      v-for="teacher in availableTeachers" 
                      :key="teacher.id" 
                      :value="teacher.id"
                      class="py-2"
                    >
                      {{ teacher.name }}
                    </option>
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
              <button
                type="submit"
                :disabled="form.processing || isSubmitting"
                class="relative bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold px-8 py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:ring-4 focus:ring-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 transition-all duration-200 shadow-lg"
              >
                <span v-if="!form.processing && !isSubmitting" class="flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Assign Teacher
                </span>
                <span v-else class="flex items-center gap-2">
                  <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Processing...
                </span>
              </button>
            </div>
          </form>
        </div>

        <!-- Assignments Table Card -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
          <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white flex items-center gap-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
              Current Assignments
            </h2>
          </div>

          <div class="p-6">
            <div v-if="hasAssignments" class="overflow-hidden rounded-xl border border-gray-200">
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead class="bg-gray-50/80">
                    <tr>
                      <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Class Information
                      </th>
                      <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Grade Level
                      </th>
                      <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Assigned Teacher
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100">
                    <tr
                      v-for="cls in props.assignments.data"
                      :key="cls.id"
                      class="hover:bg-blue-50/50 transition-colors duration-150"
                    >
                      <td class="px-6 py-4">
                        <div class="font-semibold text-gray-900">{{ cls.name }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                          Grade {{ cls.grade_level }}
                        </span>
                      </td>
                      <td class="px-6 py-4">
                        <div v-if="cls.teacher?.name" class="flex items-center gap-2">
                          <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                          </div>
                          <span class="font-medium text-gray-900">{{ cls.teacher.name }}</span>
                        </div>
                        <div v-else class="flex items-center gap-2 text-gray-500">
                          <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                          </div>
                          <span class="italic">No teacher assigned</span>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div v-else class="text-center py-12">
              <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No assignments found</h3>
              <p class="text-gray-500">Start by assigning teachers to classes using the form above.</p>
            </div>

            <!-- Enhanced Pagination -->
            <div v-if="props.assignments?.links && props.assignments.links.length > 3" class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
              <div class="text-sm text-gray-500">
                Showing {{ props.assignments.from || 0 }} to {{ props.assignments.to || 0 }} 
                of {{ props.assignments.total || 0 }} results
              </div>
              
              <nav class="flex items-center space-x-1">
                <template v-for="(link, index) in props.assignments.links" :key="index">
                  <span
                    v-if="!link.url"
                    class="px-3 py-2 text-gray-400 cursor-default text-sm"
                    v-html="link.label"
                  ></span>
                  <Link
                    v-else
                    :href="link.url"
                    class="px-3 py-2 text-sm rounded-lg transition-all duration-200 hover:scale-105"
                    :class="link.active 
                      ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' 
                      : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 hover:border-gray-300'"
                    v-html="link.label"
                  />
                </template>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>