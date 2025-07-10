<script setup>
defineProps({ classId: Number });

const downloadSF5 = () => {
  window.open(route('sf5.download', classId), '_blank');
};
</script>

<template>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">Download SF5</h1>
    <button @click="downloadSF5" class="btn-primary">Download SF5 PDF</button>
  </div>
</template>
