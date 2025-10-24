<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

// ✅ Props passed from controller
const props = defineProps({
  classes: Array,
  records: Array,
})

// ✅ Reactive form
const form = ref({
  class_id: '',
  school_year: '',
  quarter: '',
  principal_name: '',
})

// ✅ Automatically assign the teacher’s only/first class
onMounted(() => {
  if (props.classes.length === 1) {
    form.value.class_id = props.classes[0].id
  }
})

// ✅ Generate Honor Roll PDF
function submitForm() {
  const formData = new FormData()
  formData.append('class_id', form.value.class_id)
  formData.append('school_year', form.value.school_year)
  formData.append('quarter', form.value.quarter)
  formData.append('principal_name', form.value.principal_name)

  // ✅ open PDF in new tab
  const url = route('honorlist.generate')
  const newWindow = window.open('', '_blank')

  fetch(url, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content'),
    },
    body: formData,
  }).then(async (response) => {
    if (response.ok) {
      const blob = await response.blob()
      const pdfUrl = URL.createObjectURL(blob)
      newWindow.location.href = pdfUrl
      router.reload() // refresh table after generation
    } else {
      alert('Failed to generate honor list. Please check data.')
    }
  })
}

// ✅ Delete an honor roll record
function deleteRecord(id) {
  if (!confirm('Are you sure you want to delete this record?')) return
  router.delete(route('honor-rolls.destroy', id))
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Honor Roll Generator" />

    <div class="max-w-5xl mx-auto p-6">
      <h1 class="text-2xl font-bold mb-6">🏅 Honor Roll Generator</h1>

      <!-- 📋 Create New -->
      <div class="bg-transparent shadow p-5 rounded-lg mb-8">
        <h2 class="font-semibold text-lg mb-4">Create Honor List</h2>

        <form @submit.prevent="submitForm" class="grid grid-cols-2 gap-4">
          <!-- CLASS -->
          <div>
            <label class="block text-sm font-medium">Class</label>
            <select
              v-model="form.class_id"
              class="w-full border rounded p-2 text-black"
              required
              :disabled="props.classes.length === 1"
            >
              <option value="">-- Select Class --</option>
              <option
                v-for="cls in props.classes"
                :key="cls.id"
                :value="cls.id"
              >
                Grade {{ cls.grade_level }} - {{ cls.name }}
              </option>
            </select>
          </div>

          <!-- QUARTER -->
          <div>
            <label class="block text-sm font-medium">Quarter</label>
            <select
              v-model="form.quarter"
              class="w-full border rounded p-2 text-black"
              required
            >
              <option value="">-- Select Quarter --</option>
              <option>1st Quarter</option>
              <option>2nd Quarter</option>
              <option>3rd Quarter</option>
              <option>4th Quarter</option>
            </select>
          </div>

          <!-- SCHOOL YEAR -->
          <div>
            <label class="block text-sm font-medium">School Year</label>
            <input
              v-model="form.school_year"
              placeholder="e.g. 2025-2026"
              class="w-full border rounded p-2 text-black"
              required
            />
          </div>

          <!-- PRINCIPAL -->
          <div>
            <label class="block text-sm font-medium">Principal Name</label>
            <input
              v-model="form.principal_name"
              placeholder="e.g. Mrs. Fina Pangan"
              class="w-full border rounded p-2 text-black"
              required
            />
          </div>

          <div class="col-span-2 text-right">
            <button
              type="submit"
              class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
            >
              Generate PDF
            </button>
          </div>
        </form>
      </div>

      <!-- 📂 Existing Records -->
      <div class="bg-transparent shadow p-5 rounded-lg">
        <h2 class="font-semibold text-lg mb-4">📂 Generated Honor Lists</h2>

        <table class="min-w-full border border-gray-200 text-sm">
          <thead class="bg-transparent">
            <tr>
              <th class="border px-3 py-2 text-left">Class</th>
              <th class="border px-3 py-2">Quarter</th>
              <th class="border px-3 py-2">School Year</th>
              <th class="border px-3 py-2">Principal</th>
              <th class="border px-3 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!records.length">
              <td colspan="5" class="text-center py-4 text-gray-500">
                No honor lists yet.
              </td>
            </tr>
            <tr v-for="rec in records" :key="rec.id">
              <td class="border px-3 py-2">
                Grade {{ rec.class.grade_level }} - {{ rec.class.name }}
              </td>
              <td class="border px-3 py-2 text-center">{{ rec.quarter }}</td>
              <td class="border px-3 py-2 text-center">{{ rec.school_year }}</td>
              <td class="border px-3 py-2 text-center">{{ rec.principal_name }}</td>
              <td class="border px-3 py-2 text-center space-x-2">
                <a
                  :href="route('honorlist.download', rec.id)"
                  target="_blank"
                  class="text-blue-600 hover:underline"
                >
                  Download
                </a>
                <button
                  @click="deleteRecord(rec.id)"
                  class="text-red-600 hover:underline"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
