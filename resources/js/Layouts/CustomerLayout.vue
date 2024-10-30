<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
      <!-- Header -->
      <header class="bg-white dark:bg-gray-800 shadow-sm">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <!-- Logo and Main Nav -->
            <div class="flex">
              <div class="flex-shrink-0 flex items-center">
                <Link :href="route('home')">
                  <ApplicationLogo class="block h-8 w-auto" />
                </Link>
              </div>

              <!-- Main Navigation -->
              <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                <Link
                  :href="route('customer.locations')"
                  class="inline-flex items-center px-1 pt-1 border-b-2"
                  :class="[
                    route().current('customer.locations')
                      ? 'border-indigo-500 text-gray-900 dark:text-gray-100'
                      : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
                  ]"
                >
                  Locations
                </Link>

                <Link
                  v-if="selectedBranch"
                  :href="route('customer.menu', { branch: selectedBranch.id })"
                  class="inline-flex items-center px-1 pt-1 border-b-2"
                  :class="[
                    route().current('customer.menu')
                      ? 'border-indigo-500 text-gray-900 dark:text-gray-100'
                      : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
                  ]"
                >
                  Menu
                </Link>

                <Link
                  :href="route('customer.orders.index')"
                  class="inline-flex items-center px-1 pt-1 border-b-2"
                  :class="[
                    route().current('customer.orders.index')
                      ? 'border-indigo-500 text-gray-900 dark:text-gray-100'
                      : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
                  ]"
                >
                  Orders
                </Link>
              </div>
            </div>

            <!-- Right Side Nav -->
            <div class="flex items-center">
              <!-- Shopping Cart -->
              <button
                @click="toggleCart"
                class="relative p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            >
                <i class="fas fa-shopping-cart text-xl"></i>
                <span
                v-show="itemCount > 0"
                class="absolute -top-1 -right-1 flex items-center justify-center h-5 w-5 text-xs font-bold text-white bg-indigo-600 rounded-full"
                >
                {{ itemCount }}
                </span>
            </button>

              <!-- User Menu -->
              <div class="ml-3 relative">
                <Dropdown align="right" width="48">
                  <template #trigger>
                    <button
                      class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition duration-150 ease-in-out"
                    >
                      <span>{{ $page.props.auth.user.name }}</span>
                      <i class="fas fa-chevron-down ml-2"></i>
                    </button>
                  </template>

                  <template #content>
                    <DropdownLink :href="route('profile.edit')">
                      Profile
                    </DropdownLink>
                    <DropdownLink>
                      Addresses
                    </DropdownLink>
                    <DropdownLink :href="route('customer.orders.index')">
                      Orders
                    </DropdownLink>
                    <div class="border-t border-gray-200 dark:border-gray-600"></div>
                    <form @submit.prevent="logout">
                      <DropdownLink as="button">
                        Log Out
                      </DropdownLink>
                    </form>
                  </template>
                </Dropdown>
              </div>
            </div>
          </div>
        </nav>
      </header>

      <!-- Selected Branch Bar (if a branch is selected) -->
      <div
        v-if="selectedBranch"
        class="bg-indigo-600 text-white"
      >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <i class="fas fa-store"></i>
              <span>{{ selectedBranch.name }}</span>
              <span class="text-indigo-200">|</span>
              <span class="text-sm text-indigo-200">
                {{ selectedBranch.address }}
              </span>
            </div>
            <Link
              :href="route('customer.locations')"
              class="text-sm text-indigo-200 hover:text-white"
            >
              Change Branch
            </Link>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <main>
        <div class="py-6">
          <slot />
        </div>
      </main>

      <!-- Footer -->
      <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div class="col-span-2 md:col-span-1">
              <ApplicationLogo class="h-8 w-auto mb-4" />
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Delicious food delivered to your doorstep
              </p>
            </div>

            <!-- Quick Links -->
            <div>
              <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 tracking-wider uppercase">
                Quick Links
              </h3>
              <ul class="mt-4 space-y-4">
                <li>
                  <Link :href="route('customer.locations')"
                    class="text-base text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200"
                  >
                    Locations
                  </Link>
                </li>
                <li>
                  <Link :href="route('customer.orders.index')"
                    class="text-base text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200"
                  >
                    Orders
                  </Link>
                </li>
              </ul>
            </div>

            <!-- Contact -->
            <div>
              <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 tracking-wider uppercase">
                Contact Us
              </h3>
              <ul class="mt-4 space-y-4">
                <li class="text-base text-gray-500 dark:text-gray-400">
                  <i class="fas fa-phone mr-2"></i>
                  +1 234 567 890
                </li>
                <li class="text-base text-gray-500 dark:text-gray-400">
                  <i class="fas fa-envelope mr-2"></i>
                  support@example.com
                </li>
              </ul>
            </div>

            <!-- Social Links -->
            <div>
              <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 tracking-wider uppercase">
                Follow Us
              </h3>
              <div class="mt-4 flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-gray-500">
                  <i class="fab fa-facebook text-xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-gray-500">
                  <i class="fab fa-twitter text-xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-gray-500">
                  <i class="fab fa-instagram text-xl"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
            <p class="text-base text-gray-400 text-center">
              © {{ new Date().getFullYear() }} Your Company. All rights reserved.
            </p>
          </div>
        </div>
      </footer>

      <!-- Cart Sidebar -->
      <CartSidebar
        :show="showCart"
        @close="showCart = false"
    />
    </div>
  </template>

  <script setup>
  import { ref, computed } from 'vue'
  import { Link, usePage } from '@inertiajs/vue3'
  import { useCart } from '@/Composables/useCart'
  import ApplicationLogo from '@/Components/ApplicationLogo.vue'
  import Dropdown from '@/Components/Dropdown.vue'
  import DropdownLink from '@/Components/DropdownLink.vue'
  import CartSidebar from '@/Components/CartSidebar.vue'


  const showCart = ref(false)
  const { itemCount } = useCart()

  const toggleCart = () => {
    showCart.value = !showCart.value
    }

  const { selectedBranch } = usePage().props


  const openCart = () => {
    showCart.value = true
  }

  const closeCart = () => {
    showCart.value = false
  }

  const logout = () => {
    router.post(route('logout'))
  }
  </script>

  <style scoped>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.3s;
  }

  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }

  /* Custom scrollbar */
  ::-webkit-scrollbar {
    width: 6px;
  }

  ::-webkit-scrollbar-track {
    @apply bg-gray-100 dark:bg-gray-800;
  }

  ::-webkit-scrollbar-thumb {
    @apply bg-gray-400 dark:bg-gray-600 rounded;
  }

  ::-webkit-scrollbar-thumb:hover {
    @apply bg-gray-500 dark:bg-gray-500;
  }
  </style>
