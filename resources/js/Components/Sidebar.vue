<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
  ChevronRight, 
  LayoutDashboard, 
  Speaker, 
  Users, 
  User, 
  ClipboardList, 
  MessageCirclePlus, 
  BarChart3, 
  School, 
  BookOpen, 
  UserPlus, 
  Megaphone, 
  TrendingUp, 
  Settings,
  Cog,
  Award,
  FileText
} from 'lucide-vue-next';

const props = defineProps({
  collapsed: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['toggle']);

const { url } = usePage();
const { props: pageProps } = usePage();
const user = pageProps.auth.user;
const roles = user.roles?.map(role => role.name) || [];

const isParent = computed(() => roles.includes('parent'));
const isTeacher = computed(() => roles.includes('teacher'));
const isAdmin = computed(() => roles.includes('admin'));

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
      'honorlist.index': '/honorlist',
      'classes.index': '/classes',
      'subjects.index': '/subjects',
      'admin.assign-teacher': '/admin/classes/assign',
      'announcements.create': '/announcements/create',
      'admin.records.index': '/admin/records',
      'analytics.index': '/analytics',
      'teachers.index': '/teachers',
      'parents.index': '/parents'
    };
    
    return routeMap[routeName] || '#';
  } catch (error) {
    console.warn(`Route ${routeName} not found, using fallback`);
    return '#';
  }
};

// Quick Actions
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
        name: 'Honor List',
        href: routeUrl('honorlist.index'),
        icon: Award, 
        description: 'Generate and view class honor roll reports'
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
    sections['Admin Portal'] = [
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
        name: 'Reports Management',
        href: routeUrl('admin.records.index'),
        icon: FileText, // you can import FileText from lucide-react or similar
        description: 'All SF5 and Honor Roll submissions'
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
  <aside :class="[
    'bg-white/10 backdrop-blur-xl border-r border-white/20 transition-all duration-500 ease-in-out shadow-2xl shadow-black-20 flex flex-col',
    collapsed ? 'w-20' : 'w-80',
    'hidden lg:flex sticky top-16 h-[calc(100vh-4rem)]' // Changed to sticky and fixed height
  ]">
    <!-- Sidebar Header (Fixed) -->
    <div class="flex-shrink-0 p-6">
      <div v-if="!collapsed" class="flex justify-end">
        <button 
          @click="emit('toggle')"
          class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-300 hover:scale-105 group"
        >
          <svg class="w-5 h-5 transition-all duration-300 group-hover:-rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
      </div>
      
      <div v-else class="flex justify-center">
        <button 
          @click="emit('toggle')"
          class="p-3 text-white/70 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-300 hover:scale-105 group"
        >
          <svg class="w-6 h-6 transition-all duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Scrollable Navigation Content -->
    <div class="flex-1 overflow-y-auto sidebar-scroll px-6 pb-6">
      <!-- Expanded Navigation -->
      <nav v-if="!collapsed" class="space-y-8">
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
              <div 
                v-if="isActiveRoute(action.href)" 
                class="absolute bottom-0 left-0 w-full h-[6px]
                      bg-gradient-to-r from-teal-400 via-cyan-400 to-sky-400
                      rounded-full shadow-[0_0_12px_rgba(167,139,250,0.8)]
                      animate-pulse">
              </div>
              <component :is="action.icon" :class="isActiveRoute(action.href) ? 'text-white' : 'text-white/70 group-hover:text-white group-hover:scale-110 transition-all duration-300'" class="w-5 h-5 flex-shrink-0" />
              <div class="flex flex-col flex-1 relative z-10">
                <span class="font-semibold transition-all duration-300">{{ action.name }}</span>
                <span class="text-xs opacity-70 transition-all duration-300">{{ action.description }}</span>
              </div>
              <div v-if="isActiveRoute(action.href)" class="ml-auto flex items-center flex-shrink-0">
                <ChevronRight class="w-6 h-6 animate-pulse text-sky-400 drop-shadow-[0_0_6px_rgba(99,102,241,0.8)]" />
              </div>
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
          <h3 class="text-sm font-bold text-white/80 uppercase tracking-wider mb-4 px-2 transition-colors duration-300 flex items-center space-x-2">
            <div 
              class="w-1 h-4 rounded-full animate-pulse flex-shrink-0"
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
              :aria-current="isActiveRoute(item.href) ? 'page' : null"
              :class="[
                'group flex items-center space-x-4 p-4 rounded-2xl transition-all duration-500 border-l-4 relative overflow-hidden',
                isActiveRoute(item.href)
                  ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-blue-400 shadow-xl shadow-blue-500/30 scale-[1.02]'
                  : 'text-white/80 hover:text-white hover:bg-white/10 border-transparent hover:border-blue-400 hover:scale-[1.02]'
              ]"
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
                class="w-5 h-5 transform group-hover:scale-110 transition-transform duration-300 flex-shrink-0"
              />

              <div class="flex flex-col flex-1 relative z-10">
                <span class="font-semibold transition-all duration-300">{{ item.name }}</span>
                <span class="text-xs opacity-70 transition-all duration-300">{{ item.description }}</span>
              </div>

              <div v-if="isActiveRoute(item.href)" class="ml-auto flex items-center flex-shrink-0">
                <ChevronRight class="w-6 h-6 animate-pulse text-sky-400 drop-shadow-[0_0_6px_rgba(99,102,241,0.8)]" />
              </div>

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
            <component 
              :is="action.icon" 
              :class="isActiveRoute(action.href) ? 'text-white' : 'text-white/70 group-hover:text-white transition-colors duration-300'" 
              class="w-6 h-6"
            />
            
            <div 
              v-if="isActiveRoute(action.href)" 
              class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-6 h-[3px]
                    bg-gradient-to-r from-teal-400 via-cyan-400 to-sky-400
                    rounded-full shadow-[0_0_8px_rgba(167,139,250,0.8)]
                    animate-pulse">
            </div>
          </Link>
        </div>
        
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
            <component 
              :is="item.icon" 
              :class="isActiveRoute(item.href) ? 'text-white' : 'text-white/70 group-hover:text-white transition-colors duration-300'" 
              class="w-6 h-6"
            />
            
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
</template>

<style scoped>
/* Custom scrollbar for sidebar */
.sidebar-scroll::-webkit-scrollbar {
  width: 6px;
}

.sidebar-scroll::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 3px;
  margin: 8px 0;
}

.sidebar-scroll::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
  transition: background 0.3s;
}

.sidebar-scroll::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.4);
}

/* Smooth scrolling */
.sidebar-scroll {
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
}

/* Ensure proper spacing for sticky positioning */
.sticky {
  position: sticky;
  top: 0;
}
</style>