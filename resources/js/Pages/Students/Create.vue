<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const { props } = usePage();

const form = useForm({
    first_name: '',
    last_name: '',
    class_id: '',
});

const submit = () => {
    form.post(route('students.store'));
};
</script>

<template>
    <Head title="Register Student" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">
                Register a Student
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded shadow-sm">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- First Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">First Name</label>
                            <input v-model="form.first_name" type="text" class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                            <div v-if="form.errors.first_name" class="text-red-600 text-sm mt-1">{{ form.errors.first_name }}</div>
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input v-model="form.last_name" type="text" class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                            <div v-if="form.errors.last_name" class="text-red-600 text-sm mt-1">{{ form.errors.last_name }}</div>
                        </div>

                        <!-- Class/Grade Level Dropdown -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Select Class / Grade Level</label>
                            <select v-model="form.class_id" class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                                <option disabled value="">-- Choose Class --</option>
                                <option v-for="cls in props.classes" :key="cls.id" :value="cls.id">
                                    {{ cls.name }} (Grade {{ cls.grade_level }})
                                </option>
                            </select>
                            <div v-if="form.errors.class_id" class="text-red-600 text-sm mt-1">{{ form.errors.class_id }}</div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Register Student
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
