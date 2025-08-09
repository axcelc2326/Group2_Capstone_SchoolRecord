<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
  students: Array,
});

const quarters = ['Q1', 'Q2', 'Q3', 'Q4'];

// Ultra-modern grade color mapping with high contrast
const getGradeColor = (grade) => {
  if (!grade || grade === '—') return 'text-slate-800 bg-gradient-to-br from-gray-100 to-gray-200 border border-gray-300';
  const numGrade = parseFloat(grade);
  if (numGrade >= 90) return 'text-white bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 border border-blue-500 shadow-lg shadow-blue-500/30';
  if (numGrade >= 80) return 'text-white bg-gradient-to-br from-sky-600 via-blue-700 to-blue-800 border border-sky-400 shadow-lg shadow-sky-500/20';
  if (numGrade >= 75) return 'text-white bg-gradient-to-br from-cyan-600 via-sky-700 to-blue-800 border border-cyan-400 shadow-lg shadow-cyan-500/20';
  return 'text-white bg-gradient-to-br from-slate-600 via-slate-700 to-slate-800 border border-slate-400 shadow-lg shadow-slate-500/20';
};

// Get ultra-modern grade display with performance indicators
const getGradeDisplay = (grade) => {
  if (!grade || grade === '—') return '—';
  const numGrade = parseFloat(grade);
  return numGrade <= 74 ? 'Fail' : grade;
};

// Get performance icon
const getPerformanceIcon = (grade) => {
  if (!grade || grade === '—') return '📋';
  const numGrade = parseFloat(grade);
  if (numGrade >= 90) return '🏆';
  if (numGrade >= 80) return '⭐';
  if (numGrade >= 75) return '✨';
  return '📈';
};

// Calculate subject average
const calculateSubjectAverage = (grades, subject) => {
  const subjectGrades = grades.filter(g => g.subject.name === subject && g.grade !== '—' && g.grade);
  if (subjectGrades.length === 0) return '—';
  
  const sum = subjectGrades.reduce((acc, g) => acc + parseFloat(g.grade), 0);
  return (sum / subjectGrades.length).toFixed(1);
};

// Calculate overall GPA
const calculateGPA = (grades) => {
  const validGrades = grades.filter(g => g.grade !== '—' && g.grade);
  if (validGrades.length === 0) return '—';
  
  const sum = validGrades.reduce((acc, g) => acc + parseFloat(g.grade), 0);
  return (sum / validGrades.length).toFixed(1);
};

// Get ultra-modern performance status with high contrast
const getPerformanceStatus = (gpa) => {
  if (gpa === '—') return { 
    text: 'No Data', 
    color: 'text-slate-900', 
    bgColor: 'bg-gradient-to-br from-gray-100 to-gray-200',
    icon: '📋',
    ringColor: 'ring-slate-500/20'
  };
  const numGpa = parseFloat(gpa);
  if (numGpa >= 90) return { 
    text: 'Exceptional', 
    color: 'text-white', 
    bgColor: 'bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800',
    icon: '🏆',
    ringColor: 'ring-blue-500/30'
  };
  if (numGpa >= 80) return { 
    text: 'Excellent', 
    color: 'text-white', 
    bgColor: 'bg-gradient-to-br from-sky-600 via-blue-700 to-blue-800',
    icon: '⭐',
    ringColor: 'ring-sky-500/30'
  };
  if (numGpa >= 75) return { 
    text: 'Good', 
    color: 'text-white', 
    bgColor: 'bg-gradient-to-br from-cyan-600 via-sky-700 to-blue-800',
    icon: '✨',
    ringColor: 'ring-cyan-500/30'
  };
  return { 
    text: 'Needs Focus', 
    color: 'text-white', 
    bgColor: 'bg-gradient-to-br from-slate-600 via-slate-700 to-slate-800',
    icon: '📈',
    ringColor: 'ring-slate-500/30'
  };
};

// Get grade statistics for performance cards
const getGradeStats = (grades) => {
  return {
    excellent: grades.filter(g => parseFloat(g.grade) >= 90).length,
    good: grades.filter(g => parseFloat(g.grade) >= 80 && parseFloat(g.grade) < 90).length,
    average: grades.filter(g => parseFloat(g.grade) >= 75 && parseFloat(g.grade) < 80).length,
    failed: grades.filter(g => parseFloat(g.grade) <= 74).length
  };
};
</script>

<template>
  <Head title="Academic Dashboard" />

  <AuthenticatedLayout>
    <!-- Ultra-Modern Mobile-First Layout -->
    <div class="relative z-10 min-h-screen">
      <!-- Ultra-Modern Header Section - Mobile Optimized -->
      <div class="mb-8 lg:mb-12">
        <div class="flex flex-col space-y-6 lg:flex-row lg:items-center lg:justify-between lg:space-y-0">
          <div class="flex items-center gap-4 lg:gap-6">
            <div class="relative">
              <div class="w-12 h-12 lg:w-16 lg:h-16 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-700 rounded-2xl lg:rounded-3xl flex items-center justify-center shadow-2xl shadow-blue-500/25 ring-4 ring-blue-500/10">
                <span class="text-white text-2xl lg:text-3xl filter drop-shadow-lg">🎓</span>
              </div>
              <div class="absolute -inset-1 bg-gradient-to-br from-blue-500/20 to-indigo-500/20 rounded-2xl lg:rounded-3xl blur-xl -z-10"></div>
            </div>
            <div>
              <h1 class="text-3xl lg:text-5xl text-white mb-1 lg:mb-2 font-black tracking-tight">
                Academic Dashboard
              </h1>
              <p class="text-slate-300 text-sm lg:text-xl font-medium">Track academic progress & achievements</p>
            </div>
          </div>
        </div>
      </div>

      <!-- No Students Default State -->
      <div v-if="!students || students.length === 0" class="text-center py-16 lg:py-24">
        <div class="max-w-2xl mx-auto px-4">
          <div class="relative mb-8">
            <!-- Main Icon Container -->
            <div class="relative">
              <div class="w-32 h-32 lg:w-40 lg:h-40 mx-auto bg-gradient-to-br from-slate-700 via-blue-800 to-indigo-900 rounded-3xl lg:rounded-4xl flex items-center justify-center mb-8 shadow-2xl ring-4 ring-blue-500/20">
                <svg class="w-16 h-16 lg:w-20 lg:h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                </svg>
              </div>
              <div class="absolute -inset-4 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-3xl lg:rounded-4xl blur-2xl"></div>
            </div>
            
            <!-- Floating Academic Icons -->
            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-8">
              <div class="flex space-x-4 lg:space-x-6">
                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-br from-amber-400 to-yellow-500 rounded-xl flex items-center justify-center shadow-lg animate-float-gentle">
                  <span class="text-amber-900 text-sm lg:text-base">📚</span>
                </div>
                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-br from-emerald-400 to-green-500 rounded-xl flex items-center justify-center shadow-lg animate-float-gentle" style="animation-delay: 1s;">
                  <span class="text-emerald-900 text-sm lg:text-base">✏️</span>
                </div>
                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-br from-purple-400 to-violet-500 rounded-xl flex items-center justify-center shadow-lg animate-float-gentle" style="animation-delay: 2s;">
                  <span class="text-purple-900 text-sm lg:text-base">🎯</span>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div>
              <h2 class="text-3xl lg:text-4xl font-black text-white mb-4 tracking-tight">
                No Students Registered
              </h2>
              <p class="text-lg lg:text-xl text-slate-300 max-w-xl mx-auto leading-relaxed font-medium">
                Welcome to your Academic Dashboard! Once students are enrolled and registered, their academic performance and progress will be displayed here.
              </p>
            </div>

            <!-- Feature Preview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mt-12">
              <!-- GPA Tracking -->
              <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 rounded-2xl blur-xl opacity-75 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative bg-gradient-to-br from-slate-800 via-blue-900 to-indigo-900 rounded-2xl p-6 border border-slate-600 hover:border-slate-500 transition-all duration-500">
                  <div class="text-center">
                    <div class="w-12 h-12 mx-auto bg-blue-500/20 rounded-xl flex items-center justify-center mb-4">
                      <span class="text-2xl">📊</span>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">GPA Tracking</h3>
                    <p class="text-sm text-slate-300">Monitor overall academic performance with real-time GPA calculations</p>
                  </div>
                </div>
              </div>

              <!-- Grade Analytics -->
              <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500/20 to-teal-500/20 rounded-2xl blur-xl opacity-75 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative bg-gradient-to-br from-slate-800 via-emerald-900 to-teal-900 rounded-2xl p-6 border border-slate-600 hover:border-slate-500 transition-all duration-500">
                  <div class="text-center">
                    <div class="w-12 h-12 mx-auto bg-emerald-500/20 rounded-xl flex items-center justify-center mb-4">
                      <span class="text-2xl">📈</span>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Grade Analytics</h3>
                    <p class="text-sm text-slate-300">Detailed subject-wise performance analysis and progress tracking</p>
                  </div>
                </div>
              </div>

              <!-- Performance Insights -->
              <div class="relative group md:col-span-2 lg:col-span-1">
                <div class="absolute -inset-1 bg-gradient-to-r from-purple-500/20 to-violet-500/20 rounded-2xl blur-xl opacity-75 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative bg-gradient-to-br from-slate-800 via-purple-900 to-violet-900 rounded-2xl p-6 border border-slate-600 hover:border-slate-500 transition-all duration-500">
                  <div class="text-center">
                    <div class="w-12 h-12 mx-auto bg-purple-500/20 rounded-xl flex items-center justify-center mb-4">
                      <span class="text-2xl">🎯</span>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Performance Insights</h3>
                    <p class="text-sm text-slate-300">Visual performance indicators and achievement tracking</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Cards -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mt-8">
              <div class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-blue-600/20 to-indigo-600/20 rounded-2xl border border-blue-500/30 backdrop-blur-sm">
                <div class="w-3 h-3 bg-blue-400 rounded-full animate-pulse"></div>
                <span class="text-sm font-bold text-blue-200">Awaiting student enrollment</span>
              </div>
              
              <div class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-emerald-600/20 to-teal-600/20 rounded-2xl border border-emerald-500/30 backdrop-blur-sm">
                <div class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                <span class="text-sm font-bold text-emerald-200">Ready for grade tracking</span>
              </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-12 p-6 lg:p-8 bg-gradient-to-br from-slate-800/50 to-blue-900/50 rounded-2xl border border-slate-600 backdrop-blur-sm">
              <div class="flex flex-col lg:flex-row items-center gap-6 lg:gap-8 text-center lg:text-left">
                <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center flex-shrink-0">
                  <span class="text-2xl">💡</span>
                </div>
                <div class="flex-1">
                  <h3 class="text-xl font-bold text-white mb-2">Getting Started</h3>
                  <p class="text-slate-300 leading-relaxed">
                    This dashboard will automatically populate with student data once enrollment is complete. 
                    Each student's academic journey will be tracked with detailed grade reports, performance analytics, 
                    and progress monitoring across all subjects and quarters.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Existing Student Cards - Only show when students exist -->
      <div v-else class="space-y-8 lg:space-y-12">
        <div 
          v-for="(student, idx) in students" 
          :key="student.id" 
          class="group relative"
        >
          
          <!-- Glow effect -->
          <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/10 via-sky-500/5 to-indigo-500/10 rounded-2xl lg:rounded-3xl blur-2xl group-hover:blur-3xl transition-all duration-700 opacity-0 group-hover:opacity-100"></div>
          
          <div class="relative bg-transparent backdrop-blur-2xl border border-slate-600 rounded-2xl lg:rounded-3xl shadow-2xl hover:shadow-blue-500/10 transition-all duration-700 overflow-hidden">
            
            <!-- Ultra-Modern Student Header - Mobile Optimized -->
            <div class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 p-6 lg:p-10 text-white overflow-hidden">
              <!-- Background pattern -->
              <div class="absolute inset-0 bg-gradient-to-br from-blue-600/10 to-indigo-600/10 opacity-50"></div>
              <div class="absolute top-0 right-0 w-48 lg:w-96 h-48 lg:h-96 bg-gradient-to-bl from-blue-500/10 to-transparent rounded-full transform translate-x-24 lg:translate-x-48 -translate-y-24 lg:-translate-y-48"></div>
              
              <div class="relative z-10">
                <!-- Mobile Layout -->
                <div class="flex flex-col space-y-6 lg:hidden">
                  <div class="flex items-center gap-4">
                    <div class="relative">
                      <div class="w-16 h-16 bg-white/20 backdrop-blur-xl rounded-2xl flex items-center justify-center text-2xl font-black text-white shadow-2xl border border-white/30 ring-4 ring-white/10">
                        {{ student.first_name.charAt(0) }}{{ student.last_name.charAt(0) }}
                      </div>
                      <div class="absolute -inset-2 bg-gradient-to-br from-white/20 to-white/5 rounded-2xl blur-xl"></div>
                    </div>
                    <div class="flex-1">
                      <h2 class="text-2xl font-black text-white mb-2 tracking-tight">{{ student.first_name }} {{ student.last_name }}</h2>
                      <div class="flex flex-wrap gap-2">
                        <span class="flex items-center gap-2 px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-full border border-white/30 text-xs font-semibold">
                          <div class="w-2 h-2 bg-blue-300 rounded-full animate-pulse"></div>
                          <span class="text-white">{{ student.class?.name ?? 'N/A' }}</span>
                        </span>
                        <span class="flex items-center gap-2 px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-full border border-white/30 text-xs font-semibold">
                          <div class="w-2 h-2 bg-sky-300 rounded-full animate-pulse delay-150"></div>
                          <span class="text-white">Grade {{ student.class?.grade_level ?? 'N/A' }}</span>
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Mobile GPA Display -->
                  <div v-if="student.grades && student.grades.length > 0" class="text-center">
                    <div class="inline-flex items-center gap-4 bg-white/15 backdrop-blur-xl rounded-2xl p-4 border border-white/30">
                      <div>
                        <div class="text-white text-xs font-semibold uppercase tracking-wide mb-1">Overall GPA</div>
                        <div class="text-3xl font-black text-white">{{ calculateGPA(student.grades) }}</div>
                      </div>
                      <div class="w-px h-12 bg-white/30"></div>
                      <div class="text-center">
                        <div class="text-2xl mb-1">{{ getPerformanceStatus(calculateGPA(student.grades)).icon }}</div>
                        <div class="text-xs font-bold text-white">{{ getPerformanceStatus(calculateGPA(student.grades)).text }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Desktop Layout -->
                <div class="hidden lg:flex items-center justify-between">
                  <div class="flex items-center gap-8">
                    <div class="relative">
                      <div class="w-20 h-20 bg-white/20 backdrop-blur-xl rounded-3xl flex items-center justify-center text-3xl font-black text-white shadow-2xl border border-white/30 ring-4 ring-white/10">
                        {{ student.first_name.charAt(0) }}{{ student.last_name.charAt(0) }}
                      </div>
                      <div class="absolute -inset-2 bg-gradient-to-br from-white/20 to-white/5 rounded-3xl blur-xl"></div>
                    </div>
                    <div>
                      <h2 class="text-4xl font-black text-white mb-3 tracking-tight">{{ student.first_name }} {{ student.last_name }}</h2>
                      <div class="flex items-center gap-6">
                        <span class="flex items-center gap-3 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full border border-white/30 font-semibold">
                          <div class="w-2 h-2 bg-white rounded-full animate-pulse shadow-lg shadow-gray-500"></div>
                          <span class="text-white">{{ student.class?.name ?? 'N/A' }}</span>
                        </span>
                        <span class="flex items-center gap-3 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full border border-white/30 font-semibold">
                          <div class="w-2 h-2 bg-white rounded-full animate-pulse shadow-lg shadow-gray-500"></div>
                          <span class="text-white">Grade {{ student.class?.grade_level ?? 'N/A' }}</span>
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <div v-if="student.grades && student.grades.length > 0" class="text-right">
                    <div class="text-6xl font-black text-white mb-2 filter drop-shadow-lg">
                      {{ calculateGPA(student.grades) }}
                    </div>
                    <div class="text-white text-xl font-semibold">Overall GPA</div>
                    <div class="flex items-center justify-end gap-2 mt-2">
                      <span class="text-2xl">{{ getPerformanceStatus(calculateGPA(student.grades)).icon }}</span>
                      <span class="text-sm font-bold text-white">{{ getPerformanceStatus(calculateGPA(student.grades)).text }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="p-4 lg:p-10">
              <!-- Performance Overview Cards - Mobile-First Grid -->
              <div v-if="student.grades && student.grades.length > 0" class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6 lg:mb-12">
                <!-- Excellent Performance -->
                <div class="group/card relative">
                  <div class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-xl lg:rounded-2xl p-4 lg:p-6 border border-slate-500 hover:border-slate-600 transition-all duration-500 hover:scale-105">
                    <div class="text-center">
                      <div class="text-2xl lg:text-3xl mb-2">🏆</div>
                      <div class="text-white text-xs lg:text-sm mb-1 font-bold">Excellent</div>
                      <div class="text-2xl lg:text-3xl font-black text-white">{{ getGradeStats(student.grades).excellent }}</div>
                      <div class="text-xs text-white font-semibold">90+ grades</div>
                    </div>
                  </div>
                </div>

                <!-- Good Performance -->
                <div class="group/card relative">
                  <div class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-xl lg:rounded-2xl p-4 lg:p-6 border border-slate-500 hover:border-slate-600 transition-all duration-500 hover:scale-105">
                    <div class="text-center">
                      <div class="text-2xl lg:text-3xl mb-2">⭐</div>
                      <div class="text-white text-xs lg:text-sm mb-1 font-bold">Good</div>
                      <div class="text-2xl lg:text-3xl font-black text-white">{{ getGradeStats(student.grades).good }}</div>
                      <div class="text-xs text-white font-semibold">80-89 grades</div>
                    </div>
                  </div>
                </div>

                <!-- Average Performance -->
                <div class="group/card relative">
                  <div class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-xl lg:rounded-2xl p-4 lg:p-6 border border-slate-500 hover:border-slate-600 transition-all duration-500 hover:scale-105">
                    <div class="text-center">
                      <div class="text-2xl lg:text-3xl mb-2">✨</div>
                      <div class="text-white text-xs lg:text-sm mb-1 font-bold">Average</div>
                      <div class="text-2xl lg:text-3xl font-black text-white">{{ getGradeStats(student.grades).average }}</div>
                      <div class="text-xs text-white font-semibold">75-79 grades</div>
                    </div>
                  </div>
                </div>

                <!-- Needs Focus -->
                <div class="group/card relative">
                  <div class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-xl lg:rounded-2xl p-4 lg:p-6 border border-slate-500 hover:border-slate-600 transition-all duration-500 hover:scale-105">
                    <div class="text-center">
                      <div class="text-2xl lg:text-3xl mb-2">📈</div>
                      <div class="text-white text-xs lg:text-sm mb-1 font-bold">Focus Area</div>
                      <div class="text-2xl lg:text-3xl font-black text-white">{{ getGradeStats(student.grades).failed }}</div>
                      <div class="text-xs text-white font-semibold">≤74 grades</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Table View - Mobile-First -->
              <div v-if="student.grades && student.grades.length > 0">
                <h3 class="text-xl lg:text-2xl font-black text-white mb-6 lg:mb-8 flex items-center gap-4">
                  <div class="w-3 lg:w-4 h-3 lg:h-4 bg-white rounded-full animate-pulse shadow-lg shadow-gray-500"></div>
                  Detailed Grade Report
                </h3>
                
                <div class="relative">
                  <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/10 via-sky-500/5 to-indigo-500/10 rounded-2xl lg:rounded-3xl blur-2xl"></div>
                  <div class="relative bg-transparent backdrop-blur-2xl rounded-2xl lg:rounded-3xl shadow-xl border border-blue-200 overflow-hidden">
                    
                    <!-- Mobile Table -->
                    <div class="lg:hidden">
                      <div class="divide-y divide-slate-200">
                        <div 
                          v-for="subject in [...new Set(student.grades.map(g => g.subject.name))]" 
                          :key="subject"
                          class="p-4"
                        >
                          <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                              <div class="w-2 h-2 bg-white rounded-full animate-pulse shadow-lg shadow-gray-500"></div>
                              <span class="font-bold text-white text-sm">{{ subject }}</span>
                            </div>
                            <div :class="['inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold ring-2', getGradeColor(calculateSubjectAverage(student.grades, subject))]">
                              <span>{{ getPerformanceIcon(calculateSubjectAverage(student.grades, subject)) }}</span>
                              <span>{{ getGradeDisplay(calculateSubjectAverage(student.grades, subject)) }}</span>
                            </div>
                          </div>
                          
                          <div class="grid grid-cols-4 gap-2">
                            <div
                              v-for="q in quarters"
                              :key="q"
                              class="text-center"
                            >
                              <div class="text-xs font-semibold text-white mb-1">{{ q }}</div>
                              <div :class="['inline-block px-2 py-1 rounded-lg text-xs font-bold', getGradeColor(student.grades.find(g => g.subject.name === subject && g.quarter === q)?.grade)]">
                                {{ getGradeDisplay(student.grades.find(g => g.subject.name === subject && g.quarter === q)?.grade) }}
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden lg:block overflow-x-auto">
                      <table class="w-full">
                        <thead class="bg-white/30 backdrop-blur-sm">
                          <tr>
                            <th class="p-6 text-left font-bold text-white">
                              <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <span>Subject</span>
                              </div>
                            </th>
                            <th 
                              v-for="q in quarters" 
                              :key="q" 
                              class="p-6 text-center font-bold text-white"
                            >
                              <div class="flex items-center justify-center space-x-2">
                                <div class="w-2 h-2 bg-white rounded-full animate-pulse shadow-lg shadow-gray-500"></div>
                                <span>{{ q }}</span>
                              </div>
                            </th>
                            <th class="p-6 text-center font-bold text-white">
                              <div class="flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span>Average</span>
                              </div>
                            </th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                          <tr
                            v-for="subject in [...new Set(student.grades.map(g => g.subject.name))]"
                            :key="subject"
                          >
                            <td class="p-6">
                              <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-white rounded-full animate-pulse shadow-lg shadow-gray-500"></div>
                                <span class="font-semibold text-white">{{ subject }}</span>
                              </div>
                            </td>
                            <td
                              v-for="q in quarters"
                              :key="q"
                              class="p-6 text-center"
                            >
                              <div :class="['inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold ring-2 transition-all duration-300 hover:scale-105', getGradeColor(student.grades.find(g => g.subject.name === subject && g.quarter === q)?.grade)]">
                                <span>{{ getPerformanceIcon(student.grades.find(g => g.subject.name === subject && g.quarter === q)?.grade) }}</span>
                                <span>{{ getGradeDisplay(student.grades.find(g => g.subject.name === subject && g.quarter === q)?.grade) }}</span>
                              </div>
                            </td>
                            <td class="p-6 text-center">
                              <div :class="['inline-flex items-center gap-3 px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg ring-4 transition-all duration-300 hover:scale-105', getGradeColor(calculateSubjectAverage(student.grades, subject))]">
                                <span class="text-lg">{{ getPerformanceIcon(calculateSubjectAverage(student.grades, subject)) }}</span>
                                <span>{{ getGradeDisplay(calculateSubjectAverage(student.grades, subject)) }}</span>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                <!-- Ultra-Modern Grade Legend - Mobile-First -->
                <div class="mt-6 lg:mt-8">
                  <div class="relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/10 via-sky-500/5 to-indigo-500/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-transparent backdrop-blur-2xl rounded-2xl p-4 lg:p-6 border border-blue-200 shadow-xl">
                      <h4 class="text-lg lg:text-xl font-black text-white mb-4 flex items-center space-x-3">
                        <div class="w-3 h-3 bg-white rounded-full animate-pulse shadow-lg shadow-gray-500"></div>
                        <span>Performance Scale</span>
                      </h4>
                      
                      <!-- Mobile Legend -->
                      <div class="grid grid-cols-2 gap-3 lg:hidden">
                        <div class="flex items-center space-x-2 p-3 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-xl lg:rounded-2xl lg:p-6 border border-slate-500 hover:border-slate-600 transition-all duration-500 hover:scale-105">
                          <div class="text-lg">🏆</div>
                          <div>
                            <div class="text-xs font-bold text-white">Excellent</div>
                            <div class="text-xs text-white font-semibold">90-100</div>
                          </div>
                        </div>
                        <div class="flex items-center space-x-2 p-3 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-xl lg:rounded-2xl lg:p-6 border border-slate-500 hover:border-slate-600 transition-all duration-500 hover:scale-105">
                          <div class="text-lg">⭐</div>
                          <div>
                            <div class="text-xs font-bold text-white">Good</div>
                            <div class="text-xs text-white font-semibold">80-89</div>
                          </div>
                        </div>
                        <div class="flex items-center space-x-2 p-3 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-xl lg:rounded-2xl lg:p-6 border border-slate-500 hover:border-slate-600 transition-all duration-500 hover:scale-105">
                          <div class="text-lg">✨</div>
                          <div>
                            <div class="text-xs font-bold text-white">Average</div>
                            <div class="text-xs text-white font-semibold">75-79</div>
                          </div>
                        </div>
                        <div class="flex items-center space-x-2 p-3 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-xl lg:rounded-2xl lg:p-6 border border-slate-500 hover:border-slate-600 transition-all duration-500 hover:scale-105">
                          <div class="text-lg">📈</div>
                          <div>
                            <div class="text-xs font-bold text-white">Focus</div>
                            <div class="text-xs text-white font-semibold">≤74</div>
                          </div>
                        </div>
                      </div>

                      <!-- Desktop Legend -->
                      <div class="hidden lg:grid grid-cols-4 gap-6">
                        <div class="flex items-center space-x-4 p-4 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-xl lg:rounded-2xl lg:p-6 border border-slate-500 hover:border-slate-600 transition-all duration-500 hover:scale-105">
                          <div class="w-12 h-12 bg-transparent rounded-xl flex items-center justify-center">
                            <span class="text-white text-xl">🏆</span>
                          </div>
                          <div>
                            <div class="font-bold text-white mb-1">Excellent</div>
                            <div class="text-sm text-white font-semibold">90-100 Range</div>
                          </div>
                        </div>
                        <div class="flex items-center space-x-4 p-4 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-xl lg:rounded-2xl lg:p-6 border border-slate-500 hover:border-slate-600 transition-all duration-500 hover:scale-105">
                          <div class="w-12 h-12 bg-transparent rounded-xl flex items-center justify-center">
                            <span class="text-white text-xl">⭐</span>
                          </div>
                          <div>
                            <div class="font-bold text-white mb-1">Good</div>
                            <div class="text-sm text-white font-semibold">80-89 Range</div>
                          </div>
                        </div>
                        <div class="flex items-center space-x-4 p-4 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-xl lg:rounded-2xl lg:p-6 border border-slate-500 hover:border-slate-600 transition-all duration-500 hover:scale-105">
                          <div class="w-12 h-12 bg-transparent rounded-xl flex items-center justify-center">
                            <span class="text-white text-xl">✨</span>
                          </div>
                          <div>
                            <div class="font-bold text-white mb-1">Average</div>
                            <div class="text-sm text-white font-semibold">75-79 Range</div>
                          </div>
                        </div>
                        <div class="flex items-center space-x-4 p-4 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-xl lg:rounded-2xl lg:p-6 border border-slate-500 hover:border-slate-600 transition-all duration-500 hover:scale-105">
                          <div class="w-12 h-12 bg-transparent rounded-xl flex items-center justify-center">
                            <span class="text-white text-xl">📈</span>
                          </div>
                          <div>
                            <div class="font-bold text-white mb-1">Focus Area</div>
                            <div class="text-sm text-white font-semibold">≤74 Range</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Ultra-Modern Empty State - Mobile Optimized -->
              <div v-else class="text-center py-12 lg:py-16">
                <div class="relative">
                  <div class="w-20 h-20 lg:w-24 lg:h-24 mx-auto bg-gradient-to-br from-slate-200 to-slate-300 rounded-2xl lg:rounded-3xl flex items-center justify-center mb-6 shadow-xl ring-4 ring-slate-300/50">
                    <svg class="w-10 h-10 lg:w-12 lg:h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                  </div>
                  <div class="absolute -top-1 -right-1 lg:-top-2 lg:-right-2 w-6 h-6 lg:w-8 lg:h-8 bg-gradient-to-r from-amber-400 to-yellow-400 rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-3 h-3 lg:w-4 lg:h-4 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                  </div>
                </div>
                <h3 class="text-xl lg:text-2xl font-black text-slate-900 mb-3">No Academic Records</h3>
                <p class="text-slate-700 max-w-sm lg:max-w-md mx-auto leading-relaxed text-sm lg:text-base px-4 font-medium">
                  Academic performance data will appear here once grades are entered by teachers. Please check back regularly for updates.
                </p>
                <div class="mt-6 inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-blue-50 to-sky-50 rounded-2xl border border-blue-300">
                  <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                  <span class="text-sm font-bold text-blue-800">Awaiting grade submission</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Ultra-Modern Mobile-First Styles with High Contrast */

/* Enhanced Custom Animations */
@keyframes pulse-slow {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.05); }
}

@keyframes float-gentle {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-5px) rotate(1deg); }
}

@keyframes shimmer {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

.animate-pulse-slow {
  animation: pulse-slow 4s ease-in-out infinite;
}

.animate-float-gentle {
  animation: float-gentle 6s ease-in-out infinite;
}

/* Modern Glassmorphism Effects - Mobile Optimized */
.backdrop-blur-2xl {
  backdrop-filter: blur(40px);
  -webkit-backdrop-filter: blur(40px);
}

.backdrop-blur-xl {
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
}

.backdrop-blur-sm {
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

/* Ultra-Modern Scrollbar */
::-webkit-scrollbar {
  width: 4px;
  height: 4px;
}

@media (min-width: 1024px) {
  ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
  }
}

::-webkit-scrollbar-track {
  background: rgba(248, 250, 252, 0.5);
  border-radius: 8px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8, #1e40af);
  border-radius: 8px;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #1d4ed8, #1e40af, #1e3a8a);
}

/* Advanced Shadow Effects - Mobile Optimized */
.shadow-xl {
  box-shadow: 
    0 10px 25px -3px rgba(0, 0, 0, 0.1),
    0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

@media (min-width: 1024px) {
  .shadow-2xl {
    box-shadow: 
      0 25px 50px -12px rgba(0, 0, 0, 0.25),
      0 0 0 1px rgba(255, 255, 255, 0.05);
  }
}

/* Modern Button Hover Effects - Touch-Friendly */
@media (hover: hover) {
  button:hover {
    transform: translateY(-1px);
  }

  button:active {
    transform: translateY(0);
  }

  .group\/card:hover {
    transform: translateY(-2px);
  }

  @media (min-width: 1024px) {
    .group\/card:hover {
      transform: translateY(-4px);
    }
  }
}

/* Enhanced Ring Effects */
.ring-2 {
  box-shadow: 0 0 0 2px var(--tw-ring-color);
}

.ring-4 {
  box-shadow: 0 0 0 4px var(--tw-ring-color);
}

/* Modern Typography Enhancement - Mobile Optimized */
.font-black {
  font-weight: 900;
  letter-spacing: -0.025em;
}

@media (min-width: 768px) {
  .font-black {
    letter-spacing: -0.035em;
  }
}

/* Performance Optimizations */
.group {
  will-change: transform;
}

.group\/card {
  will-change: transform;
}

/* Enhanced Focus States - Touch-Friendly */
button:focus-visible,
.group\/btn:focus-visible {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

@media (min-width: 1024px) {
  button:focus-visible,
  .group\/btn:focus-visible {
    outline-offset: 4px;
  }
}

/* Mobile-First Responsive Utilities */
@media (max-width: 640px) {
  .text-responsive {
    font-size: 0.875rem;
  }
}

/* Modern Gradient Text Effects */
.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Touch-Friendly Interactive Elements */
@media (max-width: 1023px) {
  button,
  .group\/card {
    min-height: 44px;
    min-width: 44px;
  }
}

/* Performance-First Animations */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}

/* High Contrast Text Colors */
.text-slate-900 {
  color: #0f172a;
}

.text-slate-800 {
  color: #1e293b;
}

.text-slate-700 {
  color: #334155;
}

/* Dark Mode Support with High Contrast */
@media (prefers-color-scheme: dark) {
  .bg-white\/95 {
    background-color: rgba(30, 41, 59, 0.95);
  }
  
  .text-slate-900 {
    color: rgba(248, 250, 252, 0.95);
  }
  
  .text-slate-800 {
    color: rgba(248, 250, 252, 0.9);
  }
  
  .text-slate-700 {
    color: rgba(226, 232, 240, 0.85);
  }
  
  .border-white\/70 {
    border-color: rgba(51, 65, 85, 0.7);
  }
}
</style>