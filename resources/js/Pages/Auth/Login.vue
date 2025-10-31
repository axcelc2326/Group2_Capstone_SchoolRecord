<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { CheckCircle2, Mail, Lock, Loader2, LogIn } from 'lucide-vue-next';

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
        <div class="text-center mb-6 animate-fade-in">
            <h2 class="text-3xl font-bold bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent mb-2">
                Welcome Back
            </h2>
            <p class="text-base text-white/70 font-medium">
                Sign in to your MDC-Connect account
            </p>
        </div>

        <!-- Status Message -->
        <div v-if="status" class="mb-4 bg-green-500/20 backdrop-blur-xl border border-green-400/30 rounded-2xl p-3 shadow-xl shadow-green-500/10 animate-slide-in">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-7 h-7 bg-gradient-to-r from-green-400 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/25">
                        <CheckCircle2 class="h-4 w-4 text-white" />
                    </div>
                </div>
                <p class="ml-3 text-sm font-medium text-green-100">{{ status }}</p>
            </div>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="submit" class="space-y-4 animate-fade-in-up">
            <!-- Email Field -->
            <div class="space-y-2 group">
                <InputLabel for="email" value="Email Address" class="text-sm font-semibold text-white/90 group-focus-within:text-white transition-colors duration-300" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <div class="p-1.5 bg-blue-500/20 rounded-lg group-focus-within:bg-blue-500/30 transition-all duration-300">
                            <Mail class="h-4 w-4 text-blue-300 group-focus-within:text-blue-200 transition-colors duration-300" />
                        </div>
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="pl-14 pr-4 py-3 block w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl shadow-xl shadow-black/20 focus:bg-white/15 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-500/20 text-white placeholder-white/50 transition-all duration-300 hover:bg-white/[0.12]"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="parent@gmail.com"
                    />
                </div>
                <InputError class="mt-1 text-xs text-red-300 bg-red-500/10 backdrop-blur-xl border border-red-400/20 rounded-lg px-2 py-1" :message="form.errors.email" />
            </div>

            <!-- Password Field -->
            <div class="space-y-2 group">
                <InputLabel for="password" value="Password" class="text-sm font-semibold text-white/90 group-focus-within:text-white transition-colors duration-300" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <div class="p-1.5 bg-indigo-500/20 rounded-lg group-focus-within:bg-indigo-500/30 transition-all duration-300">
                            <Lock class="h-4 w-4 text-indigo-300 group-focus-within:text-indigo-200 transition-colors duration-300" />
                        </div>
                    </div>
                    <TextInput
                        id="password"
                        type="password"
                        class="pl-14 pr-4 py-3 block w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl shadow-xl shadow-black/20 focus:bg-white/15 focus:border-indigo-400/50 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-white/50 transition-all duration-300 hover:bg-white/[0.12]"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    />
                </div>
                <InputError class="mt-1 text-xs text-red-300 bg-red-500/10 backdrop-blur-xl border border-red-400/20 rounded-lg px-2 py-1" :message="form.errors.password" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl px-3 py-2 hover:bg-white/10 transition-all duration-300">
                <Checkbox 
                    name="remember" 
                    v-model:checked="form.remember"
                    class="h-4 w-4 text-blue-500 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-transparent border-white/30 rounded bg-white/10 backdrop-blur-xl transition-all duration-300"
                />
                <label class="ml-2 text-xs text-white/70 cursor-pointer select-none hover:text-white/90 transition-colors duration-300 font-medium">
                    Keep me signed in
                </label>
            </div>

            <!-- Submit Button -->
            <PrimaryButton
                type="submit"
                class="w-full group relative overflow-hidden bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-xl shadow-2xl shadow-blue-500/25 hover:shadow-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition-all duration-500 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                :disabled="form.processing"
            >
                <div class="relative flex items-center justify-center space-x-2">
                    <Loader2 v-if="form.processing" class="animate-spin h-5 w-5 text-white" />
                    <LogIn v-else class="h-5 w-5 text-white group-hover:scale-110 transition-transform duration-300" />
                    <span class="text-base font-bold">{{ form.processing ? 'Signing in...' : 'Sign In' }}</span>
                </div>
            </PrimaryButton>

            <!-- No Account Notice -->
            <div class="text-center pt-4 border-t border-white/20">
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-3 hover:bg-white/10 transition-all duration-300">
                    <p class="text-xs text-white/70 leading-relaxed">
                        Don't have an account? 
                        <span class="block mt-1 text-white/90 font-medium">
                            Please contact your child's teacher or the principal to request account access.
                        </span>
                    </p>
                </div>
            </div>
        </form>
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

/* Enhanced backdrop blur support */
@supports (backdrop-filter: blur(20px)) {
    .backdrop-blur-xl {
        backdrop-filter: blur(20px);
    }
}

/* Smooth transitions for all interactive elements */
* {
    transition-property: color, background-color, border-color, transform, opacity, box-shadow;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Ensure animations respect reduced motion preferences */
@media (prefers-reduced-motion: reduce) {
    .animate-fade-in,
    .animate-fade-in-up,
    .animate-slide-in {
        animation: none !important;
    }
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