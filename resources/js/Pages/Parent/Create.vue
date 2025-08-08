<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { ref } from 'vue'
import EditStudentModal from '@/Components/EditStudentModal.vue'
import RegisterStudentModal from '@/Components/RegisterStudentModal.vue'

const { props } = usePage()
const students = ref(props.students)
const classes = ref(props.classes)

const showEditModal = ref(false)
const selectedStudent = ref(null)

const showRegisterModal = ref(false)

const editStudent = (student) => {
  selectedStudent.value = student
  showEditModal.value = true
}

const deleteStudent = (id) => {
  Swal.fire({
    title: 'Delete Student?',
    text: 'Are you sure you want to delete this student?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('students.destroy', id), {
        onSuccess: () => {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Student deleted successfully',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
          }).then(() => {
            location.reload()
          });
        },
      })
    }
  })
}
</script>

<template>
  <Head title="Student Registration & List" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-white">Register Student & View List</h2>
    </template>

    <div class="py-12 max-w-5xl mx-auto space-y-8">
      <!-- ✅ Button to open registration modal -->
      <div class="flex justify-end">
        <button
          @click="showRegisterModal = true"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
        >
          + Register Student
        </button>
      </div>

      <!-- ✅ Student List -->
      <div class="bg-transparent border-white p-6 rounded">
        <h3 class="text-lg font-semibold mb-4">Your Registered Students</h3>
        <table class="min-w-full bg-transparent border rounded text-sm">
          <thead>
            <tr class="bg-transparent text-left">
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Class</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="student in students" :key="student.id" class="border-t">
              <td class="px-4 py-2">{{ student.first_name }} {{ student.last_name }}</td>
              <td class="px-4 py-2">{{ student.class?.name }} (Grade {{ student.class?.grade_level }})</td>
              <td class="px-4 py-2">
                <span
                  :class="student.approved_by_teacher ? 'bg-green-600' : 'bg-yellow-500'"
                  class="text-white text-xs px-2 py-1 rounded"
                >
                  {{ student.approved_by_teacher ? 'Approved' : 'Pending' }}
                </span>
              </td>
              <td class="px-4 py-2 text-center space-x-2">
                <button
                  class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded"
                  @click="editStudent(student)"
                >
                  Edit
                </button>
                <button
                  class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded"
                  @click="deleteStudent(student.id)"
                >
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="students.length === 0">
              <td colspan="4" class="text-center py-4 text-gray-500">
                No students registered yet.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- Modals -->
  <EditStudentModal
    :show="showEditModal"
    :student="selectedStudent"
    :classes="classes"
    @close="showEditModal = false"
    @updated="router.reload()"
  />
  <RegisterStudentModal
    :show="showRegisterModal"
    :classes="classes"
    @close="showRegisterModal = false"
    @created="router.reload()"
  />
</template>
