<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import VueApexCharts from 'vue3-apexcharts'
import { 
  Users, 
  GraduationCap,
  UserCheck,
  School,
  Target,
  TrendingUp,
  TrendingDown,
  Trophy,
  Crown,
  Medal,
  Award,
  Star,
  Sparkles,
  Activity,
  Megaphone,
  User,
  Calendar,
  Clock,
  BarChart3,
  Settings
} from 'lucide-vue-next'

const props = defineProps({
  topClasses: { type: Array, default: () => [] },
  summary: { 
    type: Object, 
    default: () => ({ 
      total_classes: 0, 
      total_students: 0, 
      total_teachers: 0, 
      total_parents: 0, 
      overall_average: 0 
    }) 
  },
  topStudents: { type: Array, default: () => [] },
  announcements: { type: Array, default: () => [] },
  promoted: { type: Number, default: 0 },
  retained: { type: Number, default: 0 },
  honorPercentages: {
    type: Object,
    default: () => ({
      with_honors: 0,
      with_high_honors: 0,
      with_highest_honors: 0,
      non_honor: 0,
    }),
  },
})

// Icon mapping for summary cards
const getSummaryIcon = (key) => {
  const icons = {
    total_parents: Users,
    total_students: GraduationCap,
    total_teachers: UserCheck,
    total_classes: School,
    overall_average: Target,
    promoted: TrendingUp,
    retained: TrendingDown,
  };
  return icons[key] || BarChart3;
};

// Modern gradient backgrounds
const getCardGradient = (key) => {
  const gradients = {
    total_parents: 'from-pink-500 to-rose-600',
    total_students: 'from-blue-500 to-indigo-600',
    total_teachers: 'from-purple-500 to-violet-600',
    total_classes: 'from-emerald-500 to-teal-600',
    overall_average: 'from-amber-500 to-orange-600',
    promoted: 'from-green-500 to-emerald-600',
    retained: 'from-red-500 to-rose-600',
  };
  return gradients[key] || 'from-gray-500 to-slate-600';
};

// Helper function to format card titles
const formatCardTitle = (key) => {
  const titles = {
    total_parents: 'Total Parents',
    total_students: 'Total Students',
    total_teachers: 'Total Teachers',
    total_classes: 'Total Classes',
    overall_average: 'Overall Average',
    promoted: 'Promoted',
    retained: 'Retained',
  };
  return titles[key] || key.replace('_', ' ');
};

// Helper function to format date and time
const formatDateTime = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffInHours = Math.abs(now - date) / (1000 * 60 * 60);
  
  if (diffInHours < 24) {
    return { 
      date: 'Today', 
      time: date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })
    };
  } else if (diffInHours < 48) {
    return { 
      date: 'Yesterday', 
      time: date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })
    };
  } else {
    const dateOptions = { month: 'short', day: 'numeric' };
    const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
    
    return {
      date: date.toLocaleDateString('en-US', dateOptions),
      time: date.toLocaleTimeString('en-US', timeOptions)
    };
  }
};

// Enhanced rank styling
const getRankStyling = (index) => {
  const styles = [
    { 
      icon: Crown, 
      gradient: 'from-yellow-400 to-yellow-600', 
      ring: 'ring-yellow-400/30',
      badge: '1st',
      glow: 'shadow-yellow-500/25'
    },
    { 
      icon: Medal, 
      gradient: 'from-slate-300 to-slate-500', 
      ring: 'ring-slate-400/30',
      badge: '2nd',
      glow: 'shadow-slate-500/25'
    },
    { 
      icon: Award, 
      gradient: 'from-orange-400 to-orange-600', 
      ring: 'ring-orange-400/30',
      badge: '3rd',
      glow: 'shadow-orange-500/25'
    }
  ];
  return styles[index] || { 
    icon: Star, 
    gradient: 'from-blue-400 to-blue-600', 
    ring: 'ring-blue-400/30',
    badge: `${index + 1}th`,
    glow: 'shadow-blue-500/25'
  };
};

// Convert summary object to arrays for easier iteration
const primarySummaryItems = [
  { key: 'total_parents', value: props.summary.total_parents },
  { key: 'total_students', value: props.summary.total_students },
  { key: 'total_teachers', value: props.summary.total_teachers },
  { key: 'total_classes', value: props.summary.total_classes }
];

const secondarySummaryItems = [
  { key: 'overall_average', value: `${props.summary.overall_average}%` },
  { key: 'promoted', value: props.promoted },
  { key: 'retained', value: props.retained }
];

// Honor items for cards
const honorItems = [
  { title: 'With Honors', value: `${props.honorPercentages.with_honors}%`, gradient: 'from-yellow-500 to-orange-600', icon: Award },
  { title: 'With High Honors', value: `${props.honorPercentages.with_high_honors}%`, gradient: 'from-orange-500 to-red-600', icon: Medal },
  { title: 'With Highest Honors', value: `${props.honorPercentages.with_highest_honors}%`, gradient: 'from-purple-500 to-pink-600', icon: Crown }
];

// Chart options for ApexCharts
const chartOptions = {
  chart: { 
    toolbar: { show: false },
    background: 'transparent',
    foreColor: '#ffffff'
  },
  xaxis: { 
    categories: props.topClasses.map(c => c.name),
    labels: {
      style: {
        colors: '#ffffff'
      }
    }
  },
  yaxis: {
    labels: {
      style: {
        colors: '#ffffff'
      }
    }
  },
  colors: ['#3b82f6', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b'],
  title: { 
    text: 'Class Average Grade',
    style: {
      color: '#ffffff'
    }
  },
  plotOptions: {
    bar: {
      borderRadius: 8,
      columnWidth: '60%'
    }
  },
  dataLabels: {
    enabled: true,
    style: {
      colors: ['#ffffff']
    }
  },
  grid: {
    borderColor: '#374151',
    strokeDashArray: 4
  },
  tooltip: {
    theme: 'dark',
    style: {
      fontSize: '14px',
      fontFamily: 'inherit'
    },
    custom: function({series, seriesIndex, dataPointIndex, w}) {
      const className = w.globals.labels[dataPointIndex];
      const value = series[seriesIndex][dataPointIndex];
      return `<div style="background: #1f2937; padding: 12px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border: 1px solid #374151;">
                <div style="color: #ffffff; font-weight: 600; margin-bottom: 4px;">${className}</div>
                <div style="color: #93c5fd; font-size: 16px; font-weight: 700;">${value}%</div>
              </div>`;
    }
  }
};

const chartSeries = [{ 
  name: 'Average', 
  data: props.topClasses.map(c => parseFloat(c.average) || 0)
}];
</script>

<script>
import VueApexCharts from 'vue3-apexcharts'

export default {
  components: {
    apexchart: VueApexCharts,
  },
}
</script>

<template>
  <Head title="Admin Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="relative">
              <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-xl shadow-indigo-500/25 ring-4 ring-white/10">
                <Settings class="w-7 h-7 text-white" />
              </div>
            </div>
            <div>
              <h1 class="text-2xl sm:text-3xl font-bold text-white">
                <span class="bg-gradient-to-r from-white via-white to-white/80 bg-clip-text text-transparent">
                  Admin Control Center
                </span>
              </h1>
              <p class="text-white/70 text-sm sm:text-base flex items-center space-x-2">
                <Activity class="w-4 h-4" />
                <span>System overview & analytics</span>
              </p>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">
      <!-- Primary Summary Cards (4 cards) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <div
          v-for="(item, index) in primarySummaryItems"
          :key="item.key"
          class="group relative overflow-hidden rounded-2xl backdrop-blur-xl bg-white/10 border border-white/20 shadow-2xl hover:shadow-3xl hover:bg-white/15 transition-all duration-500 hover:-translate-y-2 cursor-pointer"
          :style="{ 'animation-delay': `${index * 100}ms` }"
        >
          <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity duration-500', getCardGradient(item.key)]"></div>
          
          <div class="relative p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div :class="['w-12 h-12 rounded-xl bg-gradient-to-br flex items-center justify-center shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3', getCardGradient(item.key)]">
                  <component :is="getSummaryIcon(item.key)" class="w-6 h-6 text-white" />
                </div>
                
                <div class="space-y-1">
                  <div class="text-2xl sm:text-3xl font-bold text-white group-hover:text-white/90 transition-colors">
                    {{ item.value }}
                  </div>
                  <div class="text-sm text-white/70 font-medium">
                    {{ formatCardTitle(item.key) }}
                  </div>
                </div>
              </div>
            </div>
            
            <div class="absolute bottom-0 right-0 w-24 h-24 bg-gradient-to-tl from-white/5 to-transparent rounded-full transform translate-x-8 translate-y-8 group-hover:translate-x-6 group-hover:translate-y-6 transition-transform duration-500"></div>
          </div>
        </div>
      </div>

      <!-- Secondary Summary Cards (3 cards) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <div
          v-for="(item, index) in secondarySummaryItems"
          :key="item.key"
          class="group relative overflow-hidden rounded-2xl backdrop-blur-xl bg-white/10 border border-white/20 shadow-2xl hover:shadow-3xl hover:bg-white/15 transition-all duration-500 hover:-translate-y-2 cursor-pointer"
          :style="{ 'animation-delay': `${(index + 4) * 100}ms` }"
        >
          <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity duration-500', getCardGradient(item.key)]"></div>
          
          <div class="relative p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div :class="['w-12 h-12 rounded-xl bg-gradient-to-br flex items-center justify-center shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3', getCardGradient(item.key)]">
                  <component :is="getSummaryIcon(item.key)" class="w-6 h-6 text-white" />
                </div>
                
                <div class="space-y-1">
                  <div class="text-2xl sm:text-3xl font-bold text-white group-hover:text-white/90 transition-colors">
                    {{ item.value }}
                  </div>
                  <div class="text-sm text-white/70 font-medium">
                    {{ formatCardTitle(item.key) }}
                  </div>
                </div>
              </div>
            </div>
            
            <div class="absolute bottom-0 right-0 w-24 h-24 bg-gradient-to-tl from-white/5 to-transparent rounded-full transform translate-x-8 translate-y-8 group-hover:translate-x-6 group-hover:translate-y-6 transition-transform duration-500"></div>
          </div>
        </div>
      </div>

      <!-- Top 5 Performing Classes Chart -->
      <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl shadow-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-white/10 bg-gradient-to-r from-white/5 to-transparent">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-green-500/25">
                <BarChart3 class="w-5 h-5 text-white" />
              </div>
              <div>
                <h2 class="text-xl font-bold text-white flex items-center space-x-2">
                  <span>Top 5 Performing Classes</span>
                  <Sparkles class="w-5 h-5 text-green-400" />
                </h2>
                <p class="text-white/70 text-sm">Class performance analytics</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div v-if="topClasses.length > 0">
            <apexchart
              type="bar"
              height="350"
              :options="chartOptions"
              :series="chartSeries"
            />
          </div>
          <div v-else class="text-center py-16">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-green-500/20 to-emerald-500/20 flex items-center justify-center mb-4 backdrop-blur-sm border border-white/10">
              <BarChart3 class="w-8 h-8 text-white/40" />
            </div>
            <h3 class="text-lg font-semibold text-white/80 mb-2">No Class Data</h3>
            <p class="text-white/60 text-sm max-w-sm mx-auto">
              Class performance data will appear here once available
            </p>
          </div>
        </div>
      </div>

      <!-- Top Students & Honor Statistics -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 5 Students -->
        <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl shadow-2xl overflow-hidden">
          <div class="px-6 py-5 border-b border-white/10 bg-gradient-to-r from-white/5 to-transparent">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center shadow-lg shadow-yellow-500/25">
                  <Trophy class="w-5 h-5 text-white" />
                </div>
                <div>
                  <h2 class="text-xl font-bold text-white flex items-center space-x-2">
                    <span>Top 5 Students</span>
                    <Sparkles class="w-5 h-5 text-yellow-400" />
                  </h2>
                  <p class="text-white/70 text-sm">Highest performing students</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="p-6">
            <div v-if="topStudents.length" class="space-y-3">
              <div
                v-for="(student, index) in topStudents.slice(0, 10)"
                :key="student.id"
                class="group relative overflow-hidden rounded-2xl backdrop-blur-sm bg-white/5 border border-white/10 p-4 hover:bg-white/10 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
              >
                <div :class="['absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-300', getRankStyling(index).glow]"></div>
                
                <div class="relative flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                    <div class="relative">
                      <div :class="['w-10 h-10 rounded-xl bg-gradient-to-br flex items-center justify-center shadow-lg ring-2 transition-transform duration-300 group-hover:scale-110', getRankStyling(index).gradient, getRankStyling(index).ring]">
                        <component :is="getRankStyling(index).icon" class="w-5 h-5 text-white" />
                      </div>
                      <div class="absolute -top-1 -right-1 bg-white/20 backdrop-blur-sm rounded-full px-1.5 py-0.5 text-xs font-bold text-white border border-white/30">
                        {{ index + 1 }}
                      </div>
                    </div>
                    
                    <div class="relative">
                      <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-lg ring-2 ring-white/20">
                        {{ student.name.split(' ').map(n => n.charAt(0)).join('') }}
                      </div>
                    </div>
                    
                    <div>
                      <h3 class="font-semibold text-white text-sm group-hover:text-white/90 transition-colors">
                        {{ student.name }}
                      </h3>
                      <div class="flex items-center space-x-2 text-xs text-white/70">
                        <span>{{ student.class }}</span>
                        <span>•</span>
                        <span>Grade {{ student.grade_level }}</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="text-right">
                    <div class="text-lg font-bold text-white/90">
                      {{ student.average }}
                    </div>
                    <div class="text-xs text-white/50">
                      Average
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-16">
              <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-yellow-500/20 to-orange-500/20 flex items-center justify-center mb-4 backdrop-blur-sm border border-white/10">
                <Trophy class="w-8 h-8 text-white/40" />
              </div>
              <h3 class="text-lg font-semibold text-white/80 mb-2">No Students Yet</h3>
              <p class="text-white/60 text-sm max-w-sm mx-auto">
                Top performing students will appear here once data is available
              </p>
            </div>
          </div>
        </div>

        <!-- Honor Statistics -->
        <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl shadow-2xl overflow-hidden">
          <div class="px-6 py-5 border-b border-white/10 bg-gradient-to-r from-white/5 to-transparent">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg shadow-purple-500/25">
                  <Crown class="w-5 h-5 text-white" />
                </div>
                <div>
                  <h2 class="text-xl font-bold text-white flex items-center space-x-2">
                    <span>Honor Statistics</span>
                    <Sparkles class="w-5 h-5 text-purple-400" />
                  </h2>
                  <p class="text-white/70 text-sm">Academic recognition distribution</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="p-6 space-y-4">
            <div
              v-for="(honor, index) in honorItems"
              :key="honor.title"
              class="group relative overflow-hidden rounded-2xl backdrop-blur-sm bg-white/5 border border-white/10 p-6 hover:bg-white/10 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
            >
              <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity duration-500', honor.gradient]"></div>
              
              <div class="relative flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <div :class="['w-12 h-12 rounded-xl bg-gradient-to-br flex items-center justify-center shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3', honor.gradient]">
                    <component :is="honor.icon" class="w-6 h-6 text-white" />
                  </div>
                  
                  <div class="space-y-1">
                    <div class="text-2xl font-bold text-white group-hover:text-white/90 transition-colors">
                      {{ honor.value }}
                    </div>
                    <div class="text-sm text-white/70 font-medium">
                      {{ honor.title }}
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="absolute bottom-0 right-0 w-20 h-20 bg-gradient-to-tl from-white/5 to-transparent rounded-full transform translate-x-6 translate-y-6 group-hover:translate-x-4 group-hover:translate-y-4 transition-transform duration-500"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Latest Announcements -->
      <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl shadow-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-white/10 bg-gradient-to-r from-white/5 to-transparent">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/25">
                <Megaphone class="w-5 h-5 text-white" />
              </div>
              <div>
                <h2 class="text-xl font-bold text-white">Latest Announcements</h2>
                <p class="text-white/70 text-sm">Recent system updates & notifications</p>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
              <span class="text-xs text-white/70">Live</span>
            </div>
          </div>
        </div>

        <div class="p-6">
          <div v-if="announcements.length" class="space-y-4">
            <div
              v-for="(announcement, index) in announcements"
              :key="announcement.id"
              class="group relative overflow-hidden rounded-2xl backdrop-blur-sm bg-white/5 border border-white/10 p-6 hover:bg-white/10 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
            >
              <div class="flex items-start space-x-4">
                <div class="relative flex-shrink-0">
                  <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <User class="w-6 h-6 text-white" />
                  </div>
                </div>
                
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between mb-3">
                    <h3 class="font-semibold text-white text-lg group-hover:text-white/90 transition-colors leading-tight">
                      {{ announcement.title }}
                    </h3>
                  </div>
                  
                  <p class="text-white/80 text-base leading-relaxed mb-4">
                    {{ announcement.body }}
                  </p>
                  
                  <div class="flex flex-wrap gap-4 text-sm">
                    <div class="flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-white/10 border border-white/20">
                      <User class="w-4 h-4 text-white/60" />
                      <span class="text-white/80 font-medium">{{ announcement.created_by }}</span>
                    </div>
                    <div class="flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-white/10 border border-white/20">
                      <Calendar class="w-4 h-4 text-white/60" />
                      <span class="text-white/80">{{ formatDateTime(announcement.created_at).date }}</span>
                    </div>
                    <div class="flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-white/10 border border-white/20">
                      <Clock class="w-4 h-4 text-white/60" />
                      <span class="text-white/80">{{ formatDateTime(announcement.created_at).time }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-16">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center mb-4 backdrop-blur-sm border border-white/10">
              <Megaphone class="w-8 h-8 text-white/40" />
            </div>
            <h3 class="text-lg font-semibold text-white/80 mb-2">No Announcements</h3>
            <p class="text-white/60 text-sm max-w-sm mx-auto">
              New announcements and updates will appear here when available
            </p>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.group:hover .float {
  animation: float 2s ease-in-out infinite;
}

/* Enhanced glassmorphism effects */
.backdrop-blur-xl {
  backdrop-filter: blur(20px);
}

/* Smooth animations */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.grid > div {
  animation: slideInUp 0.6s ease-out forwards;
  opacity: 0;
}

.grid > div:nth-child(1) { animation-delay: 0.1s; }
.grid > div:nth-child(2) { animation-delay: 0.2s; }
.grid > div:nth-child(3) { animation-delay: 0.3s; }
.grid > div:nth-child(4) { animation-delay: 0.4s; }
.grid > div:nth-child(5) { animation-delay: 0.5s; }
.grid > div:nth-child(6) { animation-delay: 0.6s; }
.grid > div:nth-child(7) { animation-delay: 0.7s; }
</style>