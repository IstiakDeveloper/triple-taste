<template>
    <TransitionRoot appear :show="show" as="template">
      <Dialog as="div" class="relative z-50" @close="close">
        <!-- Background overlay -->
        <TransitionChild
          as="template"
          enter="ease-in-out duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="ease-in-out duration-300"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
        </TransitionChild>

        <!-- Sliding cart panel -->
        <div class="fixed inset-0 overflow-hidden">
          <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
              <TransitionChild
                as="template"
                enter="transform transition ease-in-out duration-300"
                enter-from="translate-x-full"
                enter-to="translate-x-0"
                leave="transform transition ease-in-out duration-300"
                leave-from="translate-x-0"
                leave-to="translate-x-full"
              >
                <DialogPanel class="pointer-events-auto w-screen max-w-md">
                  <div class="flex h-full flex-col bg-white dark:bg-gray-800 shadow-xl">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-4 py-6 sm:px-6 bg-indigo-600 text-white">
                      <DialogTitle class="text-lg font-medium">
                        Shopping Cart
                        <span class="text-sm text-indigo-200 ml-2">
                          ({{ cartItemsCount }} items)
                        </span>
                      </DialogTitle>
                      <button
                        type="button"
                        class="text-indigo-200 hover:text-white"
                        @click="close"
                      >
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
                          <Link
                            :href="route('customer.menu', { branch: selectedBranch?.id })"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                          >
                            View Menu
                          </Link>
                        </div>
                      </div>

                      <div v-else class="space-y-4">
                        <TransitionGroup
                          name="list"
                          tag="div"
                          class="space-y-4"
                        >
                          <div
                            v-for="item in cartItems"
                            :key="item.id"
                            class="flex items-start space-x-4 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg"
                          >
                            <!-- Food Image -->
                            <img
                              :src="item.food.image_path
                                ? `/storage/${item.food.image_path}`
                                : '/images/placeholder-food.png'"
                              :alt="item.food.name"
                              class="h-20 w-20 rounded-lg object-cover"
                            >

                            <!-- Item Details -->
                            <div class="flex-1 min-w-0">
                              <div class="flex justify-between">
                                <h4 class="text-base font-medium text-gray-900 dark:text-gray-100">
                                  {{ item.food.name }}
                                </h4>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
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

                              <!-- Quantity Controls -->
                              <div class="mt-2 flex items-center space-x-2">
                                <button
                                  @click="decreaseQuantity(item)"
                                  class="p-1 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                                  :disabled="item.quantity <= 1"
                                >
                                  <i class="fas fa-minus-circle"></i>
                                </button>
                                <span class="text-sm text-gray-600 dark:text-gray-300 w-8 text-center">
                                  {{ item.quantity }}
                                </span>
                                <button
                                  @click="increaseQuantity(item)"
                                  class="p-1 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                                >
                                  <i class="fas fa-plus-circle"></i>
                                </button>

                                <button
                                  @click="removeItem(item)"
                                  class="ml-4 p-1 text-red-400 hover:text-red-500"
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
                    <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-6 sm:px-6">
                      <div class="space-y-4">
                        <div class="flex justify-between text-base text-gray-900 dark:text-gray-100">
                          <p>Subtotal</p>
                          <p>${{ subtotal.toFixed(2) }}</p>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                          <p>Delivery Fee</p>
                          <p>${{ deliveryFee.toFixed(2) }}</p>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                          <p>Tax</p>
                          <p>${{ tax.toFixed(2) }}</p>
                        </div>
                        <div class="flex justify-between text-lg font-medium text-gray-900 dark:text-gray-100">
                          <p>Total</p>
                          <p>${{ total.toFixed(2) }}</p>
                        </div>

                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                          Estimated delivery time: 45-60 minutes
                        </p>
                      </div>

                      <div class="mt-6">
                        <Link
                          v-if="cartItems.length"
                          :href="route('customer.checkout', { branch: selectedBranch?.id })"
                          class="flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700"
                        >
                          Proceed to Checkout
                        </Link>
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
  import { computed } from 'vue'
  import { Link, usePage } from '@inertiajs/vue3'
  import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue'
  import { XMarkIcon } from '@heroicons/vue/24/outline'
  import { useCart } from '@/Composables/useCart'

  const props = defineProps({
    show: {
      type: Boolean,
      required: true
    }
  })

  const emit = defineEmits(['close'])

  const {
    cartItems,
    cartItemsCount,
    increaseQuantity,
    decreaseQuantity,
    removeItem,
    subtotal,
    tax,
    deliveryFee,
    total
  } = useCart()

  const selectedBranch = computed(() => {
    return usePage().props.selectedBranch
  })

  const close = () => {
    emit('close')
  }
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
  </style>
