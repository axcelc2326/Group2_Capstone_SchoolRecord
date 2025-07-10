<script setup>
defineProps({ analytics: Array });
</script>

<template>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">Class Performance Analytics</h1>
    <table class="table-auto w-full">
      <thead>
        <tr>
          <th class="text-left">Class</th>
          <th class="text-left">Average Grade</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in analytics" :key="row.class_id">
          <td>{{ row.class.name }}</td>
          <td>{{ row.avg_grade.toFixed(2) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
