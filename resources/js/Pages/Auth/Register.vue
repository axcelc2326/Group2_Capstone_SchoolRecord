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
        <div class="text-center mb-8 animate-fade-in">
            <h2 class="text-4xl font-bold bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent mb-3">
                Join EduPortal
            </h2>
            <p class="text-lg text-white/70 font-medium">
                Create your learning account today
            </p>
        </div>

        <!-- Register Form -->
        <form @submit.prevent="submit" class="space-y-6 animate-fade-in-up">
            <!-- Name Field -->
            <div class="space-y-3 group">
                <InputLabel for="name" value="Full Name" class="text-sm font-semibold text-white/90 group-focus-within:text-white transition-colors duration-300" />
                <div class="relative group-focus-within:scale-[1.02] transition-all duration-300">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                        <div class="p-1.5 bg-emerald-500/20 rounded-lg group-focus-within:bg-emerald-500/30 transition-all duration-300">
                            <svg class="h-4 w-4 text-emerald-300 group-focus-within:text-emerald-200 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                    <TextInput
                        id="name"
                        type="text"
                        class="pl-16 pr-4 py-4 block w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl shadow-black/20 focus:bg-white/15 focus:border-emerald-400/50 focus:ring-4 focus:ring-emerald-500/20 text-white placeholder-white/50 transition-all duration-300 hover:bg-white/[0.12] hover:shadow-2xl"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Enter your full name"
                    />
                    <!-- Floating accent -->
                    <div class="absolute -top-1 -right-1 w-3 h-3 bg-gradient-to-r from-emerald-400 to-green-500 rounded-full opacity-0 group-focus-within:opacity-100 animate-pulse transition-opacity duration-500"></div>
                </div>
                <InputError class="mt-2 text-red-300 bg-red-500/10 backdrop-blur-xl border border-red-400/20 rounded-xl px-3 py-2" :message="form.errors.name" />
            </div>

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
                        autocomplete="new-password"
                        placeholder="Create a strong password"
                    />
                    <!-- Floating accent -->
                    <div class="absolute -top-1 -right-1 w-3 h-3 bg-gradient-to-r from-indigo-400 to-purple-500 rounded-full opacity-0 group-focus-within:opacity-100 animate-pulse transition-opacity duration-500"></div>
                </div>
                <InputError class="mt-2 text-red-300 bg-red-500/10 backdrop-blur-xl border border-red-400/20 rounded-xl px-3 py-2" :message="form.errors.password" />
            </div>

            <!-- Confirm Password Field -->
            <div class="space-y-3 group">
                <InputLabel for="password_confirmation" value="Confirm Password" class="text-sm font-semibold text-white/90 group-focus-within:text-white transition-colors duration-300" />
                <div class="relative group-focus-within:scale-[1.02] transition-all duration-300">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                        <div class="p-1.5 bg-purple-500/20 rounded-lg group-focus-within:bg-purple-500/30 transition-all duration-300">
                            <svg class="h-4 w-4 text-purple-300 group-focus-within:text-purple-200 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="pl-16 pr-4 py-4 block w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl shadow-black/20 focus:bg-white/15 focus:border-purple-400/50 focus:ring-4 focus:ring-purple-500/20 text-white placeholder-white/50 transition-all duration-300 hover:bg-white/[0.12] hover:shadow-2xl"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm your password"
                    />
                    <!-- Floating accent -->
                    <div class="absolute -top-1 -right-1 w-3 h-3 bg-gradient-to-r from-purple-400 to-pink-500 rounded-full opacity-0 group-focus-within:opacity-100 animate-pulse transition-opacity duration-500"></div>
                </div>
                <InputError class="mt-2 text-red-300 bg-red-500/10 backdrop-blur-xl border border-red-400/20 rounded-xl px-3 py-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- Security Info -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 hover:bg-white/10 transition-all duration-300 group">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="w-8 h-8 bg-gradient-to-r from-cyan-400/20 to-blue-400/20 rounded-xl flex items-center justify-center border border-cyan-400/20">
                            <svg class="h-4 w-4 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-white/90 mb-1">Password Requirements</h4>
                        <ul class="text-xs text-white/60 space-y-1">
                            <li class="flex items-center space-x-2">
                                <div class="w-1 h-1 bg-cyan-400/50 rounded-full"></div>
                                <span>At least 8 characters long</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <div class="w-1 h-1 bg-cyan-400/50 rounded-full"></div>
                                <span>Include uppercase and lowercase letters</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <div class="w-1 h-1 bg-cyan-400/50 rounded-full"></div>
                                <span>Include at least one number</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <PrimaryButton
                type="submit"
                class="w-full group relative overflow-hidden bg-gradient-to-r from-emerald-500 to-blue-600 hover:from-emerald-600 hover:to-blue-700 text-white font-semibold py-4 px-6 rounded-2xl shadow-2xl shadow-emerald-500/25 hover:shadow-emerald-500/40 focus:outline-none focus:ring-4 focus:ring-emerald-500/30 transition-all duration-500 transform hover:scale-[1.02] hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:translate-y-0"
                :disabled="form.processing"
            >
                <!-- Button background effects -->
                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 transform -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-400/0 via-emerald-400/30 to-emerald-400/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative flex items-center justify-center space-x-3">
                    <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                    <svg v-else class="h-5 w-5 text-white group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span class="text-lg font-bold">{{ form.processing ? 'Creating Account...' : 'Create Account' }}</span>
                </div>
            </PrimaryButton>

            <!-- Login Link -->
            <div class="text-center pt-6 border-t border-white/20 relative">
                <!-- Decorative elements -->
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 flex space-x-1">
                    <div class="w-2 h-2 bg-emerald-400/40 rounded-full animate-pulse"></div>
                    <div class="w-1 h-1 bg-blue-400/40 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                    <div class="w-2 h-2 bg-indigo-400/40 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
                </div>
                
                <p class="text-base text-white/70 mb-4">
                    Already have an account?
                </p>
                <Link
                    :href="route('login')"
                    class="inline-flex items-center space-x-2 font-semibold text-white bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl px-6 py-3 hover:bg-white/20 hover:border-white/30 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl shadow-black/20 hover:shadow-2xl hover:shadow-black/30 group"
                >
                    <svg class="h-5 w-5 text-white/70 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <span>Sign In</span>
                    <svg class="h-4 w-4 text-white/50 group-hover:text-white/80 group-hover:-translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </Link>
            </div>
        </form>

        <!-- Floating decorative elements -->
        <div class="absolute -top-6 -left-4 w-2 h-12 bg-gradient-to-b from-emerald-400/40 to-transparent rounded-full animate-float opacity-60"></div>
        <div class="absolute -bottom-4 -right-6 w-3 h-3 bg-emerald-400/50 rounded-full animate-float animate-pulse opacity-70" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/4 -left-8 w-2 h-2 bg-blue-400/40 rounded-full animate-float animate-pulse opacity-50" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-1/3 -right-8 w-1.5 h-8 bg-gradient-to-t from-indigo-500/30 to-transparent rounded-full animate-float opacity-60" style="animation-delay: 0.5s;"></div>
        <div class="absolute top-2/3 -left-6 w-1 h-4 bg-gradient-to-b from-purple-400/30 to-transparent rounded-full animate-float opacity-50" style="animation-delay: 1.5s;"></div>
        <div class="absolute top-1/2 -right-4 w-2 h-2 bg-cyan-400/40 rounded-full animate-float animate-pulse opacity-60" style="animation-delay: 2.5s;"></div>
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

@keyframes shimmer {
    0% {
        background-position: -200px 0;
    }
    100% {
        background-position: 200px 0;
    }
}

/* Animation classes */
.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out 0.2s both;
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

/* Enhanced field focus rings */
.group-focus-within .focus-ring {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

/* Progressive field reveals */
.field-reveal {
    opacity: 0;
    transform: translateY(20px);
    animation: field-reveal 0.5s ease-out forwards;
}

@keyframes field-reveal {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Staggered animations */
.field-reveal:nth-child(1) { animation-delay: 0.1s; }
.field-reveal:nth-child(2) { animation-delay: 0.2s; }
.field-reveal:nth-child(3) { animation-delay: 0.3s; }
.field-reveal:nth-child(4) { animation-delay: 0.4s; }
</style>