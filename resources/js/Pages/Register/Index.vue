<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import RegisterModal from '@/Components/RegisterModal.vue'
import ManageParentModal from '@/Components/ManageParentModal.vue'
import EditParentModal from '@/Components/EditParentModal.vue'
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  parents: Object,
  classes: Array,
  filters: Object,
  totalMadeParents: Number,
})

const showRegisterModal = ref(false)
const showManageModal = ref(false)
const showEditParentModal = ref(false)
const selectedParent = ref(null)

const search = ref(props.filters.search || '')
const selectedClass = ref(props.filters.class_id || '')

// Statistics
const totalParents = computed(() => props.parents?.data?.length || 0)

// Filter & search
const applyFilters = () => {
  router.get(route('parents.index'), 
    { search: search.value, class_id: selectedClass.value }, 
    { preserveState: true, replace: true }
  )
}

// Open Manage modal
const openManageModal = (parent) => {
  selectedParent.value = parent
  showManageModal.value = true
}

// Open Edit modal
const openEditParentModal = (parent) => {
  selectedParent.value = parent
  showEditParentModal.value = true
}

// Delete parent
const confirmDeleteParent = (parentId) => {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will delete the parent and all their students!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('parents.destroy', parentId), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Parent deleted successfully',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
          })
        }
      })
    }
  })
}
</script>

<template>
  <Head title="Parents Management" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0">
        <h2 class="text-2xl font-bold text-white bg-gradient-to-r from-white to-white/70 bg-clip-text text-transparent">
          Parents Management
        </h2>
        <div class="flex items-center space-x-4">
          <!-- Quick Stats -->
          <div class="hidden md:flex items-center space-x-3 text-sm">
            <div class="backdrop-blur-sm bg-blue-500/20 border border-blue-300/30 px-3 py-1 rounded-full">
              <span class="text-blue-100">{{ totalMadeParents }} Total Created</span>
            </div>
            <div class="backdrop-blur-sm bg-emerald-500/20 border border-emerald-300/30 px-3 py-1 rounded-full">
              <span class="text-emerald-100">{{ totalParents }} Active</span>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Mobile Stats (visible on small screens) -->
      <div class="md:hidden grid grid-cols-2 gap-4">
        <div class="backdrop-blur-md bg-blue-500/10 border border-blue-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-blue-100">{{ totalMadeParents }}</div>
          <div class="text-sm text-blue-200">Total Created</div>
        </div>
        <div class="backdrop-blur-md bg-emerald-500/10 border border-emerald-300/30 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-emerald-100">{{ totalParents }}</div>
          <div class="text-sm text-emerald-200">Active Parents</div>
        </div>
      </div>

      <!-- Search and Filters -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
          <!-- Search and Filter Controls -->
          <div class="flex flex-col md:flex-row md:items-center gap-4 flex-1">
            <div class="relative">
              <input
                v-model="search"
                @keyup.enter="applyFilters"
                type="text"
                placeholder="Search parents..."
                class="w-full md:w-64 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40 transition-all duration-150"
              />
              <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            
            <select
              v-model="selectedClass"
              @change="applyFilters"
              class="w-full md:w-64 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40 transition-all duration-150 appearance-none"
            >
              <option value="" class="bg-gray-800 text-white">All Classes</option>
              <option 
                v-for="cls in props.classes" 
                :key="cls.id" 
                :value="cls.id"
                class="bg-gray-800 text-white"
              >
                {{ cls.name }} (Grade {{ cls.grade_level }})
              </option>
            </select>
            
            <button 
              @click="applyFilters"
              class="px-6 py-3 bg-blue-500/20 hover:bg-blue-500/30 text-blue-100 border border-blue-400/30 hover:border-blue-400/50 rounded-xl transition-all duration-150 backdrop-blur-sm"
            >
              <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
              </svg>
              Filter
            </button>
          </div>

          <!-- Register Button -->
          <button
            @click="showRegisterModal = true"
            class="px-6 py-3 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-100 border border-emerald-400/30 hover:border-emerald-400/50 rounded-xl transition-all duration-150 backdrop-blur-sm"
          >
            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Register Parent
          </button>
        </div>
      </div>

      <!-- Parents Table -->
      <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
          <h3 class="text-lg font-semibold text-white">Parents Directory</h3>
          <p class="text-sm text-white/70 mt-1">Manage and oversee parent accounts</p>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
          <table class="min-w-full divide-y divide-white/10">
            <thead class="bg-white/5">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Parent</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Students</th>
                <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              <tr
                v-for="parent in props.parents.data"
                :key="parent.id"
                class="hover:bg-white/5 transition-colors duration-150"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center text-white font-medium text-sm">
                        {{ parent.name.charAt(0) }}
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-white">{{ parent.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-white">{{ parent.email }}</div>
                </td>
                <td class="px-6 py-4">
                  <div v-if="parent.students.length" class="flex flex-wrap gap-1">
                    <span
                      v-for="student in parent.students"
                      :key="student.id"
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-500/20 text-indigo-100 border border-indigo-400/30 backdrop-blur-sm"
                    >
                      {{ student.first_name }} {{ student.last_name }}
                    </span>
                  </div>
                  <span v-else class="text-white/50 italic text-sm">No students assigned</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                  <button
                    @click="openManageModal(parent)"
                    class="inline-flex items-center px-3 py-2 bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-100 border border-indigo-400/30 hover:border-indigo-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Manage
                  </button>
                  <button
                    @click="openEditParentModal(parent)"
                    class="inline-flex items-center px-3 py-2 bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-100 border border-yellow-400/30 hover:border-yellow-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                  </button>
                  <button
                    @click="() => confirmDeleteParent(parent.id)"
                    class="inline-flex items-center px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-100 border border-red-400/30 hover:border-red-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-white/10">
          <div 
            v-for="parent in props.parents.data" 
            :key="parent.id"
            class="p-6 hover:bg-white/5 transition-colors duration-150"
          >
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-br from-purple-400 to-pink-600 flex items-center justify-center text-white font-medium">
                  {{ parent.name.charAt(0) }}
                </div>
                <div>
                  <h4 class="text-white font-medium">{{ parent.name }}</h4>
                  <p class="text-sm text-white/70">{{ parent.email }}</p>
                </div>
              </div>
            </div>
            
            <!-- Students -->
            <div class="mb-4">
              <p class="text-sm text-white/60 mb-2">Students:</p>
              <div v-if="parent.students.length" class="flex flex-wrap gap-1">
                <span
                  v-for="student in parent.students"
                  :key="student.id"
                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-500/20 text-indigo-100 border border-indigo-400/30 backdrop-blur-sm"
                >
                  {{ student.first_name }} {{ student.last_name }}
                </span>
              </div>
              <span v-else class="text-white/50 italic text-sm">No students assigned</span>
            </div>
            
            <div class="flex space-x-2">
              <button
                @click="openManageModal(parent)"
                class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-100 border border-indigo-400/30 hover:border-indigo-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Manage
              </button>
              <button
                @click="openEditParentModal(parent)"
                class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-100 border border-yellow-400/30 hover:border-yellow-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
              </button>
              <button
                @click="() => confirmDeleteParent(parent.id)"
                class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-100 border border-red-400/30 hover:border-red-400/50 rounded-lg transition-all duration-150 backdrop-blur-sm"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!props.parents?.data?.length" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-white/80">
            No parents found
          </h3>
          <p class="mt-1 text-sm text-white/60">
            Start by registering your first parent account.
          </p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="props.parents?.links?.length > 3" class="flex justify-center">
        <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl p-2 shadow-2xl">
          <div class="flex space-x-2">
            <Link
              v-for="link in props.parents.links"
              :key="link.url"
              :href="link.url || ''"
              v-html="link.label"
              class="px-4 py-2 rounded-xl transition-all duration-150"
              :class="{
                'bg-blue-500/30 text-blue-100 border border-blue-400/50 backdrop-blur-sm': link.active,
                'text-white/70 hover:bg-white/10 hover:text-white border border-transparent': !link.active && link.url,
                'text-white/40 cursor-not-allowed': !link.url
              }"
            />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- Modals -->
  <RegisterModal 
    :show="showRegisterModal" 
    @close="showRegisterModal = false"
  />
  <ManageParentModal
    v-if="selectedParent"
    :show="showManageModal"
    :parent="selectedParent"
    :classes="props.classes"
    @close="showManageModal = false"
  />
  <EditParentModal
    v-if="selectedParent"
    :show="showEditParentModal"
    :parent="selectedParent"
    @close="showEditParentModal = false"
    @updated="(updatedParent) => {
        const index = props.parents.data.findIndex(p => p.id === updatedParent.id);
        if (index !== -1) props.parents.data[index] = { ...updatedParent };
        showEditParentModal.value = false;
    }"
  />
</template>