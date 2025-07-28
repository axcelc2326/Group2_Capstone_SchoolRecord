<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  students: Object,
  filters: Object,
  message: String,
})

const search = ref(props.filters?.search || '')

watch(search, (value) => {
  router.get(route('teacher.students'), { search: value }, { preserveState: true, replace: true })
})

function removeStudent(studentId) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will unapprove the student.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, unapprove',
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(route('teacher.students.unapprove', { student: studentId }))
    }
  })
}

function clearGrades(studentId) {
  Swal.fire({
    title: 'Clear student grades?',
    text: 'This will delete all grades for this student.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, clear',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('teacher.students.clearGrades', { student: studentId }))
    }
  })
}

function unapproveAll() {
  Swal.fire({
    title: 'Unapprove ALL students?',
    text: 'This will unapprove all students in your class.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, unapprove all',
  }).then((result) => {
    if (result.isConfirmed) {
      const studentIds = props.students.data.map((s) => s.id)
      router.put(route('teacher.students.unapproveAll'), {
        student_ids: studentIds,
      })
    }
  })
}

function clearAllGrades() {
  Swal.fire({
    title: 'Clear ALL grades?',
    text: 'This will remove all grades for all students shown.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, clear all grades',
  }).then((result) => {
    if (result.isConfirmed) {
      const studentIds = props.students.data.map((s) => s.id)
      router.delete(route('teacher.students.clearAllGrades'), {
        data: { student_ids: studentIds }, // Must use `data` in router.delete
        preserveScroll: true,
      })
    }
  })
}
</script>

<template>
  <Head title="My Students" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">👩‍🏫 My Students</h2>
    </template>

    <div class="py-6 px-8 space-y-6">
      <div v-if="message" class="text-red-600 font-medium">{{ message }}</div>

      <!-- Search -->
      <input
        type="text"
        v-model="search"
        placeholder="Search by name..."
        class="border rounded px-3 py-2 w-full md:w-1/3"
      />

      <div class="flex flex-wrap gap-4 my-4">
        <button
          @click="unapproveAll"
          class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded"
        >
          ❌ Unapprove All
        </button>

        <button
          @click="clearAllGrades"
          class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded"
        >
          🧹 Clear Grades (All)
        </button>
      </div>

      <div v-if="students.data.length">
        <table class="min-w-full bg-white rounded shadow text-sm">
          <thead>
            <tr class="bg-gray-200 text-left text-gray-700">
              <th class="px-4 py-2">#</th>
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(student, index) in students.data"
              :key="student.id"
              class="border-t"
            >
              <td class="px-4 py-2">{{ index + 1 + (students.current_page - 1) * students.per_page }}</td>
              <td class="px-4 py-2">{{ student.first_name }} {{ student.last_name }}</td>
              <td class="px-4 py-2 space-x-4">
                <Link
                  :href="route('teacher.student.grades', { student: student.id })"
                  class="text-green-600 hover:underline"
                >
                  📊 View Grades
                </Link>
                <Link
                  :href="route('grades.create', { student: student.id })"
                  class="text-blue-600 hover:underline"
                >
                  📝 Input Grades
                </Link>
                <button
                  @click="removeStudent(student.id)"
                  class="text-red-600 hover:underline"
                >
                  ❌ Unapprove
                </button>
                <button
                  @click="clearGrades(student.id)"
                  class="text-yellow-600 hover:underline"
                >
                  🧹 Clear Grades
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4 flex justify-between items-center text-sm">
          <div>
            Showing {{ students.from }} to {{ students.to }} of {{ students.total }} students
          </div>
          <div class="space-x-2">
            <button
              v-for="page in students.last_page"
              :key="page"
              @click="router.get(route('teacher.students'), { page, search }, { preserveState: true })"
              :class="{
                'px-3 py-1 rounded bg-blue-500 text-white': page === students.current_page,
                'px-3 py-1 rounded bg-gray-200': page !== students.current_page,
              }"
            >
              {{ page }}
            </button>
          </div>
        </div>
      </div>

      <div v-else class="text-center text-gray-500 mt-6">
        📭 No approved students found in your class.
      </div>
    </div>
  </AuthenticatedLayout>
</template>
