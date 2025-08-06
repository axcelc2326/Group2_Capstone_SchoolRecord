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
        <div class="text-center mb-8 animate-fade-in">
            <h2 class="text-4xl font-bold bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent mb-3">
                Welcome Back
            </h2>
            <p class="text-lg text-white/70 font-medium">
                Sign in to your EduPortal account
            </p>
        </div>

        <!-- Status Message -->
        <div v-if="status" class="mb-6 bg-green-500/20 backdrop-blur-xl border border-green-400/30 rounded-2xl p-4 shadow-xl shadow-green-500/10 animate-slide-in">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gradient-to-r from-green-400 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/25">
                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <p class="ml-4 text-sm font-medium text-green-100">{{ status }}</p>
            </div>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="submit" class="space-y-6 animate-fade-in-up">
            <!-- Email Field -->
            <div class="space-y-3 group">
                <InputLabel for="email" value="Email Address" class="text-sm font-semibold text-white/90 group-focus-within:text-white transition-colors duration-300" />
                <div class="relative group-focus-within:scale-[1.02] transition-all duration-300">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                        <div class="p-1.5 bg-blue-500/20 rounded-lg group-focus-within:bg-blue-500/30 transition-all duration-300">
                            <svg class="h-4 w-4 text-blue-300 group-focus-within:text-blue-200 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="pl-16 pr-4 py-4 block w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl shadow-black/20 focus:bg-white/15 focus:border-blue-400/50 focus:ring-4 focus:ring-blue-500/20 text-white placeholder-white/50 transition-all duration-300 hover:bg-white/[0.12] hover:shadow-2xl"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="student@eduportal.edu"
                    />
                    <!-- Floating accent -->
                    <div class="absolute -top-1 -right-1 w-3 h-3 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full opacity-0 group-focus-within:opacity-100 animate-pulse transition-opacity duration-500"></div>
                </div>
                <InputError class="mt-2 text-red-300 bg-red-500/10 backdrop-blur-xl border border-red-400/20 rounded-xl px-3 py-2" :message="form.errors.email" />
            </div>

            <!-- Password Field -->
            <div class="space-y-3 group">
                <InputLabel for="password" value="Password" class="text-sm font-semibold text-white/90 group-focus-within:text-white transition-colors duration-300" />
                <div class="relative group-focus-within:scale-[1.02] transition-all duration-300">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                        <div class="p-1.5 bg-indigo-500/20 rounded-lg group-focus-within:bg-indigo-500/30 transition-all duration-300">
                            <svg class="h-4 w-4 text-indigo-300 group-focus-within:text-indigo-200 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                    </div>
                    <TextInput
                        id="password"
                        type="password"
                        class="pl-16 pr-4 py-4 block w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl shadow-black/20 focus:bg-white/15 focus:border-indigo-400/50 focus:ring-4 focus:ring-indigo-500/20 text-white placeholder-white/50 transition-all duration-300 hover:bg-white/[0.12] hover:shadow-2xl"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    />
                    <!-- Floating accent -->
                    <div class="absolute -top-1 -right-1 w-3 h-3 bg-gradient-to-r from-indigo-400 to-purple-500 rounded-full opacity-0 group-focus-within:opacity-100 animate-pulse transition-opacity duration-500"></div>
                </div>
                <InputError class="mt-2 text-red-300 bg-red-500/10 backdrop-blur-xl border border-red-400/20 rounded-xl px-3 py-2" :message="form.errors.password" />
            </div>

            <!-- Forgot Password Link -->
            <div v-if="canResetPassword" class="flex justify-end">
                <Link
                    :href="route('password.request')"
                    class="text-sm text-white/60 hover:text-white/90 transition-colors duration-300 hover:underline hover:scale-105 transform inline-block"
                >
                    Forgot your password?
                </Link>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 hover:bg-white/10 transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="relative">
                        <Checkbox 
                            name="remember" 
                            v-model:checked="form.remember"
                            class="h-5 w-5 text-blue-500 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-transparent border-white/30 rounded-lg bg-white/10 backdrop-blur-xl transition-all duration-300 hover:scale-110"
                        />
                    </div>
                    <label class="ml-4 text-sm text-white/70 cursor-pointer select-none group-hover:text-white/90 transition-colors duration-300 font-medium">
                        Keep me signed in
                    </label>
                </div>
                <div class="w-2 h-2 bg-blue-400/30 rounded-full animate-pulse group-hover:bg-blue-400/50 transition-colors duration-300"></div>
            </div>

            <!-- Submit Button -->
            <PrimaryButton
                type="submit"
                class="w-full group relative overflow-hidden bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold py-4 px-6 rounded-2xl shadow-2xl shadow-blue-500/25 hover:shadow-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition-all duration-500 transform hover:scale-[1.02] hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:translate-y-0"
                :disabled="form.processing"
            >
                <!-- Button background effects -->
                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 transform -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-blue-400/0 via-blue-400/30 to-blue-400/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative flex items-center justify-center space-x-3">
                    <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                    <svg v-else class="h-5 w-5 text-white group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <span class="text-lg font-bold">{{ form.processing ? 'Signing in...' : 'Sign In' }}</span>
                </div>
            </PrimaryButton>

            <!-- Register Link -->
            <div class="text-center pt-6 border-t border-white/20 relative">
                <!-- Decorative elements -->
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 flex space-x-1">
                    <div class="w-2 h-2 bg-blue-400/40 rounded-full animate-pulse"></div>
                    <div class="w-1 h-1 bg-indigo-400/40 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                    <div class="w-2 h-2 bg-purple-400/40 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
                </div>
                
                <p class="text-base text-white/70 mb-4">
                    New to our EduPortal?
                </p>
                <Link
                    :href="route('register')"
                    class="inline-flex items-center space-x-2 font-semibold text-white bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl px-6 py-3 hover:bg-white/20 hover:border-white/30 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl shadow-black/20 hover:shadow-2xl hover:shadow-black/30 group"
                >
                    <svg class="h-5 w-5 text-white/70 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span>Create Account</span>
                    <svg class="h-4 w-4 text-white/50 group-hover:text-white/80 group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </Link>
            </div>
        </form>

        <!-- Floating decorative elements -->
        <div class="absolute -top-4 -left-2 w-1 h-8 bg-gradient-to-b from-blue-400/40 to-transparent rounded-full animate-float opacity-60"></div>
        <div class="absolute -bottom-2 -right-4 w-2 h-2 bg-indigo-400/50 rounded-full animate-float animate-pulse opacity-70" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/3 -left-6 w-1.5 h-1.5 bg-purple-400/40 rounded-full animate-float animate-pulse opacity-50" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-1/4 -right-6 w-1 h-6 bg-gradient-to-t from-blue-500/30 to-transparent rounded-full animate-float opacity-60" style="animation-delay: 0.5s;"></div>
    </GuestLayout>
</template>

<style scoped>
/* Custom animations */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-in {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes float {
    0%, 100% { 
        transform: translateY(0px) rotate(0deg); 
    }
    33% {
        transform: translateY(-8px) rotate(1deg);
    }
    66% {
        transform: translateY(4px) rotate(-1deg);
    }
}

@keyframes shimmer {
    0% {
        background-position: -200px 0;
    }
    100% {
        background-position: 200px 0;
    }
}

@keyframes pulse-glow {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
        box-shadow: 0 0 5px currentColor;
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
        box-shadow: 0 0 20px currentColor;
    }
}

/* Animation classes */
.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out 0.2s both;
}

.animate-slide-in {
    animation: slide-in 0.5s ease-out;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-pulse {
    animation: pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Enhanced backdrop blur support */
@supports (backdrop-filter: blur(20px)) {
    .backdrop-blur-xl {
        backdrop-filter: blur(20px);
    }
}

/* Input field enhancements */
input:focus {
    transform: scale(1.02);
}

/* Button shimmer effect */
.shimmer {
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    background-size: 200px 100%;
    animation: shimmer 2s infinite;
}

/* Custom focus states */
input:focus,
button:focus,
a:focus {
    outline: none;
}

/* Smooth transitions for all interactive elements */
* {
    transition-property: color, background-color, border-color, transform, opacity, box-shadow;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Enhanced hover effects */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Micro-interactions */
.group:active {
    transform: scale(0.98);
    transition-duration: 100ms;
}

/* Ensure animations respect reduced motion preferences */
@media (prefers-reduced-motion: reduce) {
    .animate-fade-in,
    .animate-fade-in-up,
    .animate-slide-in,
    .animate-float,
    .animate-pulse {
        animation: none !important;
    }
    
    .group:hover .group-hover\:scale-105,
    .group:hover .group-hover\:scale-110,
    input:focus {
        transform: none !important;
    }
}

/* Enhanced glass effect for inputs */
.glass-input {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.glass-input:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(59, 130, 246, 0.5);
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);
}

/* Custom scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
}

/* Loading state enhancements */
@keyframes spin-glow {
    from {
        transform: rotate(0deg);
        filter: drop-shadow(0 0 5px currentColor);
    }
    to {
        transform: rotate(360deg);
        filter: drop-shadow(0 0 10px currentColor);
    }
}

.animate-spin {
    animation: spin-glow 1s linear infinite;
}
</style>