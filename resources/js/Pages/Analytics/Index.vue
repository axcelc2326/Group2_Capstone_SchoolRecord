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

// Modern chart configurations with enhanced colors
const getDonutChartOptions = (classData, idx) => ({
  chart: {
    id: 'subject-donut-' + idx,
    type: 'donut',
    fontFamily: 'Inter, sans-serif',
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 800
    }
  },
  colors: ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57', '#ff9ff3', '#54a0ff', '#5f27cd'],
  labels: Object.keys(classData.subject_averages),
  dataLabels: {
    enabled: true,
    style: {
      fontSize: '13px',
      fontWeight: 700,
      colors: ['#ffffff']
    },
    dropShadow: {
      enabled: true,
      color: '#000',
      blur: 3,
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
            fontWeight: 700,
            color: '#2d3748',
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
    fontSize: '13px',
    fontWeight: 600,
    labels: {
      colors: '#2d3748'
    }
  },
  tooltip: {
    theme: 'light',
    style: {
      fontSize: '13px'
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
    fontFamily: 'Inter, sans-serif',
    toolbar: { show: false },
    background: 'transparent',
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 1000
    }
  },
  xaxis: {
    categories: Object.keys(classData.subject_averages),
    labels: {
      style: {
        colors: '#2d3748',
        fontSize: '13px',
        fontWeight: 600
      }
    },
    axisBorder: { 
      show: true,
      color: '#cbd5e0'
    },
    axisTicks: { 
      show: true,
      color: '#cbd5e0'
    }
  },
  yaxis: {
    min: 0,
    max: 100,
    labels: {
      style: {
        colors: '#2d3748',
        fontSize: '13px',
        fontWeight: 500
      }
    }
  },
  colors: ['#667eea'],
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      gradientToColors: ['#764ba2'],
      shadeIntensity: 1,
      type: 'vertical',
      opacityFrom: 0.8,
      opacityTo: 0.3
    }
  },
  stroke: {
    width: 4,
    curve: 'smooth'
  },
  grid: {
    borderColor: '#e2e8f0',
    strokeDashArray: 3
  },
  tooltip: {
    theme: 'light',
    style: {
      fontSize: '13px'
    },
    y: {
      formatter: (val) => val.toFixed(1) + '%'
    }
  }
});

const getBarChartOptions = (classData, idx) => ({
  chart: {
    id: 'subject-bar-' + idx,
    type: 'bar',
    fontFamily: 'Inter, sans-serif',
    toolbar: { show: false },
    background: 'transparent',
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 800
    }
  },
  xaxis: {
    categories: Object.keys(classData.subject_averages),
    labels: {
      style: {
        colors: '#2d3748',
        fontSize: '13px',
        fontWeight: 600
      }
    },
    axisBorder: { 
      show: true,
      color: '#cbd5e0'
    },
    axisTicks: { 
      show: true,
      color: '#cbd5e0'
    }
  },
  yaxis: {
    min: 0,
    max: 100,
    labels: {
      style: {
        colors: '#2d3748',
        fontSize: '13px',
        fontWeight: 500
      }
    }
  },
  colors: ['#ff6b6b'],
  plotOptions: {
    bar: {
      borderRadius: 10,
      columnWidth: '65%',
      distributed: false
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      gradientToColors: ['#ee5a6f'],
      shadeIntensity: 1,
      type: 'vertical',
      opacityFrom: 1,
      opacityTo: 0.8
    }
  },
  grid: {
    borderColor: '#e2e8f0',
    strokeDashArray: 3
  },
  tooltip: {
    theme: 'light',
    style: {
      fontSize: '13px'
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

// Performance calculations with enhanced colors
const getPerformanceLevel = (average) => {
  if (average >= 90) return { label: 'Excellent', color: 'from-green-500 to-green-600', textColor: 'text-white', bgColor: 'bg-green-50', borderColor: 'border-green-200', icon: '🏆' };
  if (average >= 80) return { label: 'Good', color: 'from-blue-500 to-blue-600', textColor: 'text-white', bgColor: 'bg-blue-50', borderColor: 'border-blue-200', icon: '👍' };
  if (average >= 70) return { label: 'Fair', color: 'from-yellow-500 to-yellow-600', textColor: 'text-white', bgColor: 'bg-yellow-50', borderColor: 'border-yellow-200', icon: '⚡' };
  return { label: 'Needs Improvement', color: 'from-red-500 to-red-600', textColor: 'text-white', bgColor: 'bg-red-50', borderColor: 'border-red-200', icon: '⚠️' };
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
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6">
      <!-- Animated Background Elements -->
      <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-400/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-400/8 rounded-full blur-3xl animate-pulse delay-500"></div>
      </div>

      <div class="relative z-10">
        <!-- Header Section -->
        <div class="mb-8">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/25">
                <span class="text-white text-2xl">📊</span>
              </div>
              <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-600 bg-clip-text text-transparent">
                  Performance Analytics
                </h1>
                <p class="text-slate-700 text-lg font-medium">Advanced insights with interactive visualizations</p>
              </div>
            </div>

            <!-- Chart Type Selector -->
            <div class="flex items-center gap-2 bg-white/90 backdrop-blur-sm rounded-2xl p-2 border border-slate-200 shadow-lg">
              <button
                v-for="type in ['bar', 'area', 'donut']"
                :key="type"
                @click="selectedChart = type"
                :class="[
                  'px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-300 capitalize',
                  selectedChart === type
                    ? 'bg-gradient-to-r from-purple-500 to-blue-600 text-white shadow-lg shadow-purple-500/25'
                    : 'text-slate-700 hover:text-purple-600 hover:bg-purple-50'
                ]"
              >
                {{ type }}
              </button>
            </div>
          </div>
        </div>

        <!-- Analytics Cards Grid -->
        <div class="space-y-8">
          <div v-for="(classData, idx) in analytics" :key="idx" 
               class="bg-white/80 backdrop-blur-xl border border-slate-200 rounded-3xl shadow-xl hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-500 overflow-hidden group">
            
            <!-- Class Header -->
            <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-600 p-8 text-white">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                  <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-2xl font-bold text-white shadow-lg border border-white/30">
                    {{ classData.grade_level }}
                  </div>
                  <div>
                    <h2 class="text-3xl font-bold text-white mb-2">{{ classData.class }}</h2>
                    <div class="flex items-center gap-4 text-blue-100">
                      <span class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-blue-200 rounded-full animate-pulse"></div>
                        Grade {{ classData.grade_level }}
                      </span>
                      <span class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-indigo-200 rounded-full animate-pulse delay-150"></div>
                        {{ Object.keys(classData.subject_averages).length }} Subjects
                      </span>
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-5xl font-bold text-white mb-1">
                    {{ getOverallAverage(classData).toFixed(1) }}%
                  </div>
                  <div class="text-blue-100 text-lg">Overall Average</div>
                </div>
              </div>
            </div>

            <div class="p-8">
              <!-- Performance Overview Cards -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Overall Performance -->
                <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-6 border border-slate-200 hover:border-purple-300 hover:shadow-lg transition-all duration-300">
                  <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/25">
                      <span class="text-white text-xl">📈</span>
                    </div>
                    <div>
                      <div class="text-slate-600 text-sm mb-1 font-medium">Performance Level</div>
                      <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ getPerformanceLevel(getOverallAverage(classData)).icon }}</span>
                        <span class="px-3 py-1 rounded-full text-sm font-bold bg-gradient-to-r shadow-lg" 
                              :class="[getPerformanceLevel(getOverallAverage(classData)).color, getPerformanceLevel(getOverallAverage(classData)).textColor]">
                          {{ getPerformanceLevel(getOverallAverage(classData)).label }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Top Subject -->
                <div class="bg-emerald-50/95 backdrop-blur-sm rounded-2xl p-6 border border-emerald-200 hover:border-emerald-300 hover:shadow-lg transition-all duration-300">
                  <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/25">
                      <span class="text-white text-xl">🏆</span>
                    </div>
                    <div>
                      <div class="text-emerald-700 text-sm mb-1 font-medium">Top Performing</div>
                      <div class="font-bold text-emerald-800 text-lg">{{ classData.top_subject }}</div>
                      <div class="text-emerald-600 text-sm font-medium">
                        {{ classData.subject_averages[classData.top_subject]?.toFixed(1) }}% Average
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Lowest Subject -->
                <div class="bg-amber-50/95 backdrop-blur-sm rounded-2xl p-6 border border-amber-200 hover:border-amber-300 hover:shadow-lg transition-all duration-300">
                  <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/25">
                      <span class="text-white text-xl">📚</span>
                    </div>
                    <div>
                      <div class="text-amber-700 text-sm mb-1 font-medium">Needs Focus</div>
                      <div class="font-bold text-amber-800 text-lg">{{ classData.low_subject }}</div>
                      <div class="text-amber-600 text-sm font-medium">
                        {{ classData.subject_averages[classData.low_subject]?.toFixed(1) }}% Average
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Chart Section -->
              <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-8 border border-slate-200 mb-8 shadow-sm">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                  <div class="w-3 h-3 bg-gradient-to-r from-purple-500 to-blue-600 rounded-full animate-pulse"></div>
                  Interactive Performance Visualization
                  <span class="text-sm font-semibold text-purple-600 ml-2 px-3 py-1 bg-purple-50 rounded-full border border-purple-200">
                    {{ selectedChart.toUpperCase() }}
                  </span>
                </h3>
                
                <div class="chart-container bg-gradient-to-br from-slate-50 to-blue-50/50 rounded-xl p-6 border border-slate-100">
                  <ApexCharts
                    :type="selectedChart"
                    height="400"
                    :options="getChartOptions(classData, idx)"
                    :series="getChartData(classData)"
                  />
                </div>
              </div>

              <!-- Subject Details Grid -->
              <div>
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                  <div class="w-3 h-3 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full animate-pulse"></div>
                  Subject Performance Details
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div v-for="(average, subject) in classData.subject_averages" :key="subject"
                       class="bg-white/95 backdrop-blur-sm rounded-xl p-5 border border-slate-200 hover:border-purple-300 hover:shadow-lg hover:shadow-purple-500/10 transition-all duration-300 group">
                    <div class="flex items-center justify-between">
                      <div>
                        <div class="font-semibold text-slate-800 text-lg group-hover:text-purple-600 transition-colors">{{ subject }}</div>
                        <div class="text-slate-600 text-sm font-medium">Class Average</div>
                      </div>
                      <div class="text-right">
                        <div class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-500 bg-clip-text text-transparent">
                          {{ average.toFixed(1) }}%
                        </div>
                        <div class="text-xs px-2 py-1 rounded-full bg-gradient-to-r shadow-sm mt-1 font-semibold" 
                             :class="[getPerformanceLevel(average).color, getPerformanceLevel(average).textColor]">
                          {{ getPerformanceLevel(average).label }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modern Pagination -->
        <div v-if="pagination" class="mt-12">
          <div class="bg-white/90 backdrop-blur-xl border border-slate-200 rounded-2xl shadow-xl p-6">
            <div class="flex flex-wrap justify-center items-center space-x-2 gap-y-3">

              <!-- Previous Button -->
              <button
                @click="goToPage(pagination.prev_page_url)"
                :disabled="!pagination.prev_page_url"
                class="px-6 py-3 rounded-xl bg-gradient-to-r from-purple-500 to-blue-600 text-white font-semibold hover:from-purple-600 hover:to-blue-700 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 shadow-lg hover:shadow-purple-500/25"
              >
                ← Previous
              </button>

              <!-- Page Numbers -->
              <template v-for="page in pages" :key="page">
                <span
                  v-if="page === '...'"
                  class="px-4 py-3 text-slate-500 text-lg"
                >...</span>
                <button
                  v-else
                  @click="goToPage(pageUrl(page))"
                  :class="[
                    'px-4 py-3 rounded-xl font-semibold transition-all duration-300',
                    page === pagination.current_page
                      ? 'bg-gradient-to-r from-purple-500 to-blue-600 text-white shadow-lg shadow-purple-500/25'
                      : 'bg-slate-100 text-slate-700 hover:bg-purple-50 hover:text-purple-600 border border-slate-200'
                  ]"
                >
                  {{ page }}
                </button>
              </template>

              <!-- Next Button -->
              <button
                @click="goToPage(pagination.next_page_url)"
                :disabled="!pagination.next_page_url"
                class="px-6 py-3 rounded-xl bg-gradient-to-r from-purple-500 to-blue-600 text-white font-semibold hover:from-purple-600 hover:to-blue-700 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 shadow-lg hover:shadow-purple-500/25"
              >
                Next →
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.chart-container {
  background: linear-gradient(135deg, rgba(248, 250, 252, 0.95) 0%, rgba(239, 246, 255, 0.7) 100%);
  border-radius: 12px;
  backdrop-filter: blur(10px);
}

/* Enhanced scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(241, 245, 249, 0.5);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #8b5cf6, #3b82f6);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #7c3aed, #2563eb);
}

/* Smooth animations */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.group:hover .animate-float {
  animation: float 3s ease-in-out infinite;
}

/* Glassmorphism effect enhancement */
.backdrop-blur-xl {
  backdrop-filter: blur(20px);
}

.backdrop-blur-sm {
  backdrop-filter: blur(8px);
}

/* Modern shadow effects */
.shadow-xl {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.shadow-2xl {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
</style>