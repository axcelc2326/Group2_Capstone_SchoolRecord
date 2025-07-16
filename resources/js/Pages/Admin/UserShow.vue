<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    roles: Array, // ['teacher', 'parent']
});

const form = ref({
    name: props.user.name,
    email: props.user.email,
    password: '',
    role: props.user.role,
});

const updateUser = () => {
    router.put(route('admin.users.update', props.user.id), form.value);
};

const deleteUser = () => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('admin.users.destroy', props.user.id));
    }
};
</script>

<template>
    <Head title="Edit User" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-800">🧑‍💼 Edit User: {{ user.name }}</h2>
        </template>

        <div class="p-8 max-w-xl mx-auto space-y-6 bg-white rounded shadow">
            <div>
                <label class="block text-sm font-medium">Name</label>
                <input v-model="form.name" type="text" class="border p-2 rounded w-full" />
            </div>

            <div>
                <label class="block text-sm font-medium">Email</label>
                <input v-model="form.email" type="email" class="border p-2 rounded w-full" />
            </div>

            <div>
                <label class="block text-sm font-medium">Password (leave blank to keep current)</label>
                <input v-model="form.password" type="password" class="border p-2 rounded w-full" />
            </div>

            <div>
                <label class="block text-sm font-medium">Role</label>
                <select v-model="form.role" class="border p-2 rounded w-full">
                    <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                </select>
            </div>

            <div class="flex justify-between pt-4">
                <button @click="updateUser" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update</button>
                <button @click="deleteUser" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">Delete</button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
