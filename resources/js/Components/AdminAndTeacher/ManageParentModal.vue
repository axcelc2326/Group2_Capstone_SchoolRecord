<script setup>
import { ref, watch, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import EditStudentModal from '@/Components/AdminAndTeacher/EditStudentModal.vue'

const props = defineProps({
  show: Boolean,
  parent: Object,
  classes: Array, // classes are now passed as a prop
})

const emit = defineEmits(['close'])

// Students list
const students = ref([])

// Add student form
const form = useForm({
  first_name: '',
  last_name: '',
  class_id: '', // will convert to number before submit
})

// Edit modal state
const showEditModal = ref(false)
const selectedStudent = ref(null)

// Parent ID
const parentId = computed(() => props.parent?.id || null)

// Fetch students when modal opens
const fetchStudents = async () => {
  if (!parentId.value) return
  try {
    const res = await axios.get(route('parents.students.index', parentId.value))
    students.value = res.data.students || []
  } catch (error) {
    console.error('Failed to fetch students', error)
  }
}

// Watch modal open or parent change
watch(
  () => [props.show, parentId.value],
  ([show, id]) => {
    if (show && id) fetchStudents()
  },
  { immediate: true }
)

// Add student
const addStudent = () => {
  if (!parentId.value) return

  // Convert class_id to number
  form.class_id = Number(form.class_id)

  form.post(route('parents.students.store', parentId.value), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      fetchStudents()
    },
  })
}

// Delete student
const deleteStudent = (studentId) => {
  if (!parentId.value) return;

  router.delete(route('parents.students.destroy', { 
    parent: parentId.value, 
    student: studentId 
  }), {
    preserveScroll: true,
    onSuccess: () => {
      fetchStudents();       // optional: refresh students before reload
    },
  });
}

// Open edit modal
const openEditModal = (student) => {
  selectedStudent.value = { ...student }
  showEditModal.value = true
}

// Refresh students list after edit
const refreshStudents = () => fetchStudents()

// Handle backdrop click
const handleBackdropClick = (event) => {
  if (event.target === event.currentTarget) {
    emit('close')
  }
}

// Handle ESC key
const handleEscape = (event) => {
  if (event.key === 'Escape') {
    emit('close')
  }
}

// Add event listeners
watch(() => props.show, (show) => {
  if (show) {
    document.addEventListener('keydown', handleEscape)
  } else {
    document.removeEventListener('keydown', handleEscape)
  }
})
</script>

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
        
        <!-- Modal Container - This handles the scrolling -->
        <div class="flex min-h-full items-center justify-center p-4 py-8">
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
              class="relative w-full max-w-3xl transform rounded-2xl bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg shadow-2xl border border-gray-200/50 dark:border-gray-700/50 transition-all my-8"
              @click.stop
            >
              <!-- Gradient overlay for visual depth -->
              <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-blue-900/20 dark:to-purple-900/20 pointer-events-none z-0"></div>
              
              <!-- Content -->
              <div class="relative z-10 p-8 space-y-8">
                <!-- Header -->
                <div class="text-center space-y-2">
                  <div class="w-12 h-12 mx-auto bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                    </svg>
                  </div>
                  <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Manage {{ props.parent?.name || 'Parent' }}</h2>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Add and manage students for this parent</p>
                </div>

                <!-- Students Section -->
                <div class="space-y-4">
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-md">
                      <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                      </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assigned Students</h3>
                  </div>

                  <div v-if="students.length" class="space-y-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                    <div 
                      v-for="student in students" 
                      :key="student.id" 
                      class="relative group p-4 rounded-xl bg-gradient-to-r from-gray-50/80 to-gray-100/80 dark:from-gray-800/80 dark:to-gray-700/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-600/50 hover:shadow-lg transition-all duration-200"
                    >
                      <div class="flex justify-between items-start">
                        <div class="space-y-1">
                          <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center shadow-sm">
                              <span class="text-sm font-semibold text-white">{{ student.first_name[0] }}{{ student.last_name[0] }}</span>
                            </div>
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ student.first_name }} {{ student.last_name }}</h4>
                          </div>
                          <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center space-x-1">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                              </svg>
                              <span>{{ student.class?.name || 'No class' }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                              </svg>
                              <span>Grade {{ student.class?.grade_level || '-' }}</span>
                            </div>
                          </div>
                        </div>
                        <div class="flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                          <button 
                            @click="openEditModal(student)"
                            class="px-3 py-1.5 text-sm font-medium text-amber-700 dark:text-amber-400 bg-amber-100/80 dark:bg-amber-900/30 hover:bg-amber-200/80 dark:hover:bg-amber-800/40 border border-amber-200/50 dark:border-amber-700/50 rounded-lg transition-colors shadow-sm"
                          >
                            Edit
                          </button>
                          <button 
                            @click="deleteStudent(student.id)"
                            class="px-3 py-1.5 text-sm font-medium text-red-700 dark:text-red-400 bg-red-100/80 dark:bg-red-900/30 hover:bg-red-200/80 dark:hover:bg-red-800/40 border border-red-200/50 dark:border-red-700/50 rounded-lg transition-colors shadow-sm"
                          >
                            Delete
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div v-else class="text-center py-8">
                    <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                      <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                      </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 italic">No students assigned yet</p>
                  </div>
                </div>

                <!-- Add Student Form -->
                <div class="space-y-4 border-t border-gray-200/50 dark:border-gray-700/50 pt-6">
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center shadow-md">
                      <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                      </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add New Student</h3>
                  </div>

                  <form @submit.prevent="addStudent" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                        <div class="relative group">
                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                          </div>
                          <input 
                            v-model="form.first_name" 
                            type="text" 
                            placeholder="Enter first name" 
                            class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200" 
                            required 
                          />
                        </div>
                      </div>

                      <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                        <div class="relative group">
                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                          </div>
                          <input 
                            v-model="form.last_name" 
                            type="text" 
                            placeholder="Enter last name" 
                            class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200" 
                            required 
                          />
                        </div>
                      </div>
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Class</label>
                      <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                          </svg>
                        </div>
                        <select 
                          v-model.number="form.class_id" 
                          class="block w-full pl-10 pr-3 py-3 border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-all duration-200" 
                          required
                        >
                          <option value="" disabled>Select a class</option>
                          <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                            {{ cls.name }} (Grade {{ cls.grade_level }})
                          </option>
                        </select>
                      </div>
                    </div>

                    <button 
                      type="submit" 
                      class="w-full py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-500/50 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                      :class="{ 'opacity-75 cursor-not-allowed transform-none shadow-lg': form.processing }"
                      :disabled="form.processing"
                    >
                      <div class="flex items-center justify-center space-x-2">
                        <template v-if="form.processing">
                          <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                          </svg>
                          <span>Adding Student...</span>
                        </template>
                        <template v-else>
                          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                          </svg>
                          <span>Add Student</span>
                        </template>
                      </div>
                    </button>
                  </form>
                </div>

                <!-- Close Button -->
                <button
                  type="button"
                  @click="emit('close')"
                  class="absolute top-4 right-4 z-20 p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Edit Student Modal -->
  <EditStudentModal
    :show="showEditModal"
    :student="selectedStudent"
    :classes="classes"
    @close="() => {
        showEditModal = false;
        fetchStudents(); // refresh the students list
    }"
  />
</template>

<style scoped>
/* Custom scrollbar styles */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #2563eb, #7c3aed);
}

/* Firefox */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #3b82f6 rgba(0, 0, 0, 0.1);
}
</style>