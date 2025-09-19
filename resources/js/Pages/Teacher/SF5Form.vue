<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

// Props from controller
const props = defineProps({
  classes: Array // teacher's assigned classes
})

// Grab CSRF token from <meta> tag (set in app.blade.php)
const csrfToken = document
  .querySelector('meta[name="csrf-token"]')
  ?.getAttribute('content')

// Assume teacher only has **one assigned class** → pick first
const assignedClassId = props.classes.length ? props.classes[0].id : null
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Generate SF5 Report" />

    <div class="max-w-3xl mx-auto bg-transparent p-6 shadow rounded">
      <h1 class="text-xl font-bold mb-4">Generate SF5 Report</h1>

      <!-- Native POST form -->
      <form method="POST" action="/sf5/download" target="_blank" class="grid grid-cols-2 gap-4">
        <!-- CSRF token -->
        <input type="hidden" name="_token" :value="csrfToken" />

        <!-- Automatically assigned class -->
        <input type="hidden" name="class_id" :value="assignedClassId" />

        <!-- Region -->
        <div>
          <label class="block">Region</label>
          <select name="region" class="w-full border p-2 rounded text-black" required>
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
          <label class="block">Division</label>
          <input name="division" class="w-full border p-2 rounded text-black" required />
        </div>
        <div>
          <label class="block">School ID</label>
          <input name="school_id" class="w-full border p-2 rounded text-black" required />
        </div>
        <div>
          <label class="block">School Name</label>
          <input name="school_name" class="w-full border p-2 rounded text-black" required />
        </div>
        <div>
          <label class="block">School Year</label>
          <input name="school_year" class="w-full border p-2 rounded text-black" required />
        </div>
        <div>
          <label class="block">School Head</label>
          <input name="school_head" class="w-full border p-2 rounded text-black" required />
        </div>
        <div>
          <label class="block">SCC Chair</label>
          <input name="scc_chair" class="w-full border p-2 rounded text-black" required />
        </div>

        <div class="col-span-2 flex justify-end">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Generate PDF
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
