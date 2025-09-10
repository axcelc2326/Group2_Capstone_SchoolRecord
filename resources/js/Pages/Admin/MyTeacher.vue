<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import Swal from 'sweetalert2'
import AddTeacherModal from '@/Components/Admin/AddTeacherModal.vue'
import EditTeacherModal from '@/Components/Admin/EditTeacherModal.vue'

const props = defineProps({
  teachers: Object,
  filters: Object, // 👈 make sure filters are passed from controller
})

const showAddModal = ref(false)
const showEditModal = ref(false)
const selectedTeacher = ref(null)

// Search state
const search = ref(props.filters.search || '')

const searchTeachers = () => {
  router.get(
    route('teachers.index'),
    { search: search.value }, // send search param
    { preserveState: true, preserveScroll: true }
  )
}

const confirmDelete = (teacherId) => {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will delete the teacher account!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('teachers.destroy', teacherId), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Teacher deleted successfully',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
          })
        }
      })
    }
  })
}

const openEditModal = (teacher) => {
  selectedTeacher.value = teacher
  showEditModal.value = true
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Teachers Management" />

    <div class="max-w-6xl mx-auto py-6">
      <h1 class="text-2xl font-bold mb-6">Teachers Management</h1>
      <p class="font-semibold mb-4">Total Teachers: {{ teachers.total }}</p>

      <!-- Top bar -->
      <div class="flex justify-between items-center mb-4">
        <!-- Search -->
        <div class="flex items-center space-x-2">
          <input
            type="text"
            v-model="search"
            @keyup.enter="searchTeachers"
            placeholder="Search teachers..."
            class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white"
          />
          <button
            @click="searchTeachers"
            class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Search
          </button>
        </div>

        <!-- Add Teacher -->
        <button
          @click="showAddModal = true"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition"
        >
          + Register Teacher
        </button>
      </div>

      <!-- Teachers Table -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="w-full">
          <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
              <th class="text-left px-4 py-3">Name</th>
              <th class="text-left px-4 py-3">Email</th>
              <th class="text-left px-4 py-3">Class</th>
              <th class="text-left px-4 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="teacher in teachers.data" :key="teacher.id" class="border-t">
              <td class="px-4 py-3">{{ teacher.name }}</td>
              <td class="px-4 py-3">{{ teacher.email }}</td>
              <td class="px-4 py-3">
              <template v-if="teacher.class">
                  {{ teacher.class.name }} - Grade {{ teacher.class.grade_level }}
              </template>
              <template v-else>
                  No class assigned
              </template>
              </td>
              <td class="px-4 py-3 space-x-2">
                <button
                  @click="openEditModal(teacher)"
                  class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700"
                >
                  Edit
                </button>
                <button
                  @click="() => confirmDelete(teacher.id)"
                  class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="mt-4 flex justify-center space-x-2">
        <Link
          v-for="link in teachers.links"
          :key="link.url"
          :href="link.url || ''"
          v-html="link.label"
          class="px-3 py-1 rounded"
          :class="{
            'bg-blue-600 text-white': link.active,
            'bg-gray-200 text-gray-700': !link.active
          }"
        />
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- Modals -->
  <AddTeacherModal
    :show="showAddModal"
    @close="showAddModal = false"
  />

  <EditTeacherModal 
    :show="showEditModal" 
    :teacher="selectedTeacher" 
    @close="showEditModal = false"
  />
</template>
