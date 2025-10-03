<script setup>
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import AnnouncementAdminModal from '@/Components/Admin/AnnouncementAdminModal.vue'
import EditAdminAnnouncementModal from '@/Components/Admin/EditAdminAnnouncementModal.vue'
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'
import { Plus, Edit, Trash2, FileText, Megaphone } from 'lucide-vue-next'

const props = defineProps({
  announcements: Object,
})

const showCreateModal = ref(false)
const showEditModal = ref(false)
const selectedAnnouncement = ref(null)

// Statistics
const totalAnnouncements = computed(() => props.announcements.data.length)

const openCreateModal = () => (showCreateModal.value = true)
const closeCreateModal = () => (showCreateModal.value = false)

const openEditModal = (announcement) => {
  selectedAnnouncement.value = announcement
  showEditModal.value = true
}
const closeEditModal = () => {
  selectedAnnouncement.value = null
  showEditModal.value = false
}

const deleteAnnouncement = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will delete the announcement permanently.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('announcements.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire('Deleted!', 'The announcement has been deleted.', 'success')
            .then(() => {
              location.reload()
            })
        },
        onError: () => {
          Swal.fire('Error!', 'Something went wrong. Please try again.', 'error')
        },
      })
    }
  })
}
</script>

<template>
  <Head title="Global Announcements" />

  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-1">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-bold text-white">
              Global Announcements
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
          <span class="text-white/80 text-sm">{{ totalAnnouncements }} Announcements</span>
        </div>
        
        <button
          @click="openCreateModal"
          class="backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30 flex items-center gap-2"
        >
          <Plus class="w-4 h-4" />
          New Announcement
        </button>
      </div>

      <!-- Announcements List -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <FileText class="w-5 h-5 text-purple-300" />
            <div>
              <h3 class="text-lg font-semibold text-white">System Announcements</h3>
              <p class="text-sm text-white/70 mt-1">{{ totalAnnouncements }} announcements posted</p>
            </div>
          </div>
        </div>

        <!-- Desktop View -->
        <div class="hidden md:block">
          <div class="divide-y divide-white/10">
            <div
              v-for="announcement in props.announcements.data"
              :key="announcement.id"
              class="p-6 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <div class="flex items-center space-x-3 mb-3">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium text-sm">
                      {{ announcement.title.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <h4 class="text-lg font-semibold text-white">{{ announcement.title }}</h4>
                      <p class="text-sm text-white/60">
                        Posted: {{ new Date(announcement.created_at).toLocaleString() }}
                      </p>
                    </div>
                  </div>
                  <p class="text-white/80 leading-relaxed mb-4">{{ announcement.body }}</p>
                </div>
                <div class="flex items-center space-x-2 ml-4">
                  <button
                    class="inline-flex items-center px-3 py-2 bg-white/10 hover:bg-white/20 text-white border border-white/20 hover:border-white/30 rounded-lg transition-all duration-150 backdrop-blur-sm"
                    @click="openEditModal(announcement)"
                  >
                    <Edit class="w-4 h-4 mr-2" />
                    Edit
                  </button>
                  <button
                    class="inline-flex items-center px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-100 border border-red-400/30 hover:border-red-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                    @click="deleteAnnouncement(announcement.id)"
                  >
                    <Trash2 class="w-4 h-4 mr-2" />
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-white/10">
          <div
            v-for="announcement in props.announcements.data"
            :key="announcement.id"
            class="p-4 hover:bg-white/5 transition-colors duration-150"
          >
            <div class="flex items-start space-x-3 mb-3">
              <div class="flex-shrink-0 h-8 w-8 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium text-sm">
                {{ announcement.title.charAt(0).toUpperCase() }}
              </div>
              <div class="flex-1">
                <h4 class="text-white font-medium mb-1">{{ announcement.title }}</h4>
                <p class="text-sm text-white/60 mb-2">
                  Posted: {{ new Date(announcement.created_at).toLocaleString() }}
                </p>
                <p class="text-white/80 text-sm leading-relaxed">{{ announcement.body }}</p>
              </div>
            </div>
            <div class="flex space-x-2">
              <button
                class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-white/10 hover:bg-white/20 text-white border border-white/20 hover:border-white/30 rounded-lg transition-all duration-150 backdrop-blur-sm"
                @click="openEditModal(announcement)"
              >
                <Edit class="w-4 h-4 mr-2" />
                Edit
              </button>
              <button
                class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-100 border border-red-400/30 hover:border-red-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                @click="deleteAnnouncement(announcement.id)"
              >
                <Trash2 class="w-4 h-4 mr-2" />
                Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="props.announcements.data.length === 0" class="text-center py-8">
          <FileText class="mx-auto h-10 w-10 text-white/40 mb-3" />
          <h3 class="text-sm font-medium text-white/80">
            No announcements posted yet
          </h3>
          <p class="mt-1 text-sm text-white/60">
            Get started by creating your first system-wide announcement.
          </p>
          <div class="mt-4">
            <button
              @click="openCreateModal"
              class="inline-flex items-center px-3 py-2 bg-white/10 hover:bg-white/20 text-white border border-white/20 hover:border-white/30 rounded-lg transition-all duration-150 backdrop-blur-sm"
            >
              <Plus class="w-4 h-4 mr-2" />
              Create Announcement
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="props.announcements.links && props.announcements.links.length > 3" class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4">
          <div class="flex justify-center space-x-2">
            <template v-for="(link, index) in props.announcements.links" :key="index">
              <button
                v-if="link.url"
                @click="router.visit(link.url)"
                v-html="link.label"
                :class="[ 
                  'px-3 py-2 rounded transition-all duration-150 backdrop-blur-sm border text-sm font-medium',
                  link.active
                    ? 'bg-white/20 text-white border-white/30'
                    : 'bg-white/5 text-white/70 border-white/20 hover:bg-white/10 hover:text-white',
                ]"
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

  <!-- Modals -->
  <AnnouncementAdminModal
    :show="showCreateModal"
    @close="closeCreateModal"
    @saved="() => { closeCreateModal(); router.reload(); }"
  />

  <EditAdminAnnouncementModal
    v-if="selectedAnnouncement"
    :show="showEditModal"
    :announcement="selectedAnnouncement"
    @close="closeEditModal"
    @updated="() => { closeEditModal(); router.reload(); }"
  />
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
button:focus-visible {
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