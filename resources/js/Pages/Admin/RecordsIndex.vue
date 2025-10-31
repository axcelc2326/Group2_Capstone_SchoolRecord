<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, reactive } from 'vue'
import axios from 'axios'
import RecordDetailsModal from '@/Components/Admin/RecordDetailsModal.vue'
import { FileText, Filter, Eye, Download, CheckCircle, Clock, RefreshCw } from 'lucide-vue-next'

const props = defineProps({
  sf5Records: Object, // now paginated, not Array
  honorLists: Object, // now paginated, not Array
  teachers: Array,
  filters: Object,
})

// ✅ Filters
const teacherId = ref(props.filters.teacher_id ?? '')
const typeFilter = ref(props.filters.type ?? 'all')
const statusFilter = ref(props.filters.status ?? 'all')

// ✅ Modal state
const showModal = ref(false)
const modalData = reactive({ type: null, record: null, loading: false })

function applyFilters() {
  const params = new URLSearchParams()
  if (teacherId.value) params.append('teacher_id', teacherId.value)
  if (typeFilter.value) params.append('type', typeFilter.value)
  if (statusFilter.value) params.append('status', statusFilter.value)

  // use Inertia for navigation
  router.visit(route('admin.records.index') + '?' + params.toString())
}

async function viewDetails(type, id) {
  modalData.loading = true
  showModal.value = true
  modalData.type = type
  modalData.record = null

  try {
    const res = await axios.get(route('admin.records.show', { type, id }))
    modalData.record = res.data.record
  } catch (err) {
    modalData.record = { error: 'Failed to load record.' }
  } finally {
    modalData.loading = false
  }
}

async function toggleReviewed(type, id, index, isSF5) {
  try {
    const res = await axios.post(route('admin.records.toggleReviewed', { type, id }))
    const updatedStatus = res.data.status

    // Update local record
    if (isSF5) {
      props.sf5Records.data[index].status = updatedStatus
    } else {
      props.honorLists.data[index].status = updatedStatus
    }
  } catch (err) {
    console.error(err)
    alert('Unable to update status')
  }
}

// Calculate totals
const totalSF5 = props.sf5Records?.total || 0
const totalHonor = props.honorLists?.total || 0
const totalRecords = totalSF5 + totalHonor
</script>

<template>
  <Head title="Submitted Records" />
  
  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-1">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-bold text-white">
              Submitted Records
            </h2>
            <p class="text-white/70 mt-1">
              Review and manage all submitted reports
            </p>
          </div>
          <FileText class="w-8 h-8 text-white/60" />
        </div>
      </div>
    </template>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      <!-- Header Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3">
          <div class="text-white/70 text-sm">Total Records</div>
          <div class="text-white text-2xl font-bold">{{ totalRecords }}</div>
        </div>
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3">
          <div class="text-white/70 text-sm">SF5 Reports</div>
          <div class="text-white text-2xl font-bold">{{ totalSF5 }}</div>
        </div>
        <div class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3">
          <div class="text-white/70 text-sm">Honor Lists</div>
          <div class="text-white text-2xl font-bold">{{ totalHonor }}</div>
        </div>
      </div>

      <!-- Filters Card -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl p-6">
        <div class="flex items-center space-x-3 mb-4">
          <Filter class="w-5 h-5 text-blue-300" />
          <h3 class="text-lg font-semibold text-white">Filters</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-white">Teacher</label>
            <select 
              v-model="teacherId" 
              class="w-full appearance-none backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
            >
              <option value="" class="bg-gray-800 text-white">All Teachers</option>
              <option v-for="t in teachers" :key="t.id" :value="t.id" class="bg-gray-800 text-white">{{ t.name }}</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="block text-sm font-semibold text-white">Type</label>
            <select 
              v-model="typeFilter" 
              class="w-full appearance-none backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
            >
              <option value="all" class="bg-gray-800 text-white">All Types</option>
              <option value="sf5" class="bg-gray-800 text-white">SF5 Reports</option>
              <option value="honor" class="bg-gray-800 text-white">Honor Roll</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="block text-sm font-semibold text-white">Status</label>
            <select 
              v-model="statusFilter" 
              class="w-full appearance-none backdrop-blur-sm bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/60 focus:ring-2 focus:ring-blue-400/50 focus:border-transparent transition-all duration-200"
            >
              <option value="all" class="bg-gray-800 text-white">All Status</option>
              <option value="pending" class="bg-gray-800 text-white">Pending</option>
              <option value="reviewed" class="bg-gray-800 text-white">Reviewed</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="block text-sm font-semibold text-white opacity-0">Action</label>
            <button
              @click="applyFilters"
              class="w-full backdrop-blur-sm bg-white/10 hover:bg-white/20 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 border border-white/20 hover:border-white/30 flex items-center justify-center gap-2"
            >
              <Filter class="w-4 h-4" />
              Apply Filters
            </button>
          </div>
        </div>
      </div>

      <!-- SF5 Reports Table -->
      <div v-if="typeFilter === 'sf5' || typeFilter === 'all'" class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <FileText class="w-5 h-5 text-purple-300" />
            <div>
              <h3 class="text-lg font-semibold text-white">SF5 Reports</h3>
              <p class="text-sm text-white/70 mt-1">{{ sf5Records.total || 0 }} reports submitted</p>
            </div>
          </div>
        </div>

        <div v-if="sf5Records.data?.length">
          <!-- Desktop View -->
          <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Class</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Teacher</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">School Year</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Average</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Created</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                <tr 
                  v-for="(record, index) in sf5Records.data"
                  :key="record.id"
                  class="hover:bg-white/5 transition-colors duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-white">{{ record.class?.name }}</div>
                    <div class="text-xs text-white/60">{{ record.class?.grade_level }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ record.teacher?.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ record.school_year }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-white">{{ record.class_average }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      v-if="record.status === 'reviewed'"
                      class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100"
                    >
                      <CheckCircle class="w-3 h-3" />
                      Reviewed
                    </span>
                    <span
                      v-else
                      class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium backdrop-blur-sm bg-yellow-500/20 border border-yellow-400/30 text-yellow-100"
                    >
                      <Clock class="w-3 h-3" />
                      Pending
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ new Date(record.created_at).toLocaleDateString() }}</div>
                    <div class="text-xs text-white/60">{{ new Date(record.created_at).toLocaleTimeString() }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center justify-center space-x-2">
                      <button
                        @click="viewDetails('sf5', record.id)"
                        class="p-2 rounded-lg backdrop-blur-sm bg-indigo-500/20 border border-indigo-400/30 text-indigo-100 hover:bg-indigo-500/30 transition-all duration-150"
                        title="View Details"
                      >
                        <Eye class="w-4 h-4" />
                      </button>
                      <a
                        :href="route('admin.records.download', { type: 'sf5', id: record.id })"
                        class="p-2 rounded-lg backdrop-blur-sm bg-blue-500/20 border border-blue-400/30 text-blue-100 hover:bg-blue-500/30 transition-all duration-150"
                        title="Download"
                      >
                        <Download class="w-4 h-4" />
                      </a>
                      <button
                        @click="toggleReviewed('sf5', record.id, index, true)"
                        class="p-2 rounded-lg backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100 hover:bg-green-500/30 transition-all duration-150"
                        title="Toggle Reviewed"
                      >
                        <RefreshCw class="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Cards -->
          <div class="lg:hidden divide-y divide-white/10">
            <div 
              v-for="(record, index) in sf5Records.data"
              :key="record.id"
              class="p-4 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex items-start justify-between mb-3">
                <div>
                  <h4 class="text-white font-medium">{{ record.class?.name }}</h4>
                  <p class="text-xs text-white/70">{{ record.teacher?.name }}</p>
                </div>
                <span
                  v-if="record.status === 'reviewed'"
                  class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100"
                >
                  <CheckCircle class="w-3 h-3" />
                  Reviewed
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium backdrop-blur-sm bg-yellow-500/20 border border-yellow-400/30 text-yellow-100"
                >
                  <Clock class="w-3 h-3" />
                  Pending
                </span>
              </div>
              <div class="space-y-1 mb-3">
                <div class="text-xs text-white/70">School Year: <span class="text-white">{{ record.school_year }}</span></div>
                <div class="text-xs text-white/70">Average: <span class="text-white font-semibold">{{ record.class_average }}</span></div>
                <div class="text-xs text-white/70">Created: <span class="text-white">{{ new Date(record.created_at).toLocaleString() }}</span></div>
              </div>
              <div class="flex items-center space-x-2 pt-3 border-t border-white/10">
                <button
                  @click="viewDetails('sf5', record.id)"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-indigo-500/20 border border-indigo-400/30 text-indigo-100 hover:bg-indigo-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <Eye class="w-4 h-4" />
                  View
                </button>
                <a
                  :href="route('admin.records.download', { type: 'sf5', id: record.id })"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-blue-500/20 border border-blue-400/30 text-blue-100 hover:bg-blue-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <Download class="w-4 h-4" />
                  Download
                </a>
                <button
                  @click="toggleReviewed('sf5', record.id, index, true)"
                  class="py-2 px-3 rounded-lg backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100 hover:bg-green-500/30 transition-all duration-150 text-sm"
                >
                  <RefreshCw class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-white/10" v-if="sf5Records.links?.length">
            <nav class="flex flex-wrap justify-left gap-1">
              <Link
                v-for="link in sf5Records.links"
                :key="link.label"
                :href="link.url || '#'"
                v-html="link.label"
                :class="[
                  'px-3 py-2 rounded-lg text-sm transition-all duration-200',
                  link.active 
                    ? 'backdrop-blur-sm bg-blue-500/30 border border-blue-400/50 text-white font-semibold' 
                    : 'backdrop-blur-sm bg-white/10 border border-white/20 text-white hover:bg-white/20',
                  !link.url && 'opacity-50 cursor-not-allowed',
                ]"
              />
            </nav>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8">
          <FileText class="mx-auto h-10 w-10 text-white/40 mb-3" />
          <h3 class="text-sm font-medium text-white/80">No SF5 reports found</h3>
          <p class="mt-1 text-sm text-white/60">Try adjusting your filters.</p>
        </div>
      </div>

      <!-- Honor Roll Table -->
      <div v-if="typeFilter === 'honor' || typeFilter === 'all'" class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center space-x-3">
            <FileText class="w-5 h-5 text-amber-300" />
            <div>
              <h3 class="text-lg font-semibold text-white">Honor Roll Lists</h3>
              <p class="text-sm text-white/70 mt-1">{{ honorLists.total || 0 }} lists submitted</p>
            </div>
          </div>
        </div>

        <div v-if="honorLists.data?.length">
          <!-- Desktop View -->
          <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Class</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Teacher</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Quarter</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">School Year</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Created</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                <tr 
                  v-for="(record, index) in honorLists.data"
                  :key="record.id"
                  class="hover:bg-white/5 transition-colors duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-white">{{ record.class?.name }}</div>
                    <div class="text-xs text-white/60">{{ record.class?.grade_level }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ record.teacher?.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ record.quarter }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ record.school_year }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      v-if="record.status === 'reviewed'"
                      class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100"
                    >
                      <CheckCircle class="w-3 h-3" />
                      Reviewed
                    </span>
                    <span
                      v-else
                      class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium backdrop-blur-sm bg-yellow-500/20 border border-yellow-400/30 text-yellow-100"
                    >
                      <Clock class="w-3 h-3" />
                      Pending
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ new Date(record.created_at).toLocaleDateString() }}</div>
                    <div class="text-xs text-white/60">{{ new Date(record.created_at).toLocaleTimeString() }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center justify-center space-x-2">
                      <button
                        @click="viewDetails('honor', record.id)"
                        class="p-2 rounded-lg backdrop-blur-sm bg-indigo-500/20 border border-indigo-400/30 text-indigo-100 hover:bg-indigo-500/30 transition-all duration-150"
                        title="View Details"
                      >
                        <Eye class="w-4 h-4" />
                      </button>
                      <a
                        :href="route('admin.records.download', { type: 'honor', id: record.id })"
                        class="p-2 rounded-lg backdrop-blur-sm bg-blue-500/20 border border-blue-400/30 text-blue-100 hover:bg-blue-500/30 transition-all duration-150"
                        title="Download"
                      >
                        <Download class="w-4 h-4" />
                      </a>
                      <button
                        @click="toggleReviewed('honor', record.id, index, false)"
                        class="p-2 rounded-lg backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100 hover:bg-green-500/30 transition-all duration-150"
                        title="Toggle Reviewed"
                      >
                        <RefreshCw class="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Cards -->
          <div class="lg:hidden divide-y divide-white/10">
            <div 
              v-for="(record, index) in honorLists.data"
              :key="record.id"
              class="p-4 hover:bg-white/5 transition-colors duration-150"
            >
              <div class="flex items-start justify-between mb-3">
                <div>
                  <h4 class="text-white font-medium">{{ record.class?.name }}</h4>
                  <p class="text-xs text-white/70">{{ record.teacher?.name }}</p>
                </div>
                <span
                  v-if="record.status === 'reviewed'"
                  class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100"
                >
                  <CheckCircle class="w-3 h-3" />
                  Reviewed
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium backdrop-blur-sm bg-yellow-500/20 border border-yellow-400/30 text-yellow-100"
                >
                  <Clock class="w-3 h-3" />
                  Pending
                </span>
              </div>
              <div class="space-y-1 mb-3">
                <div class="text-xs text-white/70">Quarter: <span class="text-white">{{ record.quarter }}</span></div>
                <div class="text-xs text-white/70">School Year: <span class="text-white">{{ record.school_year }}</span></div>
                <div class="text-xs text-white/70">Created: <span class="text-white">{{ new Date(record.created_at).toLocaleString() }}</span></div>
              </div>
              <div class="flex items-center space-x-2 pt-3 border-t border-white/10">
                <button
                  @click="viewDetails('honor', record.id)"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-indigo-500/20 border border-indigo-400/30 text-indigo-100 hover:bg-indigo-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <Eye class="w-4 h-4" />
                  View
                </button>
                <a
                  :href="route('admin.records.download', { type: 'honor', id: record.id })"
                  class="flex-1 py-2 px-3 rounded-lg backdrop-blur-sm bg-blue-500/20 border border-blue-400/30 text-blue-100 hover:bg-blue-500/30 transition-all duration-150 text-sm flex items-center justify-center gap-2"
                >
                  <Download class="w-4 h-4" />
                  Download
                </a>
                <button
                  @click="toggleReviewed('honor', record.id, index, false)"
                  class="py-2 px-3 rounded-lg backdrop-blur-sm bg-green-500/20 border border-green-400/30 text-green-100 hover:bg-green-500/30 transition-all duration-150 text-sm"
                >
                  <RefreshCw class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-white/10" v-if="honorLists.links?.length">
            <nav class="flex flex-wrap justify-left gap-1">
              <Link
                v-for="link in honorLists.links"
                :key="link.label"
                :href="link.url || '#'"
                v-html="link.label"
                :class="[
                  'px-3 py-2 rounded-lg text-sm transition-all duration-200',
                  link.active 
                    ? 'backdrop-blur-sm bg-blue-500/30 border border-blue-400/50 text-white font-semibold' 
                    : 'backdrop-blur-sm bg-white/10 border border-white/20 text-white hover:bg-white/20',
                  !link.url && 'opacity-50 cursor-not-allowed',
                ]"
              />
            </nav>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8">
          <FileText class="mx-auto h-10 w-10 text-white/40 mb-3" />
          <h3 class="text-sm font-medium text-white/80">No honor lists found</h3>
          <p class="mt-1 text-sm text-white/60">Try adjusting your filters.</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- Modal -->
  <RecordDetailsModal v-model:open="showModal" :data="modalData" />
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
select:focus-visible,
input:focus-visible,
a:focus-visible {
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