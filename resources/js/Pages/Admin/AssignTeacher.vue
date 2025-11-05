<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Users, GraduationCap, UserCheck, Plus, Loader } from 'lucide-vue-next'
import Swal from 'sweetalert2'

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
    // Convert empty string to null for unassigning
    const formData = {
      ...form,
      teacher_id: form.teacher_id === '' ? null : form.teacher_id
    }
    
    await form.post(route('admin.assign-teacher.submit'), {
      data: formData,
      onSuccess: () => {
        const message = formData.teacher_id === null 
          ? 'Teacher has been successfully unassigned from the class.'
          : 'Teacher has been successfully assigned to the class.'
        
        const title = formData.teacher_id === null 
          ? 'Teacher Unassigned!'
          : 'Teacher Assigned!'
        
        Swal.fire({
          title: title,
          text: message,
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
            icon: '!border-none !bg-transparent'
          },
          buttonsStyling: false,
          confirmButtonText: 'OK',
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        })
        // Optionally reset the form after successful submission
        form.reset()
      },
      onError: (errors) => {
        // Show specific error for assignment conflicts
        if (form.errors.teacher_id && form.errors.teacher_id.toLowerCase().includes('already')) {
          Swal.fire({
            title: 'Assignment Conflict',
            text: 'This teacher is already assigned to a class. Please choose a different teacher.',
            icon: 'warning',
            background: '#1f2937',
            color: '#f9fafb',
            backdrop: 'rgba(0, 0, 0, 0.7)',
            showClass: {
              popup: 'animate__animated animate__shakeX animate__faster'
            },
            customClass: {
              popup: 'rounded-2xl shadow-2xl border border-yellow-500/50 backdrop-blur-lg',
              title: 'text-2xl font-bold text-white mb-2',
              htmlContainer: 'text-gray-300',
              confirmButton: 'py-3 px-6 bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 focus:ring-4 focus:ring-yellow-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200',
              icon: '!border-none !bg-transparent text-yellow-500'
            },
            buttonsStyling: false,
            confirmButtonText: 'OK'
          })
        } 
        // Show general error for other validation issues
        else if (Object.keys(errors).length > 0) {
          Swal.fire({
            title: 'Operation Failed',
            text: formData.teacher_id === null 
              ? 'Failed to unassign teacher. Please try again.'
              : 'Failed to assign teacher. Please check your selection and try again.',
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
        }
      }
    })
  } catch (error) {
    // Handle unexpected errors
    Swal.fire({
      title: 'Unexpected Error',
      text: 'An unexpected error occurred. Please try again.',
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
      confirmButtonText: 'OK'
    })
  } finally {
    isSubmitting.value = false
  }
}

// ✅ Build better class display
const availableClasses = computed(() => 
  props.classes?.map(cls => ({
    ...cls,
    displayName: `${cls.name} (Grade ${cls.grade_level || '?'})`,
    status: cls.teacher?.name ? `Currently: ${cls.teacher.name}` : 'Unassigned'
  })) || []
)

// ✅ Only show teachers that are NOT already assigned
const availableTeachers = computed(() => {
  if (!props.teachers || !props.classes) return []

  // Gather IDs of teachers already assigned
  const assignedTeacherIds = props.classes
    .filter(c => c.teacher_id)
    .map(c => c.teacher_id)

  return props.teachers.filter(teacher => {
    // If this teacher is already assigned but the form is editing that same class, allow
    if (form.class_id) {
      const selectedClass = props.classes.find(c => c.id === form.class_id)
      if (selectedClass && selectedClass.teacher_id === teacher.id) {
        return true
      }
    }
    // Otherwise exclude assigned teachers
    return !assignedTeacherIds.includes(teacher.id)
  })
})

const hasAssignments = computed(() => 
  props.assignments?.data && props.assignments.data.length > 0
)

const totalAssignments = computed(() => props.assignments?.total || 0)
</script>

<template>
  <Head title="Teacher Assignment Portal" />
  
  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-1">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-bold text-white">
              Teacher Assignment Portal
            </h2>
            <p class="text-white/70 mt-1">
              Manage class-teacher assignments efficiently
            </p>
          </div>
          <UserCheck class="w-8 h-8 text-white/60" />
        </div>
      </div>
    </template>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      <!-- Header Actions -->
      <div class="flex justify-between items-center">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-2">
          <span class="text-white/80 text-sm">{{ totalAssignments }} Assignments</span>
        </div>
      </div>

      <!-- Assignment Form Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl p-6">
        <div class="flex items-center space-x-3 mb-6">
          <UserCheck class="w-5 h-5 text-blue-300" />
          <div>
            <h3 class="text-lg font-semibold text-white">Assign Teacher to Class</h3>
            <p class="text-sm text-white/70 mt-1">Manage class-teacher assignments efficiently</p>
          </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid md:grid-cols-2 gap-6">
            <!-- Select Class -->
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-white">
                Select Class
                <span class="text-red-400">*</span>
              </label>
              <div class="relative">
                <select 
                  v-model="form.class_id" 
                  class="w-full appearance-none backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 pr-10 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
                  required
                >
                  <option value="" disabled class="bg-gray-800 text-white">Choose a class...</option>
                  <option 
                    v-for="cls in availableClasses" 
                    :key="cls.id" 
                    :value="cls.id"
                    class="py-2 bg-gray-800 text-white"
                  >
                    {{ cls.displayName }} — {{ cls.status }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Select Teacher -->
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-white">
                Select Teacher
                <span class="text-red-400">*</span>
              </label>
              <div class="relative">
                <select 
                  v-model="form.teacher_id" 
                  class="w-full appearance-none backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 pr-10 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
                  required
                >
                  <option value="" disabled class="bg-gray-800 text-white">Choose a teacher...</option>
                  <!-- ✅ Allow NULL choice -->
                  <option :value="null" class="py-2 italic text-gray-400 bg-gray-800">None (Unassign Teacher)</option>

                  <option 
                    v-for="teacher in availableTeachers" 
                    :key="teacher.id" 
                    :value="teacher.id"
                    class="py-2 bg-gray-800 text-white"
                  >
                    {{ teacher.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end pt-4">
            <button
              type="submit"
              :disabled="form.processing || isSubmitting"
              class="backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <Plus class="w-4 h-4" />
              <span v-if="!form.processing && !isSubmitting">Assign Teacher</span>
              <span v-else class="flex items-center gap-2">
                <Loader class="w-4 h-4 animate-spin" />
                Processing...
              </span>
            </button>
          </div>
        </form>
      </div>

      <!-- Current Assignments Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <Users class="w-5 h-5 text-purple-300" />
            <div>
              <h3 class="text-lg font-semibold text-white">Current Assignments</h3>
              <p class="text-sm text-white/70 mt-1">{{ hasAssignments ? props.assignments.data.length : 0 }} assignments shown</p>
            </div>
          </div>
        </div>

        <div v-if="hasAssignments">
          <!-- Desktop View -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">#</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Class</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Grade Level</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Teacher</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                <tr 
                  v-for="(cls, index) in props.assignments.data" 
                  :key="cls.id" 
                  class="hover:bg-white/5 transition-colors duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ index + 1 + (props.assignments.current_page - 1) * props.assignments.per_page }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                          {{ cls.grade_level }}
                        </div>
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-white">{{ cls.name }}</div>
                        <div class="text-xs text-white/60">Class Information</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-500/20 border border-blue-400/30 text-blue-100">
                      <span v-if="cls.grade_level === 'K1'">Kinder 1</span>
                      <span v-else-if="cls.grade_level === 'K2'">Kinder 2</span>
                      <span v-else>Grade {{ cls.grade_level }}</span>
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div v-if="cls.teacher?.name" class="flex items-center space-x-2">
                      <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center">
                          <Users class="w-3 h-3 text-white" />
                        </div>
                      </div>
                      <div class="text-sm font-medium text-white">{{ cls.teacher.name }}</div>
                    </div>
                    <div v-else class="flex items-center space-x-2 text-white/60">
                      <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-white/10 border border-white/20 rounded-full flex items-center justify-center">
                          <Plus class="w-3 h-3 text-white/40" />
                        </div>
                      </div>
                      <div class="text-sm italic">Unassigned</div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Cards -->
          <div class="md:hidden divide-y divide-white/10">
            <div 
              v-for="(cls, index) in props.assignments.data" 
              :key="cls.id"
              class="p-4 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-2">
                  <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                    {{ cls.grade_level }}
                  </div>
                  <div>
                    <h4 class="text-white font-medium text-sm">{{ cls.name }}</h4>
                    <p class="text-xs text-white/70">
                      <span v-if="cls.grade_level === 'K1'">Kinder 1</span>
                      <span v-else-if="cls.grade_level === 'K2'">Kinder 2</span>
                      <span v-else>Grade {{ cls.grade_level }}</span>
                    </p>
                  </div>
                </div>
              </div>
              <div class="border-t border-white/10 pt-3">
                <div v-if="cls.teacher?.name" class="flex items-center space-x-2">
                  <div class="w-6 h-6 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center">
                    <Users class="w-3 h-3 text-white" />
                  </div>
                  <div>
                    <div class="text-xs text-white/70">Assigned Teacher</div>
                    <div class="text-white font-medium text-sm">{{ cls.teacher.name }}</div>
                  </div>
                </div>
                <div v-else class="flex items-center space-x-2 text-white/60">
                  <div class="w-6 h-6 bg-white/10 border border-white/20 rounded-full flex items-center justify-center">
                    <Plus class="w-3 h-3 text-white/40" />
                  </div>
                  <div>
                    <div class="text-xs text-white/50">Status</div>
                    <div class="italic text-sm">No teacher assigned</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="props.assignments?.links && props.assignments.links.length > 3" class="px-6 py-4 border-t border-white/10 flex justify-between items-center">
            <div class="text-sm text-white/70">
              Showing {{ props.assignments.from || 0 }} to {{ props.assignments.to || 0 }} of {{ props.assignments.total || 0 }} assignments
            </div>
            <div class="flex space-x-1">
              <template v-for="(link, index) in props.assignments.links" :key="index">
                <span 
                  v-if="!link.url" 
                  class="px-3 py-2 text-white/40 cursor-not-allowed text-sm" 
                  v-html="link.label">
                </span>
                <Link 
                  v-else 
                  :href="link.url" 
                  class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150"
                  :class="link.active 
                    ? 'bg-blue-500/30 text-white border border-blue-400/50' 
                    : 'bg-white/10 text-white/70 border border-white/20 hover:bg-white/20'" 
                  v-html="link.label" 
                />
              </template>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8">
          <Users class="mx-auto h-10 w-10 text-white/40 mb-3" />
          <h3 class="text-sm font-medium text-white/80">No assignments found</h3>
          <p class="mt-1 text-sm text-white/60">Start by assigning teachers to classes using the form above.</p>
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
button:focus-visible,
select:focus-visible {
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