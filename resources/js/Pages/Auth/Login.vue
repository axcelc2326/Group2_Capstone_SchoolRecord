<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <!-- Page Header -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">
                Welcome Back
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Sign in to your school portal
            </p>
        </div>

        <!-- Status Message -->
        <div v-if="status" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 shadow-sm">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="ml-3 text-sm font-medium text-green-800">{{ status }}</p>
            </div>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="submit" class="space-y-6">
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
                                    autofocus
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
                                    autocomplete="current-password"
                                    placeholder="Enter your password"
                                />
                            </div>
                            <InputError class="mt-1" :message="form.errors.password" />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <Checkbox 
                                name="remember" 
                                v-model:checked="form.remember"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            />
                            <label class="ml-3 text-sm text-gray-600 cursor-pointer select-none">
                                Keep me signed in
                            </label>
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
                            {{ form.processing ? 'Signing in...' : 'Sign In' }}
                        </PrimaryButton>

                        <!-- Register Link -->
                        <div class="text-center pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600">
                                New to our school portal?
                                <Link
                                    :href="route('register')"
                                    class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors duration-200 ml-1"
                                >
                                    Create Account
                                </Link>
                            </p>
                        </div>
                    </form>
    </GuestLayout>
</template>