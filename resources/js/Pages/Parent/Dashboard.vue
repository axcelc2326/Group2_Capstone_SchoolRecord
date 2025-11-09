<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { 
  Users, 
  UserCheck, 
  TrendingUp,
  TrendingDown,
  BookOpen,
  School,
  Megaphone,
  Edit,
  User,
  Calendar,
  CheckCircle,
  AlertTriangle,
  Clock,
  Award,
  Sparkles,
  Activity,
  UserStar,
  Info
} from 'lucide-vue-next';

defineProps({
    user: Object,
    summary: Object,
    announcements: Array,
    childrenPerformance: Array
});

// Helper function to get icon component for summary cards
const getSummaryIcon = (key) => {
  const icons = {
    total_registered: Users,
    total_enrolled: UserCheck,
    promoted: TrendingUp,
    failed: TrendingDown
  };
  return icons[key] || Users;
};

// Modern gradient backgrounds
const getCardGradient = (key) => {
  const gradients = {
    total_registered: 'from-violet-500 to-purple-600',
    total_enrolled: 'from-emerald-500 to-teal-600',
    promoted: 'from-green-500 to-emerald-600',
    failed: 'from-rose-500 to-red-600',
  };
  return gradients[key] || 'from-gray-500 to-slate-600';
};

// Helper function to format card titles
const formatCardTitle = (key) => {
  const titles = {
    total_registered: 'Total Registered',
    total_enrolled: 'Enrolled',
    promoted: 'Promoted',
    failed: 'Needs Help'
  };
  return titles[key] || key.replace('_', ' ');
};

// Helper function to get card descriptions
const getCardDescription = (key) => {
  const descriptions = {
    total_registered: 'Children registered in your account',
    total_enrolled: 'Currently enrolled this academic year',
    promoted: 'Children advanced to next grade',
    failed: 'Children requiring additional support'
  };
  return descriptions[key] || '';
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

// Helper to determine status color and icon
const getStatusInfo = (child) => {
  if (child.status === 'Promoted') {
    return {
      bgColor: 'bg-emerald-500/20',
      textColor: 'text-emerald-100',
      borderColor: 'border-emerald-400/30',
      icon: CheckCircle,
      label: 'Promoted',
      glow: 'shadow-emerald-500/25'
    };
  } else if (child.status === 'In Progress') {
    return {
      bgColor: 'bg-blue-500/20',
      textColor: 'text-blue-100',
      borderColor: 'border-blue-400/30',
      icon: Clock, // Assuming you have a Clock icon
      label: 'In Progress',
      glow: 'shadow-blue-500/25'
    };
  } else {
    return {
      bgColor: 'bg-red-500/20',
      textColor: 'text-red-100',
      borderColor: 'border-red-400/30',
      icon: AlertTriangle,
      label: 'Needs Help',
      glow: 'shadow-red-500/25'
    };
  }
};
</script>

<template>
    <Head title="Parent Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                      <div class="relative">
                          <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-lg sm:shadow-xl shadow-rose-500/25 ring-2 sm:ring-4 ring-white/10">
                              <UserStar class="w-5 h-5 sm:w-7 sm:h-7 text-white" />
                          </div>
                      </div>
                      <div>
                          <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white">
                              <span class="bg-gradient-to-r from-white via-white to-white/80 bg-clip-text text-transparent">
                                  Welcome back! {{ user.name }}
                              </span>
                          </h1>
                          <p class="text-white/70 text-xs sm:text-sm lg:text-base flex items-center space-x-1.5 sm:space-x-2">
                              <Activity class="w-3 h-3 sm:w-4 sm:h-4" />
                              <span>Track your children's progress</span>
                          </p>
                      </div>
                  </div>
              </div>
            </div>
        </template>

        <div class="py-4 sm:py-6 px-3 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-4 sm:space-y-6 lg:space-y-8">
          <!-- Enhanced Summary Cards - Mobile First -->
          <div class="space-y-3 sm:space-y-4 lg:space-y-6">
            <!-- Mobile: Stack all cards vertically -->
            <div class="flex flex-col space-y-3 sm:hidden">
              <!-- Card 1: Total Registered -->
              <div class="group relative overflow-hidden rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 shadow-xl hover:shadow-2xl active:bg-white/15 transition-all duration-300">
                <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-active:opacity-10 transition-opacity duration-300', getCardGradient('total_registered')]"></div>
                
                <div class="relative p-4">
                  <div class="flex items-center space-x-3 mb-2">
                    <div :class="['w-10 h-10 rounded-lg bg-gradient-to-br flex items-center justify-center shadow-md', getCardGradient('total_registered')]">
                      <component :is="getSummaryIcon('total_registered')" class="w-5 h-5 text-white" />
                    </div>
                    <div class="flex-1">
                      <div class="text-2xl font-bold text-white">
                        {{ summary.total_registered || 0 }}
                      </div>
                      <div class="text-xs text-white/70 font-medium">
                        {{ formatCardTitle('total_registered') }}
                      </div>
                    </div>
                  </div>
                  <p class="text-xs text-white/60 leading-relaxed">
                    {{ getCardDescription('total_registered') }}
                  </p>
                </div>
              </div>

              <!-- Card 2: Enrolled -->
              <div class="group relative overflow-hidden rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 shadow-xl hover:shadow-2xl active:bg-white/15 transition-all duration-300">
                <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-active:opacity-10 transition-opacity duration-300', getCardGradient('total_enrolled')]"></div>
                
                <div class="relative p-4">
                  <div class="flex items-center space-x-3 mb-2">
                    <div :class="['w-10 h-10 rounded-lg bg-gradient-to-br flex items-center justify-center shadow-md', getCardGradient('total_enrolled')]">
                      <component :is="getSummaryIcon('total_enrolled')" class="w-5 h-5 text-white" />
                    </div>
                    <div class="flex-1">
                      <div class="text-2xl font-bold text-white">
                        {{ summary.total_enrolled || 0 }}
                      </div>
                      <div class="text-xs text-white/70 font-medium">
                        {{ formatCardTitle('total_enrolled') }}
                      </div>
                    </div>
                  </div>
                  <p class="text-xs text-white/60 leading-relaxed">
                    {{ getCardDescription('total_enrolled') }}
                  </p>
                </div>
              </div>

              <!-- Card 4: Promoted -->
              <div class="group relative overflow-hidden rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 shadow-xl hover:shadow-2xl active:bg-white/15 transition-all duration-300">
                <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-active:opacity-10 transition-opacity duration-300', getCardGradient('promoted')]"></div>
                
                <div class="relative p-4">
                  <div class="flex items-center space-x-3 mb-2">
                    <div :class="['w-10 h-10 rounded-lg bg-gradient-to-br flex items-center justify-center shadow-md', getCardGradient('promoted')]">
                      <component :is="getSummaryIcon('promoted')" class="w-5 h-5 text-white" />
                    </div>
                    <div class="flex-1">
                      <div class="text-2xl font-bold text-white">
                        {{ summary.promoted || 0 }}
                      </div>
                      <div class="text-xs text-white/70 font-medium">
                        {{ formatCardTitle('promoted') }}
                      </div>
                    </div>
                  </div>
                  <p class="text-xs text-white/60 leading-relaxed">
                    {{ getCardDescription('promoted') }}
                  </p>
                </div>
              </div>

              <!-- Card 5: Needs Help -->
              <div class="group relative overflow-hidden rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 shadow-xl hover:shadow-2xl active:bg-white/15 transition-all duration-300">
                <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-active:opacity-10 transition-opacity duration-300', getCardGradient('failed')]"></div>
                
                <div class="relative p-4">
                  <div class="flex items-center space-x-3 mb-2">
                    <div :class="['w-10 h-10 rounded-lg bg-gradient-to-br flex items-center justify-center shadow-md', getCardGradient('failed')]">
                      <component :is="getSummaryIcon('failed')" class="w-5 h-5 text-white" />
                    </div>
                    <div class="flex-1">
                      <div class="text-2xl font-bold text-white">
                        {{ summary.failed || 0 }}
                      </div>
                      <div class="text-xs text-white/70 font-medium">
                        {{ formatCardTitle('failed') }}
                      </div>
                    </div>
                  </div>
                  <p class="text-xs text-white/60 leading-relaxed">
                    {{ getCardDescription('failed') }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Tablet & Desktop: Grid layout -->
            <div class="hidden sm:block space-y-4 lg:space-y-6">
              <!-- First Row -->
              <div class="grid grid-cols-2 lg:grid-cols-2 gap-4 lg:gap-6">
                <div
                  v-for="(item, index) in [
                    { key: 'total_registered', value: summary.total_registered || 0 },
                    { key: 'total_enrolled', value: summary.total_enrolled || 0 },
                  ]"
                  :key="item.key"
                  class="group relative overflow-hidden rounded-2xl backdrop-blur-xl bg-white/10 border border-white/20 shadow-2xl hover:shadow-3xl hover:bg-white/15 transition-all duration-500 hover:-translate-y-2 cursor-pointer"
                  :style="{ 'animation-delay': `${index * 100}ms` }"
                >
                  <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity duration-500', getCardGradient(item.key)]"></div>
                  
                  <div class="relative p-5 lg:p-6">
                    <div class="flex items-center space-x-3 lg:space-x-4 mb-3">
                      <div :class="['w-11 h-11 lg:w-12 lg:h-12 rounded-xl bg-gradient-to-br flex items-center justify-center shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3', getCardGradient(item.key)]">
                        <component :is="getSummaryIcon(item.key)" class="w-5 h-5 lg:w-6 lg:h-6 text-white" />
                      </div>
                      
                      <div class="flex-1 space-y-1">
                        <div class="text-2xl lg:text-3xl font-bold text-white group-hover:text-white/90 transition-colors">
                          {{ item.value }}
                        </div>
                        <div class="text-sm text-white/70 font-medium">
                          {{ formatCardTitle(item.key) }}
                        </div>
                      </div>
                    </div>
                    
                    <p class="text-xs lg:text-sm text-white/60 leading-relaxed">
                      {{ getCardDescription(item.key) }}
                    </p>
                    
                    <div class="absolute bottom-0 right-0 w-20 h-20 lg:w-24 lg:h-24 bg-gradient-to-tl from-white/5 to-transparent rounded-full transform translate-x-8 translate-y-8 group-hover:translate-x-6 group-hover:translate-y-6 transition-transform duration-500"></div>
                  </div>
                </div>
              </div>

              <!-- Second Row -->
              <div class="grid grid-cols-2 gap-4 lg:gap-6">
                <div
                  v-for="(item, index) in [
                    { key: 'promoted', value: summary.promoted || 0 },
                    { key: 'failed', value: summary.failed || 0 }
                  ]"
                  :key="item.key"
                  class="group relative overflow-hidden rounded-2xl backdrop-blur-xl bg-white/10 border border-white/20 shadow-2xl hover:shadow-3xl hover:bg-white/15 transition-all duration-500 hover:-translate-y-2 cursor-pointer"
                  :style="{ 'animation-delay': `${(index + 3) * 100}ms` }"
                >
                  <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity duration-500', getCardGradient(item.key)]"></div>
                  
                  <div class="relative p-5 lg:p-6">
                    <div class="flex items-center space-x-3 lg:space-x-4 mb-3">
                      <div :class="['w-11 h-11 lg:w-12 lg:h-12 rounded-xl bg-gradient-to-br flex items-center justify-center shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3', getCardGradient(item.key)]">
                        <component :is="getSummaryIcon(item.key)" class="w-5 h-5 lg:w-6 lg:h-6 text-white" />
                      </div>
                      
                      <div class="flex-1 space-y-1">
                        <div class="text-2xl lg:text-3xl font-bold text-white group-hover:text-white/90 transition-colors">
                          {{ item.value }}
                        </div>
                        <div class="text-sm text-white/70 font-medium">
                          {{ formatCardTitle(item.key) }}
                        </div>
                      </div>
                    </div>
                    
                    <p class="text-xs lg:text-sm text-white/60 leading-relaxed">
                      {{ getCardDescription(item.key) }}
                    </p>
                    
                    <div class="absolute bottom-0 right-0 w-20 h-20 lg:w-24 lg:h-24 bg-gradient-to-tl from-white/5 to-transparent rounded-full transform translate-x-8 translate-y-8 group-hover:translate-x-6 group-hover:translate-y-6 transition-transform duration-500"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modern Children Performance Section - Mobile First -->
          <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-white/10 bg-gradient-to-r from-white/5 to-transparent">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                  <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/25">
                    <Award class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                  </div>
                  <div>
                    <h2 class="text-base sm:text-xl font-bold text-white flex items-center space-x-1.5 sm:space-x-2">
                      <span>Children Performance</span>
                      <Sparkles class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-400" />
                    </h2>
                    <p class="text-white/70 text-xs sm:text-sm">Academic progress tracking</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Children List -->
            <div class="p-4 sm:p-6">
              <div v-if="childrenPerformance && childrenPerformance.length" class="space-y-3 sm:space-y-4">
                <div
                  v-for="(child, index) in childrenPerformance"
                  :key="child.id"
                  class="group relative overflow-hidden rounded-xl sm:rounded-2xl backdrop-blur-sm bg-white/5 border border-white/10 p-4 sm:p-6 active:bg-white/10 sm:hover:bg-white/10 transition-all duration-300 sm:hover:-translate-y-1 sm:hover:shadow-xl"
                >
                  <div :class="['absolute inset-0 rounded-xl sm:rounded-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-300', getStatusInfo(child).glow]"></div>
                  
                  <div class="relative space-y-3 sm:space-y-4">
                    <!-- Mobile: Stacked layout -->
                    <div class="sm:hidden space-y-3">
                      <!-- Child Info -->
                      <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                          <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold shadow-lg ring-2 ring-white/20">
                            <User class="w-6 h-6" />
                          </div>
                          
                          <div>
                            <h3 class="font-semibold text-white text-base leading-tight">
                              {{ child.name }}
                            </h3>
                            <div class="flex items-center space-x-2 text-xs text-white/70 mt-1">
                              <span class="flex items-center space-x-1">
                                <School class="w-3 h-3" />
                                <span>Grade {{ child.grade_level || 'N/A' }}</span>
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Section -->
                      <div class="flex items-center space-x-2 text-xs text-white/70 px-3 py-2 rounded-lg bg-white/5 border border-white/10">
                        <BookOpen class="w-3 h-3" />
                        <span>Section - {{ child.class_name || 'N/A' }}</span>
                      </div>

                      <!-- Status Badge -->
                      <div
                        :class="[
                          getStatusInfo(child).bgColor, 
                          getStatusInfo(child).textColor, 
                          getStatusInfo(child).borderColor
                        ]"
                        class="flex items-center justify-center space-x-2 px-4 py-2.5 rounded-lg text-sm font-medium border backdrop-blur-sm shadow-lg w-full"
                      >
                        <component 
                          :is="getStatusInfo(child).icon"
                          class="w-4 h-4"
                        />
                        <span>{{ getStatusInfo(child).label }}</span>
                      </div>
                      
                      <!-- Performance Metrics -->
                      <div class="grid grid-cols-2 gap-3">
                        <div class="backdrop-blur-sm bg-white/5 rounded-lg p-3 border border-white/10 text-center">
                          <div class="text-xl font-bold text-white">{{ child.average || 'N/A' }}</div>
                          <div class="text-xs text-white/60 mt-1">Current Average</div>
                        </div>
                        <div class="backdrop-blur-sm bg-white/5 rounded-lg p-3 border border-white/10 text-center">
                          <div class="text-xl font-bold text-white">{{ child.final_average || 'N/A' }}</div>
                          <div class="text-xs text-white/60 mt-1">Final Average</div>
                        </div>
                      </div>
                      
                      <!-- Remarks -->
                      <div v-if="child.remarks" class="p-3 rounded-lg bg-white/5 border border-white/10">
                        <div class="flex items-start space-x-2">
                          <Info class="w-4 h-4 text-white/60 mt-0.5 flex-shrink-0" />
                          <p class="text-xs text-white/70 leading-relaxed">
                            <span class="font-medium text-white/90">Remarks:</span> {{ child.remarks }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- Desktop: Original layout -->
                    <div class="hidden sm:block">
                      <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-4">
                          <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg ring-2 ring-white/20">
                            <User class="w-6 h-6" />
                          </div>
                          
                          <div>
                            <h3 class="font-semibold text-white text-lg group-hover:text-white/90 transition-colors">
                              {{ child.name }}
                            </h3>
                            <div class="flex items-center space-x-3 text-sm text-white/70 mt-1">
                              <span class="flex items-center space-x-1">
                                <School class="w-4 h-4" />
                                <span>Grade {{ child.grade_level || 'N/A' }}</span>
                                <span>Section - {{ child.class_name || 'N/A' }}</span>
                              </span>
                            </div>
                          </div>
                        </div>
                        
                        <div
                          :class="[
                            getStatusInfo(child).bgColor, 
                            getStatusInfo(child).textColor, 
                            getStatusInfo(child).borderColor
                          ]"
                          class="flex items-center space-x-2 px-4 py-2 rounded-xl text-sm font-medium border backdrop-blur-sm shadow-lg"
                        >
                          <component 
                            :is="getStatusInfo(child).icon"
                            class="w-4 h-4"
                          />
                          <span>{{ getStatusInfo(child).label }}</span>
                        </div>
                      </div>
                      
                      <div class="flex items-center space-x-4">
                        <div class="flex-1 backdrop-blur-sm bg-white/5 rounded-xl p-4 border border-white/10">
                          <div class="text-2xl font-bold text-white">{{ child.average || 'N/A' }}</div>
                          <div class="text-xs text-white/60 mt-1">Current Average</div>
                        </div>
                        <div class="flex-1 backdrop-blur-sm bg-white/5 rounded-xl p-4 border border-white/10">
                          <div class="text-2xl font-bold text-white">{{ child.final_average || 'N/A' }}</div>
                          <div class="text-xs text-white/60 mt-1">Final Average</div>
                        </div>
                      </div>
                      
                      <div v-if="child.remarks" class="mt-4 p-3 rounded-lg bg-white/5 border border-white/10">
                        <p class="text-sm text-white/70">
                          <span class="font-medium text-white/90">Remarks:</span> {{ child.remarks }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Enhanced Empty State -->
              <div v-else class="text-center py-12 sm:py-16">
                <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto rounded-xl sm:rounded-2xl bg-gradient-to-br from-blue-500/20 to-indigo-500/20 flex items-center justify-center mb-3 sm:mb-4 backdrop-blur-sm border border-white/10">
                  <Users class="w-7 h-7 sm:w-8 sm:h-8 text-white/40" />
                </div>
                <h3 class="text-base sm:text-lg font-semibold text-white/80 mb-2">No Children Registered</h3>
                <p class="text-white/60 text-sm px-4 max-w-sm mx-auto">
                  You don't have any approved children registered yet
                </p>
              </div>
            </div>
          </div>

          <!-- Modern Announcements - Mobile First -->
          <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-white/10 bg-gradient-to-r from-white/5 to-transparent">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                  <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/25">
                    <Megaphone class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                  </div>
                  <div>
                    <h2 class="text-base sm:text-xl font-bold text-white">Latest Announcements</h2>
                    <p class="text-white/70 text-xs sm:text-sm">Stay updated with school news</p>
                  </div>
                </div>
                <div class="flex items-center space-x-1.5 sm:space-x-2">
                  <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                  <span class="text-xs text-white/70 hidden sm:inline">Live</span>
                </div>
              </div>
            </div>
            
            <!-- Description -->
            <div class="px-6 pt-4 pb-2">
              <div class="flex items-start space-x-3">
                <Info class="w-5 h-5 text-white/60 mt-0.5 flex-shrink-0" />
                <p class="text-white/60 text-sm leading-relaxed">
                  Stay informed with the latest institutional communications and updates. Review important announcements, policy changes, and system-wide notifications to ensure smooth operations across all departments.
                </p>
              </div>
            </div>

            <!-- Announcements List -->
            <div class="p-4 sm:p-6">
              <div v-if="announcements && announcements.length" class="space-y-3 sm:space-y-4">
                <div
                  v-for="(announcement, index) in announcements"
                  :key="announcement.id"
                  class="group relative overflow-hidden rounded-xl sm:rounded-2xl backdrop-blur-sm bg-white/5 border border-white/10 p-4 sm:p-6 active:bg-white/10 sm:hover:bg-white/10 transition-all duration-300 sm:hover:-translate-y-1 sm:hover:shadow-lg"
                >
                  <!-- Mobile Layout -->
                  <div class="sm:hidden space-y-3">
                    <!-- Header with icon and title -->
                    <div class="flex items-start space-x-3">
                      <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                        <Edit class="w-5 h-5 text-white" />
                      </div>
                      <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-white text-base leading-tight">
                          {{ announcement.title }}
                        </h3>
                      </div>
                    </div>
                    
                    <!-- Body -->
                    <p class="text-white/80 text-sm leading-relaxed">
                      {{ announcement.body }}
                    </p>
                    
                    <!-- Metadata - Stacked for mobile -->
                    <div class="space-y-2">
                      <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-white/10 border border-white/20">
                        <User class="w-3.5 h-3.5 text-white/60 flex-shrink-0" />
                        <span class="text-white/80 text-xs font-medium truncate">{{ announcement.created_by }}</span>
                      </div>
                      <div class="flex items-center space-x-2">
                        <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-white/10 border border-white/20 flex-1">
                          <Calendar class="w-3.5 h-3.5 text-white/60 flex-shrink-0" />
                          <span class="text-white/80 text-xs">{{ formatDateTime(announcement.created_at).date }}</span>
                        </div>
                        <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-white/10 border border-white/20 flex-1">
                          <Clock class="w-3.5 h-3.5 text-white/60 flex-shrink-0" />
                          <span class="text-white/80 text-xs">{{ formatDateTime(announcement.created_at).time }}</span>
                        </div>
                      </div>
                      <!-- Expiration Date - Mobile -->
                      <div v-if="announcement.expires_at" class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-white/10 border border-white/20">
                          <Clock class="w-3.5 h-3.5 text-white/60 flex-shrink-0" />
                          <span class="text-white/80 text-xs">Expires: {{ formatDateTime(announcement.expires_at).date }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Desktop Layout -->
                  <div class="hidden sm:flex items-start space-x-4">
                    <!-- Creator avatar -->
                    <div class="relative flex-shrink-0">
                      <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                        <User class="w-6 h-6 text-white" />
                      </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                      <div class="flex items-start justify-between mb-3">
                        <h3 class="font-semibold text-white text-lg group-hover:text-white/90 transition-colors leading-tight">
                          {{ announcement.title }}
                        </h3>
                      </div>
                      
                      <p class="text-white/80 text-base leading-relaxed mb-4">
                        {{ announcement.body }}
                      </p>
                      
                      <!-- Metadata -->
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
                        <!-- Expiration Date - Desktop -->
                        <div v-if="announcement.expires_at" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-white/10 border border-white/20">
                          <Clock class="w-4 h-4 text-white/60" />
                          <span class="text-white/80">Expires: {{ formatDateTime(announcement.expires_at).date }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Enhanced Empty State -->
              <div v-else class="text-center py-12 sm:py-16">
                <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto rounded-xl sm:rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center mb-3 sm:mb-4 backdrop-blur-sm border border-white/10">
                  <Megaphone class="w-7 h-7 sm:w-8 sm:h-8 text-white/40" />
                </div>
                <h3 class="text-base sm:text-lg font-semibold text-white/80 mb-2">No Announcements</h3>
                <p class="text-white/60 text-sm px-4 max-w-sm mx-auto">
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

/* Smooth animations for desktop */
@media (min-width: 640px) {
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
}

/* Mobile optimizations */
@media (max-width: 639px) {
  /* Ensure smooth scrolling on mobile */
  * {
    -webkit-tap-highlight-color: transparent;
  }
  
  /* Optimize touch targets */
  button, a, [role="button"] {
    min-height: 44px;
    min-width: 44px;
  }
}
</style>