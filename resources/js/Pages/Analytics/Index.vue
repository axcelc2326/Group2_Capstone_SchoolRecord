<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ApexCharts from 'vue3-apexcharts';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    analytics: Array,
    pagination: Object // optional pagination links
});

const selectedChart = ref('bar'); // bar as default, area, donut

const goToPage = (url) => {
    if (url) router.visit(url);
};

const viewClassStudents = (classId) => {
    router.visit(route('analytics.class.students', { id: classId }))
}

// Ultra-modern chart configurations with sophisticated blue palette
const getDonutChartOptions = (classData, idx) => ({
  chart: {
    id: 'subject-donut-' + idx,
    type: 'donut',
    fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
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
      opacity: 0.1,
      color: '#1e40af'
    }
  },
  colors: ['#0ea5e9', '#3b82f6', '#1d4ed8', '#1e40af', '#60a5fa', '#93c5fd', '#dbeafe', '#eff6ff'],
  labels: Object.keys(classData.subject_averages),
  dataLabels: {
    enabled: true,
    style: {
      fontSize: '14px',
      fontWeight: 700,
      colors: ['#ffffff']
    },
    dropShadow: {
      enabled: true,
      color: '#000',
      blur: 4,
      opacity: 0.9
    }
  },
  plotOptions: {
    pie: {
      donut: {
        size: '70%',
        labels: {
          show: true,
          total: {
            show: true,
            label: 'Overall',
            fontSize: '20px',
            fontWeight: 800,
            color: '#0f172a',
            formatter: () => {
              const values = Object.values(classData.subject_averages);
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
      colors: '#0f172a'
    }
  },
  tooltip: {
    theme: 'light',
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
    id: 'subject-area-' + idx,
    type: 'area',
    fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
    toolbar: { show: false },
    background: 'transparent',
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 1200
    },
    dropShadow: {
      enabled: true,
      top: 8,
      left: 0,
      blur: 24,
      opacity: 0.15,
      color: '#3b82f6'
    }
  },
  xaxis: {
    categories: Object.keys(classData.subject_averages),
    labels: {
      style: {
        colors: '#475569',
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
        colors: '#64748b',
        fontSize: '13px',
        fontWeight: 500
      }
    }
  },
  colors: ['#0ea5e9'],
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      gradientToColors: ['#1e40af'],
      shadeIntensity: 0.8,
      type: 'vertical',
      opacityFrom: 0.9,
      opacityTo: 0.1
    }
  },
  stroke: {
    width: 3,
    curve: 'smooth'
  },
  grid: {
    borderColor: '#e2e8f0',
    strokeDashArray: 0,
    show: true
  },
  tooltip: {
    theme: 'light',
    style: {
      fontSize: '14px'
    },
    y: {
      formatter: (val) => val.toFixed(1) + '%'
    }
  },
  markers: {
    size: 6,
    colors: ['#0ea5e9'],
    strokeColors: '#ffffff',
    strokeWidth: 3,
    hover: {
      size: 8
    }
  }
});

const getBarChartOptions = (classData, idx) => ({
  chart: {
    id: 'subject-bar-' + idx,
    type: 'bar',
    fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
    toolbar: { show: false },
    background: 'transparent',
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 1000
    },
    dropShadow: {
      enabled: true,
      top: 4,
      left: 0,
      blur: 16,
      opacity: 0.12,
      color: '#3b82f6'
    }
  },
  xaxis: {
    categories: Object.keys(classData.subject_averages),
    labels: {
      style: {
        colors: '#475569',
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
        colors: '#64748b',
        fontSize: '13px',
        fontWeight: 500
      }
    }
  },
  colors: ['#0ea5e9'],
  plotOptions: {
    bar: {
      borderRadius: 12,
      columnWidth: '60%',
      distributed: false
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      gradientToColors: ['#1e40af'],
      shadeIntensity: 0.8,
      type: 'vertical',
      opacityFrom: 1,
      opacityTo: 0.85
    }
  },
  grid: {
    borderColor: '#e2e8f0',
    strokeDashArray: 0,
    show: true
  },
  tooltip: {
    theme: 'light',
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
      fontSize: '13px',
      fontWeight: 700
    }
  }
});

const getChartData = (classData) => {
  switch (selectedChart.value) {
    case 'donut':
      return Object.values(classData.subject_averages);
    case 'area':
      return [{
        name: 'Average Grade',
        data: Object.values(classData.subject_averages)
      }];
    case 'bar':
      return [{
        name: 'Performance',
        data: Object.values(classData.subject_averages)
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

// Performance calculations with modern blue color scheme
const getPerformanceLevel = (average) => {
  if (average >= 90) return { 
    label: 'Exceptional', 
    color: 'from-blue-600 via-blue-700 to-indigo-700', 
    textColor: 'text-white', 
    bgColor: 'bg-gradient-to-br from-blue-50 to-indigo-50', 
    borderColor: 'border-blue-200', 
    icon: '🏆',
    ringColor: 'ring-blue-500/20'
  };
  if (average >= 80) return { 
    label: 'Excellent', 
    color: 'from-sky-500 via-blue-600 to-blue-700', 
    textColor: 'text-white', 
    bgColor: 'bg-gradient-to-br from-sky-50 to-blue-50', 
    borderColor: 'border-sky-200', 
    icon: '⭐',
    ringColor: 'ring-sky-500/20'
  };
  if (average >= 70) return { 
    label: 'Good', 
    color: 'from-cyan-500 via-sky-600 to-blue-600', 
    textColor: 'text-white', 
    bgColor: 'bg-gradient-to-br from-cyan-50 to-sky-50', 
    borderColor: 'border-cyan-200', 
    icon: '✨',
    ringColor: 'ring-cyan-500/20'
  };
  return { 
    label: 'Needs Improvement', 
    color: 'from-slate-500 via-slate-600 to-slate-700', 
    textColor: 'text-white', 
    bgColor: 'bg-gradient-to-br from-slate-50 to-gray-50', 
    borderColor: 'border-slate-200', 
    icon: '📈',
    ringColor: 'ring-slate-500/20'
  };
};

const getOverallAverage = (classData) => {
  const values = Object.values(classData.subject_averages);
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
    const baseUrl = props.pagination.path;
    return `${baseUrl}?page=${page}`;
};
</script>

<template>
  <AuthenticatedLayout>
    <div class="relative z-10 min-h-screen">
      <!-- Ultra-Modern Header Section -->
      <div class="mb-12">
        <div class="flex items-center justify-between mb-8">
          <div class="flex items-center gap-6">
            <div class="relative">
              <div class="w-16 h-16 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-700 rounded-3xl flex items-center justify-center shadow-2xl shadow-blue-500/25 ring-4 ring-blue-500/10">
                <span class="text-white text-3xl filter drop-shadow-lg">📊</span>
              </div>
              <div class="absolute -inset-1 bg-gradient-to-br from-blue-500/20 to-indigo-500/20 rounded-3xl blur-xl -z-10"></div>
            </div>
            <div>
              <h1 class="text-5xl text-white mb-2">
                Performance Analytics
              </h1>
              <p class="text-slate-300 text-xl font-medium">Next-generation insights & interactive visualizations</p>
            </div>
          </div>

          <!-- Ultra-Modern Chart Selector -->
          <div class="relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 rounded-2xl blur-xl"></div>
            <div class="relative flex items-center gap-1 bg-white/95 backdrop-blur-xl rounded-2xl p-1.5 border border-blue-100/50 shadow-xl">
              <button
                v-for="(type, index) in ['bar', 'area', 'donut']"
                :key="type"
                @click="selectedChart = type"
                :class="[
                  'px-6 py-3 rounded-xl text-sm font-bold transition-all duration-500 capitalize relative overflow-hidden',
                  selectedChart === type
                    ? 'bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 text-white shadow-lg shadow-blue-500/30 scale-105'
                    : 'text-slate-700 hover:text-blue-700 hover:bg-blue-50/80 hover:scale-105'
                ]"
                :style="{ transitionDelay: `${index * 50}ms` }"
              >
                <span class="relative z-10">{{ type }}</span>
                <div v-if="selectedChart === type" class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-indigo-600/10 animate-pulse"></div>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Ultra-Modern Analytics Cards -->
      <div class="space-y-12">
        <div v-for="(classData, idx) in analytics" :key="idx" 
              class="group relative">
          
          <!-- Glow effect -->
          <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/10 via-sky-500/5 to-indigo-500/10 rounded-3xl blur-2xl group-hover:blur-3xl transition-all duration-700 opacity-0 group-hover:opacity-100"></div>
          
          <div class="relative bg-white/80 backdrop-blur-2xl border border-white/50 rounded-3xl shadow-2xl hover:shadow-blue-500/10 transition-all duration-700 overflow-hidden">
            
            <!-- Ultra-Modern Class Header -->
            <div class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 p-10 text-white overflow-hidden">
              <!-- Background pattern -->
              <div class="absolute inset-0 bg-gradient-to-br from-blue-600/10 to-indigo-600/10 opacity-50"></div>
              <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-bl from-blue-500/10 to-transparent rounded-full transform translate-x-48 -translate-y-48"></div>
              
              <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-8">
                  <div class="relative">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-xl rounded-3xl flex items-center justify-center text-3xl font-black text-white shadow-2xl border border-white/30 ring-4 ring-white/10">
                      {{ classData.grade_level }}
                    </div>
                    <div class="absolute -inset-2 bg-gradient-to-br from-white/20 to-white/5 rounded-3xl blur-xl"></div>
                  </div>
                  <div>
                    <h2 class="text-4xl font-black text-white mb-3 tracking-tight">{{ classData.class }}</h2>
                    <div class="flex items-center gap-6 text-blue-100">
                      <span class="flex items-center gap-3 px-3 py-1.5 bg-white/10 backdrop-blur-sm rounded-full border border-white/20">
                        <div class="w-2 h-2 bg-blue-300 rounded-full animate-pulse"></div>
                        <span class="text-sm font-semibold">Grade {{ classData.grade_level }}</span>
                      </span>
                      <span class="flex items-center gap-3 px-3 py-1.5 bg-white/10 backdrop-blur-sm rounded-full border border-white/20">
                        <div class="w-2 h-2 bg-sky-300 rounded-full animate-pulse delay-150"></div>
                        <span class="text-sm font-semibold">{{ Object.keys(classData.subject_averages).length }} Subjects</span>
                      </span>
                      <span class="flex items-center gap-3 px-3 py-1.5 bg-white/10 backdrop-blur-sm rounded-full border border-white/20">
                        <div class="w-2 h-2 bg-cyan-300 rounded-full animate-pulse delay-300"></div>
                        <span class="text-sm font-semibold">{{ classData.total_students }} Students</span>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center gap-8">
                  <!-- Ultra-Modern View Students Button -->
                  <button
                    @click="viewClassStudents(classData.id)"
                    class="group/btn relative bg-white/95 backdrop-blur-sm text-blue-700 px-8 py-4 rounded-2xl font-bold hover:bg-white hover:text-blue-800 transition-all duration-300 shadow-xl hover:shadow-2xl border border-blue-100/50 hover:border-blue-200 flex items-center gap-4 hover:scale-105 overflow-hidden"
                  >
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-50/50 to-sky-50/50 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
                    <svg class="w-6 h-6 relative z-10 transition-transform group-hover/btn:scale-110" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                    </svg>
                    <span class="relative z-10">View Students</span>
                  </button>

                  <div class="text-right">
                    <div class="text-6xl font-black text-white mb-2 filter drop-shadow-lg">
                      {{ getOverallAverage(classData).toFixed(1) }}%
                    </div>
                    <div class="text-blue-200 text-xl font-semibold">Overall Average</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="p-10">
              <!-- Ultra-Modern Performance Cards -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <!-- Overall Performance -->
                <div class="group/card relative">
                  <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500/20 to-sky-500/20 rounded-2xl blur-lg group-hover/card:blur-xl transition-all duration-500"></div>
                  <div class="relative bg-white/95 backdrop-blur-xl rounded-2xl p-8 border border-blue-100/50 hover:border-blue-300/50 transition-all duration-500 hover:scale-105">
                    <div class="flex items-center gap-6">
                      <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 via-blue-600 to-sky-600 rounded-2xl flex items-center justify-center shadow-xl shadow-blue-500/30">
                          <span class="text-white text-2xl filter drop-shadow-sm">📈</span>
                        </div>
                        <div class="absolute -inset-1 bg-gradient-to-br from-blue-500/30 to-sky-500/30 rounded-2xl blur-xl -z-10"></div>
                      </div>
                      <div>
                        <div class="text-blue-700 text-sm mb-2 font-semibold">Performance Level</div>
                        <div class="flex items-center gap-4">
                          <span class="text-3xl filter drop-shadow-sm">{{ getPerformanceLevel(getOverallAverage(classData)).icon }}</span>
                          <span class="px-4 py-2 rounded-xl text-sm font-black bg-gradient-to-r shadow-lg ring-4 transition-all duration-300 hover:scale-105" 
                                :class="[getPerformanceLevel(getOverallAverage(classData)).color, getPerformanceLevel(getOverallAverage(classData)).textColor, getPerformanceLevel(getOverallAverage(classData)).ringColor]">
                            {{ getPerformanceLevel(getOverallAverage(classData)).label }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Top Subject -->
                <div class="group/card relative">
                  <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-600/20 to-indigo-500/20 rounded-2xl blur-lg group-hover/card:blur-xl transition-all duration-500"></div>
                  <div class="relative bg-gradient-to-br from-blue-50/95 to-sky-50/95 backdrop-blur-xl rounded-2xl p-8 border border-blue-200/50 hover:border-blue-300/50 transition-all duration-500 hover:scale-105">
                    <div class="flex items-center gap-6">
                      <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-700 rounded-2xl flex items-center justify-center shadow-xl shadow-blue-500/30">
                          <span class="text-white text-2xl filter drop-shadow-sm">🏆</span>
                        </div>
                        <div class="absolute -inset-1 bg-gradient-to-br from-blue-600/30 to-indigo-600/30 rounded-2xl blur-xl -z-10"></div>
                      </div>
                      <div>
                        <div class="text-blue-700 text-sm mb-2 font-semibold">Top Performing</div>
                        <div class="font-black text-blue-900 text-xl mb-1">{{ classData.top_subject }}</div>
                        <div class="text-blue-600 text-sm font-bold">
                          {{ classData.subject_averages[classData.top_subject]?.toFixed(1) }}% Average
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Lowest Subject -->
                <div class="group/card relative">
                  <div class="absolute -inset-0.5 bg-gradient-to-r from-slate-500/20 to-gray-500/20 rounded-2xl blur-lg group-hover/card:blur-xl transition-all duration-500"></div>
                  <div class="relative bg-gradient-to-br from-slate-50/95 to-gray-50/95 backdrop-blur-xl rounded-2xl p-8 border border-slate-200/50 hover:border-slate-300/50 transition-all duration-500 hover:scale-105">
                    <div class="flex items-center gap-6">
                      <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 via-blue-600 to-sky-600 rounded-2xl flex items-center justify-center shadow-xl shadow-slate-500/30">
                          <span class="text-white text-2xl filter drop-shadow-sm">🎯</span>
                        </div>
                        <div class="absolute -inset-1 bg-gradient-to-br from-slate-600/30 to-gray-600/30 rounded-2xl blur-xl -z-10"></div>
                      </div>
                      <div>
                        <div class="text-blue-700 text-sm mb-2 font-semibold">Focus Area</div>
                        <div class="font-black text-blue-900 text-xl mb-1">{{ classData.low_subject }}</div>
                        <div class="text-blue-600 text-sm font-bold">
                          {{ classData.subject_averages[classData.low_subject]?.toFixed(1) }}% Average
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Ultra-Modern Chart Section -->
              <div class="relative mb-12">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/10 via-sky-500/5 to-indigo-500/10 rounded-3xl blur-2xl"></div>
                <div class="relative bg-white/90 backdrop-blur-2xl rounded-3xl p-10 border border-blue-100/50 shadow-xl">
                  <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-black text-slate-800 flex items-center gap-4">
                      <div class="w-4 h-4 bg-gradient-to-r from-blue-500 to-sky-500 rounded-full animate-pulse shadow-lg shadow-blue-500/50"></div>
                      Interactive Performance Visualization
                    </h3>
                    <div class="flex items-center gap-3">
                      <span class="text-sm font-bold text-blue-700 px-4 py-2 bg-gradient-to-r from-blue-50 to-sky-50 rounded-xl border border-blue-200 ring-2 ring-blue-500/10">
                        {{ selectedChart.toUpperCase() }} VIEW
                      </span>
                    </div>
                  </div>
                  
                  <div class="relative chart-container-modern rounded-2xl p-8 border border-blue-100/50 overflow-hidden">
                    <!-- Chart background pattern -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 via-sky-50/20 to-indigo-50/30 opacity-50"></div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-blue-400/5 to-transparent rounded-full"></div>
                    
                    <div class="relative z-10">
                      <ApexCharts
                        :type="selectedChart"
                        height="450"
                        :options="getChartOptions(classData, idx)"
                        :series="getChartData(classData)"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Ultra-Modern Subject Details Grid -->
              <div>
                <h3 class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-4">
                  <div class="w-4 h-4 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full animate-pulse shadow-lg shadow-blue-500/50"></div>
                  Subject Performance Breakdown
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                  <div v-for="(average, subject) in classData.subject_averages" :key="subject"
                        class="group/subject relative">
                    
                    <!-- Glow effect for each card -->
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500/20 via-sky-500/10 to-cyan-500/20 rounded-2xl blur-lg group-hover/subject:blur-xl transition-all duration-500 opacity-0 group-hover/subject:opacity-100"></div>
                    
                    <div class="relative bg-white/90 backdrop-blur-xl rounded-2xl p-6 border border-blue-100/50 hover:border-blue-300/50 hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-500 group-hover/subject:scale-105 overflow-hidden">
                      
                      <!-- Card background pattern -->
                      <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-blue-400/5 to-transparent rounded-full transform translate-x-6 -translate-y-6"></div>
                      
                      <div class="relative z-10 flex items-center justify-between">
                        <div class="flex-1">
                          <div class="font-black text-slate-800 text-lg group-hover/subject:text-blue-700 transition-colors mb-1 tracking-tight">
                            {{ subject }}
                          </div>
                          <div class="text-slate-600 text-sm font-semibold mb-3">Class Average</div>
                          
                          <!-- Progress bar -->
                          <div class="w-full bg-slate-100 rounded-full h-2 mb-3 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-sky-500 rounded-full transition-all duration-1000 shadow-sm"
                                  :style="{ width: average + '%' }"></div>
                          </div>
                        </div>
                        
                        <div class="text-right ml-4">
                          <div class="text-3xl font-black bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 bg-clip-text text-transparent mb-2 filter drop-shadow-sm">
                            {{ average.toFixed(1) }}%
                          </div>
                          <div class="relative">
                            <div class="text-xs px-3 py-1.5 rounded-xl bg-gradient-to-r shadow-lg font-black ring-2 transition-all duration-300 hover:scale-105" 
                                  :class="[getPerformanceLevel(average).color, getPerformanceLevel(average).textColor, getPerformanceLevel(average).ringColor]">
                              {{ getPerformanceLevel(average).label }}
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Performance icon -->
                      <div class="absolute bottom-4 right-4 text-2xl opacity-20 group-hover/subject:opacity-40 transition-opacity">
                        {{ getPerformanceLevel(average).icon }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ultra-Modern Pagination -->
      <div v-if="pagination" class="mt-16">
        <div class="relative">
          <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/20 via-sky-500/10 to-indigo-500/20 rounded-3xl blur-2xl"></div>
          <div class="relative bg-white/90 backdrop-blur-2xl border border-blue-100/50 rounded-3xl shadow-2xl p-8">
            <div class="flex flex-wrap justify-center items-center space-x-3 gap-y-4">

              <!-- Ultra-Modern Previous Button -->
              <button
                @click="goToPage(pagination.prev_page_url)"
                :disabled="!pagination.prev_page_url"
                class="group/prev relative px-8 py-4 rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 text-white font-bold hover:from-blue-700 hover:via-blue-800 hover:to-indigo-800 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 shadow-xl hover:shadow-blue-500/30 disabled:hover:shadow-none hover:scale-105 disabled:hover:scale-100 overflow-hidden"
              >
                <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-white/5 opacity-0 group-hover/prev:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10 flex items-center gap-3">
                  <svg class="w-5 h-5 transition-transform group-hover/prev:-translate-x-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  <span>Previous</span>
                </div>
              </button>

              <!-- Ultra-Modern Page Numbers -->
              <template v-for="page in pages" :key="page">
                <span
                  v-if="page === '...'"
                  class="px-4 py-4 text-slate-500 text-lg font-bold"
                >...</span>
                <button
                  v-else
                  @click="goToPage(pageUrl(page))"
                  :class="[
                    'px-5 py-4 rounded-2xl font-bold transition-all duration-300 relative overflow-hidden hover:scale-105',
                    page === pagination.current_page
                      ? 'bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 text-white shadow-xl shadow-blue-500/30 ring-4 ring-blue-500/20'
                      : 'bg-white/80 backdrop-blur-sm text-slate-700 hover:text-blue-700 border border-slate-200 hover:border-blue-300 hover:bg-blue-50/80 shadow-lg hover:shadow-xl'
                  ]"
                >
                  <span class="relative z-10">{{ page }}</span>
                  <div v-if="page === pagination.current_page" class="absolute inset-0 bg-gradient-to-r from-white/10 to-white/5 animate-pulse"></div>
                </button>
              </template>

              <!-- Ultra-Modern Next Button -->
              <button
                @click="goToPage(pagination.next_page_url)"
                :disabled="!pagination.next_page_url"
                class="group/next relative px-8 py-4 rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 text-white font-bold hover:from-blue-700 hover:via-blue-800 hover:to-indigo-800 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 shadow-xl hover:shadow-blue-500/30 disabled:hover:shadow-none hover:scale-105 disabled:hover:scale-100 overflow-hidden"
              >
                <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-white/5 opacity-0 group-hover/next:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10 flex items-center gap-3">
                  <span>Next</span>
                  <svg class="w-5 h-5 transition-transform group-hover/next:translate-x-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Ultra-Modern Chart Container */
.chart-container-modern {
  background: linear-gradient(135deg, rgba(248, 250, 252, 0.8) 0%, rgba(241, 245, 249, 0.9) 50%, rgba(248, 250, 252, 0.8) 100%);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(59, 130, 246, 0.1);
}

/* Enhanced Custom Animations */
@keyframes pulse-slow {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.05); }
}

@keyframes float-slow {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  33% { transform: translateY(-10px) rotate(1deg); }
  66% { transform: translateY(5px) rotate(-1deg); }
}

@keyframes shimmer {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

.animate-pulse-slow {
  animation: pulse-slow 4s ease-in-out infinite;
}

.animate-float-slow {
  animation: float-slow 8s ease-in-out infinite;
}

/* Modern Glassmorphism Effects */
.backdrop-blur-2xl {
  backdrop-filter: blur(40px);
}

.backdrop-blur-xl {
  backdrop-filter: blur(24px);
}

/* Ultra-Modern Scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
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

/* Advanced Shadow Effects */
.shadow-2xl {
  box-shadow: 
    0 25px 50px -12px rgba(0, 0, 0, 0.25),
    0 0 0 1px rgba(255, 255, 255, 0.05);
}

/* Modern Button Hover Effects */
button:hover {
  transform: translateY(-1px);
}

button:active {
  transform: translateY(0);
}

/* Enhanced Ring Effects */
.ring-4 {
  box-shadow: 0 0 0 4px var(--tw-ring-color);
}

/* Modern Typography Enhancement */
.font-black {
  font-weight: 900;
  letter-spacing: -0.025em;
}

/* Performance Card Enhancements */
.group\/card:hover {
  transform: translateY(-4px);
}

.group\/subject:hover {
  transform: translateY(-2px);
}

/* Chart Animation Enhancement */
.chart-container-modern::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
  animation: shimmer 3s infinite;
}

/* Modern Loading States */
@keyframes skeleton-loading {
  0% { background-color: rgba(59, 130, 246, 0.1); }
  50% { background-color: rgba(59, 130, 246, 0.2); }
  100% { background-color: rgba(59, 130, 246, 0.1); }
}

/* Enhanced Focus States */
button:focus-visible,
.group\/btn:focus-visible {
  outline: 2px solid #3b82f6;
  outline-offset: 4px;
}

/* Modern Gradient Text Effects */
.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Performance Optimizations */
.group {
  will-change: transform;
}

.group\/card,
.group\/subject {
  will-change: transform;
}

/* Modern Border Effects */
.border-blue-100\/50 {
  border-color: rgba(219, 234, 254, 0.5);
}

.border-blue-300\/50 {
  border-color: rgba(147, 197, 253, 0.5);
}
</style>