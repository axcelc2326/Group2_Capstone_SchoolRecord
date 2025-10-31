<script setup>
import { defineEmits, defineProps, watch } from 'vue'

const props = defineProps({
  open: { type: Boolean, default: false },
  data: { type: Object, default: () => ({}) } // { type, record, loading }
})
const emit = defineEmits(['update:open'])

function close() {
  emit('update:open', false)
}
</script>

<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" @click="close"></div>

    <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full z-10 p-6 overflow-auto max-h-[80vh]">
      <div class="flex justify-between items-start">
        <h3 class="text-lg font-semibold">Record Details</h3>
        <button @click="close" class="text-gray-500">Close</button>
      </div>

      <div class="mt-4">
        <div v-if="data.loading" class="text-center py-10">Loading...</div>

        <div v-else-if="data.record">
          <div v-if="data.type === 'sf5'">
            <p><strong>Class:</strong> {{ data.record.class?.name }}</p>
            <p><strong>Teacher:</strong> {{ data.record.teacher?.name }}</p>
            <p><strong>School Year:</strong> {{ data.record.school_year }}</p>
            <p><strong>Average:</strong> {{ data.record.class_average }}</p>
            <p><strong>Status:</strong> {{ data.record.status }}</p>
            <p class="mt-4"><strong>Totals:</strong></p>
            <ul class="list-disc ml-6">
              <li>Male: {{ data.record.male_count }}</li>
              <li>Female: {{ data.record.female_count }}</li>
              <li>Overall: {{ data.record.overall_count }}</li>
            </ul>
            <p class="mt-4"><strong>Created:</strong> {{ new Date(data.record.created_at).toLocaleString() }}</p>
            <p v-if="data.record.reviewed_at"><strong>Reviewed at:</strong> {{ new Date(data.record.reviewed_at).toLocaleString() }}</p>
          </div>

          <div v-else-if="data.type === 'honor'">
            <p><strong>Class:</strong> {{ data.record.class?.name }}</p>
            <p><strong>Teacher:</strong> {{ data.record.teacher?.name }}</p>
            <p><strong>School Year:</strong> {{ data.record.school_year }}</p>
            <p><strong>Quarter:</strong> {{ data.record.quarter }}</p>
            <p><strong>Status:</strong> {{ data.record.status }}</p>
            <p class="mt-4"><strong>Created:</strong> {{ new Date(data.record.created_at).toLocaleString() }}</p>
            <p v-if="data.record.reviewed_at"><strong>Reviewed at:</strong> {{ new Date(data.record.reviewed_at).toLocaleString() }}</p>
          </div>
        </div>

        <div v-else class="text-center py-6">No details available.</div>
      </div>

      <div class="mt-6 flex justify-end gap-3">
        <button @click="close" class="px-4 py-2 border rounded">Close</button>
        <a v-if="data.record" :href="`/admin/records/${data.type}/${data.record.id}/download`" class="px-4 py-2 bg-blue-600 text-white rounded">Redownload</a>
      </div>
    </div>
  </div>
</template>
