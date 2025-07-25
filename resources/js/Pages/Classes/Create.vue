<script setup>
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, watch } from 'vue'
import Swal from 'sweetalert2'

const page = usePage()
const props = defineProps({
  classes: Object,
})

const classes = ref(props.classes)

// 👀 Watch for changes when Inertia reloads
watch(
  () => page.props.classes,
  (newVal) => {
    classes.value = newVal
  }
)

const form = useForm({
  name: '',
  grade_level: '',
})

function submit() {
  form.post(route('classes.store'), {
    onSuccess: () => form.reset()
  })
}

function deleteClass(id) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This class will be permanently deleted.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, delete it!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('classes.destroy', id), {
        onSuccess: () => {
          Swal.fire('Deleted!', 'The class has been deleted.', 'success')
          router.reload({ only: ['classes'] })
        }
      })
    }
  })
}
</script>

<template>
  <Head title="Create Class" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">🏫 Create a New Class</h2>
    </template>

    <!-- Create Class Form -->
    <div class="max-w-xl mx-auto py-8">
      <form @submit.prevent="submit" class="space-y-4 bg-white p-6 rounded shadow">
        <div>
          <label class="block font-medium text-gray-700">Class Name</label>
          <input
            v-model="form.name"
            type="text"
            class="mt-1 block w-full border rounded p-2"
          />
          <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
            {{ form.errors.name }}
          </div>
        </div>

        <div>
          <label class="block font-medium text-gray-700">Grade Level</label>
          <input
            v-model="form.grade_level"
            type="number"
            min="1"
            max="12"
            class="mt-1 block w-full border rounded p-2"
          />
          <div v-if="form.errors.grade_level" class="text-red-600 text-sm mt-1">
            {{ form.errors.grade_level }}
          </div>
        </div>

        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Create Class
        </button>
      </form>
    </div>

    <!-- List of Created Classes -->
<div class="max-w-4xl mx-auto mt-10 bg-white p-6 rounded shadow space-y-4">
  <h2 class="text-lg font-bold">📚 Created Classes</h2>

  <div v-if="classes.data.length === 0" class="text-gray-500">
    No classes created yet.
  </div>

  <div v-else>
    <div
      v-for="classItem in classes.data"
      :key="classItem.id"
      class="flex justify-between items-center border-b pb-2"
    >
      <!-- Class name with actions beside -->
      <div class="flex flex-wrap items-center gap-2 font-medium">
        {{ classItem.name }} - Grade {{ classItem.grade_level }}

        <Link 
          :href="route('classes.edit', classItem.id)"
          class="px-2 py-1 bg-yellow-400 text-white rounded text-xs hover:bg-yellow-500"
        >
          Edit
        </Link>

        <button 
          @click="deleteClass(classItem.id)"
          class="px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700"
        >
          Delete
        </button>
      </div>
    </div>
  </div>

  <!-- Pagination -->
  <div v-if="classes.links.length > 3" class="flex justify-center space-x-1 mt-4 flex-wrap">
    <template v-for="(link, index) in classes.links" :key="index">
      <span
        v-if="!link.url"
        class="px-3 py-1 text-gray-500 cursor-default"
        v-html="link.label"
      ></span>
      <Link
        v-else
        :href="link.url"
        class="px-3 py-1 rounded"
        :class="link.active ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
        v-html="link.label"
      />
    </template>
  </div>
</div>
  </AuthenticatedLayout>
</template>
