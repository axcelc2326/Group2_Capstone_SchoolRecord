<script setup>
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, watch, computed } from 'vue'
import Swal from 'sweetalert2'

const page = usePage()
const props = defineProps({
  classes: Object,
})

const classes = ref(props.classes)
const isLoading = ref(false)
const searchQuery = ref('')

// Watch for changes when Inertia reloads
watch(
  () => page.props.classes,
  (newVal) => {
    classes.value = newVal
  }
)

// Filter classes based on grade level
const filteredClasses = computed(() => {
  if (!searchQuery.value) return classes.value.data
  
  return classes.value.data.filter(classItem =>
    classItem.grade_level.toString().includes(searchQuery.value)
  )
})

const form = useForm({
  name: '',
  grade_level: '',
})

function submit() {
  isLoading.value = true
  form.post(route('classes.store'), {
    onSuccess: () => {
      form.reset()
      isLoading.value = false
      // Modern success notification
      Swal.fire({
        icon: 'success',
        title: 'Class Created!',
        text: 'Your new class has been successfully created.',
        timer: 2000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
      })
    },
    onError: () => {
      isLoading.value = false
    }
  })
}

function deleteClass(classItem) {
  Swal.fire({
    title: `Delete "${classItem.name}"?`,
    text: 'This action cannot be undone. All associated data will be permanently removed.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, delete it',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
    customClass: {
      popup: 'rounded-xl',
      confirmButton: 'rounded-lg font-medium',
      cancelButton: 'rounded-lg font-medium'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      isLoading.value = true
      router.delete(route('classes.destroy', classItem.id), {
        onSuccess: () => {
          isLoading.value = false
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: `"${classItem.name}" has been removed.`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
          })
          router.reload({ only: ['classes'] })
        },
        onError: () => {
          isLoading.value = false
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to delete the class. Please try again.',
            toast: true,
            position: 'top-end'
          })
        }
      })
    }
  })
}

// Grade level options for better UX
const gradeOptions = Array.from({ length: 6 }, (_, i) => i + 1)
</script>

<template>
  <Head title="Class Management" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Class Management</h2>
            <p class="text-sm text-gray-600">Create and manage your classes</p>
          </div>
        </div>
      </div>
    </template>

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <div class="space-y-8">
        
        <!-- Create Class Form -->
        <div class="max-w-xl mx-auto">
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center space-x-2 mb-6">
              <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900">Create New Class</h3>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Class Name</label>
                <input
                  v-model="form.name"
                  type="text"
                  placeholder="e.g., Section A, Section B, Section C"
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 placeholder-gray-400"
                  :class="{ 'border-red-300 bg-red-50': form.errors.name }"
                />
                <div v-if="form.errors.name" class="flex items-center space-x-1 text-red-600 text-sm">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                  </svg>
                  <span>{{ form.errors.name }}</span>
                </div>
              </div>

              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Grade Level</label>
                <select
                  v-model="form.grade_level"
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                  :class="{ 'border-red-300 bg-red-50': form.errors.grade_level }"
                >
                  <option value="">Select grade level</option>
                  <option v-for="grade in gradeOptions" :key="grade" :value="grade">
                    Grade {{ grade }}
                  </option>
                </select>
                <div v-if="form.errors.grade_level" class="flex items-center space-x-1 text-red-600 text-sm">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                  </svg>
                  <span>{{ form.errors.grade_level }}</span>
                </div>
              </div>

              <button
                type="submit"
                :disabled="isLoading || !form.name || !form.grade_level"
                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-200 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
              >
                <svg v-if="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ isLoading ? 'Creating...' : 'Create Class' }}</span>
              </button>
            </form>
          </div>
        </div>

        <!-- Classes List -->
        <div class="max-w-4xl mx-auto">
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header with search -->
            <div class="p-6 border-b border-gray-100">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-2">
                  <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900">Your Classes</h3>
                    <p class="text-sm text-gray-500">{{ classes.data.length }} total classes</p>
                  </div>
                </div>
                
                <div class="relative">
                  <select
                    v-model="searchQuery"
                    class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-64"
                  >
                    <option value="">All grades</option>
                    <option v-for="grade in gradeOptions" :key="grade" :value="grade.toString()">
                      Grade {{ grade }}
                    </option>
                  </select>
                  <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Classes Grid -->
            <div class="p-6">
              <div v-if="filteredClasses.length === 0 && !searchQuery" class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No classes yet</h3>
                <p class="text-gray-500">Create your first class to get started!</p>
              </div>

              <div v-else-if="filteredClasses.length === 0 && searchQuery" class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No classes found</h3>
                <p class="text-gray-500">No classes found for Grade {{ searchQuery }}</p>
              </div>

              <div v-else class="grid grid-cols-1 gap-4">
                <div
                  v-for="classItem in filteredClasses"
                  :key="classItem.id"
                  class="group bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-5 hover:shadow-md hover:border-gray-300 transition duration-200"
                >
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                      <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                        {{ classItem.grade_level }}
                      </div>
                      <div>
                        <h4 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition duration-200">
                          {{ classItem.name }}
                        </h4>
                        <p class="text-sm text-gray-500">Grade {{ classItem.grade_level }}</p>
                      </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                      <Link 
                        :href="route('classes.edit', classItem.id)"
                        class="px-4 py-2 bg-amber-100 text-amber-700 rounded-lg text-sm font-medium hover:bg-amber-200 transition duration-200 flex items-center space-x-1"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Edit</span>
                      </Link>

                      <button 
                        @click="deleteClass(classItem)"
                        class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200 transition duration-200 flex items-center space-x-1"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Delete</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Enhanced Pagination -->
            <div v-if="classes.links.length > 3" class="px-6 py-4 border-t border-gray-100 bg-gray-50">
              <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                  Showing {{ classes.from || 0 }} to {{ classes.to || 0 }} of {{ classes.total || 0 }} results
                </div>
                <div class="flex items-center space-x-2">
                  <template v-for="(link, index) in classes.links" :key="index">
                    <span
                      v-if="!link.url"
                      class="px-3 py-2 text-gray-400 cursor-not-allowed text-sm"
                      v-html="link.label"
                    ></span>
                    <Link
                      v-else
                      :href="link.url"
                      class="px-3 py-2 rounded-lg text-sm font-medium transition duration-200"
                      :class="link.active 
                        ? 'bg-blue-600 text-white' 
                        : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
                      v-html="link.label"
                    />
                  </template>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>