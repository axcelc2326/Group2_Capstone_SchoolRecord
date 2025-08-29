<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import RegisterModal from '@/Components/RegisterModal.vue'
import ManageParentModal from '@/Components/ManageParentModal.vue'
import { ref } from 'vue'

const props = defineProps({
  parents: Object,
  classes: Array, // classes already come from backend
  filters: Object,
})

const showRegisterModal = ref(false)
const showManageModal = ref(false)
const selectedParent = ref(null)

const search = ref(props.filters.search || '')

const performSearch = () => {
  router.get(route('parents.index'), { search: search.value }, { preserveState: true, replace: true })
}

const openManageModal = (parent) => {
  selectedParent.value = parent
  showManageModal.value = true
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Parents Management" />

    <div class="max-w-6xl mx-auto py-6">
      <h1 class="text-2xl font-bold mb-6">Parents Management</h1>

      <!-- Search + Register Parent -->
      <div class="flex justify-between items-center mb-4">
        <div>
          <input
            v-model="search"
            @keyup.enter="performSearch"
            type="text"
            placeholder="Search parents..."
            class="px-4 py-2 border rounded-lg"
          />
          <button @click="performSearch" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg">
            Search
          </button>
        </div>

        <button
          @click="showRegisterModal = true"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition"
        >
          + Register Parent
        </button>
      </div>

      <!-- Parents Table -->
      <div class="bg-transparent rounded-xl shadow overflow-hidden">
        <table class="w-full border-collapse">
          <thead class="bg-transparent">
            <tr>
              <th class="text-left px-4 py-3">Parent Name</th>
              <th class="text-left px-4 py-3">Email</th>
              <th class="text-left px-4 py-3">Assigned Students</th>
              <th class="text-left px-4 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="parent in props.parents.data"
              :key="parent.id"
              class="border-t hover:bg-gray-500"
            >
              <td class="px-4 py-3">{{ parent.name }}</td>
              <td class="px-4 py-3">{{ parent.email }}</td>
              <td class="px-4 py-3">
                <span
                  v-if="parent.students.length"
                  v-for="student in parent.students"
                  :key="student.id"
                  class="inline-block bg-transparent px-2 py-1 rounded text-sm mr-2"
                >
                  {{ student.first_name }} {{ student.last_name }}
                </span>
                <span v-else class="text-gray-400 italic">No student assigned</span>
              </td>
              <td class="px-4 py-3">
                <button
                  @click="openManageModal(parent)"
                  class="px-3 py-1 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700"
                >
                  Manage
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="mt-4 flex justify-center space-x-2">
        <Link
          v-for="link in props.parents.links"
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

  <!-- Register Modal -->
  <RegisterModal 
    :show="showRegisterModal" 
    @close="showRegisterModal = false"
  />

  <!-- Manage Parent Modal -->
  <ManageParentModal
    v-if="selectedParent"
    :show="showManageModal"
    :parent="selectedParent"
    :classes="props.classes"
    @close="showManageModal = false"
  />
</template>
