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
const { props } = usePage();
const user = props.auth.user;
const roles = user.roles?.map(role => role.name) || [];

const isParent = computed(() => roles.includes('parent'));
const isTeacher = computed(() => roles.includes('teacher'));
const isAdmin = computed(() => roles.includes('admin'));

const currentYear = ref(new Date().getFullYear());

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value;
};

// Auto-collapse sidebar on mobile
onMounted(() => {
  const handleResize = () => {
    if (window.innerWidth < 1024) {
      sidebarCollapsed.value = true;
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
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Modern Top Navigation -->
    <nav class="bg-white/80 backdrop-blur-lg border-b border-gray-200/60 shadow-sm sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <!-- Left: Logo + School Name + User -->
          <div class="flex items-center space-x-4">
            <button
              @click="toggleSidebar"
              class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            
            <Link :href="route('dashboard')" class="flex items-center space-x-3 group">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 text-white rounded-xl flex items-center justify-center text-lg font-bold shadow-lg group-hover:shadow-xl transition-shadow">
                E
              </div>
              <div class="hidden sm:block">
                <div class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                  EduPortal
                </div>
                <div class="text-xs text-gray-500 font-medium">Learning Management System</div>
              </div>
            </Link>
            
            <div class="hidden xl:block pl-4 border-l border-gray-200">
              <div class="text-sm font-semibold text-gray-900">Welcome back, {{ user.name }}</div>
              <div class="text-xs text-gray-500 flex items-center space-x-2">
                <div class="w-2 h-2 rounded-full"
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
                <button class="flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-50 shadow-sm hover:shadow-md transition-all duration-200">
                  <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-500 text-white rounded-lg flex items-center justify-center text-sm font-medium mr-3 shadow">
                    {{ user.name.charAt(0).toUpperCase() }}
                  </div>
                  <div class="text-left hidden sm:block">
                    <div class="text-sm font-semibold">{{ user.name }}</div>
                    <div class="text-xs text-gray-500">{{ user.email }}</div>
                  </div>
                  <svg class="ml-3 h-4 w-4 transition-transform" 
                       :class="{ 'rotate-180': showingNavigationDropdown }"
                       viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </template>
              <template #content>
                <div class="p-2">
                  <DropdownLink :href="route('profile.edit')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile
                  </DropdownLink>
                  <hr class="my-2">
                  <DropdownLink :href="route('logout')" method="post" as="button" 
                               class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Log Out
                  </DropdownLink>
                </div>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation Menu -->
      <div v-if="showingNavigationDropdown" class="sm:hidden border-t bg-white/95 backdrop-blur-lg">
        <div class="px-4 py-4 space-y-3">
          <div class="flex items-center space-x-3 pb-4 border-b border-gray-200">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 text-white rounded-lg flex items-center justify-center text-sm font-medium shadow">
              {{ user.name.charAt(0).toUpperCase() }}
            </div>
            <div>
              <div class="text-base font-semibold">{{ user.name }}</div>
              <div class="text-sm text-gray-500">{{ user.email }}</div>
            </div>
          </div>
          <ResponsiveNavLink :href="route('profile.edit')" class="flex items-center space-x-3 py-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Profile</span>
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="flex items-center space-x-3 py-2 text-red-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span>Log Out</span>
          </ResponsiveNavLink>
        </div>
      </div>
    </nav>

    <!-- Layout: Sidebar + Main -->
    <div class="flex flex-1">
      <!-- Modern Sidebar -->
      <aside :class="[
        'bg-white/60 backdrop-blur-lg border-r border-gray-200/60 transition-all duration-300 ease-in-out',
        sidebarCollapsed ? 'w-0 lg:w-16' : 'w-72',
        'hidden lg:block shadow-lg'
      ]">
        <div class="p-4 h-full overflow-hidden">
          <!-- Sidebar Toggle -->
          <div class="flex justify-end mb-4" v-if="!sidebarCollapsed">
            <button 
              @click="toggleSidebar"
              class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </button>
          </div>
          
          <div v-if="sidebarCollapsed" class="flex justify-center mb-6">
            <button 
              @click="toggleSidebar"
              class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
          
          <!-- Navigation -->
          <nav class="space-y-6" v-if="!sidebarCollapsed">
            <div>
              <h2 class="text-lg font-bold bg-gradient-to-r from-gray-700 to-gray-900 bg-clip-text text-transparent mb-4">
                Quick Actions
              </h2>
              
              <!-- Dashboard Link -->
              <Link 
                :href="route('dashboard')" 
                class="group flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 border-l-4 border-transparent hover:border-blue-500"
              >
                <div v-html="navigationItems[0].icon" class="text-gray-500 group-hover:text-blue-600 transition-colors"></div>
                <span class="font-medium">Dashboard</span>
              </Link>
            </div>

            <!-- Role-based Navigation Sections -->
            <div v-for="(items, category) in groupedNavigation" :key="category" class="space-y-2">
              <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-3">
                {{ category }}
              </h3>
              <div class="space-y-1">
                <Link 
                  v-for="item in items" 
                  :key="item.name"
                  :href="item.href" 
                  class="group flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 border-l-4 border-transparent hover:border-blue-500"
                >
                  <div v-html="item.icon" class="text-gray-500 group-hover:text-blue-600 transition-colors"></div>
                  <span class="font-medium">{{ item.name }}</span>
                </Link>
              </div>
            </div>
          </nav>
          
          <!-- Collapsed Navigation -->
          <nav v-else class="space-y-4">
            <Link 
              :href="route('dashboard')" 
              class="group flex items-center justify-center p-3 rounded-xl text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200"
              title="Dashboard"
            >
              <div v-html="navigationItems[0].icon" class="text-gray-500 group-hover:text-blue-600 transition-colors"></div>
            </Link>
            
            <div v-for="(items, category) in groupedNavigation" :key="category" class="space-y-2">
              <Link 
                v-for="item in items" 
                :key="item.name"
                :href="item.href" 
                class="group flex items-center justify-center p-3 rounded-xl text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200"
                :title="item.name"
              >
                <div v-html="item.icon" class="text-gray-500 group-hover:text-blue-600 transition-colors"></div>
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
            <div class="bg-white/60 backdrop-blur-lg rounded-2xl border border-gray-200/60 p-6 shadow-lg">
              <slot name="header" />
            </div>
          </header>
          
          <div class="bg-white/60 backdrop-blur-lg rounded-2xl border border-gray-200/60 p-8 shadow-lg min-h-96">
            <slot />
          </div>
        </main>

        <!-- Modern Footer -->
        <footer class="bg-white/80 backdrop-blur-lg border-t border-gray-200/60 shadow-sm mt-8">
          <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between text-center md:text-left space-y-6 md:space-y-0 md:space-x-8">
              <!-- Company Info -->
              <div class="flex items-center justify-center space-x-3 md:justify-start">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 text-white rounded-lg flex items-center justify-center text-sm font-bold shadow">
                  E
                </div>
                <div>
                  <div class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    EduPortal
                  </div>
                  <div class="text-xs text-gray-500">Learning Management System</div>
                </div>
              </div>

              <!-- Paragraph -->
              <p class="text-gray-600 text-sm max-w-xl mx-auto md:mx-0">
                Empowering education through innovative technology solutions.
                Connecting students, teachers, and parents in a seamless learning experience.
              </p>
            </div>
            <!-- Bottom Footer -->
            <div class="border-t border-gray-200 pt-6 mt-8">
              <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="text-sm text-gray-500">
                  © {{ currentYear }} EduPortal. All rights reserved. Built with ❤️ for education.
                </div>
                <div class="flex items-center space-x-6">
                  <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span>All systems operational</span>
                  </div>
                  <div class="text-xs text-gray-400">
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
@supports (backdrop-filter: blur(16px)) {
  .backdrop-blur-lg {
    backdrop-filter: blur(16px);
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
  background: #cbd5e1;
  border-radius: 2px;
}

aside::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Gradient text animation */
@keyframes gradient {
  0%, 100% {
    background-size: 200% 200%;
    background-position: left center;
  }
  50% {
    background-size: 200% 200%;
    background-position: right center;
  }
}

/* Pulse animation for status indicator */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
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
  transition: left 0.5s;
}

.nav-item:hover::before {
  left: 100%;
}
</style>