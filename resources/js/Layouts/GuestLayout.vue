<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 flex items-center justify-center px-6 sm:px-8 lg:px-10">
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-32 w-96 h-96 bg-gradient-to-br from-blue-200/30 to-indigo-300/30 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-32 w-96 h-96 bg-gradient-to-tr from-purple-200/30 to-blue-300/30 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-md w-full space-y-12 relative z-10 pt-4">
            <!-- Logo Section -->
            <div class="text-center">
                <Link href="/" class="inline-block group">
                    <div class="mx-auto h-24 w-24 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center shadow-xl group-hover:shadow-2xl transition-all duration-300 group-hover:scale-105">
                        <!-- School/Academic Icon -->
                        <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <div class="mt-6">
                        <h1 class="text-3xl font-bold text-gray-900 group-hover:text-indigo-600 transition-colors duration-200">
                            School Portal
                        </h1>
                        <p class="text-base text-gray-600 mt-2">
                            Education Management System
                        </p>
                    </div>
                </Link>
            </div>

            <!-- Main Content Card -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                <!-- Card Header with subtle pattern -->
                <div class="bg-gradient-to-r from-indigo-500/10 to-blue-500/10 px-8 py-8 border-b border-gray-100">
                    <div class="flex items-center justify-center">
                        <div class="h-1 w-16 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full"></div>
                    </div>
                </div>
                
                <!-- Content Area -->
                <div class="px-8 py-10">
                    <slot />
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center space-y-4 pt-4 pb-4">
                <div class="flex items-center justify-center space-x-6 text-sm text-gray-500">
                    <a href="#" class="hover:text-indigo-600 transition-colors duration-200" role="button" tabindex="0">Privacy Policy</a>
                    <span class="w-1 h-1 bg-gray-300 rounded-full" aria-hidden="true"></span>
                    <a href="#" class="hover:text-indigo-600 transition-colors duration-200" role="button" tabindex="0">Terms of Service</a>
                    <span class="w-1 h-1 bg-gray-300 rounded-full" aria-hidden="true"></span>
                    <a href="mailto:support@school.edu" class="hover:text-indigo-600 transition-colors duration-200">Support</a>
                </div>
                <p class="text-sm text-gray-400 pt-1">
                    © {{ new Date().getFullYear() }} School Portal. All rights reserved.
                </p>
            </div>
        </div>

        <!-- Floating Elements for Visual Interest -->
        <div 
            class="absolute top-20 left-10 w-2 h-2 bg-indigo-300/50 rounded-full animate-pulse animate-float hidden lg:block"
            aria-hidden="true"
        ></div>
        <div 
            class="absolute top-40 right-20 w-1 h-1 bg-blue-400/50 rounded-full animate-pulse animate-float hidden lg:block" 
            style="animation-delay: 1s;"
            aria-hidden="true"
        ></div>
        <div 
            class="absolute bottom-32 left-20 w-1.5 h-1.5 bg-purple-300/50 rounded-full animate-pulse animate-float hidden lg:block" 
            style="animation-delay: 2s;"
            aria-hidden="true"
        ></div>
        <div 
            class="absolute bottom-20 right-10 w-2 h-2 bg-indigo-400/50 rounded-full animate-pulse animate-float hidden lg:block" 
            style="animation-delay: 0.5s;"
            aria-hidden="true"
        ></div>
    </div>
</template>

<style scoped>
/* Custom animations for floating elements */
@keyframes float {
    0%, 100% { 
        transform: translateY(0px); 
    }
    50% { 
        transform: translateY(-10px); 
    }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

/* Ensure proper backdrop blur support */
@supports not (backdrop-filter: blur(8px)) {
    .backdrop-blur-sm {
        background-color: rgba(255, 255, 255, 0.9);
    }
}

/* Improve focus styles for better accessibility */
a:focus,
.group:focus {
    outline: 2px solid #4f46e5;
    outline-offset: 2px;
    border-radius: 4px;
}

/* Ensure animations respect reduced motion preferences */
@media (prefers-reduced-motion: reduce) {
    .animate-pulse,
    .animate-float,
    .transition-all,
    .transition-colors {
        animation: none !important;
        transition: none !important;
    }
}
</style>