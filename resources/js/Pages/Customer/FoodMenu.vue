```vue
// resources/js/Pages/Customer/FoodMenu.vue

<template>
  <CustomerLayout>
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="md:flex md:items-center md:justify-between">
          <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-gray-100 sm:text-3xl sm:truncate">
              Our Menu
            </h2>
            <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-6">
              <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-store flex-shrink-0 mr-1.5 text-gray-400 dark:text-gray-500"></i>
                {{ branch.name }}
              </div>
              <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-clock flex-shrink-0 mr-1.5 text-gray-400 dark:text-gray-500"></i>
                {{ isOpen ? 'Open Now' : 'Closed' }}
              </div>
              <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-motorcycle flex-shrink-0 mr-1.5 text-gray-400 dark:text-gray-500"></i>
                {{ orderType === 'delivery' ? 'Delivery' : 'Collection' }}
              </div>
            </div>
          </div>

          <div class="mt-4 flex md:mt-0 md:ml-4">
            <button
              type="button"
              class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
              @click="toggleOrderType"
            >
              <i :class="[
                'mr-2',
                orderType === 'delivery' ? 'fas fa-store' : 'fas fa-motorcycle'
              ]"></i>
              Switch to {{ orderType === 'delivery' ? 'Collection' : 'Delivery' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex flex-col lg:flex-row lg:space-x-8">
        <!-- Categories Sidebar -->
        <div class="lg:w-64 flex-shrink-0 mb-8 lg:mb-0">
          <div class="sticky top-8 space-y-4">
            <nav class="space-y-1" aria-label="Categories">
              <button
                v-for="category in categories"
                :key="category.id"
                @click="scrollToCategory(category.id)"
                class="w-full text-left px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out"
                :class="[
                  activeCategory === category.id
                    ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200'
                    : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'
                ]"
              >
                {{ category.name }}
              </button>
            </nav>
          </div>
        </div>

        <!-- Food Items Grid -->
        <div class="flex-1">
          <div v-for="category in categories" :key="category.id" :id="`category-${category.id}`" class="mb-12">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
              {{ category.name }}
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="food in getFoodsByCategory(category.id)"
                :key="food.id"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 border border-gray-200 dark:border-gray-700 overflow-hidden"
              >
                <!-- Food Image -->
                <div class="relative h-48 overflow-hidden">
                  <img
                    :src="food.image_path ? `/storage/${food.image_path}` : '/images/placeholder-food.png'"
                    :alt="food.name"
                    class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-300"
                  >
                  <div
                    v-if="food.is_new"
                    class="absolute top-2 right-2 bg-indigo-600 text-white px-2 py-1 rounded-full text-xs font-semibold"
                  >
                    New
                  </div>
                </div>

                <!-- Food Details -->
                <div class="p-4">
                  <div class="flex justify-between items-start">
                    <div>
                      <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ food.name }}
                      </h4>
                      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                        {{ food.description }}
                      </p>
                    </div>
                    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                      ${{ food.base_price }}
                    </div>
                  </div>

                  <!-- Food Badges -->
                  <div class="mt-2 flex flex-wrap gap-2">
                    <span
                      v-if="food.is_vegetarian"
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                    >
                      <i class="fas fa-leaf mr-1"></i>
                      Vegetarian
                    </span>
                    <span
                      v-if="food.is_spicy"
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200"
                    >
                      <i class="fas fa-pepper-hot mr-1"></i>
                      Spicy
                    </span>
                    <span
                      v-if="food.preparation_time"
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200"
                    >
                      <i class="fas fa-clock mr-1"></i>
                      {{ food.preparation_time }}min
                    </span>
                  </div>

                  <!-- Add to Cart Button -->
                  <button
                    @click="showFoodModal(food)"
                    class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                  >
                    <i class="fas fa-plus mr-2"></i>
                    Add to Cart
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Food Modal -->
    <TransitionRoot appear :show="!!selectedFood" as="template">
  <Dialog as="div" class="relative z-50" @close="closeFoodModal">
    <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
      <div class="fixed inset-0 bg-black bg-opacity-25" />
    </TransitionChild>

    <div class="fixed inset-0 overflow-y-auto">
      <div class="flex min-h-full items-center justify-center p-4 text-center">
        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95" enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
          <DialogPanel class="w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 text-left align-middle shadow-xl transition-all">
            <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
              {{ selectedFood?.name }}
            </DialogTitle>

            <div class="mt-4">
              <!-- Food Image -->
              <img :src="selectedFood?.image_path ? `/storage/${selectedFood.image_path}` : '/images/placeholder-food.png'" :alt="selectedFood?.name" class="w-full h-48 object-cover rounded-lg">

              <!-- Description -->
              <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                {{ selectedFood?.description }}
              </p>

              <div v-if="selectedFood?.extra_options.length" class="mt-6">
                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Extra Options</h4>
                <div class="mt-2 space-y-2">
                    <label v-for="option in selectedFood.extra_options" :key="option.id" class="flex items-center">
                        <input type="checkbox" v-model="selectedExtras" :value="option" class="h-4 w-4 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 border-gray-300 dark:border-gray-600 rounded">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
  {{ option.name }} (+${{ parseFloat(option.price || 0).toFixed(2) }})
</span>

                    </label>
                </div>
            </div>


              <!-- Special Instructions -->
              <div class="mt-6">
                <label for="special-instructions" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Special Instructions</label>
                <textarea id="special-instructions" v-model="specialInstructions" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm dark:bg-gray-700 dark:text-gray-300" placeholder="Any special requests?"></textarea>
              </div>

              <!-- Quantity -->
              <div class="mt-6 flex items-center space-x-4">
                <label for="quantity" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Quantity:</label>
                <div class="flex items-center space-x-2">
                  <button @click="quantity > 1 && quantity--" class="p-1 rounded-full text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400" :disabled="quantity <= 1">
                    <i class="fas fa-minus-circle"></i>
                  </button>
                  <span class="text-gray-900 dark:text-gray-100 w-8 text-center">{{ quantity }}</span>
                  <button @click="quantity++" class="p-1 rounded-full text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                    <i class="fas fa-plus-circle"></i>
                  </button>
                </div>
              </div>

              <!-- Total Price -->
              <div class="mt-6 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Total Price:</span>
                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ calculateTotalPrice().toFixed(2) }}</span>
              </div>

              <!-- Action Buttons -->
              <div class="mt-6 flex justify-end space-x-3">
                <button type="button" class="inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400" @click="closeFoodModal">Cancel</button>
                <button type="button" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400" @click="addToCart">
                  <i class="fas fa-shopping-cart mr-2"></i> Add to Cart - ${{ calculateTotalPrice().toFixed(2) }}
                </button>
              </div>
            </div>
          </DialogPanel>
        </TransitionChild>
      </div>
    </div>
  </Dialog>
</TransitionRoot>

    <!-- Floating Cart Summary (Mobile) -->
    <div
      v-if="cartItemsCount > 0"
      class="fixed bottom-0 inset-x-0 lg:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg p-4"
    >
      <div class="flex items-center justify-between max-w-7xl mx-auto">
        <div class="flex items-center space-x-4">
          <div class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900">
            <i class="fas fa-shopping-cart text-indigo-600 dark:text-indigo-400"></i>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
              {{ cartItemsCount }} items
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              ${{ subtotal.toFixed(2) }}
            </p>
          </div>
        </div>
        <button
          @click="openCart"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
        >
          View Cart
        </button>
      </div>
    </div>

    <!-- Quick Category Navigation (Mobile) -->
    <div class="fixed left-0 right-0 bottom-0 lg:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 transform translate-y-full transition-transform duration-300 ease-in-out"
         :class="{ 'translate-y-0': showCategoryNav }"
    >
      <div class="px-4 py-3">
        <div class="flex items-center justify-between mb-2">
          <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">
            Categories
          </h3>
          <button
            @click="showCategoryNav = false"
            class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <button
            v-for="category in categories"
            :key="category.id"
            @click="scrollToCategory(category.id); showCategoryNav = false"
            class="text-left px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out"
            :class="[
              activeCategory === category.id
                ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200'
                : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'
            ]"
          >
            {{ category.name }}
          </button>
        </div>
      </div>
    </div>

    <!-- Category Navigation Toggle (Mobile) -->
    <button
      @click="showCategoryNav = !showCategoryNav"
      class="fixed bottom-20 right-4 lg:hidden bg-indigo-600 text-white rounded-full p-3 shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
      <i class="fas fa-list"></i>
    </button>

    <!-- Loading State -->
    <div
      v-if="loading"
      class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>
    </div>

    <!-- No Foods Available Message -->
    <div
      v-if="!hasFoods"
      class="text-center py-12"
    >
      <div class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-500">
        <i class="fas fa-utensils text-6xl"></i>
      </div>
      <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">
        No Food Items Available
      </h3>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Please check back later or try another branch.
      </p>
      <div class="mt-6">
        <Link
          :href="route('customer.locations')"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <i class="fas fa-store mr-2"></i>
          Find Another Branch
        </Link>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { useCart } from '@/Composables/useCart'
import debounce from 'lodash/debounce'

const props = defineProps({
    branch: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    foods: {
        type: Object,
        required: true
    },
    orderType: {
        type: String,
        default: 'collection'
    }
})

// Cart handling
const { addToCart } = useCart()

// State management
const selectedFood = ref(null)
const selectedExtras = ref([])
const specialInstructions = ref('')
const quantity = ref(1)
const activeCategory = ref(null)
const isModalOpen = ref(false) // Track modal open state for accessibility

// Category scrolling
const categoryObserver = ref(null)
const observerOptions = {
    root: null,
    rootMargin: '-20% 0px -80% 0px', // Adjust based on desired visibility
    threshold: 0
}

// Computed properties
const isOpen = computed(() => {
    if (!props.branch.opening_hours) return false
    const now = new Date()
    const day = now.toLocaleDateString('en-US', { weekday: 'long' })
    const time = now.toLocaleTimeString('en-US', { hour12: false })

    const todayHours = props.branch.opening_hours.find(h => h.day === day)
    if (!todayHours) return false

    return time >= todayHours.open && time <= todayHours.close
})

// Methods
const getFoodsByCategory = (categoryId) => {
    return props.foods[categoryId] || []
}

const showFoodModal = (food) => {
    selectedFood.value = {
        ...food,
        extra_options: food.extra_options || [] // Ensure extra_options exists
    }
    selectedExtras.value = []
    specialInstructions.value = ''
    quantity.value = 1
}


const closeFoodModal = () => {
    selectedFood.value = null
    selectedExtras.value = []
    specialInstructions.value = ''
    quantity.value = 1
    isModalOpen.value = false // Close modal
}

const calculateTotalPrice = () => {
    if (!selectedFood.value) return 0

    const basePrice = parseFloat(selectedFood.value.base_price)
    const extrasTotal = selectedExtras.value.reduce((sum, extra) => {
        return sum + parseFloat(extra.price)
    }, 0)

    return (basePrice + extrasTotal) * quantity.value
}


// Adding selected food item to cart
const handleAddToCart = () => {
    if (!selectedFood.value) return

    const item = {
        food: selectedFood.value,
        extras: selectedExtras.value,
        quantity: quantity.value,
        special_instructions: specialInstructions.value
    }

    addToCart(item)
    closeFoodModal()
}

const toggleOrderType = () => {
    const newType = props.orderType === 'delivery' ? 'collection' : 'delivery'
    router.get(route('customer.menu', {
        branch: props.branch.id,
        type: newType
    }), {}, {
        preserveState: true,
        preserveScroll: true
    })
}

const scrollToCategory = (categoryId) => {
    const element = document.getElementById(`category-${categoryId}`)
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' })
        activeCategory.value = categoryId // Update active category on manual scroll
    }
}

// Intersection Observer setup for category highlighting
const setupCategoryObserver = () => {
    if (categoryObserver.value) {
        categoryObserver.value.disconnect()
    }

    categoryObserver.value = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const categoryId = parseInt(entry.target.id.replace('category-', ''))
                activeCategory.value = categoryId
            }
        })
    }, observerOptions)

    // Observe all category sections
    props.categories.forEach(category => {
        const element = document.getElementById(`category-${category.id}`)
        if (element) {
            categoryObserver.value.observe(element)
        }
    })
}

// Handle scroll position restore
const saveScrollPosition = debounce(() => {
    sessionStorage.setItem('menu-scroll-position', window.scrollY.toString())
}, 100)

// Lifecycle hooks
onMounted(() => {
    setupCategoryObserver()

    // Restore scroll position
    const savedPosition = sessionStorage.getItem('menu-scroll-position')
    if (savedPosition) {
        window.scrollTo(0, parseInt(savedPosition))
        sessionStorage.removeItem('menu-scroll-position')
    }

    window.addEventListener('scroll', saveScrollPosition)
})

onUnmounted(() => {
    if (categoryObserver.value) {
        categoryObserver.value.disconnect()
    }
    window.removeEventListener('scroll', saveScrollPosition)
})
</script>


<style scoped>
.sticky {
    position: sticky;
    top: 1rem;
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Category transition */
.category-enter-active,
.category-leave-active {
    transition: all 0.3s ease;
}

.category-enter-from,
.category-leave-to {
    opacity: 0;
    transform: translateX(30px);
}

/* Food item hover effect */
.food-item {
    transition: transform 0.2s ease;
}

.food-item:hover {
    transform: translateY(-2px);
}

/* Custom scrollbar for category navigation */
.category-nav {
    scrollbar-width: thin;
    scrollbar-color: theme('colors.gray.400') theme('colors.gray.200');
}

.category-nav::-webkit-scrollbar {
    width: 6px;
}

.category-nav::-webkit-scrollbar-track {
    background: theme('colors.gray.200');
    border-radius: 3px;
}

.category-nav::-webkit-scrollbar-thumb {
    background-color: theme('colors.gray.400');
    border-radius: 3px;
}

/* Dark mode scrollbar */
.dark .category-nav {
    scrollbar-color: theme('colors.gray.600') theme('colors.gray.800');
}

.dark .category-nav::-webkit-scrollbar-track {
    background: theme('colors.gray.800');
}

.dark .category-nav::-webkit-scrollbar-thumb {
    background-color: theme('colors.gray.600');
}
</style>
