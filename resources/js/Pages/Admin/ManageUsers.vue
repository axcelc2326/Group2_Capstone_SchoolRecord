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
                        <!-- ✅ Conditional Edit -->
                        <template v-if="user.role !== 'admin'">
                            <Link
                                :href="route('admin.users.show', user.id)"
                                class="block text-center bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 rounded transition"
                            >
                                ✏️ Edit User
                            </Link>
                        </template>
                        <template v-else>
                            <button
                                disabled
                                class="block text-center w-full bg-gray-300 text-gray-600 font-medium py-2 rounded cursor-not-allowed"
                            >
                                🔒 Admin Cannot be Edited
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- ✅ Pagination Fixed -->
            <div v-if="users.links" class="flex flex-wrap gap-2 justify-center pt-6">
                <template v-for="link in users.links" :key="link.label">
                    <span
                        v-if="link.url === null"
                        class="px-4 py-2 text-gray-500 text-sm"
                        v-html="link.label"
                    />
                    <Link
                        v-else
                        :href="link.url"
                        v-html="link.label"
                        :class="[
                            'px-4 py-2 rounded text-sm',
                            link.active ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
                        ]"
                    />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
