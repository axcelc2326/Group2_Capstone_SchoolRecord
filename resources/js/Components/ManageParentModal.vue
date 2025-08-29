<script setup>
import { ref, watch, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import EditStudentModal from '@/Components/EditStudentModal.vue'

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
</script>

<template>
  <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
    <div class="bg-white rounded-xl w-full max-w-2xl p-6 shadow-xl overflow-auto max-h-[90vh]">

      <!-- Header -->
      <div class="flex justify-between items-center border-b pb-3 mb-4">
        <h2 class="text-xl font-semibold">Manage {{ props.parent?.name || 'Parent' }}</h2>
        <button @click="emit('close')" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
      </div>

      <!-- Students -->
      <div>
        <h3 class="text-lg font-medium mb-2">Assigned Students</h3>
        <ul v-if="students.length" class="space-y-2">
          <li v-for="student in students" :key="student.id" class="flex justify-between items-center bg-gray-100 px-3 py-2 rounded">
            <div>
              <span>{{ student.first_name }} {{ student.last_name }} ({{ student.class?.name || '-' }})</span>
              <div class="text-xs text-gray-600">
                Grade {{ student.class?.grade_level || '-' }}
              </div>
            </div>
            <div class="space-x-2">
              <button @click="openEditModal(student)" class="px-2 py-1 text-sm bg-yellow-500 text-white rounded">Edit</button>
              <button @click="deleteStudent(student.id)" class="px-2 py-1 text-sm bg-red-600 text-white rounded">Delete</button>
            </div>
          </li>
        </ul>
        <p v-else class="text-gray-400 italic">No students assigned yet.</p>
      </div>

      <!-- Add Student Form -->
      <div class="mt-6 border-t pt-4">
        <h3 class="text-lg font-medium mb-2">Add Student</h3>
        <form @submit.prevent="addStudent" class="space-y-3">
          <div class="flex space-x-2">
            <input v-model="form.first_name" type="text" placeholder="First Name" class="w-1/2 border rounded px-3 py-2" required />
            <input v-model="form.last_name" type="text" placeholder="Last Name" class="w-1/2 border rounded px-3 py-2" required />
          </div>
          <!-- Class selection uses classes prop -->
          <select v-model.number="form.class_id" class="w-full border rounded px-3 py-2" required>
            <option value="" disabled>Select Class</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">
              {{ cls.name }} (Grade {{ cls.grade_level }})
            </option>
          </select>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
            Add Student
          </button>
        </form>
      </div>
    </div>

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
  </div>
</template>
