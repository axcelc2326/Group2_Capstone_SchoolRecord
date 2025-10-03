<script setup>
import { ref, computed, onMounted } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Footer from '@/Components/Footer.vue';
import Sidebar from '@/Components/Sidebar.vue';
import { 
  ChevronRight, 
  LayoutDashboard, 
  Speaker, 
  Users, 
  User, 
  ClipboardList, 
  UserCheck, 
  MessageCirclePlus, 
  BarChart3, 
  Download, 
  School, 
  BookOpen, 
  UserPlus, 
  Megaphone, 
  TrendingUp, 
  Settings,
  Cog
} from 'lucide-vue-next';
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
  
  const targetPath = href.startsWith('http') ? new URL(href).pathname : href;
  const currentPath = url;
  
  if (currentPath === targetPath) return true;
  
  if (targetPath === '/announcements' && currentPath.startsWith('/announcements/create')) return false;
  if (targetPath === '/announcements/create' && currentPath === '/announcements') return false;
  
  if (targetPath !== '/' && currentPath.startsWith(targetPath + '/')) return true;
  
  return false;
};

// Auto-collapse sidebar on mobile
onMounted(() => {
  const handleResize = () => {
    if (window.innerWidth < 1024) {
      sidebarCollapsed.value = true;
    }
    if (window.innerWidth >= 1024) {
      showingNavigationDropdown.value = false;
    }
  };
  
  window.addEventListener('resize', handleResize);
  handleResize();
  
  return () => window.removeEventListener('resize', handleResize);
});

// Helper function to generate route URLs
const routeUrl = (routeName, params = {}) => {
  try {
    if (window.route) {
      return window.route(routeName, params);
    }
    
    const routeMap = {
      'dashboard': '/dashboard',
      'announcements.index': '/announcements',
      'students.index': '/students/index',
      'parent.grades': '/parent/grades',
      'teacher.students': '/teacher/students',
      'teacher.announcements.create': '/teacher/announcements/create',
      'teacher.analytics': '/teacher/analytics',
      'sf5.form': '/sf5/form',
      'classes.index': '/classes',
      'subjects.index': '/subjects',
      'admin.assign-teacher': '/admin/classes/assign',
      'announcements.create': '/announcements/create',
      'teachers.index': '/teachers',
      'analytics.index': '/analytics',
      'parents.index': '/parents'
    };
    
    return routeMap[routeName] || '#';
  } catch (error) {
    console.warn(`Route ${routeName} not found, using fallback`);
    return '#';
  }
};

// Quick Actions - Available to all users
const quickActions = computed(() => [
  {
    name: 'Dashboard',
    href: routeUrl('dashboard'),
    icon: LayoutDashboard,
    description: 'Overview & Quick Stats'
  },
]);

// Role-specific navigation items
const roleBasedNavigation = computed(() => {
  const sections = {};

  if (isParent.value) {
    sections['Parent Portal'] = [
      {
        name: 'My Student',
        href: routeUrl('students.index'),
        icon: User,
        description: 'View My student'
      },
      {
        name: 'View Child\'s Grades',
        href: routeUrl('parent.grades'),
        icon: ClipboardList,
        description: 'Academic performance'
      },
      {
        name: 'View Announcements',
        href: routeUrl('announcements.index'),
        icon: Speaker,
        description: 'School News & Updates'
      }
    ];
  }

  if (isTeacher.value) {
    sections['Teacher Portal'] = [
      {
        name: 'View My Students',
        href: routeUrl('teacher.students'),
        icon: Users,
        description: 'Manage class roster'
      },
      {
        name: 'Create Class Announcement',
        href: routeUrl('teacher.announcements.create'),
        icon: MessageCirclePlus,
        description: 'Share class updates'
      },
      {
        name: 'My Class Analytics',
        href: routeUrl('teacher.analytics'),
        icon: BarChart3,
        description: 'View analytics for my classes'
      },
      {
        name: 'SF5 Generator',
        href: routeUrl('sf5.form'),
        icon: ClipboardList,
        description: 'Input school info & generate SF5 PDF'
      },
      {
        name: 'Parent Management',
        href: routeUrl('parents.index'),
        icon: Settings,
        description: 'Manage Parent Accounts & Assign Students'
      }
    ];
  }

  if (isAdmin.value) {
    sections['Administration'] = [
      {
        name: 'Create Class',
        href: routeUrl('classes.index'),
        icon: School,
        description: 'Setup new classes'
      },
      {
        name: 'Manage Subjects',
        href: routeUrl('subjects.index'),
        icon: BookOpen,
        description: 'Configure curriculum'
      },
      {
        name: 'Assign Teachers',
        href: routeUrl('admin.assign-teacher'),
        icon: UserPlus,
        description: 'Staff assignments'
      },
      {
        name: 'Create School Announcement',
        href: routeUrl('announcements.create'),
        icon: Megaphone,
        description: 'School-wide updates'
      },
      {
        name: 'View Analytics',
        href: routeUrl('analytics.index'),
        icon: TrendingUp,
        description: 'Performance insights'
      },
      {
        name: 'Teacher Management',
        href: routeUrl('teachers.index'),
        icon: Cog,
        description: 'Manage Teacher Accounts'
      },
      {
        name: 'Parent Management',
        href: routeUrl('parents.index'),
        icon: Settings,
        description: 'Manage Parent Accounts & Assign Students'
      }
    ];
  }

  return sections;
});
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 text-white transition-all duration-700">
    <!-- Enhanced Top Navigation - Fixed -->
    <nav class="bg-white/10 backdrop-blur-xl border-b border-white/20 shadow-2xl shadow-black/30 fixed top-0 left-0 right-0 z-50 transition-all duration-500">
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
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/25 group-hover:shadow-xl group-hover:shadow-blue-500/40 transition-all duration-300 group-hover:scale-105 group-hover:rotate-6 relative overflow-hidden">
                  <img src="/images/Logo.jpg" alt="Logo" class="w-full h-full object-cover" />
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
                    <User class="w-4 h-4 mr-3 transition-all duration-300 group-hover:text-blue-600 group-hover:scale-110" />
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
      <div v-show="showingNavigationDropdown" class="lg:hidden border-t border-white/20 bg-transparent shadow-xl transition-all duration-500 max-h-[80vh] overflow-y-auto mobile-menu-scroll">
        <div class="px-4 space-y-6">
          <!-- Mobile User Info -->
          <div class="flex items-center space-x-3 pb-4 border-b transition-all duration-300 sticky top-0 bg-transparent backdrop-blur-xl z-10 -mx-4 px-4 pt-2">
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
                <div 
                  v-if="isActiveRoute(action.href)" 
                  class="absolute bottom-0 left-0 w-full h-[6px]
                        bg-gradient-to-r from-teal-400 via-cyan-400 to-sky-400
                        rounded-full shadow-[0_0_12px_rgba(167,139,250,0.8)]
                        animate-pulse">
                </div>
                
                <component :is="action.icon" :class="isActiveRoute(action.href) ? 'text-white' : 'text-white/70 group-hover:text-white group-hover:scale-110 transition-all duration-300'" class="w-5 h-5" />
                <div class="flex flex-col flex-1 relative z-10">
                  <span class="font-semibold transition-all duration-300">{{ action.name }}</span>
                  <span class="text-xs opacity-70 transition-all duration-300">{{ action.description }}</span>
                </div>
                <div v-if="isActiveRoute(action.href)" class="ml-auto flex items-center">
                  <ChevronRight class="w-6 h-6 animate-pulse text-sky-400 drop-shadow-[0_0_6px_rgba(99,102,241,0.8)]" />
                </div>
                
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
                <div 
                  v-if="isActiveRoute(item.href)" 
                  class="absolute bottom-0 left-0 w-full h-[6px]
                        bg-gradient-to-r from-teal-400 via-cyan-400 to-sky-400
                        rounded-full shadow-[0_0_12px_rgba(167,139,250,0.8)]
                        animate-pulse">
                </div>

                <component 
                  :is="item.icon" 
                  :class="isActiveRoute(item.href) 
                    ? 'text-white' 
                    : 'text-white/70 group-hover:text-white transition-colors duration-300'"
                  class="w-5 h-5 transform group-hover:scale-110 transition-transform duration-300"
                />

                <div class="flex flex-col flex-1 relative z-10">
                  <span class="font-semibold transition-all duration-300">{{ item.name }}</span>
                  <span class="text-xs opacity-70 transition-all duration-300">{{ item.description }}</span>
                </div>

                <div v-if="isActiveRoute(item.href)" class="ml-auto flex items-center">
                  <ChevronRight class="w-6 h-6 animate-pulse text-sky-400 drop-shadow-[0_0_6px_rgba(99,102,241,0.8)]" />
                </div>

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
    <div class="flex flex-1 pt-16"> <!-- Added pt-16 to account for fixed header -->
      <!-- Use the new Sidebar component -->
      <Sidebar :collapsed="sidebarCollapsed" @toggle="toggleSidebar" />

      <!-- Main Content Wrapper - Only this scrolls -->
      <div class="flex flex-col flex-1 overflow-y-auto main-content-scroll h-[calc(100vh-4rem)]">
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
          
          <!-- Enhanced Footer -->
          <Footer />
        </main>
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
.mobile-menu-scroll::-webkit-scrollbar {
  width: 4px;
}

.mobile-menu-scroll::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 2px;
}

.mobile-menu-scroll::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
}

.mobile-menu-scroll::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}

/* Custom scrollbar for main content */
.main-content-scroll::-webkit-scrollbar {
  width: 8px;
}

.main-content-scroll::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 4px;
}

.main-content-scroll::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 4px;
  transition: background 0.3s;
}

.main-content-scroll::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.4);
}

/* Smooth scrolling */
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