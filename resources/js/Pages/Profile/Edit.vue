<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { User } from 'lucide-vue-next';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0">
                <h2 class="text-2xl font-bold text-white bg-gradient-to-r from-white to-white/70 bg-clip-text text-transparent">
                    👤 Profile Settings
                </h2>
            </div>
        </template>

        <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Information Section -->
            <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <User class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Profile Information</h3>
                            <p class="text-sm text-white/70 mt-1">Update your account's profile information and email address</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>
            </div>

            <!-- Update Password Section -->
            <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Update Password</h3>
                            <p class="text-sm text-white/70 mt-1">Ensure your account is using a long, random password to stay secure</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>
            </div>

            <!-- Delete Account Section -->
            <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Delete Account</h3>
                            <p class="text-sm text-white/70 mt-1">Permanently delete your account and all associated data</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>