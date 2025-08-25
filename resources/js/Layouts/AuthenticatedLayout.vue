<script setup>
import { ref, computed, onMounted } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Footer from '@/Components/Footer.vue'
import { ChevronRight } from 'lucide-vue-next'
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const sidebarCollapsed = ref(false);
const { props, url } = usePage();
const user = props.auth.user;
const roles = user.roles?.map(role => role.name) || [];

const isParent = computed(() => roles.includes('parent'));
const isTeacher = computed(() => roles.includes('teacher'));
const isAdmin = computed(() => roles.includes('admin'));

const currentYear = ref(new Date().getFullYear());

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value;
};

const toggleMobileNavigation = () => {
  showingNavigationDropdown.value = !showingNavigationDropdown.value;
};

// Enhanced route checking with exact matching to prevent overlaps
const isActiveRoute = (href) => {
  if (!href) return false;
  
  // Extract the path from href if it's a full URL
  const targetPath = href.startsWith('http') ? new URL(href).pathname : href;
  const currentPath = url;
  
  // Exact match first
  if (currentPath === targetPath) return true;
  
  // Special handling for announcements routes to prevent overlap
  if (targetPath === '/announcements' && currentPath.startsWith('/announcements/create')) return false;
  if (targetPath === '/announcements/create' && currentPath === '/announcements') return false;
  
  // Sub-route match (but avoid matching root with everything)
  if (targetPath !== '/' && currentPath.startsWith(targetPath + '/')) return true;
  
  return false;
};

// Auto-collapse sidebar on mobile
onMounted(() => {
  const handleResize = () => {
    if (window.innerWidth < 1024) {
      sidebarCollapsed.value = true;
    }
    // Close mobile menu on resize
    if (window.innerWidth >= 1024) {
      showingNavigationDropdown.value = false;
    }
  };
  
  window.addEventListener('resize', handleResize);
  handleResize();
  
  return () => window.removeEventListener('resize', handleResize);
});

// Helper function to generate route URLs - Fixed route helper
const routeUrl = (routeName, params = {}) => {
  try {
    // This assumes you have a global route helper available
    if (window.route) {
      return window.route(routeName, params);
    }
    
    // Fallback mapping for common routes
    const routeMap = {
      'dashboard': '/dashboard',
      'announcements.index': '/announcements',
      'students.create': '/students/register',
      'parent.grades': '/parent/grades',
      'teacher.students': '/teacher/students',
      'students.approval.list': '/students/approve',
      'teacher.announcements.create': '/teacher/announcements/create',
      'sf5.download': `/sf5/${params.class || 1}`,
      'classes.create': '/classes',
      'subjects.index': '/subjects',
      'admin.assign-teacher': '/admin/classes/assign',
      'announcements.create': '/announcements/create',
      'analytics.index': '/analytics',
      'admin.users.index': '/admin/users'
    };
    
    return routeMap[routeName] || '#';
  } catch (error) {
    console.warn(`Route ${routeName} not found, using fallback`);
    return '#';
  }
};

// Quick Actions - Available to all users with proper icons
const quickActions = computed(() => [
  {
    name: 'Dashboard',
    href: routeUrl('dashboard'),
    icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 9 5 5V9l5 5"></path></svg>`,
    description: 'Overview & Quick Stats'
  },
  {
    name: 'View Announcements',
    href: routeUrl('announcements.index'),
    icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 14.142M6.343 6.343a9 9 0 000 12.728m2.829-9.9a5 5 0 000 7.072M12 12h.01"></path></svg>`,
    description: 'School News & Updates'
  }
]);

// Role-specific navigation items with proper unique icons (colors removed)
const roleBasedNavigation = computed(() => {
  const sections = {};

  if (isParent.value) {
    sections['Parent Portal'] = [
      {
        name: 'Register Student',
        href: routeUrl('students.create'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v4m0 0V4m0 4h4m-4 0H8"></path></svg>`,
        description: 'Enroll new student'
      },
      {
        name: 'View Child\'s Grades',
        href: routeUrl('parent.grades'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>`,
        description: 'Academic performance'
      }
    ];
  }

  if (isTeacher.value) {
    sections['Teacher Portal'] = [
      {
        name: 'View My Students',
        href: routeUrl('teacher.students'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path></svg>`,
        description: 'Manage class roster'
      },
      {
        name: 'Approve Students',
        href: routeUrl('students.approval.list'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>`,
        description: 'Review enrollments'
      },
      {
        name: 'Create Class Announcement',
        href: routeUrl('teacher.announcements.create'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m3-3h-6"></path></svg>`,
        description: 'Share class updates'
      },
      {
        name: 'Download SF5',
        href: routeUrl('sf5.download', { class: 1 }),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>`,
        description: 'Export class records'
      }
    ];
  }

  if (isAdmin.value) {
    sections['Administration'] = [
      {
        name: 'Create Class',
        href: routeUrl('classes.create'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>`,
        description: 'Setup new classes'
      },
      {
        name: 'Manage Subjects',
        href: routeUrl('subjects.index'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>`,
        description: 'Configure curriculum'
      },
      {
        name: 'Assign Teachers',
        href: routeUrl('admin.assign-teacher'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>`,
        description: 'Staff assignments'
      },
      {
        name: 'Create School Announcement',
        href: routeUrl('announcements.create'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path><circle cx="18" cy="8" r="2" fill="currentColor"></circle></svg>`,
        description: 'School-wide updates'
      },
      {
        name: 'View Analytics',
        href: routeUrl('analytics.index'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>`,
        description: 'Performance insights'
      },
      {
        name: 'Manage Users',
        href: routeUrl('admin.users.index'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>`,
        description: 'User management'
      }
    ];
  }

  return sections;
});
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 text-white transition-all duration-700">
    <!-- Enhanced Top Navigation -->
    <nav class="bg-white/10 backdrop-blur-xl border-b border-white/20 shadow-2xl shadow-black/30 sticky top-0 z-50 transition-all duration-500">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <!-- Left: Logo + School Name + User -->
          <div class="flex items-center space-x-4">
            <button
              @click="toggleMobileNavigation"
              class="lg:hidden p-2 text-white/80 hover:bg-white/10 rounded-xl transition-all duration-300 hover:scale-105 hover:text-white group"
            >
              <svg class="w-6 h-6 transition-all duration-300" :class="{ 'rotate-180 scale-110': showingNavigationDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!showingNavigationDropdown" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
            
            <Link :href="routeUrl('dashboard')" class="flex items-center space-x-3 group transition-all duration-300">
              <div class="relative">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 via-blue-500 to-indigo-600 text-white rounded-2xl flex items-center justify-center text-xl font-bold shadow-lg shadow-blue-500/25 group-hover:shadow-xl group-hover:shadow-blue-500/40 transition-all duration-300 group-hover:scale-105 group-hover:rotate-6 relative overflow-hidden">
                  E
                  <div class="absolute inset-0 bg-gradient-to-tr from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                <div class="absolute -inset-1 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-2xl opacity-0 group-hover:opacity-20 transition-all duration-300 blur-sm"></div>
              </div>
              <div class="hidden sm:block">
                <div class="text-xl font-bold bg-gradient-to-r from-blue-100 via-white to-indigo-100 bg-clip-text text-transparent transition-all duration-300 group-hover:from-blue-200 group-hover:to-indigo-200">
                  EduPortal
                </div>
                <div class="text-xs text-white/70 font-medium transition-colors duration-300 group-hover:text-white/90">Learning Management System</div>
              </div>
            </Link>
            
            <div class="hidden xl:block pl-4 border-l border-white/20 transition-all duration-300">
              <div class="text-sm font-semibold text-white transition-colors duration-300">Welcome back, {{ user.name }}</div>
              <div class="text-xs text-white/60 flex items-center space-x-2 transition-all duration-300">
                <div class="relative">
                  <div class="w-2 h-2 rounded-full animate-pulse transition-all duration-500"
                       :class="{
                         'bg-purple-400 shadow-lg shadow-purple-400/50': isAdmin,
                         'bg-emerald-400 shadow-lg shadow-emerald-400/50': isTeacher,
                         'bg-blue-400 shadow-lg shadow-blue-400/50': isParent
                       }">
                  </div>
                  <div class="absolute inset-0 rounded-full animate-ping transition-all duration-500"
                       :class="{
                         'bg-purple-400': isAdmin,
                         'bg-emerald-400': isTeacher,
                         'bg-blue-400': isParent
                       }">
                  </div>
                </div>
                <span v-if="isAdmin" class="text-purple-300 font-medium transition-colors duration-300">Administrator</span>
                <span v-else-if="isTeacher" class="text-emerald-300 font-medium transition-colors duration-300">Teacher</span>
                <span v-else-if="isParent" class="text-blue-300 font-medium transition-colors duration-300">Parent</span>
              </div>
            </div>
          </div>

          <!-- Right: Profile Dropdown -->
          <div class="flex items-center space-x-4">
            <Dropdown align="right" width="full">
              <template #trigger>
                <button class="flex items-center px-4 py-2 bg-white/10 border border-white/20 rounded-2xl text-sm text-white hover:bg-white/20 shadow-lg shadow-black/20 hover:shadow-xl hover:shadow-black/30 transition-all duration-300 hover:scale-105 group relative overflow-hidden">
                  <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 transform -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                  <div class="relative flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 text-white rounded-xl flex items-center justify-center text-sm font-bold mr-3 shadow-lg transition-all duration-300 group-hover:scale-110 group-hover:rotate-6 relative overflow-hidden">
                      {{ user.name.charAt(0).toUpperCase() }}
                      <div class="absolute inset-0 bg-gradient-to-tr from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="text-left hidden sm:block">
                      <div class="text-sm text-white font-semibold transition-colors duration-300">{{ user.name }}</div>
                      <div class="text-xs text-white/70 transition-colors duration-300">{{ user.email }}</div>
                    </div>
                    <svg class="ml-3 h-4 w-4 transition-all duration-300 text-white/70 group-hover:text-white group-hover:rotate-180" 
                         viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </button>
              </template>
              <template #content>
                <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl shadow-black/20 border border-white/20 overflow-hidden">
                  <DropdownLink :href="route('profile.edit')" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-all duration-300 group">
                    <svg class="w-4 h-4 mr-3 transition-all duration-300 group-hover:text-blue-600 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile Settings
                  </DropdownLink>
                  <hr class="my-2 border-gray-200">
                  <DropdownLink :href="route('logout')" method="post" as="button" 
                                class="w-full flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-all duration-300 group hover:text-red-700">
                    <svg class="w-4 h-4 mr-3 transition-all duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                  </DropdownLink>
                </div>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>

      <!-- Enhanced Mobile Navigation Menu with Scrolling -->
      <div v-show="showingNavigationDropdown" class="lg:hidden border-t border-white/20 bg-transparent shadow-xl transition-all duration-500 max-h-[80vh] overflow-y-auto">
        <div class="px-4 py-6 space-y-6">
          <!-- Mobile User Info -->
          <div class="flex items-center space-x-3 pb-4 border-b transition-all duration-300 top-0 bg-transparent z-10">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 text-white rounded-xl flex items-center justify-center text-sm font-bold shadow-lg transition-all duration-300 relative overflow-hidden">
              {{ user.name.charAt(0).toUpperCase() }}
              <div class="absolute inset-0 bg-gradient-to-tr from-white/20 to-transparent"></div>
            </div>
            <div>
              <div class="text-base font-semibold text-white transition-colors duration-300">{{ user.name }}</div>
              <div class="text-sm text-white/70 transition-colors duration-300">{{ user.email }}</div>
            </div>
          </div>
          
          <!-- Mobile Quick Actions -->
          <div class="space-y-3">
            <h3 class="text-sm font-bold text-white/80 uppercase tracking-wider px-2 flex items-center space-x-2">
              <div class="w-1 h-4 rounded-full bg-blue-400 shadow-lg shadow-blue-400/50 animate-pulse"></div>
              <span>Quick Actions</span>
            </h3>
            <div class="space-y-2">
              <Link 
                v-for="action in quickActions"
                :key="action.name"
                :href="action.href" 
                :class="[
                  'flex items-center space-x-4 py-4 px-4 rounded-2xl transition-all duration-500 border-l-4 group relative overflow-hidden',
                  isActiveRoute(action.href) 
                    ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-blue-400 shadow-xl shadow-blue-500/30 scale-[1.02]' 
                    : 'text-white/80 hover:bg-white/10 hover:text-white border-transparent hover:border-blue-400 hover:scale-[1.02]'
                ]"
                @click="showingNavigationDropdown = false"
              >
                <!-- Active underline effect -->
                <div 
                  v-if="isActiveRoute(action.href)" 
                  class="absolute bottom-0 left-0 w-full h-[6px]
                        bg-gradient-to-r from-teal-400 via-cyan-400 to-sky-400
                        rounded-full shadow-[0_0_12px_rgba(167,139,250,0.8)]
                        animate-pulse">
                </div>
                
                <div v-html="action.icon" :class="isActiveRoute(action.href) ? 'text-white' : 'text-white/70 group-hover:text-white group-hover:scale-110 transition-all duration-300'"></div>
                <div class="flex flex-col flex-1 relative z-10">
                  <span class="font-semibold transition-all duration-300">{{ action.name }}</span>
                  <span class="text-xs opacity-70 transition-all duration-300">{{ action.description }}</span>
                </div>
                <div v-if="isActiveRoute(action.href)" class="ml-auto flex items-center">
                  <ChevronRight class="w-6 h-6 animate-pulse text-sky-400 drop-shadow-[0_0_6px_rgba(99,102,241,0.8)]" />
                </div>
                
                <!-- Hover effect background -->
                <div 
                  v-if="!isActiveRoute(action.href)" 
                  class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 transform -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-in-out"
                ></div>
              </Link>
            </div>
          </div>
          
          <!-- Mobile Role-based Navigation Sections -->
          <div v-for="(items, category) in roleBasedNavigation" :key="category" class="space-y-4">
            <h3 class="text-sm font-bold text-white/80 uppercase tracking-wider mb-4 px-2 transition-colors duration-300 flex items-center space-x-2">
              <div 
                class="w-1 h-4 rounded-full animate-pulse"
                :class="{
                  'bg-purple-400 shadow-lg shadow-purple-400/50': category.includes('Administration'),
                  'bg-emerald-400 shadow-lg shadow-emerald-400/50': category.includes('Teacher'),
                  'bg-blue-400 shadow-lg shadow-blue-400/50': category.includes('Parent')
                }"
              ></div>
              <span>{{ category }}</span>
            </h3>
            <div class="grid grid-cols-1 gap-2">
              <Link 
                v-for="item in items" 
                :key="item.name"
                :href="item.href" 
                :class="[
                  'flex items-center space-x-4 py-4 px-4 rounded-2xl transition-all duration-500 border-l-4 group relative overflow-hidden',
                  isActiveRoute(item.href) 
                    ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-blue-400 shadow-xl shadow-blue-500/30 scale-[1.02]' 
                    : 'text-white/80 hover:bg-white/10 hover:text-white border-transparent hover:border-blue-400 hover:scale-[1.02]'
                ]"
                @click="showingNavigationDropdown = false"
              >
                <!-- Active underline effect -->
                <div 
                  v-if="isActiveRoute(item.href)" 
                  class="absolute bottom-0 left-0 w-full h-[6px]
                        bg-gradient-to-r from-teal-400 via-cyan-400 to-sky-400
                        rounded-full shadow-[0_0_12px_rgba(167,139,250,0.8)]
                        animate-pulse">
                </div>

                <!-- Icon -->
                <div 
                  v-html="item.icon" 
                  :class="isActiveRoute(item.href) 
                    ? 'text-white' 
                    : 'text-white/70 group-hover:text-white transition-colors duration-300'"
                  class="transform group-hover:scale-110 transition-transform duration-300"
                ></div>

                <!-- Text -->
                <div class="flex flex-col flex-1 relative z-10">
                  <span class="font-semibold transition-all duration-300">{{ item.name }}</span>
                  <span class="text-xs opacity-70 transition-all duration-300">{{ item.description }}</span>
                </div>

                <!-- Chevron indicator when active -->
                <div v-if="isActiveRoute(item.href)" class="ml-auto flex items-center">
                  <ChevronRight class="w-6 h-6 animate-pulse text-sky-400 drop-shadow-[0_0_6px_rgba(99,102,241,0.8)]" />
                </div>

                <!-- Hover effect background -->
                <div 
                  v-if="!isActiveRoute(item.href)" 
                  class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 transform -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-in-out"
                ></div>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Layout: Sidebar + Main -->
    <div class="flex flex-1">
      <!-- Enhanced Modern Sidebar -->
      <aside :class="[
        'bg-white/10 backdrop-blur-xl border-r border-white/20 transition-all duration-500 ease-in-out shadow-2xl shadow-black/20',
        sidebarCollapsed ? 'w-0 lg:w-20' : 'w-80',
        'hidden lg:block'
      ]">
        <div class="p-6 h-full overflow-hidden">
          <!-- Sidebar Toggle -->
          <div class="flex justify-end mb-6" v-if="!sidebarCollapsed">
            <button 
              @click="toggleSidebar"
              class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-300 hover:scale-105 group"
            >
              <svg class="w-5 h-5 transition-all duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </button>
          </div>
          
          <div v-if="sidebarCollapsed" class="flex justify-center mb-8">
            <button 
              @click="toggleSidebar"
              class="p-3 text-white/70 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-300 hover:scale-105 group"
            >
              <svg class="w-6 h-6 transition-all duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
          
          <!-- Enhanced Navigation -->
          <nav class="space-y-8" v-if="!sidebarCollapsed">
            <!-- Quick Actions Section -->
            <div>
              <h2 class="text-lg font-bold bg-gradient-to-r from-blue-100 to-indigo-100 bg-clip-text text-transparent mb-6 transition-all duration-300">
                Quick Actions
              </h2>
              
              <div class="space-y-3">
                <Link 
                  v-for="action in quickActions"
                  :key="action.name"
                  :href="action.href" 
                  :class="[
                    'group flex items-center space-x-4 p-4 rounded-2xl transition-all duration-500 border-l-4 relative overflow-hidden',
                    isActiveRoute(action.href)
                      ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-blue-400 shadow-xl shadow-blue-500/30 scale-[1.02]'
                      : 'text-white/80 hover:text-white hover:bg-white/10 border-transparent hover:border-blue-400 hover:scale-[1.02]'
                  ]"
                >
                  <!-- Active underline effect (modernized) -->
                  <div 
                    v-if="isActiveRoute(action.href)" 
                    class="absolute bottom-0 left-0 w-full h-[6px]
                          bg-gradient-to-r from-teal-400 via-cyan-400 to-sky-400
                          rounded-full shadow-[0_0_12px_rgba(167,139,250,0.8)]
                          animate-pulse">
                  </div>
                  <div v-html="action.icon" :class="isActiveRoute(action.href) ? 'text-white' : 'text-white/70 group-hover:text-white group-hover:scale-110 transition-all duration-300'"></div>
                  <div class="flex flex-col flex-1 relative z-10">
                    <span class="font-semibold transition-all duration-300">{{ action.name }}</span>
                    <span class="text-xs opacity-70 transition-all duration-300">{{ action.description }}</span>
                  </div>
                  <div v-if="isActiveRoute(action.href)" class="ml-auto flex items-center">
                    <ChevronRight class="w-6 h-6 animate-pulse text-sky-400 drop-shadow-[0_0_6px_rgba(99,102,241,0.8)]" />
                  </div>
                  <!-- Hover effect background -->
                  <div 
                    v-if="!isActiveRoute(action.href)" 
                    class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 transform -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-in-out"
                  ></div>
                </Link>
              </div>
            </div>

            <!-- Role-based Navigation Sections -->
            <div 
              v-for="(items, category) in roleBasedNavigation" 
              :key="category" 
              class="space-y-4"
            >
              <!-- Category Heading -->
              <h3 class="text-sm font-bold text-white/80 uppercase tracking-wider mb-4 px-2 transition-colors duration-300 flex items-center space-x-2">
                <div 
                  class="w-1 h-4 rounded-full animate-pulse"
                  :class="{
                    'bg-purple-400 shadow-lg shadow-purple-400/50': category.includes('Administration'),
                    'bg-emerald-400 shadow-lg shadow-emerald-400/50': category.includes('Teacher'),
                    'bg-blue-400 shadow-lg shadow-blue-400/50': category.includes('Parent')
                  }"
                ></div>
                <span>{{ category }}</span>
              </h3>

              <!-- Navigation Links -->
              <div class="grid grid-cols-1 gap-2">
                <Link 
                  v-for="item in items" 
                  :key="item.name"
                  :href="item.href" 
                  :aria-current="isActiveRoute(item.href) ? 'page' : null"
                  :class="[
                    'group flex items-center space-x-4 p-4 rounded-2xl transition-all duration-500 border-l-4 relative overflow-hidden',
                    isActiveRoute(item.href)
                      ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-blue-400 shadow-xl shadow-blue-500/30 scale-[1.02]'
                      : 'text-white/80 hover:text-white hover:bg-white/10 border-transparent hover:border-blue-400 hover:scale-[1.02]'
                  ]"
                >
                  <!-- Active underline effect (modernized) -->
                  <div 
                    v-if="isActiveRoute(item.href)" 
                    class="absolute bottom-0 left-0 w-full h-[6px]
                          bg-gradient-to-r from-teal-400 via-cyan-400 to-sky-400
                          rounded-full shadow-[0_0_12px_rgba(167,139,250,0.8)]
                          animate-pulse">
                  </div>

                  <!-- Icon -->
                  <div 
                    v-html="item.icon" 
                    :class="isActiveRoute(item.href) 
                      ? 'text-white' 
                      : 'text-white/70 group-hover:text-white transition-colors duration-300'"
                    class="transform group-hover:scale-110 transition-transform duration-300"
                  ></div>

                  <!-- Text -->
                  <div class="flex flex-col flex-1 relative z-10">
                    <span class="font-semibold transition-all duration-300">{{ item.name }}</span>
                    <span class="text-xs opacity-70 transition-all duration-300">{{ item.description }}</span>
                  </div>

                  <!-- Chevron indicator when active -->
                  <div v-if="isActiveRoute(item.href)" class="ml-auto flex items-center">
                    <ChevronRight class="w-6 h-6 animate-pulse text-sky-400 drop-shadow-[0_0_6px_rgba(99,102,241,0.8)]" />
                  </div>

                  <!-- Hover effect background -->
                  <div 
                    v-if="!isActiveRoute(item.href)" 
                    class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 transform -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-in-out"
                  ></div>
                </Link>
              </div>
            </div>
          </nav>
          
          <!-- Collapsed Navigation -->
          <nav v-else class="space-y-4">
            <!-- Collapsed Quick Actions -->
            <div class="space-y-3">
              <Link 
                v-for="action in quickActions"
                :key="action.name"
                :href="action.href" 
                :class="[
                  'group flex items-center justify-center p-4 rounded-2xl transition-all duration-500 relative',
                  isActiveRoute(action.href)
                    ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-xl shadow-blue-500/30 scale-110'
                    : 'text-white/70 hover:text-white hover:bg-white/10 hover:scale-110'
                ]"
                :title="action.name"
              >
                <div v-html="action.icon" :class="isActiveRoute(action.href) ? 'text-white' : 'text-white/70 group-hover:text-white transition-colors duration-300'"></div>
                
                <!-- Active underline effect for collapsed -->
                <div 
                  v-if="isActiveRoute(action.href)" 
                  class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-6 h-[3px]
                        bg-gradient-to-r from-teal-400 via-cyan-400 to-sky-400
                        rounded-full shadow-[0_0_8px_rgba(167,139,250,0.8)]
                        animate-pulse">
                </div>
              </Link>
            </div>
            
            <!-- Collapsed Role-based Navigation -->
            <div v-for="(items, category) in roleBasedNavigation" :key="category" class="space-y-3">
              <div class="w-full h-px bg-white/20 my-4 transition-colors duration-300"></div>
              <Link 
                v-for="item in items" 
                :key="item.name"
                :href="item.href" 
                :class="[
                  'group flex items-center justify-center p-4 rounded-2xl transition-all duration-500 relative',
                  isActiveRoute(item.href)
                    ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-xl shadow-blue-500/30 scale-110'
                    : 'text-white/70 hover:text-white hover:bg-white/10 hover:scale-110'
                ]"
                :title="item.name"
              >
                <div v-html="item.icon" :class="isActiveRoute(item.href) ? 'text-white' : 'text-white/70 group-hover:text-white transition-colors duration-300'"></div>
                
                <!-- Active underline effect for collapsed -->
                <div 
                  v-if="isActiveRoute(item.href)" 
                  class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-6 h-[3px]
                        bg-gradient-to-r from-teal-400 via-cyan-400 to-sky-400
                        rounded-full shadow-[0_0_8px_rgba(167,139,250,0.8)]
                        animate-pulse">
                </div>
              </Link>
            </div>
          </nav>
        </div>
      </aside>

      <!-- Main Content Wrapper -->
      <div class="flex flex-col flex-1">
        <!-- Main Content -->
        <main class="flex-1 p-6">
          <header v-if="$slots.header" class="mb-8">
            <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 p-8 shadow-2xl shadow-black/20 transition-all duration-500 hover:shadow-3xl hover:shadow-black/25">
              <slot name="header" />
            </div>
          </header>
          
          <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 p-4 shadow-2xl shadow-black/20 min-h-96 relative overflow-hidden transition-all duration-500 hover:shadow-3xl hover:shadow-black/25">
            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full -translate-y-32 translate-x-32 transition-all duration-700"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-indigo-400/30 to-transparent rounded-full translate-y-24 -translate-x-24 transition-all duration-700"></div>
            
            <div class="relative z-10">
              <slot />
            </div>
          </div>
        </main>

        <!-- Enhanced Footer -->
        <Footer />
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Enhanced animations and transitions */
.mobile-menu-enter {
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Enhanced glassmorphism effects */
.backdrop-blur-xl {
  backdrop-filter: blur(20px);
}

/* Custom scrollbar for mobile navigation */
.lg\:hidden::-webkit-scrollbar {
  width: 4px;
}

.lg\:hidden::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 2px;
}

.lg\:hidden::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
}

.lg\:hidden::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}

/* Smooth scrolling for mobile navigation */
.overflow-y-auto {
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
}

/* Enhanced hover effects */
.group:hover .animate-pulse {
  animation-duration: 1s;
}

/* Improved focus states */
button:focus,
.focus\:ring-2:focus {
  outline: none;
}

/* Glow effects for active states */
.ring-1 {
  box-shadow: 0 0 0 1px var(--tw-ring-color);
}
</style>