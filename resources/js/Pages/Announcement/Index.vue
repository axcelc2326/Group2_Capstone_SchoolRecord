<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { 
  BookOpen, 
  Star, 
  ChevronDown, 
  Megaphone, 
  Calendar,
  User,
  ChevronLeft,
  ChevronRight
} from 'lucide-vue-next'

const props = defineProps({
  announcements: Object,
  parentClassIds: Array,
  filter: String,
  auth: Object,
  counts: Object // Add counts prop from Laravel controller
})

const selectedFilter = ref(props.filter || 'all')

watch(selectedFilter, (value) => {
  router.get(route('announcements.index'), { filter: value }, {
    preserveState: true,
    preserveScroll: true
  })
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getCreatorInitials = (name) => {
  return name.split(' ').map(word => word.charAt(0)).join('').toUpperCase().substring(0, 2)
}
</script>

<template>
  <Head title="Announcements" />

  <AuthenticatedLayout :user="auth.user">
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0">
        <h2 class="text-2xl font-bold text-white bg-gradient-to-r from-white to-white/70 bg-clip-text text-transparent">
          Announcements
        </h2>
        <div class="flex items-center space-x-4">
          <!-- Quick Stats -->
          <div class="hidden md:flex items-center space-x-3 text-sm">
            <div class="backdrop-blur-sm bg-blue-500/20 border border-blue-300/30 px-3 py-1 rounded-full">
              <span class="text-blue-100">{{ counts?.total_global || 0 }} Global</span>
            </div>
            <div class="backdrop-blur-sm bg-purple-500/20 border border-purple-300/30 px-3 py-1 rounded-full">
              <span class="text-purple-100">{{ counts?.total_class || 0 }} Class</span>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Top Action Bar -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl p-6 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
          
          <!-- Filter Dropdown -->
          <div class="flex-1 max-w-md">
            <label for="filter" class="block text-sm font-medium text-white/80 mb-2">Filter Announcements</label>
            <div class="relative">
              <select
                id="filter"
                v-model="selectedFilter"
                class="block w-full pl-4 pr-10 py-2.5 border-0 rounded-xl backdrop-blur-sm bg-white/10 border-white/20 text-white focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200 appearance-none"
              >
                <option value="all" class="bg-gray-800 text-white">All Announcements</option>
                <option value="global" class="bg-gray-800 text-white">Global Only</option>
                <option value="class" class="bg-gray-800 text-white">Class Only</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <ChevronDown class="h-5 w-5 text-white/60" />
              </div>
            </div>
          </div>

          <!-- Total Count -->
          <div class="text-right">
            <div class="text-2xl font-bold text-white">{{ counts?.total_announcements || announcements.data.length }}</div>
            <div class="text-sm text-white/70">Total Announcements</div>
          </div>
        </div>
      </div>

      <!-- Mobile Stats (visible on small screens) -->
      <div class="md:hidden grid grid-cols-2 gap-4">
        <div class="backdrop-blur-md bg-blue-500/10 border border-blue-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-blue-100">{{ counts?.total_global || 0 }}</div>
          <div class="text-sm text-blue-200">Global</div>
        </div>
        <div class="backdrop-blur-md bg-purple-500/10 border border-purple-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-purple-100">{{ counts?.total_class || 0 }}</div>
          <div class="text-sm text-purple-200">Class</div>
        </div>
      </div>

      <!-- Announcements Container -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <h3 class="text-lg font-semibold text-white">Recent Announcements</h3>
          <p class="text-sm text-white/70 mt-1">Stay updated with the latest news and updates</p>
        </div>

        <!-- Empty State -->
        <div v-if="announcements.data.length === 0" class="text-center py-16">
          <Megaphone class="mx-auto h-16 w-16 text-white/40 mb-4" />
          <h3 class="text-xl font-medium text-white/80 mb-2">No announcements found</h3>
          <p class="text-white/60">Check back later for new updates and announcements.</p>
        </div>

        <!-- Announcements List -->
        <div v-else class="divide-y divide-white/10">
          <article 
            v-for="announcement in announcements.data" 
            :key="announcement.id"
            class="p-6 hover:bg-white/5 transition-colors duration-150 group"
          >
            <div class="flex items-start space-x-4">
              <!-- Announcement Type Icon -->
              <div 
                :class="announcement.class_id 
                  ? 'bg-gradient-to-br from-purple-400 to-indigo-600' 
                  : 'bg-gradient-to-br from-amber-400 to-orange-600'"
                class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center shadow-lg"
              >
                <BookOpen v-if="announcement.class_id" class="w-6 h-6 text-white" />
                <Star v-else class="w-6 h-6 text-white" />
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <h3 class="text-lg font-semibold text-white group-hover:text-blue-200 transition-colors">
                      {{ announcement.title }}
                    </h3>
                    
                    <!-- Meta Info -->
                    <div class="flex items-center space-x-4 mt-1 mb-3">
                      <span 
                        :class="announcement.class_id 
                          ? 'bg-purple-500/20 text-purple-100 border-purple-400/30' 
                          : 'bg-amber-500/20 text-amber-100 border-amber-400/30'"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border backdrop-blur-sm"
                      >
                        <span 
                          :class="announcement.class_id ? 'bg-purple-400' : 'bg-amber-400'"
                          class="w-1.5 h-1.5 rounded-full mr-2"
                        ></span>
                        {{ announcement.class_id ? `Class ${announcement.class_id}` : 'Global' }}
                      </span>
                      <div class="flex items-center text-sm text-white/60">
                        <Calendar class="w-4 h-4 mr-1" />
                        {{ formatDate(announcement.created_at) }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Announcement Body -->
                <div class="prose prose-invert max-w-none">
                  <p class="text-white/90 leading-relaxed">{{ announcement.body }}</p>
                </div>

                <!-- Author Info -->
                <div class="flex items-center mt-4 pt-4 border-t border-white/10">
                  <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-r from-emerald-500 to-blue-600 flex items-center justify-center text-white text-xs font-medium">
                    {{ getCreatorInitials(announcement.creator.name) }}
                  </div>
                  <div class="ml-3 flex items-center">
                    <User class="w-4 h-4 text-white/60 mr-1" />
                    <p class="text-sm text-white/80">
                      Published by <span class="font-medium text-white">{{ announcement.creator.name }}</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </article>
        </div>

        <!-- Pagination -->
        <div v-if="announcements.links && announcements.links.length > 3" class="px-6 py-4 border-t border-white/10">
          <div class="flex items-center justify-center space-x-1">
            <template v-for="(link, index) in announcements.links" :key="index">
              <Link
                v-if="link.url"
                :href="link.url"
                :class="{
                  'bg-blue-500/30 text-blue-100 border-blue-400/50': link.active,
                  'bg-white/10 text-white/70 border-white/20 hover:bg-white/20 hover:text-white': !link.active
                }"
                class="flex items-center justify-center px-3 py-2 text-sm font-medium border rounded-lg transition-all duration-150 backdrop-blur-sm min-w-[40px]"
              >
                <ChevronLeft v-if="link.label.includes('Previous')" class="w-4 h-4" />
                <ChevronRight v-else-if="link.label.includes('Next')" class="w-4 h-4" />
                <span v-else v-html="link.label" />
              </Link>
              <span
                v-else
                class="flex items-center justify-center px-3 py-2 text-sm font-medium border rounded-lg opacity-50 cursor-not-allowed bg-white/5 text-white/40 border-white/10 backdrop-blur-sm min-w-[40px]"
              >
                <ChevronLeft v-if="link.label.includes('Previous')" class="w-4 h-4" />
                <ChevronRight v-else-if="link.label.includes('Next')" class="w-4 h-4" />
                <span v-else v-html="link.label" />
              </span>
            </template>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>