<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ApexCharts from 'vue3-apexcharts';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    analytics: Array,
    pagination: Object // optional pagination links
});

const goToPage = (url) => {
    if (url) router.visit(url);
};

// Enhanced chart options for modern look
const getChartOptions = (classData, idx) => ({
  chart: {
    id: 'subject-bar-' + idx,
    type: 'bar',
    fontFamily: 'Inter, sans-serif',
    toolbar: { show: false },
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 800,
      animateGradually: {
        enabled: true,
        delay: 150
      },
      dynamicAnimation: {
        enabled: true,
        speed: 350
      }
    }
  },
  plotOptions: {
    bar: {
      borderRadius: 8,
      horizontal: false,
      columnWidth: '60%',
      dataLabels: {
        position: 'top'
      }
    }
  },
  dataLabels: {
    enabled: true,
    formatter: (val) => val.toFixed(1),
    offsetY: -20,
    style: {
      fontSize: '12px',
      fontWeight: 600,
      colors: ['#374151']
    }
  },
  colors: ['#3b82f6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
  xaxis: {
    categories: Object.keys(classData.subject_averages),
    labels: {
      style: {
        colors: '#6b7280',
        fontSize: '12px',
        fontWeight: 500
      }
    },
    axisBorder: { show: false },
    axisTicks: { show: false }
  },
  yaxis: {
    labels: {
      style: {
        colors: '#6b7280',
        fontSize: '12px'
      },
      formatter: (val) => val.toFixed(0)
    },
    min: 0,
    max: 100
  },
  grid: {
    borderColor: '#f3f4f6',
    strokeDashArray: 3,
    xaxis: { lines: { show: false } },
    yaxis: { lines: { show: true } }
  },
  tooltip: {
    theme: 'dark',
    y: {
      formatter: (val) => val.toFixed(2) + '%'
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      type: 'vertical',
      shadeIntensity: 0.3,
      gradientToColors: ['#60a5fa', '#22d3ee', '#34d399', '#fbbf24', '#f87171', '#a78bfa'],
      inverseColors: false,
      opacityFrom: 1,
      opacityTo: 0.8
    }
  }
});

// Calculate performance metrics
const getPerformanceLevel = (average) => {
  if (average >= 90) return { label: 'Excellent', color: 'bg-emerald-100 text-emerald-800', icon: '🏆' };
  if (average >= 80) return { label: 'Good', color: 'bg-blue-100 text-blue-800', icon: '👍' };
  if (average >= 70) return { label: 'Fair', color: 'bg-yellow-100 text-yellow-800', icon: '⚡' };
  return { label: 'Needs Improvement', color: 'bg-red-100 text-red-800', icon: '⚠️' };
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
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6">
      <!-- Header Section -->
      <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
          <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
            <span class="text-white text-lg">📊</span>
          </div>
          <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-purple-900 bg-clip-text text-transparent">
            Class Performance Analytics
          </h1>
        </div>
        <p class="text-gray-600 ml-11">Real-time insights into academic performance across all subjects</p>
      </div>

      <!-- Analytics Cards Grid -->
      <div class="space-y-6">
        <div v-for="(classData, idx) in analytics" :key="idx" 
             class="bg-white/80 backdrop-blur-sm border border-white/20 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
          
          <!-- Class Header -->
          <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 p-6 text-white">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-2xl font-bold mb-1">{{ classData.class }}</h2>
                <p class="text-blue-100">Grade {{ classData.grade_level }} • {{ Object.keys(classData.subject_averages).length }} Subjects</p>
              </div>
              <div class="text-right">
                <div class="text-3xl font-bold">{{ getOverallAverage(classData).toFixed(1) }}%</div>
                <div class="text-sm text-blue-100">Overall Average</div>
              </div>
            </div>
          </div>

          <div class="p-6">
            <!-- Performance Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
              <!-- Overall Performance -->
              <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <span class="text-white">📈</span>
                  </div>
                  <div>
                    <div class="text-sm text-gray-600">Performance Level</div>
                    <div class="flex items-center gap-2">
                      <span class="text-lg">{{ getPerformanceLevel(getOverallAverage(classData)).icon }}</span>
                      <span class="px-2 py-1 rounded-full text-xs font-semibold" :class="getPerformanceLevel(getOverallAverage(classData)).color">
                        {{ getPerformanceLevel(getOverallAverage(classData)).label }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Top Subject -->
              <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-4 border border-emerald-200">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center">
                    <span class="text-white">🏆</span>
                  </div>
                  <div>
                    <div class="text-sm text-emerald-600">Top Performing</div>
                    <div class="font-semibold text-emerald-800">{{ classData.top_subject }}</div>
                    <div class="text-xs text-emerald-600">
                      {{ classData.subject_averages[classData.top_subject]?.toFixed(1) }}% Average
                    </div>
                  </div>
                </div>
              </div>

              <!-- Lowest Subject -->
              <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-4 border border-amber-200">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg flex items-center justify-center">
                    <span class="text-white">📚</span>
                  </div>
                  <div>
                    <div class="text-sm text-amber-600">Needs Focus</div>
                    <div class="font-semibold text-amber-800">{{ classData.low_subject }}</div>
                    <div class="text-xs text-amber-600">
                      {{ classData.subject_averages[classData.low_subject]?.toFixed(1) }}% Average
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
              <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                Subject Performance Breakdown
              </h3>
              
              <div class="chart-container">
                <ApexCharts
                  type="bar"
                  height="350"
                  :options="getChartOptions(classData, idx)"
                  :series="[
                    {
                      name: 'Average Grade (%)',
                      data: Object.values(classData.subject_averages)
                    }
                  ]"
                />
              </div>
            </div>

            <!-- Subject Details Grid -->
            <div class="mt-6">
              <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                Subject Details
              </h3>
              
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div v-for="(average, subject) in classData.subject_averages" :key="subject"
                     class="bg-white rounded-lg p-4 border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="font-medium text-gray-800">{{ subject }}</div>
                      <div class="text-sm text-gray-500">Class Average</div>
                    </div>
                    <div class="text-right">
                      <div class="text-xl font-bold text-gray-900">{{ average.toFixed(1) }}%</div>
                      <div class="text-xs px-2 py-1 rounded-full" :class="getPerformanceLevel(average).color">
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

      <!-- Pagination Controls -->
      <div v-if="pagination" class="mt-8">
        <div class="bg-white/80 backdrop-blur-sm border border-white/20 rounded-2xl shadow-lg p-6">
          <div class="flex flex-wrap justify-center items-center space-x-1 gap-y-2">

            <!-- Previous Button -->
            <button
              @click="goToPage(pagination.prev_page_url)"
              :disabled="!pagination.prev_page_url"
              class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 text-white font-medium hover:from-blue-600 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl"
            >
              Previous
            </button>

            <!-- Page Numbers with Dots -->
            <template v-for="page in pages" :key="page">
              <span
                v-if="page === '...'"
                class="px-4 py-2 text-gray-500"
              >...</span>
              <button
                v-else
                @click="goToPage(pageUrl(page))"
                :class="[
                  'px-4 py-2 rounded-lg font-medium transition-all duration-200 shadow-md',
                  page === pagination.current_page
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 text-gray-700 hover:bg-blue-100'
                ]"
              >
                {{ page }}
              </button>
            </template>

            <!-- Next Button -->
            <button
              @click="goToPage(pagination.next_page_url)"
              :disabled="!pagination.next_page_url"
              class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 text-white font-medium hover:from-blue-600 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.chart-container {
  background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
  border-radius: 12px;
  padding: 1rem;
}

/* Custom scrollbar for better aesthetics */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>