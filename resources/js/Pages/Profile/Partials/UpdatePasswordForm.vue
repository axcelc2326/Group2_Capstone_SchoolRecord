<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Lock, Key, ShieldCheck, CheckCircle2, Loader2, AlertCircle } from 'lucide-vue-next';
import Swal from 'sweetalert2';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};

// Watch for successful form submission
watch(() => form.recentlySuccessful, (newValue) => {
    if (newValue) {
        Swal.fire({
            title: 'Success!',
            text: 'Your password has been updated successfully.',
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
        });
    }
});
</script>

<template>
    <section class="animate-fade-in">
        <header class="mb-8 animate-fade-in-up">
            <h2 class="text-2xl font-bold text-white mb-2">
                Update Password
            </h2>

            <p class="text-sm text-white/70 leading-relaxed">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </header>

        <form 
            @submit.prevent="updatePassword" 
            class="space-y-6 animate-fade-in-up"
        >
            <!-- Current Password Field -->
            <div class="space-y-3 group">
                <InputLabel 
                    for="current_password" 
                    value="Current Password" 
                    class="text-sm font-semibold text-white/90 group-focus-within:text-white transition-colors duration-300"
                />
                <div class="relative group-focus-within:scale-[1.02] transition-all duration-300">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                        <div class="p-1.5 bg-purple-500/20 rounded-lg group-focus-within:bg-purple-500/30 transition-all duration-300">
                            <Lock class="h-4 w-4 text-purple-300 group-focus-within:text-purple-200 transition-colors duration-300" />
                        </div>
                    </div>
                    <TextInput
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        type="password"
                        class="pl-16 pr-4 py-4 block w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl shadow-black/20 focus:bg-white/15 focus:border-purple-400/50 focus:ring-4 focus:ring-purple-500/20 text-white placeholder-white/50 transition-all duration-300 hover:bg-white/[0.12] hover:shadow-2xl"
                        autocomplete="current-password"
                        placeholder="Enter your current password"
                    />
                </div>
                <InputError
                    :message="form.errors.current_password"
                    class="mt-2 text-red-300 bg-red-500/10 backdrop-blur-xl border border-red-400/20 rounded-xl px-3 py-2"
                />
            </div>

            <!-- New Password Field -->
            <div class="space-y-3 group">
                <InputLabel 
                    for="password" 
                    value="New Password" 
                    class="text-sm font-semibold text-white/90 group-focus-within:text-white transition-colors duration-300"
                />
                <div class="relative group-focus-within:scale-[1.02] transition-all duration-300">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                        <div class="p-1.5 bg-blue-500/20 rounded-lg group-focus-within:bg-blue-500/30 transition-all duration-300">
                            <Key class="h-4 w-4 text-blue-300 group-focus-within:text-blue-200 transition-colors duration-300" />
                        </div>
                    </div>
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="pl-16 pr-4 py-4 block w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl shadow-black/20 focus:bg-white/15 focus:border-blue-400/50 focus:ring-4 focus:ring-blue-500/20 text-white placeholder-white/50 transition-all duration-300 hover:bg-white/[0.12] hover:shadow-2xl"
                        autocomplete="new-password"
                        placeholder="Enter your new password"
                    />
                </div>
                <InputError 
                    :message="form.errors.password" 
                    class="mt-2 text-red-300 bg-red-500/10 backdrop-blur-xl border border-red-400/20 rounded-xl px-3 py-2"
                />
            </div>

            <!-- Confirm Password Field -->
            <div class="space-y-3 group">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                    class="text-sm font-semibold text-white/90 group-focus-within:text-white transition-colors duration-300"
                />
                <div class="relative group-focus-within:scale-[1.02] transition-all duration-300">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                        <div class="p-1.5 bg-indigo-500/20 rounded-lg group-focus-within:bg-indigo-500/30 transition-all duration-300">
                            <ShieldCheck class="h-4 w-4 text-indigo-300 group-focus-within:text-indigo-200 transition-colors duration-300" />
                        </div>
                    </div>
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="pl-16 pr-4 py-4 block w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl shadow-black/20 focus:bg-white/15 focus:border-indigo-400/50 focus:ring-4 focus:ring-indigo-500/20 text-white placeholder-white/50 transition-all duration-300 hover:bg-white/[0.12] hover:shadow-2xl"
                        autocomplete="new-password"
                        placeholder="Confirm your new password"
                    />
                </div>
                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2 text-red-300 bg-red-500/10 backdrop-blur-xl border border-red-400/20 rounded-xl px-3 py-2"
                />
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
                        <span class="text-base font-bold">{{ form.processing ? 'Updating...' : 'Update Password' }}</span>
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
    .animate-fade-in-up {
        animation: none !important;
    }
    
    input:focus {
        transform: none !important;
    }
}
</style>