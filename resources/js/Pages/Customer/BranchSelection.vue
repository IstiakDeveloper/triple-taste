<template>
    <CustomerLayout>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            Available Branches ({{ filteredBranches.length }})
          </h2>

          <!-- Search Bar -->
          <div class="relative mb-6 max-w-lg mx-auto">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search branches..."
              class="w-full py-2 px-4 rounded-md border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600"
            />
            <i class="fas fa-search absolute right-4 top-2.5 text-gray-500 dark:text-gray-400"></i>
          </div>

          <!-- Branches Grid -->
          <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="branch in filteredBranches"
                :key="branch.id"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition duration-150 ease-in-out hover:shadow-md"
                data-aos="fade-up"
                data-aos-delay="100"
              >
            <TransitionGroup name="list" tag="div">

                <div class="flex justify-between items-start">
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ branch.name }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ branch.address }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Contact: {{ branch.contact_number }}</p>

                    <!-- Distance & Delivery Info -->
                    <div class="mt-2 flex items-center space-x-4">
                      <span class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-map-marker-alt mr-1 text-indigo-500"></i>
                        {{ formatDistance(branch.distance) }}
                      </span>
                      <span class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-truck mr-1 text-indigo-500"></i>
                        Delivers up to {{ branch.delivery_radius }} km
                      </span>
                    </div>

                    <!-- Opening Days & Hours -->
                    <div class="mt-2 space-y-1">
                      <span :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                          isOpen(branch.opening_hours) ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300'
                        ]">
                        {{ isOpen(branch.opening_hours) ? 'Open Now' : 'Closed' }}
                      </span>
                      <ul class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        <li v-for="hours in branch.opening_hours" :key="hours.day">
                          {{ hours.day }}: {{ hours.open }} - {{ hours.close }}
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 flex flex-wrap gap-3">
                    <Link
                    v-if="isDeliveryAvailable(branch)"
                    :href="route('customer.branch.menu', { branch: branch.id, type: 'delivery' })"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    >
                    <i class="fas fa-motorcycle mr-2"></i>
                    Order Delivery
                    </Link>

                    <Link
                    :href="route('customer.branch.menu', { branch: branch.id, type: 'collection' })"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    >
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Order Collection
                    </Link>

                  <a
                    :href="getDirectionsUrl(branch)"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                  >
                    <i class="fas fa-directions mr-2"></i>
                    Directions
                  </a>
                </div>

            </TransitionGroup>
        </div>
          </div>
        </div>
      </div>
    </CustomerLayout>
  </template>

  <script setup>
  import { computed, ref } from 'vue'
  import { Link } from '@inertiajs/vue3'
  import CustomerLayout from '@/Layouts/CustomerLayout.vue'
  import AOS from 'aos'
  import 'aos/dist/aos.css'

  const props = defineProps({
    branches: {
      type: Array,
      required: true
    }
  })

  const searchQuery = ref('')

  // Filter branches based on search query
  const filteredBranches = computed(() => {
    return props.branches.filter(branch => branch.name.toLowerCase().includes(searchQuery.value.toLowerCase()))
  })

  // Initialize AOS
  AOS.init({ duration: 700 })

  // Check if branch is open
  const isOpen = (hours) => {
    if (!hours) return false

    const now = new Date()
    const day = now.toLocaleDateString('en-US', { weekday: 'long' })
    const time = now.toLocaleTimeString('en-US', { hour12: false })

    const todayHours = hours.find(h => h.day === day)
    if (!todayHours) return false

    return time >= todayHours.open && time <= todayHours.close
  }

  // Check if delivery is available based on distance
  const isDeliveryAvailable = (branch) => {
    if (!branch.distance || !branch.delivery_radius) return true
    return branch.distance <= branch.delivery_radius
  }

  // Format distance for display
  const formatDistance = (distance) => {
    if (!distance) return 'Distance unknown'
    return `${distance.toFixed(1)} km away`
  }

  // Get Google Maps directions URL
  const getDirectionsUrl = (branch) => {
    const destination = `${branch.latitude},${branch.longitude}`
    return `https://www.google.com/maps/dir/?api=1&destination=${destination}`
  }
  </script>

  <style scoped>
  /* Additional styles if necessary */
  </style>
