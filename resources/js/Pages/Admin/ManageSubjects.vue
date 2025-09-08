<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import EditSubjectModal from '@/Components/EditSubjectModal.vue'
import CreateSubjectModal from '@/Components/CreateSubjectModal.vue'

const props = defineProps({
  subjects: Object, // Laravel paginator
  filters: Object,  // passed from backend (grade_level)
})

const showEditModal = ref(false)
const showCreateModal = ref(false)
const selectedSubject = ref(null)

// ✅ Dropdown selected grade filter (default: All if not set)
const selectedGrade = ref(props.filters.grade_level || '')

function openEdit(subject) {
  selectedSubject.value = subject
  showEditModal.value = true
}

function destroySubject(id) {
  if (confirm('Are you sure you want to delete this subject?')) {
    router.delete(route('subjects.destroy', id))
  }
}

function filterSubjects() {
  router.get(
    route('subjects.index'),
    { grade_level: selectedGrade.value },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true, // ✅ ensures new data is fetched
    }
  )
}
</script>

<template>
  <Head title="Subjects" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Manage Subjects</h2>
        <button
          @click="showCreateModal = true"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          + Add Subject
        </button>
      </div>
    </template>

    <!-- Filter Dropdown -->
    <div class="mb-4 relative inline-block">
      <label class="mr-2 font-semibold text-white">Filter by Grade:</label>

      <select v-model="selectedGrade" @change="filterSubjects"
              class="border border-white/40 rounded px-3 py-2 bg-transparent text-white 
                     focus:outline-none focus:ring-2 focus:ring-white/50 appearance-none pr-8">
        <option value="">All Subjects</option>
        <option value="K1">Kinder 1</option>
        <option value="K2">Kinder 2</option>
        <option v-for="grade in 6" :key="grade" :value="String(grade)">
          Grade {{ grade }}
        </option>
      </select>

      <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-white">▼</span>
    </div>

    <!-- List Subjects -->
    <div class="bg-transparent p-4 rounded shadow">
      <table class="w-full border">
        <thead>
          <tr class="bg-transparent text-left">
            <th class="px-3 py-2">Name</th>
            <th class="px-3 py-2">Grade Level</th>
            <th class="px-3 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="subject in subjects.data" :key="subject.id" class="border-t">
            <td class="px-3 py-2">{{ subject.name }}</td>
            <td class="px-3 py-2">{{ subject.grade_level }}</td>
            <td class="px-3 py-2 flex gap-2">
              <button @click="openEdit(subject)" class="bg-green-600 text-white px-2 py-1 rounded">
                Edit
              </button>
              <button @click="destroySubject(subject.id)" class="bg-red-600 text-white px-2 py-1 rounded">
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="subjects.data.length === 0">
            <td colspan="3" class="text-center py-4 text-gray-400">No subjects found.</td>
          </tr>
        </tbody>
      </table>

      <!-- ✅ Pagination -->
      <div class="flex justify-between items-center mt-4" v-if="subjects.total > 0">
        <div class="text-sm text-gray-300">
          Showing {{ subjects.from }} to {{ subjects.to }} of {{ subjects.total }} subjects
        </div>
        <div class="flex gap-2">
          <button
            v-for="link in subjects.links"
            :key="link.label"
            @click="link.url && router.get(link.url, {}, { preserveState: true })"
            v-html="link.label"
            :disabled="!link.url"
            class="px-3 py-1 rounded border"
            :class="[
              link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white/10 text-white border-gray-600',
              !link.url ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-500 hover:text-white'
            ]"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- Modals -->
  <CreateSubjectModal
    :show="showCreateModal"
    @close="showCreateModal = false"
    @created="filterSubjects"
  />

  <EditSubjectModal
    :show="showEditModal"
    :subject="selectedSubject"
    @close="showEditModal = false"
  />
</template>
