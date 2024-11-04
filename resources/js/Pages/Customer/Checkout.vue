<template>
    <CustomerLayout>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Order Summary -->
          <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
              <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Order Summary</h2>
              <div v-if="cart.length" class="space-y-4">
                <div v-for="item in cart" :key="item.id"
                     class="flex justify-between items-start border-b border-gray-200 dark:border-gray-700 pb-4">
                  <div>
                    <h3 class="text-gray-900 dark:text-gray-200 font-medium">{{ item.food.name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Quantity: {{ item.quantity }}</p>
                    <div v-if="item.extras?.length" class="text-sm text-gray-600 dark:text-gray-400">
                      Extras:
                      <span v-for="(extraId, index) in item.extras" :key="extraId">
                        {{ item.food.extra_options.find(e => e.id === extraId)?.name }}
                        <span v-if="index < item.extras.length - 1">, </span>
                      </span>
                    </div>
                    <p v-if="item.specialInstructions" class="text-sm text-gray-600 dark:text-gray-400">
                      Note: {{ item.specialInstructions }}
                    </p>
                  </div>
                  <span class="text-gray-900 dark:text-gray-200">{{ formatCurrency(item.total) }}</span>
                </div>
              </div>
              <div v-else class="text-gray-600 dark:text-gray-400 text-center py-4">
                Your cart is empty
              </div>
            </div>

            <!-- Delivery Address -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
              <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Delivery Address</h2>
                <button @click="openAddressModal" class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300">
                  + Add New Address
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="address in addresses" :key="address.id"
                  @click="selectedAddress = address"
                  class="border rounded-lg p-4 cursor-pointer transition-colors duration-200"
                  :class="[
                    selectedAddress?.id === address.id
                      ? 'border-orange-500 bg-orange-50 dark:bg-gray-700'
                      : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'
                  ]"
                >
                  <div class="flex items-start space-x-3">
                    <MapPinIcon class="w-5 h-5 text-gray-400 flex-shrink-0" />
                    <div>
                      <p class="text-gray-900 dark:text-gray-200 font-medium">{{ address.address_label }}</p>
                      <p class="text-sm text-gray-600 dark:text-gray-400">{{ address.address }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
              <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment Method</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button
                  v-for="method in ['card', 'cash', 'upi', 'wallet']"
                  :key="method"
                  @click="selectedPaymentMethod = method"
                  class="flex items-center space-x-3 border rounded-lg p-4 transition-colors duration-200"
                  :class="[
                    selectedPaymentMethod === method
                      ? 'border-orange-500 bg-orange-50 dark:bg-gray-700'
                      : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'
                  ]"
                >
                  <CreditCardIcon class="w-5 h-5 text-gray-400" />
                  <span class="text-gray-900 dark:text-gray-200 capitalize">{{ method }}</span>
                </button>
              </div>
            </div>

            <!-- Delivery Notes -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
              <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Delivery Notes</h2>
              <textarea
                v-model="deliveryNotes"
                rows="3"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-orange-500 focus:border-orange-500"
                placeholder="Any special instructions for delivery..."
              ></textarea>
            </div>
          </div>

          <!-- Order Total -->
          <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 sticky top-4">
              <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Order Total</h2>
              <div class="space-y-3">
                <div class="flex justify-between text-gray-600 dark:text-gray-300">
                  <span>Subtotal</span>
                  <span>{{ formatCurrency(subtotal) }}</span>
                </div>
                <div class="flex justify-between text-gray-600 dark:text-gray-300">
                  <span>Delivery Fee</span>
                  <span>{{ formatCurrency(deliveryFee) }}</span>
                </div>
                <div class="flex justify-between text-gray-600 dark:text-gray-300">
                  <span>Tax (20%)</span>
                  <span>{{ formatCurrency(tax) }}</span>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                  <div class="flex justify-between text-lg font-semibold text-gray-900 dark:text-gray-100">
                    <span>Total</span>
                    <span>{{ formatCurrency(total) }}</span>
                  </div>
                </div>
              </div>

              <button
                @click="placeOrder"
                :disabled="isProcessing || !cart.length"
                class="w-full mt-6 bg-orange-600 text-white py-3 px-4 rounded-lg hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
              >
                {{ isProcessing ? 'Processing...' : 'Place Order' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- New Address Modal -->
      <TransitionRoot appear :show="isAddressModalOpen" as="template">
        <Dialog as="div" @close="closeAddressModal" class="relative z-50">
          <div class="fixed inset-0 bg-black/50" />

          <div class="fixed inset-0 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
              <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 p-6 text-left align-middle shadow-xl transition-all">
                <DialogTitle as="h3" class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  Add New Address
                </DialogTitle>

                <div class="mt-4 space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address Label</label>
                    <input
                      type="text"
                      v-model="newAddress.address_label"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                      placeholder="Home, Work, etc."
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Address</label>
                    <textarea
                      v-model="newAddress.address"
                      rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                    ></textarea>
                  </div>

                  <div class="flex items-center">
                    <input
                      type="checkbox"
                      v-model="newAddress.is_default"
                      class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500"
                    />
                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Set as default address</label>
                  </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                  <button
                    @click="closeAddressModal"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200"
                  >
                    Cancel
                  </button>
                  <button
                    @click="saveAddress"
                    class="px-4 py-2 text-sm bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors duration-200"
                  >
                    Save Address
                  </button>
                </div>
              </DialogPanel>
            </div>
          </div>
        </Dialog>
      </TransitionRoot>
    </CustomerLayout>
  </template>


<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue';
import { CreditCardIcon, MapPinIcon, TruckIcon, ClockIcon } from '@heroicons/vue/24/outline';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';

const props = defineProps({
  auth: Object,
  addresses: {
    type: Array,
    default: () => []
  },
  branch: {
    type: Object,
    required: true,
    validator: (value) => {
      return value && typeof value.id !== 'undefined';
    }
  }
});

// State management
const cart = ref([]);
const selectedAddress = ref(null);
const selectedPaymentMethod = ref('card');
const deliveryNotes = ref('');
const isAddressModalOpen = ref(false);
const isProcessing = ref(false);
const errors = ref({});


// Form state for new address
const newAddress = ref({
  address_label: '',
  address: '',
  latitude: null,
  longitude: null,
  is_default: false
});

// Load cart data on component mount
onMounted(() => {
  loadCart();
  initializeAddress();
});

// Initialize default address if available
const initializeAddress = () => {
  if (props.addresses?.length > 0) {
    const defaultAddress = props.addresses.find(addr => addr.is_default) || props.addresses[0];
    selectedAddress.value = defaultAddress;
  }
};



// Load cart data from session storage
const loadCart = () => {
  try {
    const savedCart = sessionStorage.getItem('foodCart');
    if (!savedCart) {
      redirectToMenu();
      return;
    }

    const cartData = JSON.parse(savedCart);

    // Debug logs
    console.log('Cart Data:', cartData);
    console.log('Current Branch:', props.branch);

    // Validate cart data structure
    if (!cartData || !cartData.items || !Array.isArray(cartData.items)) {
      console.error('Invalid cart data structure');
      sessionStorage.removeItem('foodCart');
      redirectToMenu();
      return;
    }

    // Check if cart belongs to current branch
    if (!props.branch?.id) {
      console.error('Branch data is missing');
      redirectToMenu();
      return;
    }

    if (Number(cartData.branchId) !== Number(props.branch.id)) {
      console.error('Cart branch ID does not match current branch');
      redirectToMenu();
      return;
    }

    cart.value = cartData.items;

  } catch (error) {
    console.error('Error loading cart:', error);
    sessionStorage.removeItem('foodCart');
    redirectToMenu();
  }
};


// Helper function to redirect to menu
const redirectToMenu = () => {
  if (props.branch?.id) {
    router.visit(route('customer.branch.menu', {
      branch: props.branch.id
    }));
  } else {
    // Fallback if no branch ID
    router.visit('/');
  }
};

// Computed properties
const subtotal = computed(() => {
  if (!cart.value?.length) return 0;
  return cart.value.reduce((total, item) => total + Number(item.total), 0);
});

const deliveryFee = computed(() => {
  if (!selectedAddress.value || !props.branch) return 2.99; // Default delivery fee
  return 2.99; // You can implement dynamic delivery fee calculation here
});

const tax = computed(() => {
  return subtotal.value * 0.20; // 20% VAT
});

const total = computed(() => {
  return subtotal.value + deliveryFee.value + tax.value;
});

// Place order
const placeOrder = () => {
  console.log('hello');
  isProcessing.value = true;

  const orderData = {
    branch_id: props.branch.id,
    items: cart.value.map(item => ({
      food_id: item.food.id,
      quantity: item.quantity,
      extras: item.extras,
      special_instructions: item.specialInstructions || '',
      unit_price: item.food.base_price,
      subtotal: item.total
    })),
    delivery_address: selectedAddress.value.address,
    delivery_latitude: selectedAddress.value.latitude,
    delivery_longitude: selectedAddress.value.longitude,
    payment_method: selectedPaymentMethod.value,
    delivery_notes: deliveryNotes.value,
    subtotal: subtotal.value,
    delivery_fee: deliveryFee.value,
    tax: tax.value,
    total_amount: total.value
  };
  console.log(orderData);

  router.post(route('customer.orders.store'), orderData, {
    onSuccess: (page) => {
      // Clear the cart from session storage
      sessionStorage.removeItem('foodCart');

      // Extract order ID from the URL
      const urlParts = page.url.split('/');
      const orderId = urlParts[urlParts.length - 1];

      if (orderId) {
        // Navigate to the order show page
        router.visit(route('customer.orders.show', { order: orderId }));
      } else {
        console.error('Order ID not found in URL:', page.url);
        isProcessing.value = false;
        errors.value = { message: 'Failed to process order. Please try again.' };
      }
    },
    onError: (e) => {
      console.error('Error:', e);
      isProcessing.value = false;
      errors.value = e;
    }
  });
};

// Validate order data before submission
const validateOrderData = () => {
  if (!cart.value?.length) {
    alert('Your cart is empty');
    return false;
  }

  if (!selectedAddress.value) {
    alert('Please select a delivery address');
    return false;
  }

  if (!selectedPaymentMethod.value) {
    alert('Please select a payment method');
    return false;
  }

  if (!props.branch?.id) {
    alert('Invalid branch information');
    return false;
  }

  return true;
};

// Address modal methods
const openAddressModal = () => {
  isAddressModalOpen.value = true;
};

const closeAddressModal = () => {
  isAddressModalOpen.value = false;
  newAddress.value = {
    address_label: '',
    address: '',
    latitude: null,
    longitude: null,
    is_default: false
  };
  errors.value = {};
};

const saveAddress = () => {
  router.post(route('customer.addresses.store'), newAddress.value, {
    onSuccess: () => {
      closeAddressModal();
      // Reload addresses list
      router.reload({ only: ['addresses'] });
    },
    onError: (errors) => {
      errors.value = errors;
    }
  });
};

// Set address as default
const setDefaultAddress = (address) => {
  router.post(route('customer.addresses.setDefault', { address: address.id }), {}, {
    onSuccess: () => {
      router.reload({ only: ['addresses'] });
    }
  });
};

// Delete address
const deleteAddress = (address) => {
  if (confirm('Are you sure you want to delete this address?')) {
    router.delete(route('customer.addresses.delete', { address: address.id }), {
      onSuccess: () => {
        router.reload({ only: ['addresses'] });
      }
    });
  }
};

// Format currency helper
const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-GB', {
    style: 'currency',
    currency: 'GBP'
  }).format(Number(value));
};
</script>
