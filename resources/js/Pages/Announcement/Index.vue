<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import { 
  BookOpen, 
  Star, 
  ChevronDown, 
  Megaphone, 
  Calendar,
  User,
  ChevronLeft,
  ChevronRight,
  FileText,
  Plus
} from 'lucide-vue-next'

const props = defineProps({
  announcements: Object,
  parentClassIds: Array,
  filter: String,
  auth: Object,
  counts: Object
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
      <div class="space-y-1">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-bold text-white">
              Announcements
            </h2>
            <p class="text-white/70 mt-1">
              System-wide announcements and notifications
            </p>
          </div>
          <Megaphone class="w-8 h-8 text-white/60" />
        </div>
      </div>
    </template>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      <!-- Header Actions -->
      <div class="flex justify-between items-center">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-2">
          <span class="text-white/80 text-sm">{{ counts?.total_announcements || announcements.data.length }} Announcements</span>
        </div>
        
        <!-- Quick Stats -->
        <div class="hidden md:flex items-center space-x-3">
          <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-2">
            <span class="text-blue-100 text-sm">{{ counts?.total_global || 0 }} Global</span>
          </div>
          <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-2">
            <span class="text-purple-100 text-sm">{{ counts?.total_class || 0 }} Class</span>
          </div>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
          <!-- Filter Dropdown -->
          <div class="flex-1 max-w-md">
            <label for="filter" class="block text-sm font-medium text-white/80 mb-2">Filter Announcements</label>
            <div class="relative">
              <select
                id="filter"
                v-model="selectedFilter"
                class="block w-full pl-4 pr-10 py-2.5 border-0 rounded-lg backdrop-blur-sm bg-white/10 border-white/20 text-white focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200 appearance-none"
              >
                <option value="all" class="bg-gray-800 text-white">All Announcements</option>
                <option value="global" class="bg-gray-800 text-white">Global Only</option>
                <option value="class" class="bg-gray-800 text-white">Class Only</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Stats (visible on small screens) -->
      <div class="md:hidden grid grid-cols-2 gap-4">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-center">
          <div class="text-2xl font-bold text-blue-100">{{ counts?.total_global || 0 }}</div>
          <div class="text-sm text-blue-100">Global</div>
        </div>
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-center">
          <div class="text-2xl font-bold text-purple-100">{{ counts?.total_class || 0 }}</div>
          <div class="text-sm text-purple-100">Class</div>
        </div>
      </div>

      <!-- Announcements List -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <FileText class="w-5 h-5 text-purple-300" />
            <div>
              <h3 class="text-lg font-semibold text-white">System Announcements</h3>
              <p class="text-sm text-white/70 mt-1">{{ counts?.total_announcements || announcements.data.length }} announcements posted</p>
            </div>
          </div>
        </div>

        <!-- Desktop View -->
        <div class="hidden md:block">
          <div class="divide-y divide-white/10">
            <article 
              v-for="announcement in announcements.data" 
              :key="announcement.id"
              class="p-6 hover:bg-white/5 transition-colors duration-150 group"
            >
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <div class="flex items-center space-x-3 mb-3">
                    <div 
                      :class="announcement.class_id 
                        ? 'bg-transparent backdrop-blur-md' 
                        : 'bg-transparent backdrop-blur-md'"
                      class="flex-shrink-0 h-10 w-10 rounded-lg flex items-center justify-center text-white font-medium text-sm"
                    >
                      <BookOpen v-if="announcement.class_id" class="w-5 h-5" />
                      <Star v-else class="w-5 h-5" />
                    </div>
                    <div>
                      <h4 class="text-lg font-semibold text-white">{{ announcement.title }}</h4>
                      <div class="flex items-center space-x-4 mt-1">
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
                        <p class="text-sm text-white/60">
                          Posted: {{ formatDate(announcement.created_at) }}
                        </p>
                      </div>
                    </div>
                  </div>
                  <p class="text-white/80 leading-relaxed mb-4">{{ announcement.body }}</p>
                  
                  <!-- Author Info -->
                  <div class="flex items-center mt-4 pt-4 border-t border-white/10">
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
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-white/10">
          <article 
            v-for="announcement in announcements.data" 
            :key="announcement.id"
            class="p-4 hover:bg-white/5 transition-colors duration-150"
          >
            <div class="flex items-start space-x-3 mb-3">
              <div 
                :class="announcement.class_id 
                  ? 'bg-transparent backdrop-blur-md' 
                  : 'bg-transparent backdrop-blur-md'"
                class="flex-shrink-0 h-8 w-8 rounded-lg flex items-center justify-center text-white font-medium text-sm"
              >
                <BookOpen v-if="announcement.class_id" class="w-4 h-4" />
                <Star v-else class="w-4 h-4" />
              </div>
              <div class="flex-1">
                <h4 class="text-white font-medium mb-1">{{ announcement.title }}</h4>
                <div class="flex items-center space-x-2 mb-2">
                  <span 
                    :class="announcement.class_id 
                      ? 'bg-purple-500/20 text-purple-100 border-purple-400/30' 
                      : 'bg-amber-500/20 text-amber-100 border-amber-400/30'"
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border backdrop-blur-sm"
                  >
                    <span 
                      :class="announcement.class_id ? 'bg-purple-400' : 'bg-amber-400'"
                      class="w-1.5 h-1.5 rounded-full mr-1"
                    ></span>
                    {{ announcement.class_id ? `Class ${announcement.class_id}` : 'Global' }}
                  </span>
                </div>
                <p class="text-sm text-white/60 mb-2">
                  Posted: {{ formatDate(announcement.created_at) }}
                </p>
                <p class="text-white/80 text-sm leading-relaxed mb-3">{{ announcement.body }}</p>
                
                <!-- Author Info -->
                <div class="flex items-center mt-3 pt-3 border-t border-white/10">
                  <div class="ml-2 flex items-center">
                    <User class="w-3 h-3 text-white/60 mr-1" />
                    <p class="text-xs text-white/80">
                      By <span class="font-medium text-white">{{ announcement.creator.name }}</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </article>
        </div>

        <!-- Empty State -->
        <div v-if="announcements.data.length === 0" class="text-center py-8">
          <FileText class="mx-auto h-10 w-10 text-white/40 mb-3" />
          <h3 class="text-sm font-medium text-white/80">
            No announcements posted yet
          </h3>
          <p class="mt-1 text-sm text-white/60">
            Check back later for new updates and announcements.
          </p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="announcements.links && announcements.links.length > 3" class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4">
          <div class="flex justify-center space-x-2">
            <template v-for="(link, index) in announcements.links" :key="index">
              <Link
                v-if="link.url"
                :href="link.url"
                :class="[ 
                  'px-3 py-2 rounded transition-all duration-150 backdrop-blur-sm border text-sm font-medium',
                  link.active
                    ? 'bg-white/20 text-white border-white/30'
                    : 'bg-white/5 text-white/70 border-white/20 hover:bg-white/10 hover:text-white',
                ]"
                v-html="link.label"
              />
              <span
                v-else
                v-html="link.label"
                class="px-3 py-2 rounded text-white/40 text-sm font-medium"
              />
            </template>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Glassmorphism base effects */
.backdrop-blur-md {
  backdrop-filter: blur(16px);
}

.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

/* Smooth transitions */
.transition-all {
  transition: all 0.2s ease-in-out;
}

/* Focus states for accessibility */
button:focus-visible,
select:focus-visible {
  outline: 2px solid rgba(59, 130, 246, 0.5);
  outline-offset: 2px;
  border-radius: 0.375rem;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .bg-white\/10 {
    background-color: rgba(255, 255, 255, 0.2);
  }
  
  .border-white\/20 {
    border-color: rgba(255, 255, 255, 0.4);
  }
  
  .text-white\/70 {
    color: rgba(255, 255, 255, 0.9);
  }
}
</style>