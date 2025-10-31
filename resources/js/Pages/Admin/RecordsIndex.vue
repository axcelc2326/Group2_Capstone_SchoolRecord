<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, reactive } from 'vue'
import axios from 'axios'
import RecordDetailsModal from '@/Components/Admin/RecordDetailsModal.vue'

const props = defineProps({
  sf5Records: Array,
  honorLists: Array,
  teachers: Array,
  filters: Object,
})

const search = ref(props.filters.q ?? '')
const teacherId = ref(props.filters.teacher_id ?? '')
const typeFilter = ref(props.filters.type ?? 'all')

const showModal = ref(false)
const modalData = reactive({ type: null, record: null, loading: false })

function applyFilters() {
  const params = new URLSearchParams()
  if (search.value) params.append('q', search.value)
  if (teacherId.value) params.append('teacher_id', teacherId.value)
  if (typeFilter.value) params.append('type', typeFilter.value)
  // Use Inertia visit for filtering (keeps SPA)
  window.location.href = route('admin.records.index') + '?' + params.toString()
}

async function viewDetails(type, id) {
  modalData.loading = true
  showModal.value = true
  modalData.type = type
  modalData.record = null

  try {
    const res = await axios.get(route('admin.records.show', { type, id }))
    modalData.record = res.data.record
  } catch (err) {
    modalData.record = { error: 'Failed to load record.' }
  } finally {
    modalData.loading = false
  }
}

async function toggleReviewed(type, id, index, isSF5) {
  try {
    const res = await axios.post(route('admin.records.toggleReviewed', { type, id }))
    // Update local copy of records to reflect new status
    if (isSF5) {
      props.sf5Records[index].status = res.data.status
      props.sf5Records[index].reviewed_at = res.data.reviewed_at
    } else {
      props.honorLists[index].status = res.data.status
      props.honorLists[index].reviewed_at = res.data.reviewed_at
    }
  } catch (err) {
    console.error(err)
    alert('Unable to update status')
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Submitted Records" />

    <div class="max-w-7xl mx-auto py-10 px-6">
      <h1 class="text-2xl font-bold mb-6">📑 Submitted Records</h1>

      <!-- Filters -->
      <div class="flex gap-3 items-end mb-6">
        <div class="flex-1">
          <label class="block text-xs text-gray-600">Search</label>
          <input v-model="search" @keyup.enter="applyFilters" class="w-full border rounded px-3 py-2" placeholder="search by teacher, school, year, quarter..." />
        </div>

        <div>
          <label class="block text-xs text-gray-600">Teacher</label>
          <select v-model="teacherId" class="border rounded px-3 py-2">
            <option value="">All teachers</option>
            <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
          </select>
        </div>

        <div>
          <label class="block text-xs text-gray-600">Type</label>
          <select v-model="typeFilter" class="border rounded px-3 py-2">
            <option value="all">All</option>
            <option value="sf5">SF5</option>
            <option value="honor">Honor List</option>
          </select>
        </div>

        <div>
          <button @click="applyFilters" class="bg-blue-600 text-white px-4 py-2 rounded">Apply</button>
        </div>
      </div>

      <!-- SF5 Table -->
      <div class="mb-10">
        <h2 class="text-xl font-semibold mb-3">SF5 Reports</h2>
        <div class="overflow-x-auto bg-white shadow rounded-lg">
          <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-600 uppercase">
              <tr>
                <th class="px-4 py-3 text-left">Class</th>
                <th class="px-4 py-3 text-left">Teacher</th>
                <th class="px-4 py-3 text-left">School Year</th>
                <th class="px-4 py-3 text-left">Average</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Created</th>
                <th class="px-4 py-3">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(record, index) in sf5Records" :key="record.id" class="border-t hover:bg-gray-50">
                <td class="px-4 py-2">{{ record.class?.name }}</td>
                <td class="px-4 py-2">{{ record.teacher?.name }}</td>
                <td class="px-4 py-2">{{ record.school_year }}</td>
                <td class="px-4 py-2">{{ record.class_average }}</td>
                <td class="px-4 py-2">
                  <span v-if="record.status === 'reviewed'" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                    Reviewed
                  </span>
                  <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                    Pending
                  </span>
                </td>
                <td class="px-4 py-2">{{ new Date(record.created_at).toLocaleString() }}</td>
                <td class="px-4 py-2 text-center">
                  <button @click="viewDetails('sf5', record.id)" class="text-indigo-600 hover:underline mr-3">View</button>
                  <a :href="route('admin.records.download', { type: 'sf5', id: record.id })" class="text-blue-600 hover:underline mr-3">Redownload</a>
                  <button @click="toggleReviewed('sf5', record.id, index, true)" class="text-sm text-gray-600">Toggle Reviewed</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Honor Roll Table -->
      <div>
        <h2 class="text-xl font-semibold mb-3">Honor Roll Lists</h2>
        <div class="overflow-x-auto bg-white shadow rounded-lg">
          <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-600 uppercase">
              <tr>
                <th class="px-4 py-3 text-left">Class</th>
                <th class="px-4 py-3 text-left">Teacher</th>
                <th class="px-4 py-3 text-left">Quarter</th>
                <th class="px-4 py-3 text-left">School Year</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Created</th>
                <th class="px-4 py-3">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(record, index) in honorLists" :key="record.id" class="border-t hover:bg-gray-50">
                <td class="px-4 py-2">{{ record.class?.name }}</td>
                <td class="px-4 py-2">{{ record.teacher?.name }}</td>
                <td class="px-4 py-2">{{ record.quarter }}</td>
                <td class="px-4 py-2">{{ record.school_year }}</td>
                <td class="px-4 py-2">
                  <span v-if="record.status === 'reviewed'" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                    Reviewed
                  </span>
                  <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                    Pending
                  </span>
                </td>
                <td class="px-4 py-2">{{ new Date(record.created_at).toLocaleString() }}</td>
                <td class="px-4 py-2 text-center">
                  <button @click="viewDetails('honor', record.id)" class="text-indigo-600 hover:underline mr-3">View</button>
                  <a :href="route('admin.records.download', { type: 'honor', id: record.id })" class="text-blue-600 hover:underline mr-3">Redownload</a>
                  <button @click="toggleReviewed('honor', record.id, index, false)" class="text-sm text-gray-600">Toggle Reviewed</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    </AuthenticatedLayout>
    <RecordDetailsModal 
        v-model:open="showModal" 
        :data="modalData" 
    />

</template>
