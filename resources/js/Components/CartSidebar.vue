<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" class="relative z-50" @close="close">
            <!-- Background overlay -->
            <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0"
                enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <!-- Sliding cart panel -->
            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                        <TransitionChild as="template" enter="transform transition ease-in-out duration-300"
                            enter-from="translate-x-full" enter-to="translate-x-0"
                            leave="transform transition ease-in-out duration-300" leave-from="translate-x-0"
                            leave-to="translate-x-full">
                            <DialogPanel class="pointer-events-auto w-screen max-w-md">
                                <div class="flex h-full flex-col bg-white dark:bg-gray-800 shadow-xl">
                                    <!-- Header -->
                                    <div
                                        class="flex items-center justify-between px-4 py-6 sm:px-6 bg-indigo-600 text-white">
                                        <DialogTitle class="text-lg font-medium">
                                            Shopping Cart
                                            <span class="text-sm text-indigo-200 ml-2">
                                                ({{ cartItemsCount }} items)
                                            </span>
                                        </DialogTitle>
                                        <button type="button" class="text-indigo-200 hover:text-white" @click="close">
                                            <span class="sr-only">Close panel</span>
                                            <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                                        </button>
                                    </div>

                                    <!-- Cart Items -->
                                    <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                                        <div v-if="!cartItems.length" class="text-center py-12">
                                            <div class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-500">
                                                <i class="fas fa-shopping-cart text-6xl"></i>
                                            </div>
                                            <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">
                                                Your cart is empty
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Start adding some delicious items to your cart!
                                            </p>
                                            <div class="mt-6">
                                                <Link :href="route('customer.menu', { branch: selectedBranch?.id })"
                                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                                View Menu
                                                </Link>
                                            </div>
                                        </div>

                                        <div v-else class="space-y-4">
                                            <TransitionGroup name="list" tag="div" class="space-y-4">
                                                <div v-for="item in cartItems" :key="item.id"
                                                    class="flex items-start space-x-4 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                                    <!-- Food Image -->
                                                    <img :src="item.food.image_path
                                                        ? `/storage/${item.food.image_path}`
                                                        : '/images/placeholder-food.png'" :alt="item.food.name"
                                                        class="h-20 w-20 rounded-lg object-cover">

                                                    <!-- Item Details -->
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex justify-between">
                                                            <h4
                                                                class="text-base font-medium text-gray-900 dark:text-gray-100">
                                                                {{ item.food.name }}
                                                            </h4>
                                                            <p
                                                                class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                                ${{ (item.quantity * item.unit_price).toFixed(2) }}
                                                            </p>
                                                        </div>

                                                        <!-- Extra Options -->
                                                        <div v-if="item.extras.length" class="mt-1">
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                Extras:
                                                                {{ item.extras.map(extra => extra.name).join(', ') }}
                                                            </p>
                                                        </div>

                                                        <!-- Special Instructions -->
                                                        <div v-if="item.special_instructions" class="mt-1">
                                                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                                                                "{{ item.special_instructions }}"
                                                            </p>
                                                        </div>


                                                        <div class="mt-2 flex items-center space-x-4">
    <div class="flex items-center border rounded-md border-gray-200 dark:border-gray-600">
        <button
            @click="updateQuantity(item.id, item.quantity - 1)"
            class="px-2 py-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            :disabled="item.quantity <= 1"
            :class="{ 'opacity-50 cursor-not-allowed': item.quantity <= 1 }"
        >
            <i class="fas fa-minus"></i>
        </button>
        <span class="px-4 py-1 text-gray-700 dark:text-gray-200 border-x border-gray-200 dark:border-gray-600">
            {{ item.quantity }}
        </span>
        <button
            @click="updateQuantity(item.id, item.quantity + 1)"
            class="px-2 py-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
        >
            <i class="fas fa-plus"></i>
        </button>
    </div>

    <button
        @click="confirmRemove(item)"
        class="p-2 text-red-400 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 transition-colors"
    >
        <i class="fas fa-trash"></i>
    </button>
</div>
                                                    </div>
                                                </div>
                                            </TransitionGroup>
                                        </div>
                                    </div>

                                    <!-- Cart Summary -->
                                    <div class="border-t border-gray-200 dark:border-gray-700">
    <!-- Summary Details -->
    <div class="px-4 py-6 sm:px-6 space-y-4">
        <div class="flow-root">
            <div class="space-y-3">
                <div class="flex justify-between text-base text-gray-500 dark:text-gray-400">
                    <p>Subtotal</p>
                    <p>${{ formatPrice(subtotal) }}</p>
                </div>
                <div class="flex justify-between text-base text-gray-500 dark:text-gray-400">
                    <div class="flex items-center">
                        <p>Delivery Fee</p>
                        <i class="fas fa-info-circle ml-1 cursor-help"
                           title="Fixed delivery fee for all orders"></i>
                    </div>
                    <p>${{ formatPrice(deliveryFee) }}</p>
                </div>
                <div class="flex justify-between text-base text-gray-500 dark:text-gray-400">
                    <div class="flex items-center">
                        <p>Tax</p>
                        <i class="fas fa-info-circle ml-1 cursor-help"
                           title="10% tax on subtotal"></i>
                    </div>
                    <p>${{ formatPrice(tax) }}</p>
                </div>
            </div>
        </div>

        <div class="flex justify-between text-lg font-medium text-gray-900 dark:text-gray-100">
            <p>Total</p>
            <p>${{ formatPrice(total) }}</p>
        </div>

        <p v-if="cartItems.length" class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
            <i class="fas fa-clock mr-2"></i>
            Estimated delivery: 45-60 minutes
        </p>
    </div>

    <!-- Checkout Action -->
    <div class="px-4 py-6 sm:px-6 bg-gray-50 dark:bg-gray-700/50">
        <template v-if="cartItems.length">
            <Link
                v-if="selectedBranch"
                :href="route('customer.checkout', { branch: selectedBranch.id })"
                class="w-full flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
            >
                <i class="fas fa-shopping-cart mr-2"></i>
                Proceed to Checkout
            </Link>
            <div v-else class="text-center">
                <p class="text-sm text-red-600 dark:text-red-400 mb-2">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Please select a branch to proceed
                </p>
                <Link
                    :href="route('customer.locations')"
                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                >
                    <i class="fas fa-store mr-2"></i>
                    Select Branch
                </Link>
            </div>
        </template>
    </div>
</div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>


<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import { useCart } from '@/Composables/useCart';

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    }
});

const emit = defineEmits(['close']);

const {
    cartItems,
    cartItemsCount,
    updateQuantity,
    removeItem,
    subtotal,
    tax,
    deliveryFee,
    total
} = useCart();

const selectedBranch = computed(() => usePage().props.selectedBranch);

// Format price consistently
const formatPrice = (price) => {
    return parseFloat(price).toFixed(2);
};

// Calculate item total including extras
const calculateItemTotal = (item) => {
    const baseTotal = item.unit_price * item.quantity;
    const extrasTotal = item.extras.reduce((sum, extra) =>
        sum + (parseFloat(extra.price) * item.quantity), 0
    );
    return formatPrice(baseTotal + extrasTotal);
};

const close = () => {
    emit('close');
};
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
    transition: all 0.3s ease;
}

.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}

.list-move {
    transition: transform 0.3s ease;
}

/* Add smooth transitions */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Custom scrollbar */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #CBD5E1 #F1F5F9;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #F1F5F9;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #CBD5E1;
    border-radius: 3px;
}

.dark .overflow-y-auto {
    scrollbar-color: #4B5563 #1F2937;
}

.dark .overflow-y-auto::-webkit-scrollbar-track {
    background: #1F2937;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #4B5563;
}
</style>
