<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ApexCharts from 'vue3-apexcharts';
import { router } from '@inertiajs/vue3';
import { computed, ref, nextTick, watch } from 'vue';
import { 
  BarChart3, 
  PieChart, 
  TrendingUp, 
  Users, 
  BookOpen, 
  Award, 
  Target, 
  School,
  ChevronLeft,
  ChevronRight,
  LineChart
} from 'lucide-vue-next';

const props = defineProps({
    analytics: Array,
    pagination: Object
});

const selectedChart = ref('bar');
const chartKey = ref(0);

watch(selectedChart, async () => {
    chartKey.value++;
    await nextTick();
});

const goToPage = (page) => {
    if (page && page !== '...') {
        const baseUrl = props.pagination?.path || '';
        const url = `${baseUrl}?page=${page}`;
        router.visit(url);
    }
};

const viewGradeLevelStudents = (gradeLevel) => {
    router.get(route('analytics.grade.students', { gradeLevel }));
};

// Chart configurations
const getDonutChartOptions = (classData, idx) => ({
  chart: {
    id: `subject-donut-${idx}-${chartKey.value}`,
    type: 'donut',
    fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
    background: 'transparent',
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 800,
    }
  },
  colors: ['#60a5fa', '#3b82f6', '#2563eb', '#1d4ed8', '#1e40af', '#93c5fd'],
  labels: Object.keys(classData.subject_averages || {}),
  dataLabels: {
    enabled: true,
    style: {
      fontSize: '12px',
      fontWeight: 600,
      colors: ['#ffffff']
    }
  },
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        labels: {
          show: true,
          total: {
            show: true,
            label: 'Average',
            fontSize: '16px',
            fontWeight: 600,
            color: '#ffffff',
            formatter: () => {
              const values = Object.values(classData.subject_averages || {});
              if (values.length === 0) return '0%';
              const avg = values.reduce((sum, val) => sum + val, 0) / values.length;
              return Math.round(avg) + '%'; // Removed decimal
            }
          }
        }
      }
    }
  },
  legend: {
    position: 'bottom',
    fontSize: '12px',
    fontWeight: 500,
    labels: {
      colors: '#ffffff'
    }
  },
  tooltip: {
    theme: 'dark',
    y: {
      formatter: (val) => Math.round(val) + '%' // Removed decimal
    }
  }
});

const getAreaChartOptions = (classData, idx) => ({
  chart: {
    id: `subject-area-${idx}-${chartKey.value}`,
    type: 'area',
    fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
    toolbar: { show: false },
    background: 'transparent',
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 800
    }
  },
  xaxis: {
    categories: Object.keys(classData.subject_averages || {}),
    labels: {
      style: {
        colors: '#ffffff',
        fontSize: '12px',
        fontWeight: 500
      }
    },
    axisBorder: { show: false },
    axisTicks: { show: false }
  },
  yaxis: {
    min: 0,
    max: 100,
    labels: {
      style: {
        colors: '#ffffff',
        fontSize: '12px',
        fontWeight: 500
      }
    }
  },
  colors: ['#60a5fa'],
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'dark',
      gradientToColors: ['#3b82f6'],
      shadeIntensity: 0.8,
      type: 'vertical',
      opacityFrom: 0.8,
      opacityTo: 0.1
    }
  },
  stroke: {
    width: 2,
    curve: 'smooth'
  },
  grid: {
    borderColor: 'rgba(255, 255, 255, 0.1)',
    strokeDashArray: 3
  },
  tooltip: {
    theme: 'dark',
    y: {
      formatter: (val) => Math.round(val) + '%' // Removed decimal
    }
  }
});

const getBarChartOptions = (classData, idx) => ({
  chart: {
    id: `subject-bar-${idx}-${chartKey.value}`,
    type: 'bar',
    fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
    toolbar: { show: false },
    background: 'transparent',
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 600
    }
  },
  xaxis: {
    categories: Object.keys(classData.subject_averages || {}),
    labels: {
      style: {
        colors: '#ffffff',
        fontSize: '12px',
        fontWeight: 500
      }
    },
    axisBorder: { show: false },
    axisTicks: { show: false }
  },
  yaxis: {
    min: 0,
    max: 100,
    labels: {
      style: {
        colors: '#ffffff',
        fontSize: '12px',
        fontWeight: 500
      }
    }
  },
  colors: ['#60a5fa'],
  plotOptions: {
    bar: {
      borderRadius: 6,
      columnWidth: '70%',
    }
  },
  grid: {
    borderColor: 'rgba(255, 255, 255, 0.1)',
    strokeDashArray: 3
  },
  tooltip: {
    theme: 'dark',
    y: {
      formatter: (val) => Math.round(val) + '%' // Removed decimal
    }
  },
  dataLabels: {
    enabled: true,
    style: {
      colors: ['#ffffff'],
      fontSize: '11px',
      fontWeight: 600
    }
  }
});

const getChartData = (classData) => {
  const subjects = classData?.subject_averages || {};
  
  switch (selectedChart.value) {
    case 'donut':
      return Object.values(subjects);
    case 'area':
      return [{
        name: 'Average Grade',
        data: Object.values(subjects)
      }];
    case 'bar':
      return [{
        name: 'Performance',
        data: Object.values(subjects)
      }];
    default:
      return [];
  }
};

const getChartOptions = (classData, idx) => {
  switch (selectedChart.value) {
    case 'donut':
      return getDonutChartOptions(classData, idx);
    case 'area':
      return getAreaChartOptions(classData, idx);
    case 'bar':
      return getBarChartOptions(classData, idx);
    default:
      return {};
  }
};

// Performance calculations
const getPerformanceLevel = (average) => {
  if (average >= 90) return { 
    label: 'Exceptional', 
    color: 'bg-emerald-500/20 text-emerald-100 border-emerald-500/30', 
    icon: Award
  };
  if (average >= 80) return { 
    label: 'Excellent', 
    color: 'bg-blue-500/20 text-blue-100 border-blue-500/30', 
    icon: TrendingUp
  };
  if (average >= 70) return { 
    label: 'Good', 
    color: 'bg-violet-500/20 text-violet-100 border-violet-500/30', 
    icon: BarChart3
  };
  return { 
    label: 'Needs Support', 
    color: 'bg-amber-500/20 text-amber-100 border-amber-500/30', 
    icon: Target
  };
};

const getOverallAverage = (classData) => {
  const values = Object.values(classData?.subject_averages || {});
  if (values.length === 0) return 0;
  return values.reduce((sum, val) => sum + val, 0) / values.length;
};

const getClassDisplayName = (classData) => {
  return `Grade ${classData.grade_level} Classes`;
};

const getTotalStudents = (classData) => {
  return classData.total_students || 0;
};

const getSubjectsCount = (classData) => {
  return Object.keys(classData.subject_averages || {}).length;
};

const pages = computed(() => {
    if (!props.pagination) return [];
    const current = props.pagination.current_page;
    const last = props.pagination.last_page;
    const pagesArray = [];

    if (last <= 5) {
        for (let i = 1; i <= last; i++) pagesArray.push(i);
    } else {
        if (current <= 3) {
            pagesArray.push(1, 2, 3, 4, '...', last);
        } else if (current >= last - 2) {
            pagesArray.push(1, '...', last - 3, last - 2, last - 1, last);
        } else {
            pagesArray.push(1, '...', current - 1, current, current + 1, '...', last);
        }
    }
    return pagesArray;
});

const hasValidChartData = (classData) => {
  return classData && 
         classData.subject_averages && 
         Object.keys(classData.subject_averages).length > 0 &&
         Object.values(classData.subject_averages).some(val => !isNaN(val) && val !== null);
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-white">
                            Performance Analytics
                        </h2>
                        <p class="text-white/70 mt-1">
                            Interactive performance insights & visualizations
                        </p>
                    </div>
                    <LineChart class="w-8 h-8 text-white/60" />
                </div>
            </div>
        </template>

        <!-- Desktop container -->
        <div class="px-6 py-6 space-y-8 max-w-7xl mx-auto">
          <!-- Analytics Cards -->
          <div class="space-y-8">
              <div v-for="(classData, idx) in analytics" :key="`class-${idx}-${classData.grade_level}`" 
                    class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg overflow-hidden">
                  
                  <!-- Class Header -->
                  <div class="px-8 py-6 border-b border-white/10 bg-white/5">
                      <div class="flex items-center justify-between">
                          <div class="flex items-center space-x-4">
                            <!-- Grade level badge -->
                            <div class="w-16 h-16 rounded-xl bg-gradient-to-r from-emerald-500 to-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                              {{ classData.grade_level || 'N/A' }}
                            </div>

                            <!-- Grade Level + Classes -->
                            <div>
                              <!-- Grade level title -->
                              <h2 class="text-2xl font-bold text-white mb-1 flex items-center gap-2">
                                <School class="w-6 h-6 text-white" />
                                Grade {{ classData.grade_level }}
                              </h2>

                              <!-- ✅ Class names list -->
                              <p class="text-white/80 text-sm">
                                Classes:
                                <span v-for="(className, i) in classData.class_names" :key="i">
                                  {{ className }}<span v-if="i < classData.class_names.length - 1">, </span>
                                </span>
                              </p>

                              <!-- Stats -->
                              <div class="flex items-center gap-4 text-white/70 text-sm mt-1">
                                <span class="flex items-center space-x-2">
                                  <BookOpen class="w-4 h-4" />
                                  <span>{{ getSubjectsCount(classData) }} Subjects</span>
                                </span>
                                <span class="flex items-center space-x-2">
                                  <Users class="w-4 h-4" />
                                  <span>{{ getTotalStudents(classData) }} Students</span>
                                </span>
                              </div>
                            </div>
                          </div>
                         
                          <div class="flex items-center gap-4">
                              <!-- View Students Button -->
                              <button
                                  @click="viewGradeLevelStudents(classData.grade_level)"
                                  class="backdrop-blur-sm bg-white/15 hover:bg-white/25 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 shadow border border-white/20 hover:border-white/30 flex items-center gap-2"
                              >
                                  View Students
                              </button>
                              
                              <!-- Overall Average -->
                              <div class="text-right">
                                  <div class="text-3xl font-bold text-white mb-1">
                                      {{ Math.round(getOverallAverage(classData)) }}% <!-- Removed decimal -->
                                  </div>
                                  <div class="text-white/70 text-sm">Overall Average</div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="p-6">
                      <!-- Performance Summary Cards -->
                      <div class="grid grid-cols-4 gap-4 mb-8">
                          <!-- Overall Performance -->
                          <div class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-lg p-4">
                              <div class="flex items-center gap-4">
                                  <component 
                                    :is="getPerformanceLevel(getOverallAverage(classData)).icon" 
                                    class="w-8 h-8 text-emerald-300" 
                                  />
                                  <div>
                                      <div class="text-white/70 text-xs mb-1">Performance Level</div>
                                      <div :class="['px-3 py-1 rounded text-sm font-medium border', getPerformanceLevel(getOverallAverage(classData)).color]">
                                          {{ getPerformanceLevel(getOverallAverage(classData)).label }}
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <!-- Top Subject -->
                          <div class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-lg p-4">
                              <div class="flex items-center gap-4">
                                  <Award class="w-8 h-8 text-blue-300" />
                                  <div>
                                      <div class="text-white/70 text-xs mb-1">Top Subject</div>
                                      <div class="font-medium text-white text-sm mb-1">{{ classData.top_subject || 'N/A' }}</div>
                                      <div class="text-blue-300 text-xs">
                                          {{ classData.subject_averages && classData.top_subject ? Math.round(classData.subject_averages[classData.top_subject]) : '0' }}% Average <!-- Removed decimal -->
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <!-- Focus Area -->
                          <div class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-lg p-4">
                              <div class="flex items-center gap-4">
                                  <Target class="w-8 h-8 text-amber-300" />
                                  <div>
                                      <div class="text-white/70 text-xs mb-1">Focus Area</div>
                                      <div class="font-medium text-white text-sm mb-1">{{ classData.low_subject || 'N/A' }}</div>
                                      <div class="text-amber-300 text-xs">
                                          {{ classData.subject_averages && classData.low_subject ? Math.round(classData.subject_averages[classData.low_subject]) : '0' }}% Average <!-- Removed decimal -->
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <!-- Promotion/Retention Stats -->
                          <div class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-lg p-4">
                              <div class="flex items-center gap-4">
                                  <TrendingUp class="w-8 h-8 text-violet-300" />
                                  <div>
                                      <div class="text-white/70 text-xs mb-1">Student Progress</div>
                                      <div class="font-medium text-green-300 text-sm mb-1">Promoted: {{ classData.promoted || 0 }}</div>
                                      <div class="font-medium text-amber-300 text-sm">Retained: {{ classData.retained || 0 }}</div>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <!-- Chart Section -->
                      <div v-if="hasValidChartData(classData)" class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-lg p-6 mb-8">
                          <div class="flex items-center justify-between mb-6">
                              <h3 class="text-xl font-semibold text-white flex items-center gap-2">
                                  <BarChart3 class="w-5 h-5" />
                                  Performance Visualization
                              </h3>
                              <!-- Chart Type Selector -->
                              <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-lg p-1">
                                  <div class="flex items-center gap-1">
                                      <button
                                          v-for="type in ['bar', 'area', 'donut']"
                                          :key="type"
                                          @click="selectedChart = type"
                                          :class="[
                                              'px-4 py-2 rounded text-sm font-medium transition-all duration-200 capitalize',
                                              selectedChart === type
                                                  ? 'bg-white/20 text-white shadow backdrop-blur-sm'
                                                  : 'text-white/70 hover:text-white hover:bg-white/10'
                                          ]"
                                      >
                                          {{ type }}
                                      </button>
                                  </div>
                              </div>
                          </div>
                          
                          <!-- Chart Container -->
                          <div class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-lg p-4 min-h-[400px]">
                              <ApexCharts
                                  :key="`chart-${idx}-${selectedChart}-${chartKey}`"
                                  :type="selectedChart"
                                  :height="400"
                                  :options="getChartOptions(classData, idx)"
                                  :series="getChartData(classData)"
                              />
                          </div>
                      </div>

                      <!-- No Chart Data Message -->
                      <div v-else class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-lg p-6 mb-8">
                          <div class="text-center py-6">
                              <BarChart3 class="w-12 h-12 mx-auto text-white/60 mb-3" />
                              <h3 class="text-lg font-semibold text-white/80 mb-2">No Chart Data Available</h3>
                              <p class="text-sm text-white/60">Subject performance data is not available for visualization.</p>
                          </div>
                      </div>

                      <!-- Subject Details Grid -->
                      <div v-if="classData.subject_averages && Object.keys(classData.subject_averages).length > 0">
                          <h3 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                              <TrendingUp class="w-5 h-5" />
                              Subject Performance Breakdown
                          </h3>
                          
                          <div class="grid grid-cols-4 gap-4">
                              <div v-for="(average, subject) in classData.subject_averages" :key="`${subject}-${idx}`"
                                    class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-lg p-4">
                                  
                                  <div class="mb-3">
                                      <h4 class="font-medium text-white text-sm mb-2">{{ subject }}</h4>
                                      <div class="text-white/60 text-xs mb-2">Subject Average</div>
                                      
                                      <!-- Progress bar -->
                                      <div class="w-full bg-white/10 rounded-full h-2 mb-2 overflow-hidden">
                                          <div 
                                              class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full transition-all duration-1000"
                                              :style="{ width: Math.min(Math.max(average || 0, 0), 100) + '%' }"
                                          ></div>
                                      </div>
                                  </div>
                                  
                                  <div class="flex items-center justify-between">
                                      <div class="text-2xl font-bold text-white">
                                          {{ Math.round(average || 0) }}% <!-- Removed decimal -->
                                      </div>
                                      <div :class="['px-2 py-1 rounded text-xs font-medium border', getPerformanceLevel(average || 0).color]">
                                          {{ getPerformanceLevel(average || 0).label }}
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      
                      <!-- No Subject Data Message -->
                      <div v-else class="text-center py-6">
                          <BookOpen class="w-12 h-12 mx-auto text-white/60 mb-3" />
                          <h3 class="text-lg font-semibold text-white/80 mb-2">No Subject Data Available</h3>
                          <p class="text-sm text-white/60">Subject performance breakdown will appear here once data is available.</p>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Pagination -->
          <div v-if="pagination && pagination.last_page > 1" class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg p-6">
              <div class="flex justify-center items-center gap-2">

                  <!-- Previous Button -->
                  <button
                      @click="goToPage(pagination.current_page - 1)"
                      :disabled="pagination.current_page === 1"
                      class="backdrop-blur-sm bg-white/10 hover:bg-white/20 disabled:bg-white/5 text-white disabled:text-white/40 px-4 py-2 rounded font-medium transition-all duration-200 shadow hover:shadow-none disabled:hover:shadow-none border border-white/20 hover:border-white/30 disabled:border-white/10 flex items-center gap-2"
                  >
                      <ChevronLeft class="w-4 h-4" />
                      <span>Previous</span>
                  </button>

                  <!-- Page Numbers -->
                  <template v-for="page in pages" :key="page">
                      <span
                          v-if="page === '...'"
                          class="px-3 py-2 text-white/60 text-sm font-medium"
                      >...</span>
                      <button
                          v-else
                          @click="goToPage(page)"
                          :class="[
                              'px-3 py-2 rounded text-sm font-medium transition-all duration-200 border',
                              page === pagination.current_page
                                  ? 'bg-white/20 text-white shadow border-white/30 backdrop-blur-sm'
                                  : 'bg-white/5 hover:bg-white/15 text-white/70 hover:text-white border-white/10 hover:border-white/20'
                          ]"
                      >
                          {{ page }}
                      </button>
                  </template>

                  <!-- Next Button -->
                  <button
                      @click="goToPage(pagination.current_page + 1)"
                      :disabled="pagination.current_page === pagination.last_page"
                      class="backdrop-blur-sm bg-white/10 hover:bg-white/20 disabled:bg-white/5 text-white disabled:text-white/40 px-4 py-2 rounded font-medium transition-all duration-200 shadow hover:shadow-none disabled:hover:shadow-none border border-white/20 hover:border-white/30 disabled:border-white/10 flex items-center gap-2"
                  >
                      <span>Next</span>
                      <ChevronRight class="w-4 h-4" />
                  </button>
              </div>
          </div>

          <!-- Empty State -->
          <div v-if="!analytics || analytics.length === 0" class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg">
              <div class="p-12">
                  <div class="text-center">
                      <LineChart class="w-16 h-16 mx-auto text-white/60 mb-4" />
                      <h3 class="text-xl font-semibold text-white/80 mb-3">No Analytics Data Available</h3>
                      <p class="text-sm text-white/60 max-w-md mx-auto">
                          Performance analytics will appear here once student data is available.
                      </p>
                  </div>
              </div>
          </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Glassmorphism base effects */
.backdrop-blur-xl {
  backdrop-filter: blur(16px);
}

.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

/* Smooth transitions */
.transition-all {
  transition: all 0.2s ease-in-out;
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

/* Focus states for accessibility */
button:focus-visible {
  outline: 2px solid rgba(59, 130, 246, 0.5);
  outline-offset: 2px;
  border-radius: 0.375rem;
}

/* Chart responsiveness */
.apexcharts-canvas {
  background: transparent !important;
}

/* Progress bar enhancements */
.h-2 {
  position: relative;
  overflow: hidden;
}

/* Reduce motion for accessibility */
@media (prefers-reduced-motion: reduce) {
  .transition-all {
    transition: none;
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