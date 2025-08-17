<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ApexCharts from 'vue3-apexcharts';
import { router } from '@inertiajs/vue3';
import { computed, ref, nextTick, watch } from 'vue';

const props = defineProps({
    analytics: Array,
    pagination: Object
});

const selectedChart = ref('bar');
const chartKey = ref(0); // Force re-render key

// Watch for chart type changes and force re-render
watch(selectedChart, async () => {
    chartKey.value++;
    await nextTick();
});

const goToPage = (url) => {
    if (url) router.visit(url);
};

const viewClassStudents = (classId) => {
    router.visit(route('analytics.class.students', { id: classId }))
}

// Enhanced chart configurations with glassmorphism styling
const getDonutChartOptions = (classData, idx) => ({
  chart: {
    id: `subject-donut-${idx}-${chartKey.value}`, // Unique ID with key
    type: 'donut',
    fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
    background: 'transparent',
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 1200,
      animateGradually: {
        enabled: true,
        delay: 150
      }
    },
    dropShadow: {
      enabled: true,
      top: 4,
      left: 0,
      blur: 20,
      opacity: 0.15,
      color: '#ffffff'
    }
  },
  colors: ['#60a5fa', '#3b82f6', '#2563eb', '#1d4ed8', '#1e40af', '#93c5fd', '#dbeafe', '#f0f9ff'],
  labels: Object.keys(classData.subject_averages || {}),
  dataLabels: {
    enabled: true,
    style: {
      fontSize: '14px',
      fontWeight: 700,
      colors: ['#ffffff']
    },
    dropShadow: {
      enabled: true,
      color: '#000000',
      blur: 4,
      opacity: 0.8
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
            fontSize: '18px',
            fontWeight: 800,
            color: '#ffffff',
            formatter: () => {
              const values = Object.values(classData.subject_averages || {});
              if (values.length === 0) return '0%';
              const avg = values.reduce((sum, val) => sum + val, 0) / values.length;
              return avg.toFixed(1) + '%';
            }
          }
        }
      }
    }
  },
  legend: {
    position: 'bottom',
    fontSize: '14px',
    fontWeight: 600,
    labels: {
      colors: '#ffffff'
    },
    markers: {
      width: 12,
      height: 12,
      strokeWidth: 2,
      strokeColor: '#ffffff'
    }
  },
  tooltip: {
    theme: 'dark',
    style: {
      fontSize: '14px'
    },
    y: {
      formatter: (val) => val.toFixed(1) + '%'
    }
  }
});

const getAreaChartOptions = (classData, idx) => ({
  chart: {
    id: `subject-area-${idx}-${chartKey.value}`, // Unique ID with key
    type: 'area',
    fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
    toolbar: { show: false },
    background: 'transparent',
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 1200
    }
  },
  xaxis: {
    categories: Object.keys(classData.subject_averages || {}),
    labels: {
      style: {
        colors: '#ffffff',
        fontSize: '13px',
        fontWeight: 600
      }
    },
    axisBorder: { 
      show: false
    },
    axisTicks: { 
      show: false
    }
  },
  yaxis: {
    min: 0,
    max: 100,
    labels: {
      style: {
        colors: '#ffffff',
        fontSize: '13px',
        fontWeight: 500
      }
    }
  },
  colors: ['#60a5fa'],
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      gradientToColors: ['#3b82f6'],
      shadeIntensity: 0.8,
      type: 'vertical',
      opacityFrom: 0.8,
      opacityTo: 0.1
    }
  },
  stroke: {
    width: 3,
    curve: 'smooth'
  },
  grid: {
    borderColor: 'rgba(255, 255, 255, 0.1)',
    strokeDashArray: 4
  },
  tooltip: {
    theme: 'dark',
    style: {
      fontSize: '14px'
    },
    y: {
      formatter: (val) => val.toFixed(1) + '%'
    }
  },
  markers: {
    size: 6,
    colors: ['#60a5fa'],
    strokeColors: '#ffffff',
    strokeWidth: 2
  }
});

const getBarChartOptions = (classData, idx) => ({
  chart: {
    id: `subject-bar-${idx}-${chartKey.value}`, // Unique ID with key
    type: 'bar',
    fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
    toolbar: { show: false },
    background: 'transparent',
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 1000
    }
  },
  xaxis: {
    categories: Object.keys(classData.subject_averages || {}),
    labels: {
      style: {
        colors: '#ffffff',
        fontSize: '13px',
        fontWeight: 600
      }
    },
    axisBorder: { 
      show: false
    },
    axisTicks: { 
      show: false
    }
  },
  yaxis: {
    min: 0,
    max: 100,
    labels: {
      style: {
        colors: '#ffffff',
        fontSize: '13px',
        fontWeight: 500
      }
    }
  },
  colors: ['#60a5fa'],
  plotOptions: {
    bar: {
      borderRadius: 8,
      columnWidth: '60%',
      distributed: false
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      gradientToColors: ['#3b82f6'],
      shadeIntensity: 0.8,
      type: 'vertical',
      opacityFrom: 1,
      opacityTo: 0.8
    }
  },
  grid: {
    borderColor: 'rgba(255, 255, 255, 0.1)',
    strokeDashArray: 4
  },
  tooltip: {
    theme: 'dark',
    style: {
      fontSize: '14px'
    },
    y: {
      formatter: (val) => val.toFixed(1) + '%'
    }
  },
  dataLabels: {
    enabled: true,
    style: {
      colors: ['#ffffff'],
      fontSize: '12px',
      fontWeight: 700
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

// Performance calculations with glassmorphism color scheme
const getPerformanceLevel = (average) => {
  if (average >= 90) return { 
    label: 'Exceptional', 
    color: 'bg-emerald-400/20 text-emerald-100 border-emerald-400/30', 
    icon: '🏆'
  };
  if (average >= 80) return { 
    label: 'Excellent', 
    color: 'bg-blue-400/20 text-blue-100 border-blue-400/30', 
    icon: '⭐'
  };
  if (average >= 70) return { 
    label: 'Good', 
    color: 'bg-violet-400/20 text-violet-100 border-violet-400/30', 
    icon: '✨'
  };
  return { 
    label: 'Needs Support', 
    color: 'bg-amber-400/20 text-amber-100 border-amber-400/30', 
    icon: '📈'
  };
};

const getOverallAverage = (classData) => {
  const values = Object.values(classData?.subject_averages || {});
  if (values.length === 0) return 0;
  return values.reduce((sum, val) => sum + val, 0) / values.length;
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

const pageUrl = (page) => {
    if (page === '...') return null;
    const baseUrl = props.pagination?.path || '';
    return `${baseUrl}?page=${page}`;
};

// Check if chart data is valid
const hasValidChartData = (classData) => {
  return classData && 
         classData.subject_averages && 
         Object.keys(classData.subject_averages).length > 0 &&
         Object.values(classData.subject_averages).some(val => !isNaN(val) && val !== null);
};

// SVG icons
const icons = {
  analytics: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"></path></svg>`,
  chart: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"></path></svg>`,
  users: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>`,
  award: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21,13.89 7,23 12,20 17,23 15.79,13.88"></polyline></svg>`,
  trending: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22,7 13.5,15.5 8.5,10.5 2,17"></polyline><polyline points="16,7 22,7 22,13"></polyline></svg>`,
  target: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>`,
  school: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>`,
  book: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>`,
  chevronLeft: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15,18 9,12 15,6"></polyline></svg>`,
  chevronRight: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9,18 15,12 9,6"></polyline></svg>`
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-4xl font-bold text-white">
                            Performance Analytics
                        </h2>
                        <p class="text-white/70 mt-1 text-lg">
                            Interactive performance insights & visualizations
                        </p>
                    </div>
                    <div class="w-12 h-12 text-white/60" v-html="icons.analytics"></div>
                </div>
            </div>
        </template>

        <!-- Desktop container -->
        <div class="px-8 py-8 space-y-10 max-w-7xl mx-auto">
          
          <!-- Chart Type Selector -->
          <div class="flex justify-end">
              <div class="backdrop-blur-xl bg-white/10 hover:bg-white/15 border border-white/20 hover:border-white/30 rounded-xl p-1 shadow-lg transition-all duration-300">
                  <div class="flex items-center gap-2">
                      <button
                          v-for="type in ['bar', 'area', 'donut']"
                          :key="type"
                          @click="selectedChart = type"
                          :class="[
                              'px-6 py-3 rounded-lg text-base font-semibold transition-all duration-300 capitalize',
                              selectedChart === type
                                  ? 'bg-white/20 text-white shadow-lg backdrop-blur-sm'
                                  : 'text-white/70 hover:text-white hover:bg-white/10'
                          ]"
                      >
                          {{ type }}
                      </button>
                  </div>
              </div>
          </div>

          <!-- Analytics Cards -->
          <div class="space-y-12">
              <div v-for="(classData, idx) in analytics" :key="`class-${idx}-${classData.id}`" 
                    class="group backdrop-blur-xl bg-white/10 hover:bg-white/15 border border-white/20 hover:border-white/30 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-500 overflow-hidden">
                  
                  <!-- Class Header -->
                  <div class="px-10 py-10 border-b border-white/10 bg-white/5">
                      <div class="flex items-center justify-between">
                          <div class="flex items-center space-x-6">
                              <div class="w-20 h-20 rounded-xl bg-gradient-to-r from-emerald-500 to-blue-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                                  {{ classData.grade_level || 'N/A' }}
                              </div>
                              <div>
                                  <h2 class="text-3xl font-bold text-white mb-2">{{ classData.class || 'Unknown Class' }}</h2>
                                  <div class="flex items-center gap-4 text-white/70 text-base">
                                      <span class="flex items-center space-x-2">
                                          <div class="w-5 h-5" v-html="icons.school"></div>
                                          <span>Grade {{ classData.grade_level || 'N/A' }}</span>
                                      </span>
                                      <span class="flex items-center space-x-2">
                                          <div class="w-5 h-5" v-html="icons.book"></div>
                                          <span>{{ Object.keys(classData.subject_averages || {}).length }} Subjects</span>
                                      </span>
                                      <span class="flex items-center space-x-2">
                                          <div class="w-5 h-5" v-html="icons.users"></div>
                                          <span>{{ classData.total_students || 0 }} Students</span>
                                      </span>
                                  </div>
                              </div>
                          </div>
                          
                          <div class="flex items-center gap-6">
                              <!-- View Students Button -->
                              <button
                                  @click="viewClassStudents(classData.id)"
                                  class="group/btn backdrop-blur-sm bg-white/15 hover:bg-white/25 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20 hover:border-white/30 flex items-center gap-3"
                              >
                                  <div class="w-5 h-5" v-html="icons.users"></div>
                                  <span>View Students</span>
                              </button>
                              
                              <!-- Overall Average -->
                              <div class="text-right">
                                  <div class="text-4xl font-bold text-white mb-1">
                                      {{ getOverallAverage(classData).toFixed(1) }}%
                                  </div>
                                  <div class="text-white/70 text-base">Overall Average</div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="p-10">
                      <!-- Performance Summary Cards -->
                      <div class="grid grid-cols-3 gap-6 mb-12">
                          <!-- Overall Performance -->
                          <div class="backdrop-blur-sm bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-xl p-6 transition-all duration-300 group/card">
                              <div class="flex items-center gap-6">
                                  <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 flex items-center justify-center text-emerald-300 text-2xl border border-emerald-400/30">
                                      {{ getPerformanceLevel(getOverallAverage(classData)).icon }}
                                  </div>
                                  <div>
                                      <div class="text-white/70 text-base mb-1">Performance Level</div>
                                      <div :class="['px-4 py-2 rounded-lg text-base font-semibold border', getPerformanceLevel(getOverallAverage(classData)).color]">
                                          {{ getPerformanceLevel(getOverallAverage(classData)).label }}
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <!-- Top Subject -->
                          <div class="backdrop-blur-sm bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-xl p-6 transition-all duration-300 group/card">
                              <div class="flex items-center gap-6">
                                  <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-blue-500/20 to-blue-600/20 flex items-center justify-center text-blue-300 border border-blue-400/30">
                                      <div class="w-7 h-7" v-html="icons.award"></div>
                                  </div>
                                  <div>
                                      <div class="text-white/70 text-base mb-1">Top Subject</div>
                                      <div class="font-semibold text-white text-base mb-1">{{ classData.top_subject || 'N/A' }}</div>
                                      <div class="text-blue-300 text-sm">
                                          {{ classData.subject_averages && classData.top_subject ? classData.subject_averages[classData.top_subject]?.toFixed(1) : '0' }}% Average
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <!-- Focus Area -->
                          <div class="backdrop-blur-sm bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-xl p-6 transition-all duration-300 group/card">
                              <div class="flex items-center gap-6">
                                  <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-amber-500/20 to-amber-600/20 flex items-center justify-center text-amber-300 border border-amber-400/30">
                                      <div class="w-7 h-7" v-html="icons.target"></div>
                                  </div>
                                  <div>
                                      <div class="text-white/70 text-base mb-1">Focus Area</div>
                                      <div class="font-semibold text-white text-base mb-1">{{ classData.low_subject || 'N/A' }}</div>
                                      <div class="text-amber-300 text-sm">
                                          {{ classData.subject_averages && classData.low_subject ? classData.subject_averages[classData.low_subject]?.toFixed(1) : '0' }}% Average
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <!-- Chart Section -->
                      <div v-if="hasValidChartData(classData)" class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-xl p-8 mb-12">
                          <div class="flex items-center justify-between mb-8">
                              <h3 class="text-2xl font-semibold text-white flex items-center gap-3">
                                  <div class="w-8 h-8" v-html="icons.chart"></div>
                                  Performance Visualization
                              </h3>
                              <div class="px-4 py-2 bg-white/10 rounded-lg text-white/70 text-base font-medium border border-white/20">
                                  {{ selectedChart.toUpperCase() }} View
                              </div>
                          </div>
                          
                          <!-- Chart Container with glassmorphism -->
                          <div class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-xl p-6 min-h-[500px]">
                              <ApexCharts
                                  :key="`chart-${idx}-${selectedChart}-${chartKey}`"
                                  :type="selectedChart"
                                  :height="500"
                                  :options="getChartOptions(classData, idx)"
                                  :series="getChartData(classData)"
                              />
                          </div>
                      </div>

                      <!-- No Chart Data Message -->
                      <div v-else class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-xl p-8 mb-12">
                          <div class="text-center py-8">
                              <div class="w-16 h-16 mx-auto rounded-xl bg-white/10 flex items-center justify-center mb-4 text-white/60" v-html="icons.chart"></div>
                              <h3 class="text-lg font-semibold text-white/80 mb-2">No Chart Data Available</h3>
                              <p class="text-sm text-white/60">Subject performance data is not available for visualization.</p>
                          </div>
                      </div>

                      <!-- Subject Details Grid -->
                      <div v-if="classData.subject_averages && Object.keys(classData.subject_averages).length > 0">
                          <h3 class="text-2xl font-semibold text-white mb-8 flex items-center gap-3">
                              <div class="w-8 h-8" v-html="icons.trending"></div>
                              Subject Performance Breakdown
                          </h3>
                          
                          <div class="grid grid-cols-4 gap-6">
                              <div v-for="(average, subject) in classData.subject_averages" :key="`${subject}-${idx}`"
                                    class="backdrop-blur-sm bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-xl p-6 transition-all duration-300 group/subject hover:scale-105">
                                  
                                  <div class="flex items-center justify-between mb-4">
                                      <div class="flex-1">
                                          <h4 class="font-semibold text-white text-base mb-2">{{ subject }}</h4>
                                          <div class="text-white/60 text-sm mb-3">Subject Average</div>
                                          
                                          <!-- Progress bar -->
                                          <div class="w-full bg-white/10 rounded-full h-3 mb-3 overflow-hidden">
                                              <div 
                                                  class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full transition-all duration-1000"
                                                  :style="{ width: Math.min(Math.max(average || 0, 0), 100) + '%' }"
                                              ></div>
                                          </div>
                                      </div>
                                  </div>
                                  
                                  <div class="flex items-center justify-between">
                                      <div class="text-3xl font-bold text-white">
                                          {{ (average || 0).toFixed(1) }}%
                                      </div>
                                      <div :class="['px-4 py-2 rounded-lg text-sm font-semibold border', getPerformanceLevel(average || 0).color]">
                                          {{ getPerformanceLevel(average || 0).label }}
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      
                      <!-- No Subject Data Message -->
                      <div v-else class="text-center py-8">
                          <div class="w-16 h-16 mx-auto rounded-xl bg-white/10 flex items-center justify-center mb-4 text-white/60" v-html="icons.book"></div>
                          <h3 class="text-lg font-semibold text-white/80 mb-2">No Subject Data Available</h3>
                          <p class="text-sm text-white/60">Subject performance breakdown will appear here once data is available.</p>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Pagination -->
          <div v-if="pagination" class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-xl p-8">
              <div class="flex justify-center items-center gap-3">

                  <!-- Previous Button -->
                  <button
                      @click="goToPage(pagination.prev_page_url)"
                      :disabled="!pagination.prev_page_url"
                      class="group/prev backdrop-blur-sm bg-white/10 hover:bg-white/20 disabled:bg-white/5 text-white disabled:text-white/40 px-6 py-3 rounded-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl disabled:hover:shadow-none border border-white/20 hover:border-white/30 disabled:border-white/10 flex items-center gap-3"
                  >
                      <div class="w-5 h-5" v-html="icons.chevronLeft"></div>
                      <span>Previous</span>
                  </button>

                  <!-- Page Numbers -->
                  <template v-for="page in pages" :key="page">
                      <span
                          v-if="page === '...'"
                          class="px-4 py-3 text-white/60 text-base font-semibold"
                      >...</span>
                      <button
                          v-else
                          @click="goToPage(pageUrl(page))"
                          :class="[
                              'px-4 py-3 rounded-lg text-base font-semibold transition-all duration-300 border',
                              page === pagination.current_page
                                  ? 'bg-white/20 text-white shadow-lg border-white/30 backdrop-blur-sm'
                                  : 'bg-white/5 hover:bg-white/15 text-white/70 hover:text-white border-white/10 hover:border-white/20'
                          ]"
                      >
                          {{ page }}
                      </button>
                  </template>

                  <!-- Next Button -->
                  <button
                      @click="goToPage(pagination.next_page_url)"
                      :disabled="!pagination.next_page_url"
                      class="group/next backdrop-blur-sm bg-white/10 hover:bg-white/20 disabled:bg-white/5 text-white disabled:text-white/40 px-6 py-3 rounded-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl disabled:hover:shadow-none border border-white/20 hover:border-white/30 disabled:border-white/10 flex items-center gap-3"
                  >
                      <span>Next</span>
                      <div class="w-5 h-5" v-html="icons.chevronRight"></div>
                  </button>
              </div>
          </div>

          <!-- Empty State -->
          <div v-if="!analytics || analytics.length === 0" class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-xl">
              <div class="p-16">
                  <div class="text-center">
                      <div class="w-24 h-24 mx-auto rounded-xl bg-white/10 flex items-center justify-center mb-6 text-white/60 border border-white/20" v-html="icons.analytics"></div>
                      <h3 class="text-2xl font-semibold text-white/80 mb-4">No Analytics Data Available</h3>
                      <p class="text-base text-white/60 max-w-md mx-auto">
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
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced hover effects */
.group:hover {
  transform: translateY(-2px);
}

.group\/card:hover {
  transform: translateY(-1px);
}

.group\/subject:hover {
  transform: translateY(-2px);
}

/* Chart container glassmorphism */
.chart-container {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.1);
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
  border-radius: 0.5rem;
}

/* Icons scale properly */
[v-html] svg {
  width: 100%;
  height: 100%;
}

/* Enhanced shadow effects for depth */
.shadow-xl {
  box-shadow: 
    0 20px 25px -5px rgba(0, 0, 0, 0.1),
    0 10px 10px -5px rgba(0, 0, 0, 0.04),
    0 0 0 1px rgba(255, 255, 255, 0.05);
}

.shadow-2xl {
  box-shadow: 
    0 25px 50px -12px rgba(0, 0, 0, 0.25),
    0 0 0 1px rgba(255, 255, 255, 0.05);
}

.hover\:shadow-2xl:hover {
  box-shadow: 
    0 25px 50px -12px rgba(0, 0, 0, 0.25),
    0 0 0 1px rgba(255, 255, 255, 0.1);
}

/* Gradient enhancements */
.bg-gradient-to-r {
  background-image: linear-gradient(to right, var(--tw-gradient-stops));
}

.bg-gradient-to-br {
  background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
}

/* Performance optimizations */
.group,
.group\/card,
.group\/subject {
  will-change: transform;
}

/* Chart responsiveness */
.apexcharts-canvas {
  background: transparent !important;
}

/* Button enhancements */
.group\/btn:hover {
  transform: translateY(-1px);
}

.group\/prev:hover:not(:disabled),
.group\/next:hover:not(:disabled) {
  transform: translateY(-1px);
}

/* Progress bar enhancements */
.h-3 {
  position: relative;
  overflow: hidden;
}

.h-3::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% { left: -100%; }
  100% { left: 100%; }
}

/* Card hover glow effects */
.group\/card::before,
.group\/subject::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at center, rgba(59, 130, 246, 0.1), transparent 70%);
  opacity: 0;
  transition: opacity 0.3s ease;
  pointer-events: none;
  border-radius: inherit;
}

.group\/card:hover::before,
.group\/subject:hover::before {
  opacity: 1;
}

/* Reduce motion for accessibility */
@media (prefers-reduced-motion: reduce) {
  .transition-all {
    transition: none;
  }
  
  .group:hover,
  .group\/card:hover,
  .group\/subject:hover {
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