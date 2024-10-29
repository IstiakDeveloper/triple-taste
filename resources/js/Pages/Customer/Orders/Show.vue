<script setup>
import { computed } from 'vue';
import OrderStatus from '@/Components/OrderStatus.vue';

const props = defineProps({
  order: Object,
  statusTimeline: Array,
});

console.log(props);


const deliveryEta = computed(() => {
  if (!props.order.estimated_delivery_time) return null;
  return new Date(props.order.estimated_delivery_time).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
  });
});
</script>

<template>
  <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
      <!-- Order Header -->
      <div class="flex justify-between items-start">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">
            Order #{{ order.id }}
          </h1>
          <p class="mt-1 text-sm text-gray-500">
            Placed on {{ new Date(order.created_at).toLocaleDateString() }}
          </p>
        </div>
        <OrderStatus :status="order.status" />
      </div>

      <!-- Order Timeline -->
      <div class="mt-8">
        <h2 class="text-lg font-medium text-gray-900">Order Status</h2>
        <div class="mt-4 flow-root">
          <ul class="-mb-8">
            <li v-for="(status, index) in statusTimeline" :key="status.status" class="relative pb-8">
              <div class="relative flex items-start space-x-3">
                <!-- Timeline line -->
                <div v-if="index !== statusTimeline.length - 1"
                     class="absolute top-4 left-4 -ml-px h-full w-0.5"
                     :class="status.completed ? 'bg-indigo-600' : 'bg-gray-200'" />

                <!-- Status icon -->
                <div :class="[
                  'relative h-8 w-8 flex items-center justify-center rounded-full',
                  status.completed ? 'bg-indigo-600' : 'bg-gray-200'
                ]">
                  <svg v-if="status.completed"
                       class="h-5 w-5 text-white"
                       fill="currentColor"
                       viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                          clip-rule="evenodd" />
                  </svg>
                </div>

                <!-- Status details -->
                <div class="min-w-0 flex-1">
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ status.label }}
                    </p>
                    <p v-if="status.timestamp" class="mt-0.5 text-sm text-gray-500">
                      {{ new Date(status.timestamp).toLocaleTimeString() }}
                    </p>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Order Details -->
      <div class="mt-8">
        <h2 class="text-lg font-medium text-gray-900">Order Details</h2>
        <div class="mt-4 bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
              <div>
                <dt class="text-sm font-medium text-gray-500">Delivery Address</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ order.delivery_address }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Restaurant</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ order.branch?.name || 'Unknown Restaurant' }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Estimated Delivery</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ deliveryEta }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ order.payment_method }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ order.payment_status }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>

      <!-- Order Items -->
      <div class="mt-8">
        <h2 class="text-lg font-medium text-gray-900">Order Items</h2>
        <div class="mt-4">
          <ul v-if="order.items && order.items.length" class="divide-y divide-gray-200">
            <li v-for="item in order.items" :key="item.id" class="py-4">
              <div class="flex justify-between">
                <div>
                  <h4 class="text-sm font-medium text-gray-900">
                    {{ item.food ? item.food.name : 'Unknown Item' }} × {{ item.quantity }}
                  </h4>
                  <ul v-if="item.extras && item.extras.length" class="mt-1">
                    <li v-for="extra in item.extras" :key="extra.id" class="text-sm text-gray-500">
                      + {{ extra.extra_option?.name || 'Unknown Extra' }}
                    </li>
                  </ul>
                  <p v-if="item.special_instructions" class="mt-1 text-sm text-gray-500">
                    Note: {{ item.special_instructions }}
                  </p>
                </div>
                <p class="text-sm font-medium text-gray-900">
                  ${{ parseFloat(item.subtotal).toFixed(2) }}
                </p>
              </div>
            </li>
          </ul>
          <p v-else>No items found in this order.</p>
        </div>

        <!-- Order Summary -->
        <div class="mt-8 bg-gray-50 p-4 rounded-lg">
          <dl class="space-y-2">
            <div class="flex justify-between">
              <dt class="text-sm text-gray-600">Subtotal</dt>
              <dd class="text-sm font-medium text-gray-900">${{ parseFloat(order.subtotal).toFixed(2) }}</dd>
            </div>
            <div class="flex justify-between">
              <dt class="text-sm text-gray-600">Delivery Fee</dt>
              <dd class="text-sm font-medium text-gray-900">${{ parseFloat(order.delivery_fee).toFixed(2) }}</dd>
            </div>
            <div class="flex justify-between">
              <dt class="text-sm text-gray-600">Tax</dt>
              <dd class="text-sm font-medium text-gray-900">${{ parseFloat(order.tax).toFixed(2) }}</dd>
            </div>
            <div class="flex justify-between border-t border-gray-200 pt-2">
              <dt class="text-base font-medium text-gray-900">Total</dt>
              <dd class="text-base font-medium text-gray-900">${{ parseFloat(order.total_amount).toFixed(2) }}</dd>
            </div>
          </dl>
        </div>
      </div>
    </div>
  </div>
</template>
