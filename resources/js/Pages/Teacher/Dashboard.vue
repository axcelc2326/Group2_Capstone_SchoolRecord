<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { 
  Users, 
  BookOpen, 
  TrendingUp, 
  Trophy,
  AlertCircle,
  Crown,
  Megaphone,
  User,
  Calendar,
  Clock,
  BarChart3,
  GraduationCap,
  Target,
  Zap,
  Award,
  Medal,
  Star
} from 'lucide-vue-next'

const props = defineProps({
  summary: {
    type: Object,
    default: () => ({
      total_students: 0,
      total_subjects: 0,
      class_average: 0,
      top_subject: null,
      worst_subject: null,
    }),
  },
  topStudents: {
    type: Array,
    default: () => [],
  },
  announcements: {
    type: Array,
    default: () => [],
  },
});

// Simple icon mapping
const getSummaryIcon = (key) => {
  const icons = {
    total_students: Users,
    total_subjects: BookOpen,
    class_average: Target,
    top_subject: Zap,
    worst_subject: AlertCircle
  };
  return icons[key] || BarChart3;
};

// Simple background colors for each card
const getCardColor = (key) => {
  const colors = {
    total_students: 'bg-blue-500',
    total_subjects: 'bg-green-500',
    class_average: 'bg-purple-500',
    top_subject: 'bg-orange-500',
    worst_subject: 'bg-red-500'
  };
  return colors[key] || 'bg-gray-500';
};

// Helper function to format card titles
const formatCardTitle = (key) => {
  const titles = {
    total_students: 'Students',
    total_subjects: 'Subjects',
    class_average: 'Class Average',
    top_subject: 'Best Subject',
    worst_subject: 'Needs Help'
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

// Get rank icon based on position
const getRankIcon = (index) => {
  const icons = [Crown, Medal, Award];
  return icons[index] || Star;
};

// Get rank colors
const getRankColors = (index) => {
  const colors = [
    'bg-yellow-500', // Gold
    'bg-gray-400',   // Silver  
    'bg-orange-500'  // Bronze
  ];
  return colors[index] || 'bg-blue-500';
};

// Convert summary object to array for easier iteration
const summaryItems = [
  { key: 'total_students', value: props.summary.total_students },
  { key: 'total_subjects', value: props.summary.total_subjects },
  { key: 'class_average', value: props.summary.class_average },
  { key: 'top_subject', value: props.summary.top_subject || 'N/A' },
  { key: 'worst_subject', value: props.summary.worst_subject || 'N/A' }
];
</script>

<template>
  <Head title="Teacher Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-2 sm:space-x-3">
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-indigo-500 flex items-center justify-center">
              <GraduationCap class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
            </div>
            <div>
              <h2 class="text-lg sm:text-xl font-bold text-white bg-gradient-to-r from-white to-white/70 bg-clip-text text-transparent">
                Teaching Hub
              </h2>
              <p class="text-white/70 text-xs sm:text-sm">
                Track your teaching progress
              </p>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-4 px-4 sm:py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
      <!-- Summary Cards - Mobile First -->
      <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
        <div
          v-for="item in summaryItems"
          :key="item.key"
          class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl p-3 sm:p-4 shadow-xl hover:shadow-2xl hover:bg-white/15 transition-all duration-300 transform active:scale-95 sm:hover:scale-105"
        >
          <!-- Mobile optimized layout -->
          <div class="text-center space-y-2">
            <div :class="['w-8 h-8 sm:w-10 sm:h-10 mx-auto rounded-lg flex items-center justify-center', getCardColor(item.key)]">
              <component :is="getSummaryIcon(item.key)" class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
            </div>
            
            <div>
              <p class="text-lg sm:text-xl font-bold text-white">
                {{ item.value }}
              </p>
              <h3 class="text-xs sm:text-sm font-medium text-white/70 leading-tight">
                {{ formatCardTitle(item.key) }}
              </h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Students Section -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl sm:rounded-2xl shadow-2xl overflow-hidden">
        <!-- Mobile-optimized header -->
        <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-white/10">
          <div class="flex items-center space-x-2 sm:space-x-3">
            <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-yellow-500 flex items-center justify-center">
              <Trophy class="w-3 h-3 sm:w-4 sm:h-4 text-white" />
            </div>
            <div>
              <h2 class="text-base sm:text-lg font-semibold text-white">Top Students</h2>
              <p class="text-xs sm:text-sm text-white/70 hidden sm:block">Best performers</p>
            </div>
          </div>
        </div>
        
        <!-- Student List -->
        <div class="p-4 sm:p-6">
          <div v-if="topStudents.length" class="space-y-3 sm:space-y-4">
            <div
              v-for="(student, index) in topStudents.slice(0, 3)"
              :key="student.id"
              class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-xl p-4 sm:p-5 hover:bg-white/10 transition-all duration-200 active:bg-white/15"
            >
              <div class="space-y-3 sm:space-y-0 sm:flex sm:items-center sm:justify-between">
                <div class="flex items-center space-x-3">
                  <!-- Rank Badge -->
                  <div :class="['flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center', getRankColors(index)]">
                    <component :is="getRankIcon(index)" class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                  </div>
                  
                  <!-- Student Avatar -->
                  <div class="w-8 h-8 sm:w-10 sm:h-10 flex-shrink-0 rounded-lg bg-indigo-500 flex items-center justify-center text-white font-bold text-xs sm:text-sm">
                    {{ student.name.split(' ').map(n => n.charAt(0)).join('') }}
                  </div>
                  
                  <!-- Student Info -->
                  <div class="flex-1 min-w-0">
                    <h3 class="font-medium text-white text-sm sm:text-base truncate">
                      {{ student.name }}
                    </h3>
                    <p class="text-white/70 text-xs sm:text-sm">
                      {{ student.average }}% Average
                    </p>
                  </div>
                </div>
                
                <!-- Position -->
                <div class="flex items-center justify-between sm:justify-end">
                  <div class="text-white/50 text-sm sm:text-base font-bold">
                    #{{ index + 1 }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-8 sm:py-12">
            <div class="w-10 h-10 sm:w-12 sm:h-12 mx-auto rounded-lg bg-yellow-500/20 flex items-center justify-center mb-3">
              <Trophy class="w-5 h-5 sm:w-6 sm:h-6 text-white/40" />
            </div>
            <h3 class="text-sm font-medium text-white/80 mb-1">No Students Yet</h3>
            <p class="text-white/60 text-xs sm:text-sm">
              Top performers will appear here
            </p>
          </div>
        </div>
      </div>

      <!-- Announcements -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl sm:rounded-2xl shadow-2xl overflow-hidden">
        <!-- Mobile-optimized header -->
        <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-white/10">
          <div class="flex items-center space-x-2 sm:space-x-3">
            <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-green-500 flex items-center justify-center">
              <Megaphone class="w-3 h-3 sm:w-4 sm:h-4 text-white" />
            </div>
            <div>
              <h2 class="text-base sm:text-lg font-semibold text-white">Announcements</h2>
              <p class="text-xs sm:text-sm text-white/70 hidden sm:block">Latest updates</p>
            </div>
          </div>
        </div>

        <!-- Announcements List -->
        <div class="p-4 sm:p-6">
          <div v-if="announcements.length" class="space-y-3 sm:space-y-4">
            <div
              v-for="announcement in announcements"
              :key="announcement.id"
              class="backdrop-blur-sm bg-white/5 border border-white/10 rounded-xl p-4 sm:p-5 hover:bg-white/10 transition-all duration-200 active:bg-white/15 group"
            >
              <div class="space-y-3 sm:space-y-0 sm:flex sm:items-start sm:space-x-4">
                <!-- Icon -->
                <div class="flex items-start space-x-3 sm:space-x-0">
                  <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-indigo-500 flex items-center justify-center">
                    <User class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                  </div>
                  <div class="flex-1 min-w-0 sm:hidden">
                    <h3 class="font-medium text-white text-sm group-hover:text-white/90 transition-colors duration-200">
                      {{ announcement.title }}
                    </h3>
                  </div>
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0 sm:ml-0">
                  <h3 class="hidden sm:block font-medium text-white text-base group-hover:text-white/90 transition-colors duration-200 mb-2">
                    {{ announcement.title }}
                  </h3>
                  <p class="text-white/80 text-sm sm:text-base leading-relaxed mb-3">
                    {{ announcement.body }}
                  </p>
                  
                  <!-- Mobile-optimized metadata -->
                  <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-1 sm:space-y-0 text-xs">
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
            <p class="mt-1 text-xs sm:text-sm text-white/60">Check back for updates</p>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>