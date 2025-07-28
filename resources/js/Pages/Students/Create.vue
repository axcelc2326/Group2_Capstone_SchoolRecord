<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage, useForm, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { ref } from 'vue'

const { props } = usePage()
const students = ref(props.students)
const classes = ref(props.classes)

// ✅ Student registration form
const form = useForm({
  first_name: '',
  last_name: '',
  class_id: '',
})

const submit = () => {
  form.post(route('students.store'), {
    onSuccess: () => {
      form.reset()
      Swal.fire('Success!', 'Student registered successfully.', 'success')
    },
  })
}

// ✅ Delete student
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
          Swal.fire('Deleted!', 'The student was deleted.', 'success')
        },
      })
    }
  })
}

// ✅ Navigate to edit page
const editStudent = (id) => {
  router.get(route('students.edit', id))
}
</script>

<template>
  <Head title="Student Registration & List" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Register Student & View List</h2>
    </template>

    <div class="py-12 max-w-5xl mx-auto space-y-8">
      <!-- ✅ Registration Form -->
      <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-semibold mb-4">Register a Student</h3>
        <form @submit.prevent="submit" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium">First Name</label>
              <input
                v-model="form.first_name"
                type="text"
                class="mt-1 w-full border rounded px-3 py-2"
                required
              />
              <div v-if="form.errors.first_name" class="text-red-500 text-sm">
                {{ form.errors.first_name }}
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium">Last Name</label>
              <input
                v-model="form.last_name"
                type="text"
                class="mt-1 w-full border rounded px-3 py-2"
                required
              />
              <div v-if="form.errors.last_name" class="text-red-500 text-sm">
                {{ form.errors.last_name }}
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium">Select Class</label>
            <select
              v-model="form.class_id"
              class="mt-1 w-full border rounded px-3 py-2"
              required
            >
              <option value="">Select Class</option>
              <option
                v-for="cls in classes"
                :key="cls.id"
                :value="cls.id"
              >
                {{ cls.name }} (Grade {{ cls.grade_level }})
              </option>
            </select>
            <div v-if="form.errors.class_id" class="text-red-500 text-sm">
              {{ form.errors.class_id }}
            </div>
          </div>

          <button
            type="submit"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
          >
            Register Student
          </button>
        </form>
      </div>

      <!-- ✅ Student List -->
      <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-semibold mb-4">Your Registered Students</h3>
        <table class="min-w-full bg-white border rounded shadow text-sm">
          <thead>
            <tr class="bg-gray-100 text-left">
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Class</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="student in students" :key="student.id" class="border-t">
              <td class="px-4 py-2">
                {{ student.first_name }} {{ student.last_name }}
              </td>
              <td class="px-4 py-2">
                {{ student.class?.name }} (Grade {{ student.class?.grade_level }})
              </td>
              <td class="px-4 py-2">
                <span
                  :class="student.approved_by_teacher ? 'bg-green-600' : 'bg-yellow-500'"
                  class="text-white text-xs px-2 py-1 rounded"
                >
                  {{ student.approved_by_teacher ? 'Approved' : 'Pending' }}
                </span>
              </td>
              <td class="px-4 py-2 text-right space-x-2">
                <button
                  class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded"
                  @click="editStudent(student.id)"
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
              <td colspan="4" class="text-center py-4 text-gray-500">No students registered yet.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
