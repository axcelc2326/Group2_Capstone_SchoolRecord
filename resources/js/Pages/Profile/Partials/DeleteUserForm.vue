<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref, watch } from 'vue';
import { Trash2, AlertTriangle, Lock, XCircle, Loader2 } from 'lucide-vue-next';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};

// Close modal on escape key
watch(confirmingUserDeletion, (value) => {
    if (value) {
        document.body.style.overflow = 'hidden';
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                closeModal();
            }
        };
        document.addEventListener('keydown', handleEscape);
        return () => {
            document.removeEventListener('keydown', handleEscape);
            document.body.style.overflow = '';
        };
    } else {
        document.body.style.overflow = '';
    }
});
</script>

<template>
    <section class="space-y-6 animate-fade-in">
        <header class="animate-fade-in-up">
            <h2 class="text-2xl font-bold text-white mb-2">
                Delete Account
            </h2>

            <p class="text-sm text-white/70 leading-relaxed">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </p>
        </header>

        <DangerButton 
            @click="confirmUserDeletion"
            class="group relative overflow-hidden bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold py-3.5 px-8 rounded-2xl shadow-2xl shadow-red-500/25 hover:shadow-red-500/40 focus:outline-none focus:ring-4 focus:ring-red-500/30 transition-all duration-500 transform hover:scale-[1.02] hover:-translate-y-1 animate-fade-in-up"
        >
            <div class="relative flex items-center justify-center space-x-2">
                <Trash2 class="h-5 w-5 text-white group-hover:scale-110 transition-transform duration-300" />
                <span class="text-base font-bold">Delete Account</span>
            </div>
        </DangerButton>

        <!-- Custom Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-opacity duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="confirmingUserDeletion"
                    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
                    @click.self="closeModal"
                >
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-black/75 backdrop-blur-sm"></div>

                    <!-- Modal Content -->
                    <div class="relative min-h-screen flex items-center justify-center">
                        <Transition
                            enter-active-class="transition-all duration-300 ease-out"
                            enter-from-class="opacity-0 scale-95 translate-y-4"
                            enter-to-class="opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition-all duration-200 ease-in"
                            leave-from-class="opacity-100 scale-100 translate-y-0"
                            leave-to-class="opacity-0 scale-95 translate-y-4"
                        >
                            <div
                                v-if="confirmingUserDeletion"
                                class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-8 rounded-3xl shadow-2xl max-w-2xl w-full mx-auto"
                            >
                                <!-- Warning Header -->
                                <div class="flex items-start space-x-4 mb-6">
                                    <div class="flex-shrink-0">
                                        <div class="w-14 h-14 bg-gradient-to-r from-red-500 to-rose-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-red-500/30 animate-pulse-glow">
                                            <AlertTriangle class="h-7 w-7 text-white" />
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h2 class="text-2xl font-bold text-white mb-2">
                                            Are you sure you want to delete your account?
                                        </h2>
                                        <p class="text-sm text-white/70 leading-relaxed">
                                            Once your account is deleted, all of its resources and data
                                            will be permanently deleted. Please enter your password to
                                            confirm you would like to permanently delete your account.
                                        </p>
                                    </div>
                                </div>

                                <!-- Warning Banner -->
                                <div class="bg-red-500/20 backdrop-blur-xl border border-red-400/30 rounded-2xl p-4 mb-6 shadow-xl shadow-red-500/10">
                                    <div class="flex items-center space-x-3">
                                        <AlertTriangle class="h-5 w-5 text-red-300 flex-shrink-0" />
                                        <p class="text-sm font-medium text-red-100">
                                            This action cannot be undone. All your data will be permanently deleted.
                                        </p>
                                    </div>
                                </div>

                                <!-- Password Input -->
                                <div class="space-y-3 group mb-8">
                                    <InputLabel
                                        for="password"
                                        value="Confirm Password"
                                        class="text-sm font-semibold text-white/90 group-focus-within:text-white transition-colors duration-300"
                                    />
                                    <div class="relative group-focus-within:scale-[1.02] transition-all duration-300">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                            <div class="p-1.5 bg-red-500/20 rounded-lg group-focus-within:bg-red-500/30 transition-all duration-300">
                                                <Lock class="h-4 w-4 text-red-300 group-focus-within:text-red-200 transition-colors duration-300" />
                                            </div>
                                        </div>
                                        <TextInput
                                            id="password"
                                            ref="passwordInput"
                                            v-model="form.password"
                                            type="password"
                                            class="pl-16 pr-4 py-4 block w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl shadow-black/20 focus:bg-white/15 focus:border-red-400/50 focus:ring-4 focus:ring-red-500/20 text-white placeholder-white/50 transition-all duration-300 hover:bg-white/[0.12] hover:shadow-2xl"
                                            placeholder="Enter your password to confirm"
                                            @keyup.enter="deleteUser"
                                        />
                                    </div>
                                    <InputError 
                                        :message="form.errors.password" 
                                        class="mt-2 text-red-300 bg-red-500/10 backdrop-blur-xl border border-red-400/20 rounded-xl px-3 py-2"
                                    />
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-end gap-4">
                                    <SecondaryButton 
                                        @click="closeModal"
                                        class="group relative overflow-hidden bg-white/10 backdrop-blur-xl hover:bg-white/15 text-white font-semibold py-3.5 px-8 rounded-2xl shadow-xl shadow-black/20 hover:shadow-black/30 border border-white/20 hover:border-white/30 focus:outline-none focus:ring-4 focus:ring-white/20 transition-all duration-300 transform hover:scale-[1.02]"
                                    >
                                        <div class="relative flex items-center justify-center space-x-2">
                                            <XCircle class="h-5 w-5 text-white/80 group-hover:text-white group-hover:scale-110 transition-all duration-300" />
                                            <span class="text-base font-bold">Cancel</span>
                                        </div>
                                    </SecondaryButton>

                                    <DangerButton
                                        class="group relative overflow-hidden bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold py-3.5 px-8 rounded-2xl shadow-2xl shadow-red-500/25 hover:shadow-red-500/40 focus:outline-none focus:ring-4 focus:ring-red-500/30 transition-all duration-500 transform hover:scale-[1.02] hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:translate-y-0"
                                        :disabled="form.processing"
                                        @click="deleteUser"
                                    >
                                        <div class="relative flex items-center justify-center space-x-2">
                                            <Loader2 v-if="form.processing" class="animate-spin h-5 w-5 text-white" />
                                            <Trash2 v-else class="h-5 w-5 text-white group-hover:scale-110 transition-transform duration-300" />
                                            <span class="text-base font-bold">{{ form.processing ? 'Deleting...' : 'Delete Account' }}</span>
                                        </div>
                                    </DangerButton>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </Transition>
        </Teleport>
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

@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
    }
    50% {
        box-shadow: 0 0 30px rgba(239, 68, 68, 0.5);
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

.animate-pulse-glow {
    animation: pulse-glow 2s ease-in-out infinite;
}

.animate-spin {
    animation: spin-glow 1s linear infinite;
}

/* Enhanced backdrop blur support */
@supports (backdrop-filter: blur(20px)) {
    .backdrop-blur-xl {
        backdrop-filter: blur(20px);
    }
    .backdrop-blur-sm {
        backdrop-filter: blur(8px);
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
    .animate-pulse-glow {
        animation: none !important;
    }
    
    input:focus {
        transform: none !important;
    }
}
</style>