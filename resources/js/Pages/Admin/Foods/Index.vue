<template>
  <AdminLayout title="Foods">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
          Food Management
        </h2>
        <Link
          :href="route('admin.foods.create')"
          class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md shadow-sm transition dark:bg-green-500 dark:hover:bg-green-600"
        >
          <i class="fas fa-plus mr-2"></i>
          Add Food
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <!-- Flash Messages -->
            <FlashMessage
              v-if="flash.success"
              :message="flash.success"
              class="mb-4"
            />

            <!-- Foods Table -->
            <div v-if="foods.data.length > 0" class="overflow-x-auto">
              <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                  <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      Food Name
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      Price
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      Category
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                      Status
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-40">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="food in foods.data"
                      :key="food.id"
                      class="group transition-colors duration-150 hover:bg-gray-50/90 dark:hover:bg-gray-700/50"
                  >
                    <td class="px-6 py-4">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ food.name }}
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900 dark:text-gray-100">
                        ${{ food.price }}
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900 dark:text-gray-100">
                        {{ food.category }}
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <span
                        :class="[
                          'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                          food.is_available
                            ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300'
                            : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300'
                        ]"
                      >
                        {{ food.is_available ? 'Available' : 'Not Available' }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex items-center space-x-4">
                        <Link
                          :href="route('admin.foods.edit', food.id)"
                          class="inline-flex items-center px-3 py-1.5 bg-green-50 hover:bg-green-100 active:bg-green-200 dark:bg-green-900/50 dark:hover:bg-green-800/50 text-green-600 dark:text-green-300 rounded-md transition-colors duration-200"
                        >
                          <i class="fas fa-edit text-xs mr-1.5"></i>
                          <span>Edit</span>
                        </Link>
                        <button
                          @click="confirmDelete(food)"
                          class="inline-flex items-center px-3 py-1.5 bg-red-50 hover:bg-red-100 active:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-800/50 text-red-600 dark:text-red-300 rounded-md transition-colors duration-200"
                        >
                          <i class="fas fa-trash text-xs mr-1.5"></i>
                          <span>Delete</span>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
              <i class="fas fa-hamburger text-gray-400 text-5xl mb-4"></i>
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">No Foods Found</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Start by adding a new food item.
              </p>
              <div class="mt-6">
                <Link
                  :href="route('admin.foods.create')"
                  class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md"
                >
                  <i class="fas fa-plus mr-2"></i>
                  Add Food
                </Link>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="foods.data.length > 0" class="mt-4">
              <SimplePagination
                :links="foods.meta.links"
                :meta="foods.meta"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
          Delete Food
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Are you sure you want to delete this food item? This action cannot be undone.
        </p>
        <div class="mt-6 flex justify-end space-x-3">
          <SecondaryButton @click="showDeleteModal = false">
            Cancel
          </SecondaryButton>
          <DangerButton
            class="ml-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="deleteFood"
          >
            Delete Food
          </DangerButton>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import SimplePagination from '@/Components/SimplePagination.vue'
import FlashMessage from '@/Components/FlashMessage.vue'
import Modal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'

const props = defineProps({
  foods: {
    type: Object,
    required: true,
    default: () => ({
      data: [],
      meta: {
        current_page: 1,
        from: 1,
        last_page: 1,
        links: [],
        path: '',
        per_page: 10,
        to: 1,
        total: 0
      }
    })
  },
  flash: {
    type: Object,
    default: () => ({
      success: null,
      error: null
    })
  }
})

const showDeleteModal = ref(false)
const foodToDelete = ref(null)
const form = useForm({})

const confirmDelete = (food) => {
  foodToDelete.value = food
  showDeleteModal.value = true
}

const deleteFood = () => {
  if (foodToDelete.value) {
    form.delete(route('admin.foods.destroy', foodToDelete.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteModal.value = false
        foodToDelete.value = null
      },
    })
  }
}
</script>
