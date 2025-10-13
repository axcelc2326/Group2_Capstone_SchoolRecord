<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'

const props = defineProps({
  classes: Array,      // Teacher's assigned classes
  sf5Records: Array    // Existing SF5 records from controller
})

// Grab CSRF token from <meta> tag
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

// Assume teacher only has one assigned class
const assignedClassId = props.classes.length ? props.classes[0].id : null

// Methods
const deleteRecord = (id) => {
  if (confirm('Are you sure you want to delete this SF5 record?')) {
    router.delete(`/sf5/${id}`)
  }
}

const editRecord = (id) => {
  router.get(`/sf5/${id}/edit`)
}

const downloadAgain = (id) => {
  window.open(`/sf5/download-again/${id}`, '_blank')
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Generate SF5 Report" />

    <div class="max-w-4xl mx-auto bg-white/80 backdrop-blur-md p-6 rounded-2xl shadow-lg border border-gray-200">
      <!-- Header -->
      <h1 class="text-2xl font-bold text-gray-800 mb-4">Generate SF5 Report</h1>
      <p class="text-gray-600 mb-6 text-sm">
        Fill out the form below to generate and download a new School Form 5 (SF5) report.
      </p>

      <!-- Form -->
      <form method="POST" action="/sf5/download" target="_blank" class="grid grid-cols-2 gap-4">
        <input type="hidden" name="_token" :value="csrfToken" />
        <input type="hidden" name="class_id" :value="assignedClassId" />

        <div>
          <label class="block font-medium mb-1 text-gray-700">Region</label>
          <select name="region" class="w-full border p-2 rounded text-gray-900" required>
            <option value="">-- Select Region --</option>
            <option value="NCR">NCR - National Capital Region</option>
            <option value="CAR">CAR - Cordillera Administrative Region</option>
            <option value="Region I">Region I - Ilocos Region</option>
            <option value="Region II">Region II - Cagayan Valley</option>
            <option value="Region III">Region III - Central Luzon</option>
            <option value="Region IV-A">Region IV-A - CALABARZON</option>
            <option value="MIMAROPA">MIMAROPA Region</option>
            <option value="Region V">Region V - Bicol Region</option>
            <option value="Region VI">Region VI - Western Visayas</option>
            <option value="Region VII">Region VII - Central Visayas</option>
            <option value="Region VIII">Region VIII - Eastern Visayas</option>
            <option value="Region IX">Region IX - Zamboanga Peninsula</option>
            <option value="Region X">Region X - Northern Mindanao</option>
            <option value="Region XI">Region XI - Davao Region</option>
            <option value="Region XII">Region XII - SOCCSKSARGEN</option>
            <option value="Region XIII">Region XIII - Caraga</option>
            <option value="BARMM">BARMM - Bangsamoro Autonomous Region in Muslim Mindanao</option>
          </select>
        </div>

        <div>
          <label class="block font-medium mb-1 text-gray-700">Division</label>
          <input name="division" class="w-full border p-2 rounded text-gray-900" required />
        </div>

        <div>
          <label class="block font-medium mb-1 text-gray-700">School ID</label>
          <input name="school_id" class="w-full border p-2 rounded text-gray-900" required />
        </div>

        <div>
          <label class="block font-medium mb-1 text-gray-700">School Name</label>
          <input name="school_name" class="w-full border p-2 rounded text-gray-900" required />
        </div>

        <div>
          <label class="block font-medium mb-1 text-gray-700">School Year</label>
          <input name="school_year" class="w-full border p-2 rounded text-gray-900" placeholder="e.g., 2025 - 2026" required />
        </div>

        <div class="col-span-2">
          <label class="block font-medium mb-1 text-gray-700">School Head / SCC Chair</label>
          <input name="school_head_chair" class="w-full border p-2 rounded text-gray-900" required />
        </div>

        <div class="col-span-2 flex justify-end">
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition-all">
            Generate PDF
          </button>
        </div>
      </form>

      <!-- Divider -->
      <hr class="my-8 border-gray-300" />

      <!-- Table of Existing Records -->
      <h2 class="text-lg font-semibold text-gray-800 mb-3">Generated SF5 Reports</h2>

      <div v-if="props.sf5Records?.length" class="overflow-x-auto">
        <table class="w-full text-sm border border-gray-300 rounded-lg overflow-hidden">
          <thead class="bg-gray-100 text-gray-700">
            <tr>
              <th class="border p-2">#</th>
              <th class="border p-2">School Year</th>
              <th class="border p-2">Class</th>
              <th class="border p-2">School Name</th>
              <th class="border p-2">Division</th>
              <th class="border p-2">Created</th>
              <th class="border p-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(record, index) in props.sf5Records"
              :key="record.id"
              class="odd:bg-white even:bg-gray-50 hover:bg-blue-50 transition-colors text-black"
            >
              <td class="border p-2 text-center">{{ index + 1 }}</td>
              <td class="border p-2">{{ record.school_year }}</td>
              <td class="border p-2">
                {{ record.class?.grade_level }} - {{ record.class?.name }}
              </td>
              <td class="border p-2">{{ record.school_name }}</td>
              <td class="border p-2">{{ record.division }}</td>
              <td class="border p-2 text-center">
                {{ new Date(record.created_at).toLocaleDateString() }}
              </td>
              <td class="border p-2 text-center space-x-2">
                <button
                  @click="editRecord(record.id)"
                  class="text-blue-600 hover:underline"
                >
                  Edit
                </button>
                <button
                  @click="deleteRecord(record.id)"
                  class="text-red-600 hover:underline"
                >
                  Delete
                </button>
                <button
                  @click="downloadAgain(record.id)"
                  class="text-green-600 hover:underline"
                >
                  Download
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <p v-else class="text-gray-500 text-sm italic mt-4">No SF5 reports generated yet.</p>
    </div>
  </AuthenticatedLayout>
</template>
