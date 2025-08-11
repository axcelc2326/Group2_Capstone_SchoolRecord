<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
  students: Array,
});

const quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
const expandedStudents = ref(new Set());

const toggleStudent = (studentId) => {
  if (expandedStudents.value.has(studentId)) {
    expandedStudents.value.delete(studentId);
  } else {
    expandedStudents.value.add(studentId);
  }
};

// Enhanced grade color system with transparency
const getGradeColor = (grade) => {
  if (!grade || grade === '—') return 'bg-white/10 text-white/60 border-white/20';
  const numGrade = parseFloat(grade);
  if (numGrade >= 95) return 'bg-emerald-400/20 text-emerald-100 border-emerald-400/30';
  if (numGrade >= 90) return 'bg-blue-400/20 text-blue-100 border-blue-400/30';
  if (numGrade >= 85) return 'bg-violet-400/20 text-violet-100 border-violet-400/30';
  if (numGrade >= 80) return 'bg-cyan-400/20 text-cyan-100 border-cyan-400/30';
  if (numGrade >= 75) return 'bg-amber-400/20 text-amber-100 border-amber-400/30';
  return 'bg-red-400/20 text-red-100 border-red-400/30';
};

const getGradeDisplay = (grade) => {
  if (!grade || grade === '—') return '—';
  const numGrade = parseFloat(grade);
  if (numGrade <= 74) return 'F';
  return grade;
};

const calculateSubjectAverage = (grades, subject) => {
  const subjectGrades = grades.filter(g => g.subject.name === subject && g.grade !== '—' && g.grade);
  if (subjectGrades.length === 0) return '—';
  
  const sum = subjectGrades.reduce((acc, g) => acc + parseFloat(g.grade), 0);
  return (sum / subjectGrades.length).toFixed(1);
};

const calculateGPA = (grades) => {
  const validGrades = grades.filter(g => g.grade !== '—' && g.grade);
  if (validGrades.length === 0) return '—';
  
  const sum = validGrades.reduce((acc, g) => acc + parseFloat(g.grade), 0);
  return (sum / validGrades.length).toFixed(1);
};

const getGradeStats = (grades) => {
  const validGrades = grades.filter(g => g.grade !== '—' && g.grade);
  return {
    excellent: validGrades.filter(g => parseFloat(g.grade) >= 90).length,
    good: validGrades.filter(g => parseFloat(g.grade) >= 80 && parseFloat(g.grade) < 90).length,
    average: validGrades.filter(g => parseFloat(g.grade) >= 75 && parseFloat(g.grade) < 80).length,
    failed: validGrades.filter(g => parseFloat(g.grade) <= 74).length
  };
};

// Enhanced summary
const summary = computed(() => {
  if (!props.students || props.students.length === 0) {
    return {
      total_students: 0,
      enrolled_students: 0,
      excellent_grades: 0,
      average_gpa: '—'
    };
  }

  const allGrades = props.students.flatMap(student => student.grades || []);
  const validGrades = allGrades.filter(g => g.grade !== '—' && g.grade);
  const excellentGrades = validGrades.filter(g => parseFloat(g.grade) >= 90).length;
  
  let averageGPA = '—';
  if (validGrades.length > 0) {
    const sum = validGrades.reduce((acc, g) => acc + parseFloat(g.grade), 0);
    averageGPA = (sum / validGrades.length).toFixed(1);
  }

  return {
    total_students: props.students.length,
    enrolled_students: props.students.filter(s => s.grades && s.grades.length > 0).length,
    excellent_grades: excellentGrades,
    average_gpa: averageGPA
  };
});

// Helper to get performance status with transparency
const getPerformanceStatus = (gpa) => {
  if (gpa === '—') return { text: 'No Data', color: 'text-white/60', bg: 'bg-white/10' };
  const num = parseFloat(gpa);
  if (num >= 90) return { text: 'Excellent', color: 'text-emerald-100', bg: 'bg-emerald-400/20' };
  if (num >= 80) return { text: 'Good', color: 'text-blue-100', bg: 'bg-blue-400/20' };
  if (num >= 75) return { text: 'Average', color: 'text-amber-100', bg: 'bg-amber-400/20' };
  return { text: 'Needs Support', color: 'text-red-100', bg: 'bg-red-400/20' };
};

// Fixed SVG icons with proper viewBox and paths
const icons = {
  users: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>`,
  userCheck: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17,11 19,13 23,9"></polyline></svg>`,
  award: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21,13.89 7,23 12,20 17,23 15.79,13.88"></polyline></svg>`,
  trending: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22,7 13.5,15.5 8.5,10.5 2,17"></polyline><polyline points="16,7 22,7 22,13"></polyline></svg>`,
  chart: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"></path></svg>`,
  book: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>`,
  school: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>`,
  chevronDown: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6,9 12,15 18,9"></polyline></svg>`,
  target: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>`,
  graduation: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>`,
  star: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"></polygon></svg>`,
  good: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9 12l2 2 4-4"></path></svg>`,
  average: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>`,

};
</script>

<template>
    <Head title="Academic Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-1">
              <div class="flex items-center justify-between">
                  <div>
                      <h2 class="text-xl font-bold text-white sm:text-2xl lg:text-3xl">
                          Academic Dashboard
                      </h2>
                      <p class="text-white/70 mt-1 text-sm sm:text-base">
                          Student performance overview
                      </p>
                  </div>
                  <div class="hidden sm:block w-6 h-6 sm:w-8 sm:h-8 text-white/60" v-html="icons.graduation"></div>
              </div>
            </div>
        </template>

        <!-- Mobile-first container with proper padding -->
        <div class="px-3 py-3 space-y-3 max-w-7xl mx-auto sm:px-4 sm:py-4 sm:space-y-4 lg:px-8 lg:py-6 lg:space-y-6">
          
          <!-- Summary Cards - Mobile First (2 cols mobile, 4 cols desktop) -->
          <div class="grid grid-cols-2 gap-2 sm:gap-3 lg:grid-cols-4 lg:gap-4">
              <!-- Total Students -->
              <div class="group backdrop-blur-xl bg-white/10 hover:bg-white/15 border border-white/20 hover:border-white/30 rounded-xl p-3 shadow-lg hover:shadow-xl transition-all duration-300 sm:p-4 lg:p-6">
                  <div class="flex flex-col items-center text-center space-y-2 lg:space-y-3">
                      <div class="w-8 h-8 rounded-lg bg-transparent flex items-center justify-center text-white sm:w-10 sm:h-10 lg:w-12 lg:h-12" v-html="icons.users"></div>
                      <div>
                          <p class="text-lg font-bold text-white sm:text-xl lg:text-2xl">{{ summary.total_students }}</p>
                          <h3 class="text-xs font-medium text-white/70 leading-tight sm:text-sm">Total Students</h3>
                      </div>
                  </div>
              </div>

              <!-- Active Students -->
              <div class="group backdrop-blur-xl bg-white/10 hover:bg-white/15 border border-white/20 hover:border-white/30 rounded-xl p-3 shadow-lg hover:shadow-xl transition-all duration-300 sm:p-4 lg:p-6">
                  <div class="flex flex-col items-center text-center space-y-2 lg:space-y-3">
                      <div class="w-8 h-8 rounded-lg bg-transparent flex items-center justify-center text-white sm:w-10 sm:h-10 lg:w-12 lg:h-12" v-html="icons.userCheck"></div>
                      <div>
                          <p class="text-lg font-bold text-white sm:text-xl lg:text-2xl">{{ summary.enrolled_students }}</p>
                          <h3 class="text-xs font-medium text-white/70 leading-tight sm:text-sm">Active Students</h3>
                      </div>
                  </div>
              </div>

              <!-- Excellence Count -->
              <div class="group backdrop-blur-xl bg-white/10 hover:bg-white/15 border border-white/20 hover:border-white/30 rounded-xl p-3 shadow-lg hover:shadow-xl transition-all duration-300 sm:p-4 lg:p-6">
                  <div class="flex flex-col items-center text-center space-y-2 lg:space-y-3">
                      <div class="w-8 h-8 rounded-lg bg-transparent flex items-center justify-center text-white sm:w-10 sm:h-10 lg:w-12 lg:h-12" v-html="icons.award"></div>
                      <div>
                          <p class="text-lg font-bold text-white sm:text-xl lg:text-2xl">{{ summary.excellent_grades }}</p>
                          <h3 class="text-xs font-medium text-white/70 leading-tight sm:text-sm">Excellence</h3>
                      </div>
                  </div>
              </div>

              <!-- Class Average -->
              <div class="group backdrop-blur-xl bg-white/10 hover:bg-white/15 border border-white/20 hover:border-white/30 rounded-xl p-3 shadow-lg hover:shadow-xl transition-all duration-300 sm:p-4 lg:p-6">
                  <div class="flex flex-col items-center text-center space-y-2 lg:space-y-3">
                      <div class="w-8 h-8 rounded-lg bg-transparent flex items-center justify-center text-white sm:w-10 sm:h-10 lg:w-12 lg:h-12" v-html="icons.trending"></div>
                      <div>
                          <p class="text-lg font-bold text-white sm:text-xl lg:text-2xl">{{ summary.average_gpa }}</p>
                          <h3 class="text-xs font-medium text-white/70 leading-tight sm:text-sm">Class Average</h3>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Students Performance Section -->
          <div v-if="students && students.length > 0" class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-xl overflow-hidden">
              <!-- Header - Mobile optimized -->
              <div class="px-3 py-3 border-b border-white/10 bg-white/5 sm:px-4 sm:py-4 lg:px-6 lg:py-5">
                  <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-2 sm:space-x-3">
                          <div class="w-8 h-8 rounded-lg bg-transparent flex items-center justify-center text-white sm:w-10 sm:h-10 lg:w-12 lg:h-12" v-html="icons.chart"></div>
                          <div>
                              <h2 class="text-base font-semibold text-white sm:text-lg lg:text-xl">Student Performance</h2>
                              <p class="text-xs text-white/70 hidden sm:block lg:text-sm">Academic progress overview</p>
                          </div>
                      </div>
                      <div class="text-xs text-white/60 sm:text-sm">
                          {{ students.length }}
                      </div>
                  </div>
              </div>
              
              <!-- Students List - Mobile optimized -->
              <div class="p-3 space-y-2 sm:p-4 sm:space-y-3 lg:p-6 lg:space-y-4">
                  <div
                      v-for="student in students"
                      :key="student.id"
                      class="group backdrop-blur-sm bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-xl transition-all duration-300"
                  >
                      <!-- Student header - Mobile first -->
                      <div class="p-3 cursor-pointer sm:p-4 lg:p-6" @click="toggleStudent(student.id)">
                          <div class="flex items-center justify-between w-full">
                              <div class="flex items-center space-x-2 flex-1 min-w-0 sm:space-x-3">
                                  <div class="w-8 h-8 flex-shrink-0 rounded-full bg-gradient-to-r from-emerald-500 to-blue-600 flex items-center justify-center text-white font-semibold text-xs sm:w-10 sm:h-10 sm:text-sm lg:w-12 lg:h-12">
                                      {{ student.first_name.charAt(0) }}{{ student.last_name.charAt(0) }}
                                  </div>
                                  <div class="flex-1 min-w-0">
                                      <h3 class="font-semibold text-white text-sm truncate sm:text-base lg:text-lg">
                                          {{ student.first_name }} {{ student.last_name }}
                                      </h3>
                                      <div class="flex flex-col space-y-1 text-xs text-white/70 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-3 sm:text-sm">
                                          <span class="flex items-center space-x-1">
                                              <div class="w-3 h-3 flex-shrink-0" v-html="icons.book"></div>
                                              <span>Grade {{ student.class?.grade_level ?? 'N/A' }}</span>
                                          </span>
                                          <span class="flex items-center space-x-1">
                                              <div class="w-3 h-3 flex-shrink-0" v-html="icons.school"></div>
                                              <span class="truncate">{{ student.class?.name ?? 'N/A' }}</span>
                                          </span>
                                      </div>
                                  </div>
                              </div>
                              
                              <!-- Performance overview - Mobile optimized -->
                              <div class="flex items-center space-x-2 sm:space-x-3">
                                  <div v-if="student.grades && student.grades.length > 0" class="text-right">
                                      <div class="text-base font-bold text-white sm:text-lg lg:text-xl">{{ calculateGPA(student.grades) }}</div>
                                      <div class="text-xs text-white/60">GPA</div>
                                  </div>
                                  <div v-else class="text-right">
                                      <div class="text-base font-bold text-white/40 sm:text-lg lg:text-xl">—</div>
                                      <div class="text-xs text-white/40">No Data</div>
                                  </div>
                                  
                                  <!-- Status indicator - Mobile friendly -->
                                  <div v-if="student.grades && student.grades.length > 0" 
                                       :class="[getPerformanceStatus(calculateGPA(student.grades)).bg, getPerformanceStatus(calculateGPA(student.grades)).color]"
                                       class="px-2 py-1 rounded-full text-xs font-medium sm:px-3 sm:py-2 sm:text-sm">
                                      <span class="hidden sm:inline">{{ getPerformanceStatus(calculateGPA(student.grades)).text }}</span>
                                      <span class="sm:hidden">{{ getPerformanceStatus(calculateGPA(student.grades)).text.charAt(0) }}</span>
                                  </div>
                                  
                                  <!-- Expand/collapse indicator -->
                                  <div 
                                      :class="[expandedStudents.has(student.id) ? 'rotate-180' : '', 'w-4 h-4 text-white/60 transition-transform duration-200 flex-shrink-0 sm:w-5 sm:h-5']"
                                      v-html="icons.chevronDown"
                                  ></div>
                              </div>
                          </div>
                      </div>

                      <!-- Expandable detailed grades section - Mobile first -->
                      <div v-if="expandedStudents.has(student.id) && student.grades && student.grades.length > 0" 
                           class="border-t border-white/10 p-3 bg-white/5 sm:p-4 lg:p-6">
                          
                          <!-- Performance statistics - Mobile grid -->
                          <div class="grid grid-cols-2 gap-2 mb-3 sm:grid-cols-4 sm:gap-3 sm:mb-4 lg:mb-6">
                              <div class="text-center p-2 bg-emerald-500/10 rounded-lg border border-emerald-400/20 sm:p-3">
                                  <div class="w-4 h-4 mx-auto mb-1 text-emerald-300 sm:w-6 sm:h-6 sm:mb-2" v-html="icons.star"></div>
                                  <div class="text-sm font-bold text-emerald-100 sm:text-base lg:text-lg">{{ getGradeStats(student.grades).excellent }}</div>
                                  <div class="text-xs text-emerald-200/70">Excellence</div>
                              </div>
                              <div class="text-center p-2 bg-blue-500/10 rounded-lg border border-blue-400/20 sm:p-3">
                                  <div class="w-4 h-4 mx-auto mb-1 text-blue-300 sm:w-6 sm:h-6 sm:mb-2" v-html="icons.good"></div>
                                  <div class="text-sm font-bold text-blue-100 sm:text-base lg:text-lg">{{ getGradeStats(student.grades).good }}</div>
                                  <div class="text-xs text-blue-200/70">Good</div>
                              </div>
                              <div class="text-center p-2 bg-amber-500/10 rounded-lg border border-amber-400/20 sm:p-3">
                                  <div class="w-4 h-4 mx-auto mb-1 text-amber-300 sm:w-6 sm:h-6 sm:mb-2" v-html="icons.average"></div>
                                  <div class="text-sm font-bold text-amber-100 sm:text-base lg:text-lg">{{ getGradeStats(student.grades).average }}</div>
                                  <div class="text-xs text-amber-200/70">Average</div>
                              </div>
                              <div class="text-center p-2 bg-red-500/10 rounded-lg border border-red-400/20 sm:p-3">
                                  <div class="w-4 h-4 mx-auto mb-1 text-red-300 sm:w-6 sm:h-6 sm:mb-2" v-html="icons.trending"></div>
                                  <div class="text-sm font-bold text-red-100 sm:text-base lg:text-lg">{{ getGradeStats(student.grades).failed }}</div>
                                  <div class="text-xs text-red-200/70">Need Focus</div>
                              </div>
                          </div>

                          <!-- Subject grades - Mobile optimized -->
                          <div class="space-y-2 sm:space-y-3">
                              <h4 class="text-white font-semibold flex items-center space-x-2 text-sm sm:text-base">
                                  <div class="w-4 h-4" v-html="icons.target"></div>
                                  <span>Subject Performance</span>
                              </h4>
                              <div class="space-y-2 sm:space-y-3">
                                  <div 
                                      v-for="subject in [...new Set(student.grades.map(g => g.subject.name))]" 
                                      :key="subject"
                                      class="bg-white/5 rounded-lg border border-white/10 overflow-hidden"
                                  >
                                      <div class="p-3 sm:p-4">
                                          <div class="flex items-center justify-between mb-2 sm:mb-3">
                                              <div class="flex items-center space-x-2">
                                                  <div class="w-2 h-2 bg-white rounded-full"></div>
                                                  <h5 class="font-medium text-white text-sm sm:text-base">{{ subject }}</h5>
                                              </div>
                                              <div class="flex items-center space-x-1 sm:space-x-2">
                                                  <span class="text-xs text-white/60">Avg:</span>
                                                  <div class="px-2 py-1 bg-white/10 rounded text-xs font-semibold text-white sm:text-sm">
                                                      {{ calculateSubjectAverage(student.grades, subject) }}
                                                  </div>
                                              </div>
                                          </div>
                                          
                                          <!-- Quarter grades - Mobile responsive -->
                                          <div class="grid grid-cols-4 gap-1 sm:gap-2">
                                              <div
                                                  v-for="q in quarters"
                                                  :key="q"
                                                  class="text-center"
                                              >
                                                  <div class="text-xs text-white mb-1">{{ q }}</div>
                                                  <div
                                                      :class="['w-full px-1 py-1 text-white rounded-lg text-xs font-semibold border transition-colors sm:px-2 sm:py-2 sm:text-sm', 
                                                              getGradeColor(student.grades.find(g => g.subject.name === subject && g.quarter === q)?.grade)]"
                                                  >
                                                      {{ getGradeDisplay(student.grades.find(g => g.subject.name === subject && g.quarter === q)?.grade) }}
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Empty State - Mobile optimized -->
          <div v-else class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-xl overflow-hidden">
              <div class="p-6 sm:p-8 lg:p-12">
                  <div class="text-center">
                      <div class="w-12 h-12 mx-auto rounded-xl bg-transparent flex items-center justify-center mb-3 text-white/60 sm:w-16 sm:h-16 sm:mb-4 lg:w-20 lg:h-20" v-html="icons.chart"></div>
                      <h3 class="text-base font-semibold text-white/80 mb-2 sm:text-lg lg:text-xl">No Students Enrolled</h3>
                      <p class="text-sm text-white/60 max-w-md mx-auto sm:text-base">
                          Academic data will appear here once students are enrolled.
                      </p>
                  </div>
              </div>
          </div>

          <!-- Performance Scale Legend - Mobile first -->
          <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-xl overflow-hidden">
              <div class="px-3 py-3 border-b border-white/10 bg-white/5 sm:px-4 sm:py-4 lg:px-6 lg:py-5">
                  <div class="flex items-center space-x-2 sm:space-x-3">
                      <div class="w-8 h-8 rounded-lg bg-transparent flex items-center justify-center text-white sm:w-10 sm:h-10 lg:w-12 lg:h-12" v-html="icons.award"></div>
                      <div>
                          <h2 class="text-base font-semibold text-white sm:text-lg lg:text-xl">Performance Scale</h2>
                          <p class="text-xs text-white/70 hidden sm:block lg:text-sm">Academic achievement indicators</p>
                      </div>
                  </div>
              </div>

              <div class="p-3 sm:p-4 lg:p-6">
                  <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3 lg:gap-4">
                      <div class="group bg-emerald-500/10 hover:bg-emerald-500/15 border border-emerald-400/30 hover:border-emerald-400/50 rounded-xl p-3 text-center transition-all duration-300 sm:p-4 lg:p-6">
                          <div class="w-5 h-5 mx-auto mb-2 text-emerald-300 sm:w-6 sm:h-6 sm:mb-3 lg:text-4xl" v-html="icons.star"></div>
                          <div class="font-semibold text-emerald-100 text-xs mb-1 sm:text-sm lg:text-base">Excellence</div>
                          <div class="text-emerald-200/80 text-xs sm:text-sm">90-100 Points</div>
                          <div class="text-emerald-300/60 text-xs mt-1">Outstanding</div>
                      </div>
                      <div class="group bg-blue-500/10 hover:bg-blue-500/15 border border-blue-400/30 hover:border-blue-400/50 rounded-xl p-3 text-center transition-all duration-300 sm:p-4 lg:p-6">
                          <div class="w-5 h-5 mx-auto mb-2 text-blue-300 sm:w-6 sm:h-6 sm:mb-3" v-html="icons.good"></div>
                          <div class="font-semibold text-blue-100 text-xs mb-1 sm:text-sm lg:text-base">Good</div>
                          <div class="text-blue-200/80 text-xs sm:text-sm">80-89 Points</div>
                          <div class="text-blue-300/60 text-xs mt-1">Above Average</div>
                      </div>
                      <div class="group bg-amber-500/10 hover:bg-amber-500/15 border border-amber-400/30 hover:border-amber-400/50 rounded-xl p-3 text-center transition-all duration-300 sm:p-4 lg:p-6">
                          <div class="w-5 h-5 mx-auto mb-2 text-amber-300 sm:w-6 sm:h-6 sm:mb-3" v-html="icons.average"></div>
                          <div class="font-semibold text-amber-100 text-xs mb-1 sm:text-sm lg:text-base">Average</div>
                          <div class="text-amber-200/80 text-xs sm:text-sm">75-79 Points</div>
                          <div class="text-amber-300/60 text-xs mt-1">Meeting Standards</div>
                      </div>
                      <div class="group bg-red-500/10 hover:bg-red-500/15 border border-red-400/30 hover:border-red-400/50 rounded-xl p-3 text-center transition-all duration-300 sm:p-4 lg:p-6">
                          <div class="w-5 h-5 mx-auto mb-2 text-red-300 sm:w-6 sm:h-6 sm:mb-3" v-html="icons.trending"></div>
                          <div class="font-semibold text-red-100 text-xs mb-1 sm:text-sm lg:text-base">Needs Support</div>
                          <div class="text-red-200/80 text-xs sm:text-sm">Below 75 Points</div>
                          <div class="text-red-300/60 text-xs mt-1">Requires Attention</div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Mobile-first responsive design with glass morphism */
.backdrop-blur-xl {
  backdrop-filter: blur(16px);
}

.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

/* Smooth transitions */
.transition-all {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.transition-colors {
  transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
}

.transition-transform {
  transition: transform 0.2s ease-in-out;
}

/* Ensure icons scale properly */
[v-html] svg {
  width: 100%;
  height: 100%;
}

/* Enhanced hover effects for cards */
.group:hover {
  transform: translateY(-1px);
}

/* Mobile touch targets */
.cursor-pointer {
  min-height: 44px;
  display: flex;
  align-items: center;
}

/* Focus states for accessibility */
.cursor-pointer:focus-visible {
  outline: 2px solid rgba(59, 130, 246, 0.5);
  outline-offset: 2px;
  border-radius: 0.75rem;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}

/* Mobile-specific improvements */
@media (max-width: 640px) {
  /* Ensure proper touch spacing */
  .space-y-2 > * + * {
    margin-top: 0.5rem;
  }
  
  /* Better text contrast on mobile */
  .text-white\/70 {
    color: rgba(255, 255, 255, 0.8);
  }
  
  /* Improved button sizes for mobile */
  .rounded-xl {
    border-radius: 0.75rem;
  }
}

/* Tablet improvements */
@media (min-width: 640px) and (max-width: 1024px) {
  .space-y-3 > * + * {
    margin-top: 0.75rem;
  }
}

/* Desktop enhancements */
@media (min-width: 1024px) {
  .space-y-4 > * + * {
    margin-top: 1rem;
  }
  
  .space-y-6 > * + * {
    margin-top: 1.5rem;
  }
}

/* Reduce motion for accessibility */
@media (prefers-reduced-motion: reduce) {
  .transition-all,
  .transition-colors,
  .transition-transform {
    transition: none;
  }
  
  .group:hover {
    transform: none;
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .bg-white\/10 {
    background-color: rgba(255, 255, 255, 0.2);
  }
  
  .border-white\/20 {
    border-color: rgba(255, 255, 255, 0.4);
  }
  
  .text-white\/70 {
    color: rgba(255, 255, 255, 0.9);
  }
}
</style>