<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import EditSubjectModal from '@/Components/EditSubjectModal.vue'

const props = defineProps({
  subjects: Object,
  filters: Object,
})

const form = useForm({
  name: '',
  grade_level: '',
})

const showEditModal = ref(false)
const selectedSubject = ref(null)

// ✅ Dropdown selected grade filter with default to Grade 1
const selectedGrade = ref(props.filters.grade_level || 1)

function submit() {
  form.post(route('subjects.store'), { onSuccess: () => form.reset() })
}

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
  router.get(route('subjects.index'), { grade_level: selectedGrade.value }, { preserveState: true })
}
</script>

<template>
  <Head title="Subjects" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-bold">Manage Subjects</h2>
    </template>

    <!-- Add Form -->
    <div class="bg-white p-4 rounded shadow mb-6">
      <form @submit.prevent="submit" class="flex gap-4">
        <input v-model="form.name" type="text" placeholder="Subject name"
               class="border rounded px-2 py-1 w-1/2" required />

        <input v-model="form.grade_level" type="number" min="1" max="12"
               placeholder="Grade" class="border rounded px-2 py-1 w-20" required />

        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">
          Add
        </button>
      </form>
    </div>

    <!-- Filter Dropdown -->
    <div class="mb-4 relative inline-block">
      <label class="mr-2 font-semibold text-white">Filter by Grade:</label>

      <select v-model="selectedGrade" @change="filterSubjects"
              class="border border-white/40 rounded px-3 py-2 bg-transparent text-white 
                     focus:outline-none focus:ring-2 focus:ring-white/50 appearance-none pr-8">
        <option v-for="grade in 6" :key="grade" :value="grade">Grade {{ grade }}</option>
      </select>

      <!-- Custom white chevron -->
      <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-white">
        ▼
      </span>
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
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>

  <!-- Modal -->
  <EditSubjectModal
    :show="showEditModal"
    :subject="selectedSubject"
    @close="showEditModal = false"
  />
</template>

<style scoped>
/* ✅ Transparent dropdown options */
select option {
  background-color: transparent !important;
  color: white;
}
</style>
