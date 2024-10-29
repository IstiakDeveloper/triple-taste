<script setup>
import { Link } from '@inertiajs/vue3';
import OrderStatus from '@/Components/OrderStatus.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
  orders: Object,
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>

<template>
  <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
      <h1 class="text-2xl font-semibold text-gray-900">My Orders</h1>

      <!-- Orders List -->
      <div class="mt-6">
        <div v-if="orders.data.length === 0" class="text-center py-12">
          <p class="text-gray-500">You haven't placed any orders yet.</p>
        </div>

        <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
          <ul class="divide-y divide-gray-200">
            <li v-for="order in orders.data" :key="order.id">
              <Link
                :href="route('customer.orders.show', order.id)"
                class="block hover:bg-gray-50"
              >
                <div class="px-4 py-4 sm:px-6">
                  <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                      <span class="text-sm font-medium text-indigo-600">
                        Order #{{ order.id }}
                      </span>
                      <span class="text-sm text-gray-500">
                        {{ formatDate(order.created_at) }}
                      </span>
                    </div>
                    <div class="flex items-center">
                      <OrderStatus :status="order.status" />
                      <span class="ml-4 text-sm font-medium text-gray-900">
                        ${{ Number(order.total_amount).toFixed(2) }}
                      </span>
                    </div>
                  </div>

                  <div class="mt-2">
                    <div class="text-sm text-gray-500">
                      {{ order.branch.name }}
                    </div>
                    <div class="mt-1 text-sm text-gray-900">
                      {{ order.items.length }} items <!-- Changed from orderItems to items -->
                    </div>
                  </div>
                </div>
              </Link>
            </li>
          </ul>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
          <Pagination :links="orders.links" />
        </div>
      </div>
    </div>
  </div>
</template>
