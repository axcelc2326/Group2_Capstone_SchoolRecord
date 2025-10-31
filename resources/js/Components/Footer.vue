<script setup>
const currentYear = new Date().getFullYear()
</script>

<template>
  <!-- Ultra Modern Footer with Enhanced Glassmorphism -->
  <footer class="relative bg-gradient-to-br rounded-3xl from-white/5 via-white/10 to-white/15 backdrop-blur-2xl border-t border-white/30 shadow-[0_-20px_80px_rgba(0,0,0,0.3)] mt-8 overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute -top-4 -right-4 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-indigo-600/20 rounded-full blur-2xl animate-pulse"></div>
      <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-gradient-to-tr from-purple-400/15 to-pink-500/15 rounded-full blur-xl animate-pulse delay-1000"></div>
      <div class="absolute top-1/2 left-1/3 w-16 h-16 bg-gradient-to-r from-cyan-400/10 to-blue-500/10 rounded-full blur-lg animate-pulse delay-500"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 py-10">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between text-center md:text-left space-y-8 md:space-y-0 md:space-x-10">
        <!-- Enhanced Company Info -->
        <div class="flex items-center justify-center space-x-5 md:justify-start group cursor-pointer">
          <div
            class="relative w-16 h-16 bg-gradient-to-br from-blue-400 via-indigo-500 to-purple-600 text-white rounded-3xl flex items-center justify-center text-xl font-black shadow-2xl shadow-blue-500/30 transition-all duration-500 group-hover:scale-110 group-hover:rotate-6 group-hover:shadow-blue-500/50 overflow-hidden"
          >
            <!-- Logo image -->
            <img src="/images/Logo.jpg" alt="Logo" class="w-full h-full object-cover rounded-3xl relative z-10" />
            
            <!-- Inner glow effect -->
            <div class="absolute inset-1 bg-gradient-to-br from-white/20 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </div>
          <div class="space-y-1">
            <div
              class="text-3xl font-black bg-gradient-to-r from-white via-blue-100 to-indigo-200 bg-clip-text text-transparent transition-all duration-500 group-hover:bg-gradient-to-r group-hover:from-blue-200 group-hover:to-purple-200"
            >
              MDC-Connect
            </div>
            <div
              class="text-sm text-white/60 font-semibold tracking-wide uppercase transition-all duration-300 group-hover:text-white/80 group-hover:tracking-wider"
            >
              Announcements and Academic<br></br>Monitoring Portal
            </div>
          </div>
        </div>

        <!-- Enhanced Description -->
        <div class="max-w-2xl mx-auto md:mx-0">
          <p
            class="text-white/75 text-base leading-relaxed font-medium transition-all duration-500 hover:text-white/95 hover:scale-[1.02] cursor-default"
          >
            Empowering education through 
            <span class="bg-gradient-to-r from-blue-200 to-indigo-200 bg-clip-text text-transparent font-semibold">innovative technology solutions</span>.
            Connecting students, teachers, and parents in a 
            <span class="bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent font-semibold">seamless learning experience</span>.
          </p>
        </div>
      </div>

      <!-- Enhanced Bottom Footer -->
      <div class="border-t border-white/25 pt-8 mt-10">
        <div class="flex flex-col lg:flex-row justify-between items-center space-y-6 lg:space-y-0">
          <!-- Copyright with enhanced styling -->
          <div
            class="text-sm text-white/70 transition-all duration-300 hover:text-white/90 font-medium flex items-center space-x-2"
          >
            <span>© {{ currentYear }} MDC-Connect. All rights reserved.</span>
            <span class="hidden sm:inline">•</span>
            <span class="hidden sm:inline-flex items-center space-x-1">
              <span>Built with</span>
              <span class="text-red-400 animate-pulse text-base">❤️</span>
              <span>for education</span>
            </span>
          </div>

          <!-- Enhanced Status and Version -->
          <div class="flex items-center space-x-6">
            <!-- System Status -->
            <div
              class="flex items-center space-x-3 text-sm text-white/70 transition-all duration-300 hover:text-white/90 bg-white/5 rounded-full px-4 py-2 backdrop-blur-sm border border-white/10 hover:border-white/20"
            >
              <div class="relative">
                <div
                  class="w-3 h-3 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full animate-pulse shadow-lg shadow-green-400/60"
                ></div>
                <div class="absolute inset-0 w-3 h-3 bg-green-400 rounded-full animate-ping opacity-20"></div>
              </div>
              <span class="font-semibold">All systems operational</span>
            </div>

            <!-- Enhanced Version Badge -->
            <div
              class="text-xs text-white/60 font-bold bg-gradient-to-r from-white/10 to-white/5 px-4 py-2 rounded-full transition-all duration-500 hover:from-white/20 hover:to-white/10 hover:text-white/80 hover:scale-105 border border-white/10 hover:border-white/25 backdrop-blur-sm cursor-pointer"
            >
              <span class="bg-gradient-to-r from-blue-200 to-indigo-200 bg-clip-text text-transparent">v2.1.0</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>