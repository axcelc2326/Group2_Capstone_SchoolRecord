<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { 
  User,
  Users, 
  UserCheck, 
  Award, 
  TrendingUp, 
  BarChart3, 
  School, 
  ChevronDown,
  Target,
  GraduationCap,
  Star,
  CheckCircle,
  Minus,
  AlertCircle,
  FileText,
  Calendar,
  BookOpen
} from 'lucide-vue-next';

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

const getGradeDisplay = (grade) => {
  if (!grade || grade === '—') return '—';
  const numGrade = parseFloat(grade);
  if (numGrade <= 74) return 'F';
  return grade;
};

const calculateSubjectAverage = (grades, subject) => {
  const subjectGrades = grades.filter(g => g.subject === subject && g.grade !== '—' && g.grade);
  if (subjectGrades.length === 0) return '—';
  
  const sum = subjectGrades.reduce((acc, g) => acc + parseFloat(g.grade), 0);
  return (sum / subjectGrades.length).toFixed(1);
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

const getPerformanceStatus = (average) => {
  if (!average || average === '—') return { text: 'No Data', color: 'text-white/80', bg: 'bg-white/10' };
  const num = parseFloat(average);
  if (num >= 90) return { text: 'Excellent', color: 'text-emerald-100', bg: 'bg-emerald-500/20', icon: Star };
  if (num >= 80) return { text: 'Good', color: 'text-blue-100', bg: 'bg-blue-500/20', icon: CheckCircle };
  if (num >= 75) return { text: 'Average', color: 'text-amber-100', bg: 'bg-amber-500/20', icon: Minus };
  return { text: 'Needs Support', color: 'text-red-100', bg: 'bg-red-500/20', icon: AlertCircle };
};

const getRemarksStyle = (remarks) => {
  const remark = remarks?.toLowerCase() || '';
  if (remark.includes('passed') || remark.includes('excellent') || remark.includes('outstanding')) 
    return { color: 'text-emerald-100', bg: 'bg-emerald-500/20' };
  if (remark.includes('good') || remark.includes('satisfactory'))
    return { color: 'text-blue-100', bg: 'bg-blue-500/20' };
  if (remark.includes('progress') || remark.includes('average'))
    return { color: 'text-amber-100', bg: 'bg-amber-500/20' };
  if (remark.includes('failed') || remark.includes('needs improvement'))
    return { color: 'text-red-100', bg: 'bg-red-500/20' };
  return { color: 'text-white/80', bg: 'bg-white/10' };
};

const getGradeColor = (grade) => {
  if (!grade || grade === '—') return 'bg-white/10 text-white/60';
  const num = parseFloat(grade);
  if (num >= 90) return 'bg-emerald-500/20 text-emerald-100 border-emerald-500/30';
  if (num >= 80) return 'bg-blue-500/20 text-blue-100 border-blue-500/30';
  if (num >= 75) return 'bg-amber-500/20 text-amber-100 border-amber-500/30';
  return 'bg-red-500/20 text-red-100 border-red-500/30';
};

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
</script>

<template>
    <Head title="Academic Progress" />

    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-1">
              <div class="flex items-center justify-between">
                  <div>
                      <h2 class="text-lg font-bold text-white sm:text-xl lg:text-2xl">
                          Academic Progress
                      </h2>
                      <p class="text-white/80 mt-1 text-xs sm:text-sm">
                          Track your child's performance
                      </p>
                  </div>
                  <GraduationCap class="hidden sm:block w-6 h-6 text-white/70" />
              </div>
            </div>
        </template>

        <div class="px-3 py-4 space-y-4 max-w-7xl mx-auto sm:px-6 sm:py-6 sm:space-y-6 lg:px-8">
          
          <!-- Quick Stats - Mobile Optimized -->
          <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
              <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl p-4 shadow-lg">
                  <Users class="w-5 h-5 text-white mb-2" />
                  <p class="text-xl font-bold text-white">{{ summary.total_students }}</p>
                  <p class="text-xs text-white/80 mt-1">Total Students</p>
              </div>

              <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl p-4 shadow-lg">
                  <UserCheck class="w-5 h-5 text-white mb-2" />
                  <p class="text-xl font-bold text-white">{{ summary.enrolled_students }}</p>
                  <p class="text-xs text-white/80 mt-1">Active</p>
              </div>

              <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl p-4 shadow-lg">
                  <Award class="w-5 h-5 text-white mb-2" />
                  <p class="text-xl font-bold text-white">{{ summary.excellent_grades }}</p>
                  <p class="text-xs text-white/80 mt-1">Excellence</p>
              </div>

              <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl p-4 shadow-lg">
                  <TrendingUp class="w-5 h-5 text-white mb-2" />
                  <p class="text-xl font-bold text-white">{{ summary.average_gpa }}</p>
                  <p class="text-xs text-white/80 mt-1">Average</p>
              </div>
          </div>

          <!-- Students Cards - Mobile First Design -->
          <div v-if="students && students.length > 0" class="space-y-4">
              <div
                  v-for="student in students"
                  :key="student.id"
                  class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl shadow-lg overflow-hidden"
              >
                  <!-- Student Header Card -->
                  <div class="p-4 bg-gradient-to-br from-white/5 to-transparent">
                      <!-- Student Info -->
                      <div class="flex items-start space-x-3 mb-4">
                          <div class="w-12 h-12 flex-shrink-0 rounded-xl bg-gradient-to-br from-emerald-500 to-blue-600 flex items-center justify-center text-white font-bold text-base shadow-lg">
                              <User class="w-6 h-6" />
                          </div>
                          <div class="flex-1 min-w-0">
                              <h3 class="font-bold text-white text-base mb-1">
                                  {{ student.name }}
                              </h3>
                              <div class="flex items-center space-x-2 text-xs text-white/80">
                                  <School class="w-3 h-3" />
                                  <span>Grade {{ student.class_grade_level ?? 'N/A' }}</span> 
                                  <span>Section - {{ student.class ?? 'N/A' }}</span>
                              </div>
                          </div>
                      </div>

                      <!-- Final Average - Prominent Display -->
                      <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-xl p-3 mb-4">
                          <div class="flex items-center justify-between">
                              <div>
                                  <p class="text-xs text-white/80 mb-1">Final Average</p>
                                  <p class="text-2xl font-bold text-white">
                                      {{ student.final_average ?? '—' }}
                                  </p>
                              </div>
                              <div 
                                   :class="[getPerformanceStatus(student.final_average).bg, getPerformanceStatus(student.final_average).color]"
                                   class="px-3 py-2 rounded-lg border border-white/20 text-center">
                                  <component :is="getPerformanceStatus(student.final_average).icon" class="w-5 h-5 mx-auto mb-1" />
                                  <p class="text-xs font-semibold">{{ getPerformanceStatus(student.final_average).text }}</p>
                              </div>
                          </div>
                      </div>

                      <!-- Remarks -->
                      <div v-if="student.remarks" 
                           :class="[getRemarksStyle(student.remarks).bg, getRemarksStyle(student.remarks).color]"
                           class="rounded-lg p-3 border border-white/20">
                          <div class="flex items-start space-x-2">
                              <FileText class="w-4 h-4 mt-0.5 flex-shrink-0" />
                              <div>
                                  <p class="text-xs font-semibold mb-1">Teacher's Remarks</p>
                                  <p class="text-xs">{{ student.remarks }}</p>
                              </div>
                          </div>
                      </div>

                      <!-- Expand Button -->
                      <button 
                          @click="toggleStudent(student.id)"
                          class="w-full mt-4 py-2 backdrop-blur-sm bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg text-white font-medium transition-all flex items-center justify-center space-x-2">
                          <span class="text-sm">{{ expandedStudents.has(student.id) ? 'Hide' : 'View' }} Detailed Grades</span>
                          <ChevronDown :class="[expandedStudents.has(student.id) ? 'rotate-180' : '', 'w-4 h-4 transition-transform']" />
                      </button>
                  </div>
                  
                  <!-- Detailed Grades Section -->
                  <div v-if="expandedStudents.has(student.id) && student.grades && student.grades.length > 0" 
                       class="border-t border-white/10 p-4 space-y-4 bg-white/5">
                      
                      <!-- Grade Distribution -->
                      <div>
                          <h4 class="text-white font-semibold mb-3 flex items-center space-x-2 text-sm">
                              <BarChart3 class="w-4 h-4" />
                              <span>Grade Distribution</span>
                          </h4>
                          <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                              <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-lg p-3 text-center">
                                  <Star class="w-4 h-4 mx-auto mb-2 text-emerald-300" />
                                  <p class="text-lg font-bold text-emerald-100">{{ getGradeStats(student.grades).excellent }}</p>
                                  <p class="text-xs text-emerald-200 mt-1">Excellent</p>
                                  <p class="text-xs text-emerald-300">90-100</p>
                              </div>
                              <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-3 text-center">
                                  <CheckCircle class="w-4 h-4 mx-auto mb-2 text-blue-300" />
                                  <p class="text-lg font-bold text-blue-100">{{ getGradeStats(student.grades).good }}</p>
                                  <p class="text-xs text-blue-200 mt-1">Good</p>
                                  <p class="text-xs text-blue-300">80-89</p>
                              </div>
                              <div class="bg-amber-500/10 border border-amber-500/30 rounded-lg p-3 text-center">
                                  <Minus class="w-4 h-4 mx-auto mb-2 text-amber-300" />
                                  <p class="text-lg font-bold text-amber-100">{{ getGradeStats(student.grades).average }}</p>
                                  <p class="text-xs text-amber-200 mt-1">Average</p>
                                  <p class="text-xs text-amber-300">75-79</p>
                              </div>
                              <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-3 text-center">
                                  <AlertCircle class="w-4 h-4 mx-auto mb-2 text-red-300" />
                                  <p class="text-lg font-bold text-red-100">{{ getGradeStats(student.grades).failed }}</p>
                                  <p class="text-xs text-red-200 mt-1">Needs Help</p>
                                  <p class="text-xs text-red-300">&lt;75</p>
                              </div>
                          </div>
                      </div>

                      <!-- Subjects Performance -->
                      <div>
                          <h4 class="text-white font-semibold mb-3 flex items-center space-x-2 text-sm">
                              <BookOpen class="w-4 h-4" />
                              <span>Subject Performance</span>
                          </h4>
                          <div class="space-y-3">
                              <div 
                                  v-for="subject in [...new Set(student.grades.map(g => g.subject))]" 
                                  :key="subject"
                                  class="backdrop-blur-sm bg-black/10 rounded-lg border border-white/20 overflow-hidden"
                              >
                                  <!-- Subject Header -->
                                  <div class="bg-white/5 px-3 py-2 border-b border-white/10">
                                      <div class="flex items-center justify-between">
                                          <div class="flex items-center space-x-2">
                                              <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                              <h5 class="font-semibold text-white text-sm">{{ subject }}</h5>
                                          </div>
                                          <div class="flex items-center space-x-2">
                                              <span class="text-xs text-white/70">Average:</span>
                                              <div :class="getGradeColor(calculateSubjectAverage(student.grades, subject))" 
                                                   class="px-2 py-1 rounded text-xs font-bold border">
                                                  {{ calculateSubjectAverage(student.grades, subject) }}
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  
                                  <!-- Quarter Grades -->
                                  <div class="p-3">
                                      <div class="grid grid-cols-4 gap-2">
                                          <div
                                              v-for="q in quarters"
                                              :key="q"
                                              class="text-center"
                                          >
                                              <div class="flex items-center justify-center space-x-1 mb-1">
                                                  <Calendar class="w-3 h-3 text-white/60" />
                                                  <span class="text-xs text-white/80 font-medium">{{ q }}</span>
                                              </div>
                                              <div 
                                                  :class="getGradeColor(student.grades.find(g => g.subject === subject && g.quarter === q)?.grade)"
                                                  class="w-full px-1 py-2 rounded text-sm font-bold border">
                                                  {{ getGradeDisplay(student.grades.find(g => g.subject === subject && g.quarter === q)?.grade) }}
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

          <!-- Empty State -->
          <div v-else class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl shadow-lg p-8">
              <div class="text-center">
                  <BarChart3 class="w-12 h-12 mx-auto text-white/70 mb-4" />
                  <h3 class="text-base font-semibold text-white/90 mb-2">No Students Found</h3>
                  <p class="text-xs text-white/80 max-w-md mx-auto">
                      Academic data will appear here once students are enrolled.
                  </p>
              </div>
          </div>

          <!-- Performance Guide -->
          <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl shadow-lg overflow-hidden">
              <div class="px-4 py-3 border-b border-white/10 bg-white/5">
                  <div class="flex items-center space-x-2">
                      <Award class="w-5 h-5 text-white" />
                      <div>
                          <h2 class="text-sm font-semibold text-white">Grading Scale</h2>
                          <p class="text-xs text-white/80">Performance indicators</p>
                      </div>
                  </div>
              </div>

              <div class="p-4">
                  <div class="space-y-2">
                      <div class="flex items-center justify-between bg-emerald-500/10 border border-emerald-500/30 rounded-lg p-3">
                          <div class="flex items-center space-x-3">
                              <Star class="w-4 h-4 text-emerald-300" />
                              <div>
                                  <p class="font-semibold text-emerald-100 text-xs">Excellent</p>
                                  <p class="text-xs text-emerald-200">Outstanding performance</p>
                              </div>
                          </div>
                          <span class="text-emerald-100 font-bold text-sm">90-100</span>
                      </div>

                      <div class="flex items-center justify-between bg-blue-500/10 border border-blue-500/30 rounded-lg p-3">
                          <div class="flex items-center space-x-3">
                              <CheckCircle class="w-4 h-4 text-blue-300" />
                              <div>
                                  <p class="font-semibold text-blue-100 text-xs">Good</p>
                                  <p class="text-xs text-blue-200">Above average</p>
                              </div>
                          </div>
                          <span class="text-blue-100 font-bold text-sm">80-89</span>
                      </div>

                      <div class="flex items-center justify-between bg-amber-500/10 border border-amber-500/30 rounded-lg p-3">
                          <div class="flex items-center space-x-3">
                              <Minus class="w-4 h-4 text-amber-300" />
                              <div>
                                  <p class="font-semibold text-amber-100 text-xs">Average</p>
                                  <p class="text-xs text-amber-200">Meeting standards</p>
                              </div>
                          </div>
                          <span class="text-amber-100 font-bold text-sm">75-79</span>
                      </div>

                      <div class="flex items-center justify-between bg-red-500/10 border border-red-500/30 rounded-lg p-3">
                          <div class="flex items-center space-x-3">
                              <AlertCircle class="w-4 h-4 text-red-300" />
                              <div>
                                  <p class="font-semibold text-red-100 text-xs">Needs Support</p>
                                  <p class="text-xs text-red-200">Requires attention</p>
                              </div>
                          </div>
                          <span class="text-red-100 font-bold text-sm">&lt;75</span>
                      </div>
                  </div>
              </div>
          </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.backdrop-blur-xl {
  backdrop-filter: blur(16px);
}

.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

button {
  -webkit-tap-highlight-color: transparent;
}

@media (prefers-reduced-motion: reduce) {
  .transition-all,
  .transition-transform {
    transition: none;
  }
}
</style>