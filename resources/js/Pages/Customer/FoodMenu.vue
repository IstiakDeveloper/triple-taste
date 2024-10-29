<template>
    <CustomerLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="space-y-6">
                <h1 class="text-2xl font-bold text-gray-100">{{ branch.name }}</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ orderType === 'delivery' ? 'Delivery' : 'Collection' }}
                </p>

                <!-- Categories and Food Items -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Categories Sidebar -->
                    <div class="md:col-span-1">
                        <div class="bg-gray-200 dark:bg-gray-800 rounded-lg shadow p-4 sticky top-4">
                            <h2 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4">Categories</h2>
                            <nav class="space-y-2">
                                <button v-for="category in categories" :key="category.id"
                                    @click="scrollToCategory(category.id)"
                                    class="w-full text-left px-4 py-2 rounded-md hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-300"
                                    :class="{ 'bg-gray-300 dark:bg-gray-700': activeCategory === category.id }">
                                    {{ category.name }}
                                </button>
                            </nav>
                        </div>
                    </div>

                    <!-- Food Items -->
                    <div class="md:col-span-3 space-y-8">
                        <div v-for="category in categories" :key="category.id" :id="'category-' + category.id"
                            class="space-y-6">
                            <h2 class="text-xl font-bold text-gray-100 border-b pb-2">{{ category.name }}</h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div v-for="food in getFoodsByCategory(category.id)" :key="food.id"
                                    class="bg-gray-200 dark:bg-gray-700 rounded-lg shadow overflow-hidden">
                                    <img v-if="food.image_path" :src="getImageUrl(food.image_path)" :alt="food.name"
                                        class="w-full h-48 object-cover" />

                                    <div class="p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{
                                                    food.name }}</h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ food.description
                                                    }}</p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span v-if="food.is_vegetarian" class="text-green-400">
                                                    <VegetarianIcon class="w-5 h-5" />
                                                </span>
                                                <span v-if="food.is_spicy" class="text-red-400">
                                                    <SpicyIcon class="w-5 h-5" />
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Additional Information -->
                                        <div class="mt-2 text-gray-600 dark:text-gray-300">
                                            <p class="text-sm">Preparation Time: {{ food.preparation_time }} mins</p>
                                            <p class="text-sm">
                                                Availability:
                                                <span v-if="food.is_available" class="text-green-500">Available</span>
                                                <span v-else class="text-red-500">Not Available</span>
                                            </p>
                                            <p class="text-sm" v-if="food.allergens && food.allergens.length">
                                                Allergens: <span v-for="(allergen, index) in food.allergens"
                                                    :key="index">{{ allergen }}<span
                                                        v-if="index < food.allergens.length - 1">, </span></span>
                                            </p>
                                        </div>

                                        <div class="flex justify-between items-center mt-4">
                                            <span class="font-semibold text-gray-900 dark:text-gray-100">£{{
                                                food.base_price }}</span>
                                            <button @click="openFoodModal(food)"
                                                class="bg-indigo-600 dark:bg-indigo-700 text-white px-4 py-2 rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600">
                                                Add to Order
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Food Modal -->
            <TransitionRoot appear :show="isModalOpen" as="template">
                <Dialog as="div" @close="closeModal" class="relative z-50">
                    <div class="fixed inset-0 bg-black bg-opacity-25" />

                    <div class="fixed inset-0 overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4">
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded-2xl bg-gray-800 p-6 text-left align-middle shadow-xl transition-all">
                                <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-100">
                                    {{ selectedFood?.name }}
                                </DialogTitle>

                                <div class="mt-2">
                                    <p class="text-sm text-gray-400">
                                        {{ selectedFood?.description }}
                                    </p>

                                    <!-- Extra Options -->
                                    <div v-if="selectedFood?.extra_options?.length" class="mt-4">
                                        <h4 class="font-medium text-sm text-gray-300 mb-2">Extra Options</h4>
                                        <div class="space-y-2">
                                            <label v-for="option in selectedFood.extra_options" :key="option.id"
                                                class="flex items-center space-x-3 text-gray-300">
                                                <input type="checkbox" v-model="selectedExtras" :value="option.id"
                                                    class="rounded border-gray-600 bg-gray-700 text-indigo-600">
                                                <span>{{ option.name }} (+£{{ option.price }})</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-300">Quantity</label>
                                        <div class="flex items-center space-x-3 mt-1">
                                            <button @click="quantity > 1 && quantity--"
                                                class="rounded-md border p-2 border-gray-600">
                                                <MinusIcon class="w-4 h-4 text-gray-300" />
                                            </button>
                                            <span class="w-8 text-center text-gray-100">{{ quantity }}</span>
                                            <button @click="quantity++" class="rounded-md border p-2 border-gray-600">
                                                <PlusIcon class="w-4 h-4 text-gray-300" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Special Instructions -->
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-300">Special
                                            Instructions</label>
                                        <textarea v-model="specialInstructions" rows="2"
                                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>
                                </div>

                                <div class="mt-4 flex justify-between items-center">
                                    <div class="font-medium text-gray-100">
                                        Total: £{{ calculateTotal }}
                                    </div>
                                    <button @click="addToCart"
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                        Add to Cart
                                    </button>
                                </div>
                            </DialogPanel>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>

            <!-- Updated Cart Preview -->
            <div v-if="cart.length > 0"
                class="fixed bottom-0 inset-x-0 bg-gray-200 dark:bg-gray-800 border-t border-gray-300 dark:border-gray-700 shadow-lg p-4">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ cart.length }} items</span>
                            <span class="mx-2">|</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">Total: £{{ cartTotal }}</span>
                        </div>
                        <button @click="proceedToCheckout"
                            class="bg-indigo-600 dark:bg-indigo-700 text-white px-6 py-3 rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600">
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>

            <!-- Auth Modal -->
            <TransitionRoot appear :show="showAuthModal" as="template">
                <Dialog as="div" @close="closeAuthModal" class="relative z-50">
                    <div class="fixed inset-0 bg-black bg-opacity-25" />

                    <div class="fixed inset-0 overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4">
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded-2xl bg-gray-800 p-6 text-left align-middle shadow-xl transition-all">
                                <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-100">
                                    Authentication Required
                                </DialogTitle>

                                <div class="mt-4">
                                    <p class="text-sm text-gray-400">
                                        Please log in or create an account to proceed with your order.
                                    </p>

                                    <div class="mt-6 flex space-x-4">
                                        <button @click="redirectToLogin"
                                            class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                            Login
                                        </button>
                                        <button @click="redirectToRegister"
                                            class="flex-1 border border-indigo-600 text-indigo-600 px-4 py-2 rounded-md hover:bg-indigo-600 hover:text-white">
                                            Sign Up
                                        </button>
                                    </div>
                                </div>
                            </DialogPanel>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>
        </div>
    </CustomerLayout>
</template>

<script setup>
import { ref, computed, onMounted, } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue'
import { MinusIcon, PlusIcon } from '@heroicons/vue/24/outline'
import { router } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'


const props = defineProps({
    branch: Object,
    categories: Array,
    foods: Object,
    orderType: String,
    auth: Object // Add this prop to check authentication status
})

// Previous state declarations remain the same
const showAuthModal = ref(false)

// Load cart from session storage on mount
onMounted(() => {
    const savedCart = sessionStorage.getItem('foodCart')
    if (savedCart) {
        try {
            const cartData = JSON.parse(savedCart)
            if (cartData.branchId === props.branch.id) {
                cart.value = cartData.items
            }
        } catch (error) {
            console.error('Error loading cart:', error)
            sessionStorage.removeItem('foodCart')
        }
    }
})

// Updated addToCart function
const addToCart = () => {
    const cartItem = {
        id: Date.now(),
        food: selectedFood.value,
        quantity: quantity.value,
        extras: selectedExtras.value,
        specialInstructions: specialInstructions.value,
        total: parseFloat(calculateTotal.value)
    }

    cart.value.push(cartItem)

    // Save to session storage
    sessionStorage.setItem('foodCart', JSON.stringify({
        items: cart.value,
        branchId: props.branch.id,
        orderType: props.orderType,
        lastUpdated: new Date().toISOString()
    }))

    closeModal()
}

// Auth related methods
const closeAuthModal = () => {
    showAuthModal.value = false
}

const redirectToLogin = () => {
    // Save current path for redirect after login
    sessionStorage.setItem('redirect_after_auth', window.location.pathname)
    router.visit(route('login'))
}

const redirectToRegister = () => {
    sessionStorage.setItem('redirect_after_auth', window.location.pathname)
    router.visit(route('register'))
}

const proceedToCheckout = () => {
    if (!props.auth?.user) {
        showAuthModal.value = true;
        return;
    }

    // Store cart in session storage
    const cartData = {
        items: cart.value,
        branchId: props.branch.id,
        orderType: props.orderType,
        lastUpdated: new Date().toISOString()
    };

    sessionStorage.setItem('foodCart', JSON.stringify(cartData));

    // Pass branch ID in the route
    router.visit(route('customer.checkout', { branch: props.branch.id }));
};




// State
const activeCategory = ref(null)
const isModalOpen = ref(false)
const selectedFood = ref(null)
const selectedExtras = ref([])
const quantity = ref(1)
const specialInstructions = ref('')
const cart = ref([])

const getImageUrl = (imagePath) => {
    if (!imagePath) return null
    const cleanPath = imagePath.startsWith('/') ? imagePath.slice(1) : imagePath
    return `${window.location.origin}/storage/${cleanPath}`
}
// Methods
const scrollToCategory = (categoryId) => {
    activeCategory.value = categoryId
    document.getElementById(`category-${categoryId}`).scrollIntoView({ behavior: 'smooth' })
}

const getFoodsByCategory = (categoryId) => {
    return props.foods[categoryId] || []
}

const openFoodModal = (food) => {
    selectedFood.value = food
    selectedExtras.value = []
    quantity.value = 1
    specialInstructions.value = ''
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    selectedFood.value = null
}

const calculateTotal = computed(() => {
    if (!selectedFood.value) return 0; // No selected food

    let total = parseFloat(selectedFood.value.base_price) || 0; // Ensure base price is a number

    // Add extras
    selectedExtras.value.forEach(extraId => {
        const extra = selectedFood.value.extra_options.find(opt => opt.id === extraId);
        if (extra) {
            total += parseFloat(extra.price) || 0; // Ensure extra price is a number
        }
    });

    return (total * quantity.value).toFixed(2);
});


const cartTotal = computed(() => {
    return cart.value.reduce((total, item) => {
        return total + item.total
    }, 0).toFixed(2)
})


</script>

<style scoped>
/* Additional styles if necessary */
</style>
