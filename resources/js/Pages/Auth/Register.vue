<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <!-- Page Header -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">
                Create Account
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Join our school portal today
            </p>
        </div>

        <!-- Register Form -->
        <form @submit.prevent="submit" class="space-y-6">
            <!-- Name Field -->
            <div class="space-y-2">
                <InputLabel for="name" value="Full Name" class="text-sm font-semibold text-gray-700" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <TextInput
                        id="name"
                        type="text"
                        class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Enter your full name"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.name" />
            </div>

            <!-- Email Field -->
            <div class="space-y-2">
                <InputLabel for="email" value="Email Address" class="text-sm font-semibold text-gray-700" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="student@school.edu"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
                <InputLabel for="password" value="Password" class="text-sm font-semibold text-gray-700" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <TextInput
                        id="password"
                        type="password"
                        class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="Create a strong password"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <!-- Confirm Password Field -->
            <div class="space-y-2">
                <InputLabel for="password_confirmation" value="Confirm Password" class="text-sm font-semibold text-gray-700" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm your password"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.password_confirmation" />
            </div>

            <!-- Submit Button -->
            <PrimaryButton
                type="submit"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105"
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
            >
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
                {{ form.processing ? 'Creating Account...' : 'Create Account' }}
            </PrimaryButton>

            <!-- Login Link -->
            <div class="text-center pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <Link
                        :href="route('login')"
                        class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors duration-200 ml-1"
                    >
                        Sign In
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>