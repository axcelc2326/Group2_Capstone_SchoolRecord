<script setup>
import { ref, computed, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
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

// Check if current route is active
const isActiveRoute = (href) => {
  return url === href || url.startsWith(href + '/');
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

// Navigation items with icons
const navigationItems = computed(() => {
  const items = [
    {
      name: 'Dashboard',
      href: route('dashboard'),
      icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 9 5 5V9l5 5"></path></svg>`,
      show: true
    }
  ];

  if (isParent.value) {
    items.push(
      {
        name: 'Register Student',
        href: route('students.create'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>`,
        show: isParent.value,
        category: 'Parent Portal'
      },
      {
        name: 'View Child\'s Grades',
        href: route('parent.grades'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`,
        show: isParent.value,
        category: 'Parent Portal'
      },
      {
        name: 'View Announcements',
        href: route('announcements.index'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>`,
        show: isParent.value,
        category: 'Parent Portal'
      }
    );
  }

  if (isTeacher.value) {
    items.push(
      {
        name: 'View My Students',
        href: route('teacher.students'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path></svg>`,
        show: isTeacher.value,
        category: 'Teacher Portal'
      },
      {
        name: 'Approve Students',
        href: route('students.approval.list'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`,
        show: isTeacher.value,
        category: 'Teacher Portal'
      },
      {
        name: 'Create Announcement',
        href: route('teacher.announcements.create'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>`,
        show: isTeacher.value,
        category: 'Teacher Portal'
      },
      {
        name: 'Download SF5',
        href: route('sf5.download', { class: 1 }),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`,
        show: isTeacher.value,
        category: 'Teacher Portal'
      }
    );
  }

  if (isAdmin.value) {
    items.push(
      {
        name: 'Create Class',
        href: route('classes.create'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>`,
        show: isAdmin.value,
        category: 'Administration'
      },
      {
        name: 'Assign Teacher',
        href: route('admin.assign-teacher'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>`,
        show: isAdmin.value,
        category: 'Administration'
      },
      {
        name: 'Create Announcement',
        href: route('announcements.create'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>`,
        show: isAdmin.value,
        category: 'Administration'
      },
      {
        name: 'View Analytics',
        href: route('analytics.index'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>`,
        show: isAdmin.value,
        category: 'Administration'
      },
      {
        name: 'Manage Users',
        href: route('admin.users.index'),
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path></svg>`,
        show: isAdmin.value,
        category: 'Administration'
      }
    );
  }

  return items;
});

const groupedNavigation = computed(() => {
  const grouped = {};
  navigationItems.value.forEach(item => {
    if (item.category) {
      if (!grouped[item.category]) {
        grouped[item.category] = [];
      }
      grouped[item.category].push(item);
    }
  });
  return grouped;
});
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-white via-blue-50/30 to-blue-100/50">
    <!-- Modern Top Navigation -->
    <nav class="bg-white/95 backdrop-blur-xl border-b border-blue-100/50 shadow-lg shadow-blue-100/20 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <!-- Left: Logo + School Name + User -->
          <div class="flex items-center space-x-4">
            <button
              @click="toggleMobileNavigation"
              class="lg:hidden p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200 hover:scale-105"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!showingNavigationDropdown" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
            
            <Link :href="route('dashboard')" class="flex items-center space-x-3 group">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl flex items-center justify-center text-xl font-bold shadow-lg shadow-blue-500/25 group-hover:shadow-xl group-hover:shadow-blue-500/30 transition-all duration-300 group-hover:scale-105">
                E
              </div>
              <div class="hidden sm:block">
                <div class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">
                  EduPortal
                </div>
                <div class="text-xs text-gray-900 font-medium">Learning Management System</div>
              </div>
            </Link>
            
            <div class="hidden xl:block pl-4 border-l border-blue-200">
              <div class="text-sm font-semibold text-gray-900">Welcome back, {{ user.name }}</div>
              <div class="text-xs text-blue-600/70 flex items-center space-x-2">
                <div class="w-2 h-2 rounded-full animate-pulse"
                     :class="{
                       'bg-red-500': isAdmin,
                       'bg-green-500': isTeacher,
                       'bg-blue-500': isParent
                     }">
                </div>
                <span v-if="isAdmin" class="text-red-600 font-medium">Administrator</span>
                <span v-else-if="isTeacher" class="text-green-600 font-medium">Teacher</span>
                <span v-else-if="isParent" class="text-blue-600 font-medium">Parent</span>
              </div>
            </div>
          </div>

          <!-- Right: Profile Dropdown -->
          <div class="flex items-center space-x-4">
            <Dropdown align="right" width="full">
              <template #trigger>
                <button class="flex items-center px-4 py-2 bg-white/80 border border-blue-200 rounded-2xl text-sm text-gray-700 hover:text-blue-700 hover:bg-blue-50/80 shadow-lg shadow-blue-100/20 hover:shadow-xl hover:shadow-blue-200/30 transition-all duration-300 hover:scale-105">
                  <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl flex items-center justify-center text-sm font-bold mr-3 shadow-lg">
                    {{ user.name.charAt(0).toUpperCase() }}
                  </div>
                  <div class="text-left hidden sm:block">
                    <div class="text-sm font-semibold">{{ user.name }}</div>
                    <div class="text-xs text-gray-900">{{ user.email }}</div>
                  </div>
                  <svg class="ml-3 h-4 w-4 transition-transform text-blue-500" 
                       :class="{ 'rotate-180': showingNavigationDropdown }"
                       viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </template>
              <template #content>
                <DropdownLink :href="route('profile.edit')" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition-all duration-200">
                  <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  Profile
                </DropdownLink>
                <hr class="my-2 border-blue-100">
                <DropdownLink :href="route('logout')" method="post" as="button" 
                              class="w-full flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200">
                  <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                  </svg>
                  Log Out
                </DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation Menu -->
      <div v-show="showingNavigationDropdown" class="lg:hidden border-t bg-white/95 backdrop-blur-xl shadow-xl">
        <div class="px-4 py-6 space-y-4">
          <div class="flex items-center space-x-3 pb-4 border-b border-blue-100">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl flex items-center justify-center text-sm font-bold shadow-lg">
              {{ user.name.charAt(0).toUpperCase() }}
            </div>
            <div>
              <div class="text-base font-semibold text-gray-900">{{ user.name }}</div>
              <div class="text-sm text-blue-600/70">{{ user.email }}</div>
            </div>
          </div>
          
          <!-- Mobile Dashboard Link -->
          <Link 
            :href="route('dashboard')" 
            :class="[
              'flex items-center space-x-3 py-3 px-4 rounded-xl transition-all duration-200',
              isActiveRoute(route('dashboard')) 
                ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' 
                : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700'
            ]"
            @click="showingNavigationDropdown = false"
          >
            <div v-html="navigationItems[0].icon"></div>
            <span class="font-medium">Dashboard</span>
          </Link>
          
          <!-- Mobile Navigation Sections -->
          <div v-for="(items, category) in groupedNavigation" :key="category" class="space-y-2">
            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-3 px-2">
              {{ category }}
            </h3>
            <div class="space-y-1">
              <Link 
                v-for="item in items" 
                :key="item.name"
                :href="item.href" 
                :class="[
                  'flex items-center space-x-3 py-3 px-4 rounded-xl transition-all duration-200',
                  isActiveRoute(item.href) 
                    ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' 
                    : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700'
                ]"
                @click="showingNavigationDropdown = false"
              >
                <div v-html="item.icon"></div>
                <span class="font-medium">{{ item.name }}</span>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Layout: Sidebar + Main -->
    <div class="flex flex-1">
      <!-- Modern Sidebar -->
      <aside :class="[
        'bg-white/95 backdrop-blur-xl border-r border-blue-100/50 transition-all duration-300 ease-in-out shadow-2xl shadow-blue-100/20',
        sidebarCollapsed ? 'w-0 lg:w-20' : 'w-70',
        'hidden lg:block'
      ]">
        <div class="p-6 h-full overflow-hidden">
          <!-- Sidebar Toggle -->
          <div class="flex justify-end mb-6" v-if="!sidebarCollapsed">
            <button 
              @click="toggleSidebar"
              class="p-2 text-blue-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200 hover:scale-105"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </button>
          </div>
          
          <div v-if="sidebarCollapsed" class="flex justify-center mb-8">
            <button 
              @click="toggleSidebar"
              class="p-3 text-blue-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200 hover:scale-105"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
          
          <!-- Navigation -->
          <nav class="space-y-8" v-if="!sidebarCollapsed">
            <div>
              <h2 class="text-lg font-bold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent mb-6">
                Quick Actions
              </h2>
              
              <!-- Dashboard Link -->
              <Link 
                :href="route('dashboard')" 
                :class="[
                  'group flex items-center space-x-4 px-4 py-4 rounded-2xl transition-all duration-300 border-l-4',
                  isActiveRoute(route('dashboard'))
                    ? 'bg-blue-500 text-white border-blue-600 shadow-xl shadow-blue-500/30 scale-105'
                    : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50 border-transparent hover:border-blue-500 hover:scale-105'
                ]"
              >
                <div v-html="navigationItems[0].icon" :class="isActiveRoute(route('dashboard')) ? 'text-white' : 'text-blue-500 group-hover:text-blue-600'"></div>
                <span class="font-semibold">Dashboard</span>
                <div v-if="isActiveRoute(route('dashboard'))" class="ml-auto">
                  <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                </div>
              </Link>
            </div>

            <!-- Role-based Navigation Sections -->
            <div v-for="(items, category) in groupedNavigation" :key="category" class="space-y-3">
              <h3 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-4 px-2">
                {{ category }}
              </h3>
              <div class="space-y-2">
                <Link 
                  v-for="item in items" 
                  :key="item.name"
                  :href="item.href" 
                  :class="[
                    'group flex items-center space-x-4 px-4 py-4 rounded-2xl transition-all duration-300 border-l-4 relative overflow-hidden',
                    isActiveRoute(item.href)
                      ? 'bg-blue-500 text-white border-blue-600 shadow-xl shadow-blue-500/30 scale-105'
                      : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50 border-transparent hover:border-blue-500 hover:scale-105'
                  ]"
                >
                  <div v-html="item.icon" :class="isActiveRoute(item.href) ? 'text-white' : 'text-blue-500 group-hover:text-blue-600'"></div>
                  <span class="font-semibold">{{ item.name }}</span>
                  <div v-if="isActiveRoute(item.href)" class="ml-auto">
                    <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                  </div>
                  <!-- Hover effect background -->
                  <div class="absolute inset-0 bg-gradient-to-r from-blue-400/0 via-blue-400/10 to-blue-400/0 transform -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-in-out"></div>
                </Link>
              </div>
            </div>
          </nav>
          
          <!-- Collapsed Navigation -->
          <nav v-else class="space-y-4">
            <Link 
              :href="route('dashboard')" 
              :class="[
                'group flex items-center justify-center p-4 rounded-2xl transition-all duration-300',
                isActiveRoute(route('dashboard'))
                  ? 'bg-blue-500 text-white shadow-xl shadow-blue-500/30 scale-110'
                  : 'text-blue-500 hover:text-blue-600 hover:bg-blue-50 hover:scale-110'
              ]"
              :title="'Dashboard'"
            >
              <div v-html="navigationItems[0].icon" :class="isActiveRoute(route('dashboard')) ? 'text-white' : 'text-blue-500 group-hover:text-blue-600'"></div>
            </Link>
            
            <div v-for="(items, category) in groupedNavigation" :key="category" class="space-y-3">
              <div class="w-full h-px bg-blue-200 my-4"></div>
              <Link 
                v-for="item in items" 
                :key="item.name"
                :href="item.href" 
                :class="[
                  'group flex items-center justify-center p-4 rounded-2xl transition-all duration-300',
                  isActiveRoute(item.href)
                    ? 'bg-blue-500 text-white shadow-xl shadow-blue-500/30 scale-110'
                    : 'text-blue-500 hover:text-blue-600 hover:bg-blue-50 hover:scale-110'
                ]"
                :title="item.name"
              >
                <div v-html="item.icon" :class="isActiveRoute(item.href) ? 'text-white' : 'text-blue-500 group-hover:text-blue-600'"></div>
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
            <div class="bg-white/95 backdrop-blur-xl rounded-3xl border border-blue-100/50 p-8 shadow-2xl shadow-blue-100/20">
              <slot name="header" />
            </div>
          </header>
          
          <div class="bg-white/95 backdrop-blur-xl rounded-3xl border border-blue-100/50 p-8 shadow-2xl shadow-blue-100/20 min-h-96 relative overflow-hidden">
            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-100/30 to-transparent rounded-full -translate-y-32 translate-x-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-blue-50/50 to-transparent rounded-full translate-y-24 -translate-x-24"></div>
            
            <div class="relative z-10">
              <slot />
            </div>
          </div>
        </main>

        <!-- Modern Footer -->
        <footer class="bg-white/95 backdrop-blur-xl border-t border-blue-100/50 shadow-2xl shadow-blue-100/20 mt-8">
          <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between text-center md:text-left space-y-6 md:space-y-0 md:space-x-8">
              <!-- Company Info -->
              <div class="flex items-center justify-center space-x-4 md:justify-start">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl flex items-center justify-center text-lg font-bold shadow-xl shadow-blue-500/25">
                  E
                </div>
                <div>
                  <div class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">
                    EduPortal
                  </div>
                  <div class="text-sm text-blue-600/70 font-medium">Learning Management System</div>
                </div>
              </div>

              <!-- Paragraph -->
              <p class="text-gray-600 text-sm max-w-xl mx-auto md:mx-0 leading-relaxed">
                Empowering education through innovative technology solutions.
                Connecting students, teachers, and parents in a seamless learning experience.
              </p>
            </div>
            <!-- Bottom Footer -->
            <div class="border-t border-blue-100 pt-6 mt-8">
              <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="text-sm text-gray-500">
                  © {{ currentYear }} EduPortal. All rights reserved. Built with ❤️ for education.
                </div>
                <div class="flex items-center space-x-6">
                  <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse shadow-lg shadow-green-400/50"></div>
                    <span>All systems operational</span>
                  </div>
                  <div class="text-xs text-blue-400 font-medium bg-blue-50 px-3 py-1 rounded-full">
                    v2.1.0
                  </div>
                </div>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom animations and transitions */
.group:hover .group-hover\:scale-105 {
  transform: scale(1.05);
}

/* Smooth backdrop blur effect */
@supports (backdrop-filter: blur(20px)) {
  .backdrop-blur-xl {
    backdrop-filter: blur(20px);
  }
}

/* Custom scrollbar for sidebar */
aside::-webkit-scrollbar {
  width: 4px;
}

aside::-webkit-scrollbar-track {
  background: transparent;
}

aside::-webkit-scrollbar-thumb {
  background: #dbeafe;
  border-radius: 2px;
}

aside::-webkit-scrollbar-thumb:hover {
  background: #bfdbfe;
}

/* Enhanced gradient text animation */
@keyframes gradient-shift {
  0%, 100% {
    background-size: 200% 200%;
    background-position: left center;
  }
  50% {
    background-size: 200% 200%;
    background-position: right center;
  }
}

/* Enhanced pulse animation for status indicator */
@keyframes pulse-glow {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
    box-shadow: 0 0 5px currentColor;
  }
  50% {
    opacity: 0.7;
    transform: scale(1.1);
    box-shadow: 0 0 15px currentColor;
  }
}

.animate-pulse {
  animation: pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Floating animation for decorative elements */
@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  33% {
    transform: translateY(-10px) rotate(1deg);
  }
  66% {
    transform: translateY(5px) rotate(-1deg);
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

/* Hover effects for navigation items */
.nav-item {
  position: relative;
  overflow: hidden;
}

.nav-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
  transition: left 0.6s ease-in-out;
}

.nav-item:hover::before {
  left: 100%;
}

/* Active state glow effect */
.active-glow {
  box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
}

/* Mobile menu slide animation */
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

.mobile-menu-enter {
  animation: slideDown 0.3s ease-out;
}

/* Responsive improvements */
@media (max-width: 768px) {
  .backdrop-blur-xl {
    backdrop-filter: blur(16px);
  }
}

/* Custom focus states for accessibility */
button:focus,
a:focus {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
  border-radius: 0.75rem;
}

/* Smooth transitions for all interactive elements */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Enhanced shadow effects */
.shadow-glow {
  box-shadow: 0 10px 25px -3px rgba(59, 130, 246, 0.1), 0 4px 6px -2px rgba(59, 130, 246, 0.05);
}

.shadow-glow:hover {
  box-shadow: 0 20px 40px -3px rgba(59, 130, 246, 0.15), 0 8px 12px -2px rgba(59, 130, 246, 0.1);
}
</style>