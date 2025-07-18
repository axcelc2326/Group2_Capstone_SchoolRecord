<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ApexCharts from 'vue3-apexcharts';

const props = defineProps({
    analytics: Array
});
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-8">
      <h1 class="text-2xl font-bold">📊 Class Performance Analytics (Bar Graph)</h1>

      <div v-for="(classData, idx) in analytics" :key="idx" class="bg-white p-4 rounded shadow space-y-4">
        <h2 class="text-xl font-semibold">{{ classData.class }} - Grade {{ classData.grade_level }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- ✅ Bar Chart -->
          <ApexCharts
            type="bar"
            width="450"
            height="300"
            :options="{
              chart: { id: 'subject-bar-' + idx },
              xaxis: { categories: Object.keys(classData.subject_averages) },
              colors: ['#4ade80'], // Tailwind emerald-400
              plotOptions: {
                bar: { borderRadius: 4, horizontal: false }
              }
            }"
            :series="[
              {
                name: 'Average Grade',
                data: Object.values(classData.subject_averages)
              }
            ]"
          />

          <!-- ✅ Top & Lowest Performing Subjects -->
          <div class="space-y-2">
            <p>🎖️ <b>Top Subject:</b> {{ classData.top_subject }}</p>
            <p>⚠️ <b>Lowest Subject:</b> {{ classData.low_subject }}</p>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
