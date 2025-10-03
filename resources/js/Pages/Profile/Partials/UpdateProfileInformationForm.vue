<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { User, Mail, CheckCircle2, Send, AlertCircle, Loader2 } from 'lucide-vue-next';
import Swal from 'sweetalert2';
import { watch } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

// Watch for successful form submission
watch(() => form.recentlySuccessful, (newValue) => {
    if (newValue) {
        Swal.fire({
            title: 'Success!',
            text: 'Your profile has been updated successfully.',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3b82f6',
            position: 'top-end',
            toast: true,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            background: '#10b981',
            color: '#fff',
        }).then(() => {
            window.location.reload();
        });
    }
});
</script>

<template>
    <section class="animate-fade-in">
        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="space-y-6 animate-fade-in-up"
        >
            <!-- Name Field -->
            <div class="space-y-3 group">
                <InputLabel 
                    for="name" 
                    value="Full Name" 
                    class="text-sm font-semibold text-white/90 group-focus-within:text-white transition-colors duration-300" 
                />
                <div class="relative group-focus-within:scale-[1.02] transition-all duration-300">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                        <div class="p-1.5 bg-blue-500/20 rounded-lg group-focus-within:bg-blue-500/30 transition-all duration-300">
                            <User class="h-4 w-4 text-blue-300 group-focus-within:text-blue-200 transition-colors duration-300" />
                        </div>
                    </div>
                    <TextInput
                        id="name"
                        type="text"
                        class="pl-16 pr-4 py-4 block w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl shadow-black/20 focus:bg-white/15 focus:border-blue-400/50 focus:ring-4 focus:ring-blue-500/20 text-white placeholder-white/50 transition-all duration-300 hover:bg-white/[0.12] hover:shadow-2xl"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Enter your full name"
                    />
                </div>
                <InputError 
                    class="mt-2 text-red-300 bg-red-500/10 backdrop-blur-xl border border-red-400/20 rounded-xl px-3 py-2" 
                    :message="form.errors.name" 
                />
            </div>

            <!-- Email Field -->
            <div class="space-y-3 group">
                <InputLabel 
                    for="email" 
                    value="Email Address" 
                    class="text-sm font-semibold text-white/90 group-focus-within:text-white transition-colors duration-300" 
                />
                <div class="relative group-focus-within:scale-[1.02] transition-all duration-300">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                        <div class="p-1.5 bg-indigo-500/20 rounded-lg group-focus-within:bg-indigo-500/30 transition-all duration-300">
                            <Mail class="h-4 w-4 text-indigo-300 group-focus-within:text-indigo-200 transition-colors duration-300" />
                        </div>
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="pl-16 pr-4 py-4 block w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl shadow-black/20 focus:bg-white/15 focus:border-indigo-400/50 focus:ring-4 focus:ring-indigo-500/20 text-white placeholder-white/50 transition-all duration-300 hover:bg-white/[0.12] hover:shadow-2xl"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="your.email@example.com"
                    />
                </div>
                <InputError 
                    class="mt-2 text-red-300 bg-red-500/10 backdrop-blur-xl border border-red-400/20 rounded-xl px-3 py-2" 
                    :message="form.errors.email" 
                />
            </div>

            <!-- Email Verification Notice -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="bg-amber-500/20 backdrop-blur-xl border border-amber-400/30 rounded-2xl p-5 shadow-xl shadow-amber-500/10 animate-slide-in">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-r from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/25">
                            <AlertCircle class="h-5 w-5 text-white" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-amber-100 leading-relaxed">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="inline-flex items-center ml-1 text-white font-semibold underline hover:no-underline transition-all duration-300 hover:scale-105 transform"
                            >
                                <Send class="h-3 w-3 mr-1" />
                                Click here to re-send the verification email.
                            </Link>
                        </p>

                        <div
                            v-show="status === 'verification-link-sent'"
                            class="mt-3 bg-green-500/20 backdrop-blur-xl border border-green-400/30 rounded-xl p-3 shadow-lg shadow-green-500/10"
                        >
                            <div class="flex items-center space-x-2">
                                <CheckCircle2 class="h-4 w-4 text-green-300" />
                                <p class="text-sm font-medium text-green-100">
                                    A new verification link has been sent to your email address.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-4 pt-4">
                <PrimaryButton
                    type="submit"
                    class="group relative overflow-hidden bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold py-3.5 px-8 rounded-2xl shadow-2xl shadow-blue-500/25 hover:shadow-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition-all duration-500 transform hover:scale-[1.02] hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:translate-y-0"
                    :disabled="form.processing"
                >
                    <div class="relative flex items-center justify-center space-x-2">
                        <Loader2 v-if="form.processing" class="animate-spin h-5 w-5 text-white" />
                        <CheckCircle2 v-else class="h-5 w-5 text-white group-hover:scale-110 transition-transform duration-300" />
                        <span class="text-base font-bold">{{ form.processing ? 'Saving...' : 'Save Changes' }}</span>
                    </div>
                </PrimaryButton>
            </div>
        </form>
    </section>
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

@keyframes bounce-once {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

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

.animate-bounce-once {
    animation: bounce-once 0.5s ease-out;
}

.animate-spin {
    animation: spin-glow 1s linear infinite;
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
    .animate-bounce-once {
        animation: none !important;
    }
    
    input:focus {
        transform: none !important;
    }
}
</style>