<template>
    <div class="min-h-screen bg-white dark:bg-gray-900">
      <!-- Navigation -->
      <nav class="fixed top-0 w-full bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center">
              <div class="flex-shrink-0 flex items-center">
                <span class="text-2xl font-bold text-orange-500 dark:text-orange-400">Triple-Taste</span>
              </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
              <!-- Search Bar -->
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="fas fa-search text-gray-400"></i>
                </div>
                <input
                  type="text"
                  class="w-64 pl-10 pr-4 py-2 rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-orange-500 dark:focus:ring-orange-400"
                  placeholder="Search for food..."
                >
              </div>

              <!-- Navigation Links -->
              <div class="flex items-center space-x-6">
                <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-orange-500 dark:hover:text-orange-400">Menu</a>
                <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-orange-500 dark:hover:text-orange-400">Restaurants</a>
                <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-orange-500 dark:hover:text-orange-400">Orders</a>
              </div>

              <!-- Action Icons -->
              <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <button class="relative p-2 text-gray-600 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400">
                  <i class="fas fa-bell text-xl"></i>
                  <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-orange-500"></span>
                </button>

                <!-- Cart -->
                <!-- <button class="relative p-2 text-gray-600 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400">
                  <i class="fas fa-shopping-cart text-xl"></i>
                  <span v-if="cartCount > 0"
                        class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-orange-500 rounded-full">
                    {{ cartCount }}
                  </span>
                </button> -->

                <!-- Theme Toggle -->
                <button @click="switchTheme"
                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400">
                  <i :class="isDark ? 'fas fa-sun' : 'fas fa-moon'" class="text-xl"></i>
                </button>

                <!-- User Profile -->
                <div class="relative" @click="showProfileMenu = !showProfileMenu">
                  <button class="flex items-center space-x-2">
                    <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                      <span class="text-sm font-medium text-gray-600 dark:text-gray-300">JD</span>
                    </div>
                  </button>

                  <!-- Profile Dropdown -->
                  <div v-if="showProfileMenu"
                       class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                    <div class="py-1">
                      <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                      <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>
                      <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Sign out</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
              <button @click="isMobileMenuOpen = !isMobileMenuOpen"
                      class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-300 hover:text-orange-500 dark:hover:text-orange-400">
                <i :class="isMobileMenuOpen ? 'fas fa-times' : 'fas fa-bars'" class="text-xl"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Mobile menu -->
        <div v-show="isMobileMenuOpen" class="md:hidden">
          <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Menu</a>
            <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Restaurants</a>
            <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Orders</a>
            <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
          </div>
        </div>
      </nav>

      <!-- Main Content -->
      <main class="pt-16 min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <slot></slot>
        </div>
      </main>

      <!-- Footer -->
      <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- About Section -->
            <div>
              <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">About</h3>
              <div class="mt-4 space-y-4">
                <a href="#" class="block text-base text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300">About Us</a>
                <a href="#" class="block text-base text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300">Careers</a>
                <a href="#" class="block text-base text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300">Blog</a>
              </div>
            </div>

            <!-- Support Section -->
            <div>
              <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Support</h3>
              <div class="mt-4 space-y-4">
                <a href="#" class="block text-base text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300">Help Center</a>
                <a href="#" class="block text-base text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300">Safety</a>
                <a href="#" class="block text-base text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300">Terms</a>
              </div>
            </div>

            <!-- Delivery Section -->
            <div>
              <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Delivery</h3>
              <div class="mt-4 space-y-4">
                <a href="#" class="block text-base text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300">Restaurants</a>
                <a href="#" class="block text-base text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300">Pricing</a>
                <a href="#" class="block text-base text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300">Cities</a>
              </div>
            </div>

            <!-- Contact Section -->
            <div>
              <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Contact</h3>
              <div class="mt-4 space-y-4">
                <p class="text-base text-gray-500 dark:text-gray-400">Triple-Taste Inc.</p>
                <p class="text-base text-gray-500 dark:text-gray-400">123 Food Street</p>
                <p class="text-base text-gray-500 dark:text-gray-400">contact@tripletaste.com</p>
              </div>
            </div>
          </div>

          <!-- Footer Bottom -->
          <div class="mt-12 border-t border-gray-200 dark:border-gray-800 pt-8">
            <p class="text-base text-gray-400 text-center">
              &copy; {{ new Date().getFullYear() }} Triple-Taste. All rights reserved.
            </p>
          </div>
        </div>
      </footer>
    </div>
  </template>

  <script setup>
  import { ref, onMounted } from 'vue'
  import { switchTheme } from '@/theme';
  // State
  const isDark = ref(false)
  const isMobileMenuOpen = ref(false)
  const showProfileMenu = ref(false)
  const cartCount = ref(2) // Example cart count

  // Theme toggle function
  const toggleTheme = () => {
    isDark.value = !isDark.value
    document.documentElement.classList.toggle('dark')
  }


  </script>

  <style scoped>
  /* Add any component-specific styles here */
  </style>
