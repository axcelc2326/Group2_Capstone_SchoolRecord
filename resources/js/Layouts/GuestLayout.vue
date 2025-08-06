<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

const currentYear = new Date().getFullYear();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 text-white flex items-center justify-center px-6 sm:px-8 lg:px-10 transition-all duration-700">
        <!-- Background Pattern with animated elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-32 w-96 h-96 bg-gradient-to-br from-blue-400/20 to-indigo-500/30 rounded-full blur-3xl animate-float"></div>
            <div class="absolute -bottom-40 -left-32 w-96 h-96 bg-gradient-to-tr from-purple-400/20 to-blue-500/30 rounded-full blur-3xl animate-float" style="animation-delay: 3s;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-gradient-to-r from-indigo-400/10 to-blue-400/10 rounded-full blur-2xl animate-pulse"></div>
        </div>

        <div class="w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl 2xl:max-w-3xl space-y-8 relative z-10 pt-4">
            <!-- Logo Section -->
            <div class="text-center">
                <Link href="/" class="inline-block group">
                    <div class="mx-auto h-24 w-24 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-3xl flex items-center justify-center shadow-2xl shadow-blue-500/25 group-hover:shadow-xl group-hover:shadow-blue-500/30 transition-all duration-500 group-hover:scale-105 group-hover:rotate-3">
                        <!-- School/Academic Icon -->
                        <svg class="h-12 w-12 text-white transition-all duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <div class="mt-6 space-y-2">
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-100 to-indigo-100 bg-clip-text text-transparent group-hover:from-blue-200 group-hover:to-indigo-200 transition-all duration-500">
                            EduPortal
                        </h1>
                        <p class="text-lg text-white/70 font-medium transition-colors duration-300 group-hover:text-white/90">
                            Learning Management System
                        </p>
                    </div>
                </Link>
            </div>

            <!-- Main Content Card -->
            <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl shadow-black/20 overflow-hidden transition-all duration-500 hover:shadow-3xl hover:shadow-black/25 glass-effect">
                <!-- Card Header with gradient accent -->
                <div class="bg-gradient-to-r from-blue-500/20 to-indigo-500/20 px-6 sm:px-8 lg:px-12 xl:px-16 py-8 border-b border-white/20 relative overflow-hidden">
                    <!-- Background decoration -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/30 to-transparent rounded-full -translate-y-16 translate-x-16 transition-all duration-700"></div>
                    
                    <div class="flex items-center justify-center relative z-10">
                        <div class="flex items-center space-x-2">
                            <div class="h-2 w-2 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full animate-pulse"></div>
                            <div class="h-1 w-20 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                            <div class="h-2 w-2 bg-gradient-to-r from-purple-500 to-blue-400 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Content Area -->
                <div class="px-6 sm:px-8 lg:px-12 xl:px-16 py-10 relative">
                    <!-- Background decoration -->
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-indigo-400/20 to-transparent rounded-full translate-y-12 -translate-x-12 transition-all duration-700"></div>
                    
                    <div class="relative z-10">
                        <slot />
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center space-y-6 pt-6 pb-4">
                <div class="flex flex-wrap items-center justify-center gap-4 lg:gap-6 text-sm text-white/60">
                    <a href="#" class="hover:text-white/90 transition-all duration-300 hover:scale-105 px-3 py-1 rounded-lg hover:bg-white/10" role="button" tabindex="0">
                        Privacy Policy
                    </a>
                    <div class="w-1 h-1 bg-white/30 rounded-full animate-pulse hidden sm:block"></div>
                    <a href="#" class="hover:text-white/90 transition-all duration-300 hover:scale-105 px-3 py-1 rounded-lg hover:bg-white/10" role="button" tabindex="0">
                        Terms of Service
                    </a>
                    <div class="w-1 h-1 bg-white/30 rounded-full animate-pulse hidden sm:block"></div>
                    <a href="mailto:support@eduportal.edu" class="hover:text-white/90 transition-all duration-300 hover:scale-105 px-3 py-1 rounded-lg hover:bg-white/10">
                        Support
                    </a>
                </div>
                
                <div class="flex flex-wrap items-center justify-center gap-4 text-sm text-white/50">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse shadow-lg shadow-green-400/50"></div>
                        <span class="hover:text-white/70 transition-colors duration-300">All systems operational</span>
                    </div>
                    <div class="text-xs bg-white/10 px-3 py-1 rounded-full transition-all duration-300 hover:bg-white/20">
                        v2.1.0
                    </div>
                </div>
                
                <p class="text-sm text-white/40 pt-2 transition-colors duration-300 hover:text-white/60">
                    © {{ currentYear }} EduPortal. All rights reserved. Built with ❤️ for education.
                </p>
            </div>
        </div>

        <!-- Floating Elements for Visual Interest -->
        <div class="absolute top-20 left-10 w-3 h-3 bg-blue-400/30 rounded-full animate-pulse animate-float hidden lg:block shadow-lg shadow-blue-400/20"></div>
        <div class="absolute top-40 right-20 w-2 h-2 bg-indigo-400/40 rounded-full animate-pulse animate-float hidden lg:block shadow-lg shadow-indigo-400/20" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-32 left-20 w-4 h-4 bg-purple-400/25 rounded-full animate-pulse animate-float hidden lg:block shadow-lg shadow-purple-400/20" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 right-10 w-3 h-3 bg-blue-500/35 rounded-full animate-pulse animate-float hidden lg:block shadow-lg shadow-blue-500/20" style="animation-delay: 0.5s;"></div>
        
        <!-- Additional decorative elements -->
        <div class="absolute top-1/4 left-4 w-1 h-8 bg-gradient-to-b from-blue-400/40 to-transparent rounded-full hidden xl:block animate-pulse" style="animation-delay: 1.5s;"></div>
        <div class="absolute bottom-1/4 right-4 w-1 h-6 bg-gradient-to-t from-indigo-400/40 to-transparent rounded-full hidden xl:block animate-pulse" style="animation-delay: 2.5s;"></div>
    </div>
</template>

<style scoped>
/* Custom animations for floating elements */
@keyframes float {
    0%, 100% { 
        transform: translateY(0px) rotate(0deg); 
    }
    33% {
        transform: translateY(-10px) rotate(1deg);
    }
    66% {
        transform: translateY(5px) rotate(-1deg);
    }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

/* Enhanced backdrop blur support */
@supports (backdrop-filter: blur(20px)) {
    .backdrop-blur-xl {
        backdrop-filter: blur(20px);
    }
}

@supports not (backdrop-filter: blur(20px)) {
    .backdrop-blur-xl {
        background-color: rgba(255, 255, 255, 0.15);
    }
}

/* Glass morphism effect */
.glass-effect {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
}

/* Enhanced pulse animation for status indicator */
@keyframes pulse-glow {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
        box-shadow: 0 0 5px currentColor;
    }
    50% {
        opacity: 0.7;
        transform: scale(1.1);
        box-shadow: 0 0 15px currentColor;
    }
}

.animate-pulse {
    animation: pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Enhanced shadow effects */
.shadow-3xl {
    box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.5);
}

/* Improve focus styles for better accessibility */
a:focus,
.group:focus {
    outline: 2px solid rgba(59, 130, 246, 0.5);
    outline-offset: 2px;
    border-radius: 0.75rem;
}

/* Enhanced hover effects */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

.group:hover .group-hover\:rotate-3 {
    transform: rotate(3deg);
}

/* Smooth transitions for all interactive elements */
* {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Custom gradient text animation */
@keyframes gradient-shift {
    0%, 100% {
        background-size: 200% 200%;
        background-position: left center;
    }
    50% {
        background-size: 200% 200%;
        background-position: right center;
    }
}

/* Micro-interactions */
.micro-bounce:active {
    animation: micro-bounce 0.1s ease-in-out;
}

@keyframes micro-bounce {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(0.98);
    }
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
    
    .group:hover .group-hover\:scale-105,
    .group:hover .group-hover\:rotate-3 {
        transform: none !important;
    }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .backdrop-blur-xl {
        backdrop-filter: blur(16px);
    }
}

/* Custom scrollbar for modern browsers */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
    transition: background 0.3s ease;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>