<template>
    <AdminLayout :title="title">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Basic Food Information Section -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Food Information
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Food Name -->
                                <div>
                                    <InputLabel for="name" value="Food Name" />
                                    <TextInput id="name" v-model="form.name" type="text"
                                        class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300" required />
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>

                                <!-- Category Selection -->
                                <div>
                                    <InputLabel for="category_id" value="Category" />
                                    <select id="category_id" v-model="form.category_id"
                                        class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300" required>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.category_id" class="mt-2" />
                                </div>

                                <!-- Description -->
                                <div class="md:col-span-2">
                                    <InputLabel for="description" value="Description" />
                                    <TextArea id="description" v-model="form.description"
                                        class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300" rows="3" />
                                    <InputError :message="form.errors.description" class="mt-2" />
                                </div>

                                <!-- Base Price -->
                                <div>
                                    <InputLabel for="base_price" value="Base Price" />
                                    <TextInput id="base_price" v-model="form.base_price" type="number" step="0.01"
                                        class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300" required />
                                    <InputError :message="form.errors.base_price" class="mt-2" />
                                </div>

                                <!-- Preparation Time -->
                                <div>
                                    <InputLabel for="preparation_time" value="Preparation Time (minutes)" />
                                    <TextInput id="preparation_time" v-model="form.preparation_time" type="number"
                                        class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300" required />
                                    <InputError :message="form.errors.preparation_time" class="mt-2" />
                                </div>

                                <!-- Image Upload -->
                                <div class="md:col-span-2">
                                    <InputLabel for="image_path" value="Food Image" />
                                    <input type="file" id="image_path" @change="handleFileUpload"
                                        class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300" />
                                    <InputError :message="form.errors.image_path" class="mt-2" />
                                </div>

                                <!-- Vegetarian Option -->
                                <div>
                                    <InputLabel for="is_vegetarian" value="Vegetarian" />
                                    <Toggle v-model="form.is_vegetarian" />
                                </div>

                                <!-- Spicy Option -->
                                <div>
                                    <InputLabel for="is_spicy" value="Spicy" />
                                    <Toggle v-model="form.is_spicy" />
                                </div>

                                <!-- Allergens -->
                                <div class="md:col-span-2">
                                    <InputLabel for="allergens" value="Allergens" />
                                    <TextInput id="allergens" v-model="form.allergens" type="text"
                                        class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300"
                                        placeholder="Comma-separated list, e.g., peanuts, gluten" />
                                    <InputError :message="form.errors.allergens" class="mt-2" />
                                </div>

                                <!-- Availability Toggle -->
                                <div>
                                    <InputLabel for="is_available" value="Available" />
                                    <Toggle v-model="form.is_available" />
                                </div>

                                <!-- Sort Order -->
                                <div>
                                    <InputLabel for="sort_order" value="Sort Order" />
                                    <TextInput id="sort_order" v-model="form.sort_order" type="number"
                                        class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300" required />
                                    <InputError :message="form.errors.sort_order" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Extra Options Section -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Extra Options
                            </h3>

                            <div v-for="(option, index) in form.extra_options" :key="index"
                                class="flex items-center space-x-4 mb-4">
                                <!-- Extra Option Name -->
                                <div class="flex-1">
                                    <InputLabel :for="`extra_option_name_${index}`" value="Option Name" />
                                    <TextInput :id="`extra_option_name_${index}`" v-model="option.name" type="text"
                                        class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300" required />
                                    <InputError :message="form.errors[`extra_options.${index}.name`]" class="mt-2" />
                                </div>

                                <!-- Extra Option Price -->
                                <div class="flex-1">
                                    <InputLabel :for="`extra_option_price_${index}`" value="Option Price" />
                                    <TextInput :id="`extra_option_price_${index}`" v-model="option.price" type="number"
                                        step="0.01" class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300"
                                        required />
                                    <InputError :message="form.errors[`extra_options.${index}.price`]" class="mt-2" />
                                </div>

                                <!-- Extra Option Availability -->
                                <div class="flex items-center">
                                    <Toggle v-model="option.is_available" />
                                </div>

                                <!-- Remove Option Button -->
                                <button type="button" @click="removeExtraOption(index)"
                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <!-- Add Extra Option Button -->
                            <button type="button" @click="addExtraOption"
                                class="mt-2 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 transition ease-in-out duration-150">
                                <i class="fas fa-plus mr-2"></i>
                                Add Extra Option
                            </button>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3">
                            <Link :href="route('admin.foods.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-300 dark:active:bg-gray-600">
                                Cancel
                            </Link>
                            <PrimaryButton :disabled="form.processing">
                                {{ food ? 'Update Food' : 'Create Food' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Toggle from '@/Components/Toggle.vue'

const props = defineProps({
    food: Object,
    categories: Array,
    title: String
})

const form = useForm({
    branch_id: props.food?.branch_id || '',
    category_id: props.food?.category_id || '',
    name: props.food?.name || '',
    description: props.food?.description || '',
    base_price: props.food?.base_price || '',
    preparation_time: props.food?.preparation_time || 30,
    image_path: null,
    is_vegetarian: props.food?.is_vegetarian || false,
    is_spicy: props.food?.is_spicy || false,
    allergens: props.food?.allergens || '',
    is_available: props.food?.is_available || true,
    sort_order: props.food?.sort_order || 0,
    extra_options: props.food?.extra_options || []
})

const title = computed(() => props.food ? 'Edit Food' : 'Create Food')

function handleFileUpload(event) {
    form.image_path = event.target.files[0]
}

function addExtraOption() {
    form.extra_options.push({ name: '', price: 0, is_available: true })
}

function removeExtraOption(index) {
    form.extra_options.splice(index, 1)
}

function submit() {
    form.post(props.food ? route('admin.food.update', props.food.id) : route('admin.food.store'))
}
</script>
