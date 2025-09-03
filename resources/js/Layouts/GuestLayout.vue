<script setup>
const currentYear = new Date().getFullYear();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 flex items-center justify-center p-4">
        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-blue-300/20 rounded-full blur-2xl animate-pulse animate-delay-2"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-cyan-300/15 rounded-full blur-xl animate-pulse animate-delay-1"></div>
        </div>

        <!-- Main container with glassmorphism effect -->
        <div class="relative z-10 w-full max-w-6xl h-[600px] glass-card rounded-3xl overflow-hidden shadow-2xl">
            <div class="flex h-full">
                
                <!-- Left side - Logo section -->
                <div class="flex-1 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 backdrop-blur-md flex items-center justify-center relative overflow-hidden">
                    <!-- Logo container -->
                    <div class="relative z-10 text-center">
                        <!-- Circular logo background with actual image -->
                        <div class="mx-auto w-64 h-64 bg-white/80 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg border border-white/20 overflow-hidden">
                            <!-- Logo image -->
                            <img 
                                src="/images/Logo.jpg" 
                                alt="Mater Dei College Logo" 
                                class="w-56 h-56 object-cover rounded-full border-4 border-blue-900"
                            />
                        </div>
                        
                        <!-- Optional text below logo (you can remove this if not needed) -->
                        <div class="mt-4 text-white/80 text-sm font-semibold">
                            <div>MATER DEI COLLEGE</div>
                            <div class="text-xs text-white/60 mt-1">Tubigon, Bohol, Philippines</div>
                        </div>
                    </div>
                </div>

                <!-- Right side - Content area -->
                <div class="flex-1 bg-transparent backdrop-blur-md flex items-center justify-center p-12">
                    <div class="w-full max-w-md">
                        <slot />
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Elements for Visual Interest -->
        <div class="absolute top-20 left-10 w-3 h-3 bg-blue-400/30 rounded-full animate-pulse animate-float hidden lg:block shadow-lg shadow-blue-400/20"></div>
        <div class="absolute top-40 right-20 w-2 h-2 bg-indigo-400/40 rounded-full animate-pulse animate-float hidden lg:block shadow-lg shadow-indigo-400/20 animate-delay-1"></div>
        <div class="absolute bottom-32 left-20 w-4 h-4 bg-purple-400/25 rounded-full animate-pulse animate-float hidden lg:block shadow-lg shadow-purple-400/20 animate-delay-2"></div>
        <div class="absolute bottom-20 right-10 w-3 h-3 bg-blue-500/35 rounded-full animate-pulse animate-float hidden lg:block shadow-lg shadow-blue-500/20 animate-delay-half"></div>
        
        <!-- Additional decorative elements -->
        <div class="absolute top-1/4 left-4 w-1 h-8 bg-gradient-to-b from-blue-400/40 to-transparent rounded-full hidden xl:block animate-pulse animate-delay-1-5"></div>
        <div class="absolute bottom-1/4 right-4 w-1 h-6 bg-gradient-to-t from-indigo-400/40 to-transparent rounded-full hidden xl:block animate-pulse animate-delay-2-5"></div>
    </div>
</template>

<style scoped>
/* Glass morphism effects */
.glass-card {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.25);
}

/* Custom animations */
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

@keyframes spin-slow {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-spin-slow {
    animation: spin-slow 20s linear infinite;
}

.animate-delay-half {
    animation-delay: 0.5s;
}

.animate-delay-1 {
    animation-delay: 1s;
}

.animate-delay-1-5 {
    animation-delay: 1.5s;
}

.animate-delay-2 {
    animation-delay: 2s;
}

.animate-delay-2-5 {
    animation-delay: 2.5s;
}

.animate-delay-3 {
    animation-delay: 3s;
}

/* Enhanced backdrop blur support */
@supports (backdrop-filter: blur(16px)) {
    .backdrop-blur-md {
        backdrop-filter: blur(16px);
    }
    .backdrop-blur-sm {
        backdrop-filter: blur(6px);
    }
}

@supports not (backdrop-filter: blur(16px)) {
    .backdrop-blur-md {
        background-color: rgba(255, 255, 255, 0.12);
    }
    .backdrop-blur-sm {
        background-color: rgba(255, 255, 255, 0.05);
    }
}

/* Enhanced shadow effects */
.shadow-3xl {
    box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.5);
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
    .animate-pulse,
    .animate-float,
    .animate-spin-slow,
    .transition-all,
    .transition-colors,
    .transition-transform {
        animation: none !important;
        transition: none !important;
    }
}

/* Responsive improvements */
@media (max-width: 1024px) {
    .glass-card {
        height: auto;
        min-height: 600px;
    }
    
    .flex {
        flex-direction: column;
    }
    
    .flex-1 {
        min-height: 300px;
    }
}

@media (max-width: 768px) {
    .w-64.h-64 {
        width: 200px;
        height: 200px;
    }
    
    .w-56.h-56 {
        width: 180px;
        height: 180px;
    }
    
    .backdrop-blur-md {
        backdrop-filter: blur(12px);
    }
}

/* Smooth transitions for all interactive elements */
* {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>