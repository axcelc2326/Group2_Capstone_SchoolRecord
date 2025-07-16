<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

// ✅ Search function
watch(search, (value) => {
    router.get(route('admin.users.index'), { search: value }, { preserveState: true, replace: true });
});
</script>

<template>
    <Head title="Manage Users" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold text-gray-800">🧑‍💼 Manage Users</h2>
        </template>

        <div class="py-8 px-10 space-y-6">
            <!-- ✅ Search -->
            <input
                type="text"
                v-model="search"
                placeholder="🔎 Search by name or email"
                class="border border-gray-300 p-3 rounded w-full mb-4"
            />

            <!-- ✅ User List -->
            <div v-for="(user, index) in users.data" :key="user.id" class="bg-white rounded-xl shadow p-6 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="space-y-1">
                        <h3 class="text-lg font-bold text-gray-800">👤 {{ user.name }}</h3>
                        <p class="text-sm text-gray-600">📧 {{ user.email }}</p>
                        <p class="text-sm font-medium text-blue-700">
                            🏷️ Role: <span class="capitalize">{{ user.role }}</span>
                        </p>
                    </div>

                    <div class="space-y-3 w-full md:w-1/3">
                        <Link
                            :href="route('admin.users.show', user.id)"
                            class="block text-center bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 rounded transition"
                        >
                            ✏️ Edit User
                        </Link>

                        <p v-if="user.role === 'admin'" class="text-xs text-gray-500 text-center">Admin role is locked</p>
                    </div>
                </div>
            </div>

            <!-- ✅ Pagination -->
            <div v-if="users.links" class="flex flex-wrap gap-2 justify-center pt-6">
                <Link
                    v-for="link in users.links"
                    :key="link.label"
                    :href="link.url"
                    v-html="link.label"
                    :class="['px-4 py-2 rounded text-sm', {
                        'bg-blue-500 text-white': link.active,
                        'bg-gray-200 text-gray-800': !link.active
                    }]"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
