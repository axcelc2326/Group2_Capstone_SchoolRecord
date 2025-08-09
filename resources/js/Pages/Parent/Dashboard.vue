<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { 
  Users, 
  UserCheck, 
  Clock, 
  Star,
  BarChart3,
  BookOpen,
  School,
  Megaphone,
  Edit,
  User,
  Calendar,
  CheckCircle,
  AlertTriangle
} from 'lucide-vue-next';

defineProps({
    user: Object,
    summary: Object,
    announcements: Array,
    childrenPerformance: Array // already passed from backend
});

// Helper function to get icon component for summary cards
const getSummaryIcon = (key) => {
  const icons = {
    total_children: Users,
    enrolled_children: UserCheck,
    pending_children: Clock,
    average_grade: Star
  };
  return icons[key] || BarChart3;
};

// Helper function to format card titles for mobile
const formatCardTitle = (key) => {
  const titles = {
    total_children: 'Total Kids',
    enrolled_children: 'Enrolled',
    pending_children: 'Pending',
    average_grade: 'Avg Grade'
  };
  return titles[key] || key.replace('_', ' ');
};

// Helper function to format date and time
const formatDateTime = (dateString) => {
  const date = new Date(dateString);
  const dateOptions = { month: 'short', day: 'numeric', year: 'numeric' };
  const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
  
  const formattedDate = date.toLocaleDateString('en-US', dateOptions);
  const formattedTime = date.toLocaleTimeString('en-US', timeOptions);
  
  return { date: formattedDate, time: formattedTime };
};
</script>

<template>
    <Head title="Parent Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                  <div>
                      <h2 class="text-xl sm:text-2xl font-bold text-white bg-gradient-to-r from-white to-white/70 bg-clip-text text-transparent">
                          Welcome back! {{ user.name }}
                      </h2>
                      <p class="text-white/70 mt-1">
                          Here's your child's school overview at a glance.
                      </p>
                  </div>
              </div>
            </div>
        </template>

        <div class="py-4 px-4 sm:py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
          <!-- 📊 Summary Cards - Mobile First Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
              <div
                  v-for="(value, key) in summary"
                  :key="key"
                  class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl p-3 sm:p-4 shadow-xl hover:shadow-2xl hover:bg-white/15 transition-all duration-300 transform active:scale-95 sm:hover:scale-105"
              >
                  <!-- Mobile Layout -->
                  <div class="text-center space-y-2">
                      <div class="w-8 h-8 sm:w-10 sm:h-10 mx-auto rounded-lg bg-transparent flex items-center justify-center">
                          <component :is="getSummaryIcon(key)" class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                      </div>
                      <div>
                          <p class="text-lg sm:text-2xl font-bold text-white">{{ value }}</p>
                          <h3 class="text-xs sm:text-sm font-medium text-white/70 leading-tight">
                              {{ formatCardTitle(key) }}
                          </h3>
                      </div>
                  </div>
              </div>
          </div>

          <!-- 🧒 Children Performance (Only Enrolled) -->
          <div
              v-if="childrenPerformance.filter(child => child.status !== 'Pending').length"
              class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl sm:rounded-2xl shadow-2xl overflow-hidden"
          >
              <!-- Mobile-optimized header -->
              <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-white/10">
                  <div class="flex items-center space-x-2 sm:space-x-3">
                      <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center">
                          <BarChart3 class="w-3 h-3 sm:w-5 sm:h-5 text-white" />
                      </div>
                      <div>
                          <h2 class="text-base sm:text-lg font-semibold text-white">Performance</h2>
                          <p class="text-xs sm:text-sm text-white/70 hidden sm:block">Academic progress overview</p>
                      </div>
                  </div>
              </div>
              
              <div class="p-4 sm:p-6">
                  <div class="space-y-3 sm:space-y-4">
                      <div
                          v-for="child in childrenPerformance.filter(c => c.status !== 'Pending')"
                          :key="child.name"
                          class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-xl p-4 sm:p-5 hover:bg-white/10 transition-all duration-200 active:bg-white/15"
                      >
                          <!-- Mobile-first child card layout -->
                          <div class="space-y-3 sm:space-y-0 sm:flex sm:items-center sm:justify-between">
                              <div class="flex items-center space-x-3">
                                  <div class="w-10 h-10 sm:w-12 sm:h-12 flex-shrink-0 rounded-full bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center text-white font-medium text-xs sm:text-sm">
                                      {{ child.name.split(' ').map(n => n.charAt(0)).join('') }}
                                  </div>
                                  <div class="flex-1 min-w-0">
                                      <h3 class="font-semibold text-white text-sm sm:text-base truncate">{{ child.name }}</h3>
                                      <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 text-xs sm:text-sm text-white/70 space-y-1 sm:space-y-0">
                                          <span class="flex items-center space-x-1">
                                              <BookOpen class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0" />
                                              <span>Grade {{ child.grade_level ?? 'N/A' }}</span>
                                          </span>
                                          <span class="flex items-center space-x-1">
                                              <School class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0" />
                                              <span class="truncate">{{ child.class_name ?? 'N/A' }}</span>
                                          </span>
                                      </div>
                                  </div>
                              </div>
                              
                              <!-- Mobile performance indicator -->
                              <div class="flex items-center justify-between sm:justify-end space-x-3 sm:space-x-4">
                                  <div class="text-center sm:text-right">
                                      <div class="text-xl sm:text-2xl font-bold text-white">{{ child.average }}</div>
                                      <div class="text-xs text-white/60">Average</div>
                                  </div>
                                  <div
                                      :class="child.average >= 75 
                                          ? 'bg-emerald-500/20 text-emerald-100 border-emerald-400/30' 
                                          : 'bg-red-500/20 text-red-100 border-red-400/30'"
                                      class="flex items-center space-x-1 sm:space-x-2 px-2 sm:px-3 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-medium border backdrop-blur-sm"
                                  >
                                      <component 
                                          :is="child.average >= 75 ? CheckCircle : AlertTriangle"
                                          class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0"
                                      />
                                      <span class="hidden sm:inline">{{ child.average >= 75 ? 'Good' : 'Needs Work' }}</span>
                                      <span class="sm:hidden">{{ child.average >= 75 ? '✓' : '⚠' }}</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <!-- 📢 Latest Announcements -->
          <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl sm:rounded-2xl shadow-2xl overflow-hidden">
              <!-- Mobile-optimized header -->
              <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-white/10">
                  <div class="flex items-center space-x-2 sm:space-x-3">
                      <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-gradient-to-br from-amber-400 to-orange-600 flex items-center justify-center">
                          <Megaphone class="w-3 h-3 sm:w-5 sm:h-5 text-white" />
                      </div>
                      <div>
                          <h2 class="text-base sm:text-lg font-semibold text-white">Announcements</h2>
                          <p class="text-xs sm:text-sm text-white/70 hidden sm:block">Latest school updates</p>
                      </div>
                  </div>
              </div>

              <div class="p-4 sm:p-6">
                  <div v-if="announcements.length" class="space-y-3 sm:space-y-4">
                      <div
                          v-for="announcement in announcements"
                          :key="announcement.id"
                          class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-xl p-4 sm:p-5 hover:bg-white/10 transition-all duration-200 active:bg-white/15 group"
                      >
                          <div class="space-y-3 sm:space-y-0 sm:flex sm:items-start sm:space-x-4">
                              <div class="flex items-start space-x-3 sm:space-x-0">
                                  <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center">
                                      <Edit class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                                  </div>
                                  <div class="flex-1 min-w-0 sm:hidden">
                                      <h3 class="font-semibold text-white text-sm group-hover:text-white/90 transition-colors duration-200">
                                          {{ announcement.title }}
                                      </h3>
                                  </div>
                              </div>
                              <div class="flex-1 min-w-0 sm:ml-0">
                                  <h3 class="hidden sm:block font-semibold text-white group-hover:text-white/90 transition-colors duration-200">
                                      {{ announcement.title }}
                                  </h3>
                                  <p class="text-white/70 text-sm sm:text-base mt-1 leading-relaxed">
                                      {{ announcement.body }}
                                  </p>
                                  <!-- Mobile-optimized metadata with date and time -->
                                  <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 mt-2 space-y-1 sm:space-y-0 text-xs">
                                      <span class="flex items-center space-x-1 text-white/60">
                                          <User class="w-3 h-3" />
                                          <span class="truncate">{{ announcement.created_by }}</span>
                                      </span>
                                      <span class="flex items-center space-x-1 text-white/60">
                                          <Calendar class="w-3 h-3" />
                                          <span>{{ formatDateTime(announcement.created_at).date }}</span>
                                      </span>
                                      <span class="flex items-center space-x-1 text-white/60">
                                          <Clock class="w-3 h-3" />
                                          <span>{{ formatDateTime(announcement.created_at).time }}</span>
                                      </span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <!-- Mobile-optimized Empty State -->
                  <div v-else class="text-center py-8 sm:py-12">
                      <Megaphone class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-white/40" />
                      <h3 class="mt-2 text-sm font-medium text-white/80">No announcements</h3>
                      <p class="mt-1 text-xs sm:text-sm text-white/60">Check back for school updates</p>
                  </div>
              </div>
          </div>
        </div>
    </AuthenticatedLayout>
</template>