<template>
  <div>
    <h1>My Child's Grades</h1>
    <div v-for="student in students" :key="student.id">
      <h2>{{ student.first_name }} {{ student.last_name }}</h2>
      <ul>
        <li v-for="grade in student.grades" :key="grade.id">
          {{ grade.subject.name }} - {{ grade.quarter }}: {{ grade.grade }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
defineProps({ students: Array })
</script>
