<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import LoginModal from '@/Components/LoginModal.vue'
import RegisterModal from '@/Components/AdminAndTeacher/RegisterModal.vue'
import Footer from '@/Components/Footer.vue'
import { ref } from 'vue'

const showLogin = ref(false)
const showRegister = ref(false)

function openLogin() {
  showLogin.value = true
  showRegister.value = false
}
function openRegister() {
  showRegister.value = true
  showLogin.value = false
}

const props = defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
});

const { props: pageProps } = usePage();
const currentYear = new Date().getFullYear();
</script>

<template>
  <Head title="Welcome to EduPortal" />

  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 text-white">
    <!-- Background Pattern with animated elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-32 w-96 h-96 bg-gradient-to-br from-blue-400/20 to-indigo-500/30 rounded-full blur-3xl animate-float"></div>
      <div class="absolute -bottom-40 -left-32 w-96 h-96 bg-gradient-to-tr from-purple-400/20 to-blue-500/30 rounded-full blur-3xl animate-float" style="animation-delay: 3s;"></div>
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-gradient-to-r from-indigo-400/10 to-blue-400/10 rounded-full blur-2xl animate-pulse"></div>
    </div>

    <!-- Header Navigation -->
    <nav class="bg-white/10 backdrop-blur-xl border-b border-white/20 sticky top-0 z-50 glass-effect">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
          <!-- Logo -->
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-500 hover:scale-105 hover:rotate-3">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
              </svg>
            </div>
            <div>
              <div class="text-2xl font-bold bg-gradient-to-r from-blue-100 to-indigo-100 bg-clip-text text-transparent">EduPortal</div>
              <div class="text-sm text-white/70 hidden sm:block">Learning Management System</div>
            </div>
          </div>

          <!-- Auth Buttons -->
          <div class="flex items-center space-x-4">
            <Link
              v-if="pageProps.auth.user"
              :href="route('dashboard')"
              class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all duration-300 font-medium backdrop-blur-sm hover:scale-105"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              Dashboard
            </Link>

            <template v-else>
              <div class="flex items-center space-x-3">
                <button 
                  v-if="canLogin"
                  @click="openLogin"
                  class="inline-flex items-center px-5 py-2.5 bg-white/10 backdrop-blur-xl text-white rounded-lg hover:bg-white/20 border border-white/30 shadow-lg hover:shadow-xl transition-all duration-300 font-medium hover:scale-105"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                  </svg>
                  Login
                </button>
                
                <button 
                  v-if="canRegister"
                  @click="openRegister"
                  class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all duration-300 font-medium backdrop-blur-sm hover:scale-105"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                  </svg>
                  Register
                </button>
              </div>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden py-24">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center relative z-10">
          <!-- Welcome Badge -->
          <div class="inline-flex items-center px-6 py-3 bg-white/10 backdrop-blur-xl rounded-full text-blue-200 text-sm font-medium mb-8 border border-white/20 shadow-lg">
            <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse mr-3"></div>
            Welcome to the Future of Learning
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>

          <!-- Main Heading -->
          <h1 class="text-5xl md:text-7xl font-bold mb-8 leading-tight">
            Empowering Education Through
            <span class="block bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400 bg-clip-text text-transparent animate-gradient">
              Technology
            </span>
          </h1>

          <!-- Subheading -->
          <p class="text-xl md:text-2xl text-white/80 mb-12 max-w-4xl mx-auto leading-relaxed">
            Our comprehensive learning management system connects students, teachers, and parents in a unified educational ecosystem designed for success.
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
            <Link
              v-if="pageProps.auth.user"
              :href="route('dashboard')"
              class="group inline-flex items-center px-10 py-5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-2xl hover:from-blue-600 hover:to-indigo-700 shadow-2xl hover:shadow-blue-500/25 transition-all duration-300 font-semibold text-lg backdrop-blur-sm hover:scale-105"
            >
              <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              Access Dashboard
              <div class="ml-2 w-2 h-2 bg-white/50 rounded-full animate-pulse"></div>
            </Link>

            <template v-else>
              <button
                v-if="canLogin"
                @click="openLogin"
                class="group inline-flex items-center px-10 py-5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-2xl hover:from-blue-600 hover:to-indigo-700 shadow-2xl hover:shadow-blue-500/25 transition-all duration-300 font-semibold text-lg backdrop-blur-sm hover:scale-105"
              >
                <svg class="w-6 h-6 mr-3 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Get Started
                <div class="ml-2 w-2 h-2 bg-white/50 rounded-full animate-pulse"></div>
              </button>

              <button
                v-if="canRegister"
                @click="openRegister"
                class="group inline-flex items-center px-10 py-5 bg-white/10 backdrop-blur-xl text-white rounded-2xl hover:bg-white/20 border border-white/30 shadow-2xl hover:shadow-xl transition-all duration-300 font-semibold text-lg hover:scale-105"
              >
                <svg class="w-6 h-6 mr-3 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Join Now
                <div class="ml-2 w-2 h-2 bg-white/50 rounded-full animate-pulse"></div>
              </button>
            </template>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
          <div class="inline-flex items-center space-x-2 mb-6">
            <div class="h-2 w-2 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full animate-pulse"></div>
            <div class="h-1 w-20 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
            <div class="h-2 w-2 bg-gradient-to-r from-purple-500 to-blue-400 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
          </div>
          <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">
            Built for Everyone in Education
          </h2>
          <p class="text-xl text-white/70 max-w-3xl mx-auto">
            Our platform serves students, teachers, parents, and administrators with tailored tools for each role.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Parents -->
          <div class="group bg-white/10 backdrop-blur-xl p-8 rounded-3xl border border-white/20 shadow-2xl hover:shadow-3xl transition-all duration-500 hover:scale-105 hover:bg-white/15 glass-effect">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-8 shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-blue-200 transition-colors duration-300">For Parents</h3>
            <p class="text-white/70 mb-8 leading-relaxed">
              Stay connected with your child's education. View grades, track progress, and receive important announcements in real-time.
            </p>
            <ul class="space-y-4">
              <li class="flex items-center text-white/80 group-hover:text-white transition-colors duration-300">
                <div class="w-2 h-2 bg-blue-400 rounded-full mr-4 animate-pulse"></div>
                Real-time grade monitoring
              </li>
              <li class="flex items-center text-white/80 group-hover:text-white transition-colors duration-300">
                <div class="w-2 h-2 bg-indigo-400 rounded-full mr-4 animate-pulse" style="animation-delay: 0.5s;"></div>
                Student registration portal
              </li>
              <li class="flex items-center text-white/80 group-hover:text-white transition-colors duration-300">
                <div class="w-2 h-2 bg-purple-400 rounded-full mr-4 animate-pulse" style="animation-delay: 1s;"></div>
                School announcements
              </li>
            </ul>
          </div>

          <!-- Teachers -->
          <div class="group bg-white/10 backdrop-blur-xl p-8 rounded-3xl border border-white/20 shadow-2xl hover:shadow-3xl transition-all duration-500 hover:scale-105 hover:bg-white/15 glass-effect">
            <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mb-8 shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-emerald-200 transition-colors duration-300">For Teachers</h3>
            <p class="text-white/70 mb-8 leading-relaxed">
              Manage your classroom efficiently. Track student progress, approve registrations, and generate comprehensive reports.
            </p>
            <ul class="space-y-4">
              <li class="flex items-center text-white/80 group-hover:text-white transition-colors duration-300">
                <div class="w-2 h-2 bg-emerald-400 rounded-full mr-4 animate-pulse"></div>
                Student management system
              </li>
              <li class="flex items-center text-white/80 group-hover:text-white transition-colors duration-300">
                <div class="w-2 h-2 bg-teal-400 rounded-full mr-4 animate-pulse" style="animation-delay: 0.5s;"></div>
                Registration approval tools
              </li>
              <li class="flex items-center text-white/80 group-hover:text-white transition-colors duration-300">
                <div class="w-2 h-2 bg-green-400 rounded-full mr-4 animate-pulse" style="animation-delay: 1s;"></div>
                SF5 report generation
              </li>
            </ul>
          </div>

          <!-- Administrators -->
          <div class="group bg-white/10 backdrop-blur-xl p-8 rounded-3xl border border-white/20 shadow-2xl hover:shadow-3xl transition-all duration-500 hover:scale-105 hover:bg-white/15 glass-effect">
            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-8 shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-purple-200 transition-colors duration-300">For Administrators</h3>
            <p class="text-white/70 mb-8 leading-relaxed">
              Comprehensive school management tools. Create classes, assign teachers, and analyze school performance with advanced analytics.
            </p>
            <ul class="space-y-4">
              <li class="flex items-center text-white/80 group-hover:text-white transition-colors duration-300">
                <div class="w-2 h-2 bg-purple-400 rounded-full mr-4 animate-pulse"></div>
                Class and curriculum management
              </li>
              <li class="flex items-center text-white/80 group-hover:text-white transition-colors duration-300">
                <div class="w-2 h-2 bg-pink-400 rounded-full mr-4 animate-pulse" style="animation-delay: 0.5s;"></div>
                Teacher assignment system
              </li>
              <li class="flex items-center text-white/80 group-hover:text-white transition-colors duration-300">
                <div class="w-2 h-2 bg-rose-400 rounded-full mr-4 animate-pulse" style="animation-delay: 1s;"></div>
                Analytics and reporting
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 relative">
      <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-indigo-600/20 backdrop-blur-3xl"></div>
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl p-12 glass-effect">
          <h2 class="text-4xl md:text-5xl font-bold text-white mb-8 bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">
            Ready to Transform Your Educational Experience?
          </h2>
          <p class="text-xl text-white/80 mb-12 max-w-2xl mx-auto leading-relaxed">
            Join thousands of educators, students, and families who trust EduPortal for their educational journey.
          </p>
          <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
            <Link
              v-if="pageProps.auth.user"
              :href="route('dashboard')"
              class="group inline-flex items-center px-10 py-5 bg-gradient-to-r from-white to-blue-50 text-blue-600 rounded-2xl hover:from-blue-50 hover:to-white shadow-2xl hover:shadow-white/10 transition-all duration-300 font-semibold text-lg backdrop-blur-sm hover:scale-105"
            >
              <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              Go to Dashboard
            </Link>

            <template v-else>
              <button
                v-if="canRegister"
                @click="openRegister"
                class="group inline-flex items-center px-10 py-5 bg-gradient-to-r from-white to-blue-50 text-blue-600 rounded-2xl hover:from-blue-50 hover:to-white shadow-2xl hover:shadow-white/10 transition-all duration-300 font-semibold text-lg backdrop-blur-sm hover:scale-105"
              >
                <svg class="w-6 h-6 mr-3 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Get Started Today
              </button>

              <button
                v-if="canLogin"
                @click="openLogin"
                class="group inline-flex items-center px-10 py-5 bg-white/10 backdrop-blur-xl text-white rounded-2xl hover:bg-white/20 border border-white/30 shadow-2xl hover:shadow-xl transition-all duration-300 font-semibold text-lg hover:scale-105"
              >
                <svg class="w-6 h-6 mr-3 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Sign In
              </button>
            </template>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <Footer />

    <!-- Floating Elements for Visual Interest -->
    <div class="absolute top-20 left-10 w-3 h-3 bg-blue-400/30 rounded-full animate-pulse animate-float hidden lg:block shadow-lg shadow-blue-400/20"></div>
    <div class="absolute top-40 right-20 w-2 h-2 bg-indigo-400/40 rounded-full animate-pulse animate-float hidden lg:block shadow-lg shadow-indigo-400/20" style="animation-delay: 1s;"></div>
    <div class="absolute bottom-32 left-20 w-4 h-4 bg-purple-400/25 rounded-full animate-pulse animate-float hidden lg:block shadow-lg shadow-purple-400/20" style="animation-delay: 2s;"></div>
    <div class="absolute bottom-20 right-10 w-3 h-3 bg-blue-500/35 rounded-full animate-pulse animate-float hidden lg:block shadow-lg shadow-blue-500/20" style="animation-delay: 0.5s;"></div>
    
    <!-- Additional decorative elements -->
    <div class="absolute top-1/4 left-4 w-1 h-8 bg-gradient-to-b from-blue-400/40 to-transparent rounded-full hidden xl:block animate-pulse" style="animation-delay: 1.5s;"></div>
    <div class="absolute bottom-1/4 right-4 w-1 h-6 bg-gradient-to-t from-indigo-400/40 to-transparent rounded-full hidden xl:block animate-pulse" style="animation-delay: 2.5s;"></div>

    <!-- Login Modal -->
    <LoginModal 
      :show="showLogin" 
      @close="showLogin = false"
    />

    <!-- Register Modal -->
    <RegisterModal 
      :show="showRegister" 
      @close="showRegister = false"
    />
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

/* Gradient animation for text */
@keyframes gradient {
    0%, 100% {
        background-size: 200% 200%;
        background-position: left center;
    }
    50% {
        background-size: 200% 200%;
        background-position: right center;
    }
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 6s ease infinite;
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
    .animate-gradient,
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

/* Enhanced button hover effects */
.group:hover svg {
    filter: drop-shadow(0 0 6px rgba(255, 255, 255, 0.3));
}

/* Improved card hover effects */
.group:hover {
    transform: translateY(-4px) scale(1.02);
}

/* Additional visual enhancements */
.glass-effect::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.glass-effect:hover::before {
    opacity: 1;
}

/* Loading shimmer effect */
@keyframes shimmer {
    0% {
        background-position: -200px 0;
    }
    100% {
        background-position: calc(200px + 100%) 0;
    }
}

.shimmer {
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    background-size: 200px 100%;
    animation: shimmer 2s infinite;
}
</style>