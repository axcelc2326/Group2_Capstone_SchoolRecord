<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const { props } = usePage();
const roles = props.auth.user?.roles?.map(role => role.name) || [];
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white p-6 shadow sm:rounded-lg">
          <p class="text-gray-900">
            You're logged in as <strong>{{ roles[0] ?? 'No Role' }}</strong>
          </p>
        </div>

        <div class="bg-white p-6 shadow sm:rounded-lg">
          <h3 class="text-lg font-semibold mb-4">Quick Access</h3>
          <ul class="space-y-2 list-disc list-inside text-blue-600">
            <template v-if="roles.includes('parent')">
              <li>
                <Link :href="route('students.create')" class="hover:underline">
                  ➕ Register Student
                </Link>
              </li>
              <li>
                <Link :href="route('announcements.index')" class="hover:underline">
                  📢 View Announcements
                </Link>
              </li>
            </template>

            <template v-if="roles.includes('teacher')">
              <li>
                <Link :href="route('teacher.students')" class="hover:underline">
                  👨‍🏫 View My Students
                </Link>
              </li>
              <li>
                <Link :href="route('students.approval.list')" class="hover:underline">
                  👩‍🏫 Approve Students
                </Link>
              </li>
              <li>
                <Link :href="route('sf5.download', { class: 1 })" class="hover:underline">
                  🧾 Download SF5 (Replace class ID)
                </Link>
              </li>
            </template>

            <template v-if="roles.includes('admin')">
              <li>
                <Link :href="route('classes.create')" class="hover:underline">
                  🏫 Create Class
                </Link>
              </li>
              <li>
                <Link :href="route('admin.assign-teacher')" class="hover:underline">
                  🧑‍🏫 Assign Teacher to Class
                </Link>
              </li>
              <li>
                <Link :href="route('announcements.create')" class="hover:underline">
                  📢 Create Announcement
                </Link>
              </li>
              <li>
                <Link :href="route('analytics.index')" class="hover:underline">
                  📊 View Analytics
                </Link>
              </li>
            </template>
          </ul>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
