<template>
    <AdminLayout>
      <div class="container mx-auto py-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Order #{{ order.id }}</h1>
              <div>
                <span
                  class="text-sm px-2 py-1 rounded-full font-medium"
                  :class="getPaymentStatusClasses(order.payment_status)"
                >
                  {{ formatStatus(order.payment_status) }}
                </span>
                <span
                  class="text-sm px-2 py-1 rounded-full font-medium ml-2"
                  :class="getStatusClasses(order.status)"
                >
                  {{ formatStatus(order.status) }}
                </span>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Customer</h2>
                <div class="space-y-2">
                  <div>
                    <span class="font-medium text-gray-900 dark:text-white">Name:</span>
                    <span class="text-gray-700 dark:text-gray-400">{{ order.user.name }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-900 dark:text-white">Email:</span>
                    <span class="text-gray-700 dark:text-gray-400">{{ order.user.email }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-900 dark:text-white">Phone:</span>
                    <span class="text-gray-700 dark:text-gray-400">{{ order.user.phone }}</span>
                  </div>
                </div>
              </div>
              <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Branch</h2>
                <div class="space-y-2">
                  <div>
                    <span class="font-medium text-gray-900 dark:text-white">Name:</span>
                    <span class="text-gray-700 dark:text-gray-400">{{ order.branch.name }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-6">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Order Items</h2>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Item
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Quantity
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Unit Price
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Subtotal
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Extras
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Special Instructions
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="item in order.items" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <img :src="item.food.image" class="h-10 w-10 rounded object-cover" alt="">
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                              {{ item.food.name }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                          {{ item.quantity }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                          ${{ item.unit_price }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          ${{ item.subtotal }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                          <template v-if="item.extras.length > 0">
                            <div v-for="extra in item.extras" :key="extra.id" class="mb-1">
                              {{ extra.name }} - ${{ extra.price }}
                            </div>
                          </template>
                          <div v-else>-</div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                          {{ item.special_instructions || '-' }}
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Order Summary</h2>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-900 dark:text-white">Subtotal:</span>
                    <span class="text-gray-700 dark:text-gray-400">${{ order.subtotal }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-900 dark:text-white">Delivery Fee:</span>
                    <span class="text-gray-700 dark:text-gray-400">${{ order.delivery_fee }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-900 dark:text-white">Tax:</span>
                    <span class="text-gray-700 dark:text-gray-400">${{ order.tax }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-900 dark:text-white">Total:</span>
                    <span class="text-gray-900 dark:text-white font-medium">${{ order.total_amount }}</span>
                  </div>
                </div>
              </div>
              <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Delivery Details</h2>
                <div class="space-y-2">
                  <div>
                    <span class="font-medium text-gray-900 dark:text-white">Address:</span>
                    <span class="text-gray-700 dark:text-gray-400">{{ order.delivery_address }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-900 dark:text-white">Notes:</span>
                    <span class="text-gray-700 dark:text-gray-400">{{ order.delivery_notes || '-' }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-900 dark:text-white">Estimated Delivery Time:</span>
                    <span class="text-gray-700 dark:text-gray-400">{{ formatDate(order.estimated_delivery_time) }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-900 dark:text-white">Payment Method:</span>
                    <span class="text-gray-700 dark:text-gray-400 capitalize">{{ order.payment_method }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-900 dark:text-white">Created At:</span>
                    <span class="text-gray-700 dark:text-gray-400">{{ formatDate(order.created_at) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AdminLayout>
  </template>

  <script setup>
  import AdminLayout from '@/Layouts/AdminLayout.vue'

  const props = defineProps({
    order: Object
  })

  const formatStatus = (status) => {
    return status.split('_').map(word =>
      word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ')
  }

  const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  const getStatusClasses = (status) => {
    const classes = {
      pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
      confirmed: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
      preparing: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
      ready_for_delivery: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
      out_for_delivery: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
      delivered: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
      cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    }
    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
  }

  const getPaymentStatusClasses = (status) => {
    const classes = {
      pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
      paid: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
      failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
      refunded: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
    }
    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
  }
  </script>
