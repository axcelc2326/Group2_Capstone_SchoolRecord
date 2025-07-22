<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  announcements: Object, // ✅ Paginated data is an object
  parentClassIds: Array,
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>

<template>
  <Head title="Announcements" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center space-x-3">
        <div class="bg-blue-100 p-2 rounded-lg">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 font-sans text-header">Announcements</h2>
      </div>
    </template>

    <div class="min-h-screen bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Parent Information Banner -->
        <div v-if="parentClassIds?.length" 
             class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 md:p-6">
          <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-blue-900 font-sans text-subheader">Parent View Active</h3>
              <p class="text-base text-blue-700 mt-2 font-sans readable-meta">
                Viewing announcements for your children's classes 
                <span class="font-medium">({{ parentClassIds.join(', ') }})</span> 
                and global announcements.
              </p>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="announcements.data.length === 0" 
             class="text-center py-16 bg-white rounded-2xl shadow-sm">
          <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
            </svg>
          </div>
          <h3 class="text-2xl font-semibold text-gray-900 mb-3 font-sans text-subheader">No announcements yet</h3>
          <p class="text-lg text-gray-600 font-sans readable-meta">Check back later for updates and important information.</p>
        </div>

        <!-- Announcements Grid -->
        <div v-else class="grid gap-6 md:gap-8">
          <article
            v-for="announcement in announcements.data"
            :key="announcement.id"
            class="group bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden border border-gray-100"
          >
            <!-- Header with title and metadata -->
            <div class="p-6 pb-4 border-b border-gray-50">
              <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div class="flex-1">
                  <h3 class="text-xl font-semibold text-gray-900 group-hover:text-blue-600 transition-colors duration-200 leading-tight text-title">
                    {{ announcement.title }}
                  </h3>
                  
                  <!-- Class/Global Badge -->
                  <div class="mt-3">
                    <span v-if="announcement.class_id"
                          class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 text-badge">
                      <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.84L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.84l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                      </svg>
                      Class {{ announcement.class_id }}
                    </span>
                    <span v-else
                          class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 text-badge">
                      <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                      </svg>
                      Global
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="p-6 pt-4">
              <p class="text-content readable-text whitespace-pre-line">
                {{ announcement.body }}
              </p>
            </div>

            <!-- Footer with author and date -->
            <div class="px-6 py-5 bg-gray-50 border-t border-gray-100">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-meta">
                <div class="flex items-center space-x-4">
                  <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-semibold text-gray-800">{{ announcement.creator.name }}</span>
                  </div>
                </div>
                <div class="flex items-center space-x-2 readable-secondary">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                  </svg>
                  <time class="font-semibold">{{ formatDate(announcement.created_at) }}</time>
                </div>
              </div>
            </div>
          </article>
        </div>

        <!-- Modern Pagination -->
        <div v-if="announcements.links.length > 3" 
             class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-12 pt-8 border-t border-gray-200">
          
          <!-- Mobile: Simple prev/next -->
          <div class="flex sm:hidden space-x-2">
            <Link
              v-if="announcements.links[0].url"
              :href="announcements.links[0].url"
              class="flex items-center px-5 py-3 bg-white border border-gray-300 rounded-lg text-base font-semibold text-gray-700 hover:bg-gray-50 transition-colors duration-200 font-sans"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Previous
            </Link>
            <Link
              v-if="announcements.links[announcements.links.length - 1].url"
              :href="announcements.links[announcements.links.length - 1].url"
              class="flex items-center px-5 py-3 bg-white border border-gray-300 rounded-lg text-base font-semibold text-gray-700 hover:bg-gray-50 transition-colors duration-200 font-sans"
            >
              Next
              <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </Link>
          </div>

          <!-- Desktop: Full pagination -->
          <nav class="hidden sm:flex items-center space-x-1" aria-label="Pagination">
            <Link
              v-for="(link, index) in announcements.links"
              :key="index"
              :href="link.url || ''"
              :class="[
                'relative inline-flex items-center px-4 py-3 text-base font-semibold rounded-lg transition-all duration-200 font-sans',
                link.active 
                  ? 'bg-blue-600 text-white shadow-sm' 
                  : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300',
                !link.url && 'pointer-events-none opacity-50 cursor-not-allowed'
              ]"
              :aria-current="link.active ? 'page' : undefined"
              v-html="link.label"
            />
          </nav>
          
          <!-- Pagination info -->
          <div class="text-base text-gray-600 mt-3 sm:mt-0 font-sans readable-meta">
            Showing {{ announcements.from || 0 }} to {{ announcements.to || 0 }} 
            of {{ announcements.total || 0 }} announcements
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Import modern, readable fonts */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Source+Serif+Pro:wght@400;600&display=swap');

/* Apply fonts to different elements */
.font-sans {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.font-serif {
  font-family: 'Source Serif Pro', Georgia, Cambria, 'Times New Roman', Times, serif;
}

/* Improve text readability */
.text-content {
  font-family: 'Source Serif Pro', Georgia, Cambria, 'Times New Roman', Times, serif;
  line-height: 1.8;
  font-size: 1.25rem; /* 20px */
  letter-spacing: 0.015em;
  color: #111827;
  font-weight: 400;
}

.text-title {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  font-weight: 700;
  line-height: 1.35;
  letter-spacing: -0.02em;
  color: #111827;
}

.text-meta {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  font-weight: 500;
  letter-spacing: 0.01em;
  line-height: 1.6;
  font-size: 0.95rem; /* 15.2px */
}

.text-badge {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  font-weight: 600;
  letter-spacing: 0.03em;
  font-size: 0.8rem; /* 12.8px */
}

.text-header {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  font-weight: 800;
  letter-spacing: -0.03em;
  color: #111827;
}

.text-subheader {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  font-weight: 600;
  letter-spacing: -0.01em;
  color: #374151;
}

/* Improve readability with better contrast and spacing */
.readable-text {
  color: #111827;
  line-height: 1.8;
}

.readable-meta {
  color: #4b5563;
  line-height: 1.7;
}

.readable-secondary {
  color: #6b7280;
  line-height: 1.6;
}

/* Custom scrollbar for content areas */
.whitespace-pre-line {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}

.whitespace-pre-line::-webkit-scrollbar {
  width: 6px;
}

.whitespace-pre-line::-webkit-scrollbar-track {
  background: transparent;
}

.whitespace-pre-line::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 3px;
}

/* Smooth animations */
@media (prefers-reduced-motion: no-preference) {
  .group {
    transform: translateY(0);
  }
  
  .group:hover {
    transform: translateY(-1px);
  }
}

/* Responsive font sizes */
@media (max-width: 640px) {
  .text-content {
    font-size: 1.125rem; /* 18px on mobile */
    line-height: 1.75;
  }
  
  .text-title {
    font-size: 1.375rem; /* 22px on mobile */
    line-height: 1.4;
  }
  
  .text-header {
    font-size: 1.75rem; /* 28px on mobile */
    line-height: 1.3;
  }
  
  .text-meta {
    font-size: 0.9rem; /* 14.4px on mobile */
  }
}

@media (min-width: 641px) and (max-width: 768px) {
  .text-content {
    font-size: 1.1875rem; /* 19px on small tablet */
  }
  
  .text-title {
    font-size: 1.5rem; /* 24px on small tablet */
  }
  
  .text-header {
    font-size: 2rem; /* 32px on small tablet */
  }
}

@media (min-width: 769px) {
  .text-content {
    font-size: 1.25rem; /* 20px on tablet+ */
  }
  
  .text-title {
    font-size: 1.625rem; /* 26px on tablet+ */
  }
  
  .text-header {
    font-size: 2.25rem; /* 36px on tablet+ */
  }
}
</style>