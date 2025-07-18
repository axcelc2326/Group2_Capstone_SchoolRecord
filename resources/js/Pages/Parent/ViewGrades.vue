<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  students: Array,
});

const quarters = ['Q1', 'Q2', 'Q3', 'Q4'];

// Modern grade color mapping with fail threshold at 74
const getGradeColor = (grade) => {
  if (!grade || grade === '—') return 'text-slate-400 bg-slate-50';
  const numGrade = parseFloat(grade);
  if (numGrade >= 90) return 'text-emerald-700 bg-emerald-50 border border-emerald-200';
  if (numGrade >= 80) return 'text-blue-700 bg-blue-50 border border-blue-200';
  if (numGrade >= 75) return 'text-amber-700 bg-amber-50 border border-amber-200';
  return 'text-red-700 bg-red-50 border border-red-200';
};

// Get grade display text
const getGradeDisplay = (grade) => {
  if (!grade || grade === '—') return '—';
  const numGrade = parseFloat(grade);
  return numGrade <= 74 ? 'Fail' : grade;
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

// Get performance status
const getPerformanceStatus = (gpa) => {
  if (gpa === '—') return { text: 'No Data', color: 'text-slate-500' };
  const numGpa = parseFloat(gpa);
  if (numGpa >= 90) return { text: 'Excellent', color: 'text-emerald-600' };
  if (numGpa >= 80) return { text: 'Good', color: 'text-blue-600' };
  if (numGpa >= 75) return { text: 'Average', color: 'text-amber-600' };
  return { text: 'Needs Improvement', color: 'text-red-600' };
};
</script>

<template>
  <Head title="View Grades" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <div>
            <h2 class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
              Academic Dashboard
            </h2>
            <p class="text-slate-500 mt-1">Track your child's academic progress</p>
          </div>
        </div>
      </div>
    </template>

    <div class="py-8 px-6 bg-gradient-to-br from-slate-50 to-gray-100 min-h-screen">
      <div class="max-w-7xl mx-auto space-y-8">
        <div
          v-for="student in students"
          :key="student.id"
          class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl border border-white/20 overflow-hidden hover:shadow-2xl transition-all duration-500"
        >
          <!-- Modern Student Header -->
          <div class="relative bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative flex items-center justify-between">
              <div class="flex items-center space-x-6">
                <div class="relative">
                  <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-3xl flex items-center justify-center ring-4 ring-white/30">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                  </div>
                </div>
                <div>
                  <h3 class="text-3xl font-bold text-white mb-2">{{ student.first_name }} {{ student.last_name }}</h3>
                  <div class="flex items-center space-x-8 text-white/80">
                    <div class="flex items-center space-x-2">
                      <div class="w-5 h-5 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.84L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.84l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                        </svg>
                      </div>
                      <span class="font-medium">{{ student.class?.name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <div class="w-5 h-5 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </div>
                      <span class="font-medium">Grade {{ student.class?.grade_level ?? 'N/A' }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="student.grades && student.grades.length > 0" class="text-right">
                <div class="text-white/60 text-sm font-medium uppercase tracking-wide">Overall GPA</div>
                <div class="text-4xl font-bold text-white mt-1">{{ calculateGPA(student.grades) }}</div>
                <div :class="['text-sm font-medium mt-1', getPerformanceStatus(calculateGPA(student.grades)).color.replace('text-', 'text-white/')]">
                  {{ getPerformanceStatus(calculateGPA(student.grades)).text }}
                </div>
              </div>
            </div>
          </div>

          <!-- Modern Grade Content -->
          <div class="p-8">
            <div v-if="student.grades && student.grades.length > 0">
              <!-- Performance Overview Cards -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 p-4 rounded-2xl border border-emerald-200">
                  <div class="text-emerald-600 text-sm font-semibold">Excellent (90+)</div>
                  <div class="text-2xl font-bold text-emerald-700">
                    {{ student.grades.filter(g => parseFloat(g.grade) >= 90).length }}
                  </div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-2xl border border-blue-200">
                  <div class="text-blue-600 text-sm font-semibold">Good (80-89)</div>
                  <div class="text-2xl font-bold text-blue-700">
                    {{ student.grades.filter(g => parseFloat(g.grade) >= 80 && parseFloat(g.grade) < 90).length }}
                  </div>
                </div>
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-4 rounded-2xl border border-amber-200">
                  <div class="text-amber-600 text-sm font-semibold">Average (75-79)</div>
                  <div class="text-2xl font-bold text-amber-700">
                    {{ student.grades.filter(g => parseFloat(g.grade) >= 75 && parseFloat(g.grade) < 80).length }}
                  </div>
                </div>
                <div class="bg-gradient-to-br from-red-50 to-red-100 p-4 rounded-2xl border border-red-200">
                  <div class="text-red-600 text-sm font-semibold">Failed (≤74)</div>
                  <div class="text-2xl font-bold text-red-700">
                    {{ student.grades.filter(g => parseFloat(g.grade) <= 74).length }}
                  </div>
                </div>
              </div>

              <!-- Modern Grade Table -->
              <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-200">
                <div class="overflow-x-auto">
                  <table class="w-full">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100">
                      <tr>
                        <th class="p-6 text-left font-bold text-slate-700">
                          <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span>Subject</span>
                          </div>
                        </th>
                        <th 
                          v-for="q in quarters" 
                          :key="q" 
                          class="p-6 text-center font-bold text-slate-700"
                        >
                          <div class="flex items-center justify-center space-x-2">
                            <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                            <span>{{ q }}</span>
                          </div>
                        </th>
                        <th class="p-6 text-center font-bold text-slate-700">
                          <div class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span>Average</span>
                          </div>
                        </th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                      <tr
                        v-for="subject in [...new Set(student.grades.map(g => g.subject.name))]"
                        :key="subject"
                        class="hover:bg-slate-50/50 transition-colors duration-200"
                      >
                        <td class="p-6">
                          <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full"></div>
                            <span class="font-semibold text-slate-800">{{ subject }}</span>
                          </div>
                        </td>
                        <td
                          v-for="q in quarters"
                          :key="q"
                          class="p-6 text-center"
                        >
                          <span
                            :class="[
                              'inline-flex items-center px-3 py-2 rounded-xl text-sm font-bold transition-all duration-200',
                              getGradeColor(student.grades.find(g => g.subject.name === subject && g.quarter === q)?.grade)
                            ]"
                          >
                            {{
                              getGradeDisplay(student.grades.find(
                                g => g.subject.name === subject && g.quarter === q
                              )?.grade)
                            }}
                          </span>
                        </td>
                        <td class="p-6 text-center">
                          <span
                            :class="[
                              'inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold shadow-sm',
                              getGradeColor(calculateSubjectAverage(student.grades, subject))
                            ]"
                          >
                            {{ getGradeDisplay(calculateSubjectAverage(student.grades, subject)) }}
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Modern Grade Legend -->
              <div class="mt-8 p-6 bg-gradient-to-r from-slate-50 to-slate-100 rounded-2xl border border-slate-200">
                <h4 class="text-lg font-bold text-slate-800 mb-4 flex items-center space-x-2">
                  <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span>Grading Scale</span>
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div class="flex items-center space-x-3 p-3 bg-white rounded-xl">
                    <div class="w-4 h-4 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full shadow-sm"></div>
                    <span class="text-sm font-medium text-slate-700">90-100 (Excellent)</span>
                  </div>
                  <div class="flex items-center space-x-3 p-3 bg-white rounded-xl">
                    <div class="w-4 h-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full shadow-sm"></div>
                    <span class="text-sm font-medium text-slate-700">80-89 (Good)</span>
                  </div>
                  <div class="flex items-center space-x-3 p-3 bg-white rounded-xl">
                    <div class="w-4 h-4 bg-gradient-to-r from-amber-500 to-amber-600 rounded-full shadow-sm"></div>
                    <span class="text-sm font-medium text-slate-700">75-79 (Average)</span>
                  </div>
                  <div class="flex items-center space-x-3 p-3 bg-white rounded-xl">
                    <div class="w-4 h-4 bg-gradient-to-r from-red-500 to-red-600 rounded-full shadow-sm"></div>
                    <span class="text-sm font-medium text-slate-700">≤74 (Fail)</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modern Empty State -->
            <div v-else class="text-center py-16">
              <div class="relative">
                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-slate-100 to-slate-200 rounded-3xl flex items-center justify-center mb-6 shadow-lg">
                  <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-yellow-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                </div>
              </div>
              <h3 class="text-2xl font-bold text-slate-800 mb-3">No grades recorded yet</h3>
              <p class="text-slate-500 max-w-md mx-auto leading-relaxed">
                Academic records will appear here once grades have been entered by your child's teachers. Check back soon!
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>