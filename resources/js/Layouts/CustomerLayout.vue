<template>
    <div class="min-h-screen dark:bg-gray-900">
        <!-- Hero Section -->
        <div class="relative min-h-screen">
            <div class="absolute inset-0 bg-black/50 dark:bg-black/90" :style="{
                backgroundImage: `url('/food1.jpg')`,
                backgroundSize: 'cover',
                backgroundPosition: 'center',
            }">
                <div class="absolute inset-0 bg-black opacity-70"></div>
            </div>

            <div class="relative z-10">
                <!-- Navigation -->
                <nav class="container mx-auto px-6 py-8">
                    <div class="flex items-center justify-between">
                        <h1 class="text-3xl font-bold text-white dark:text-gray-100">Triple-Taste</h1>
                        <div class="flex items-center gap-4">
                            <div class="hidden md:flex space-x-8 text-white dark:text-gray-300 ml-4">
                                <a href="#about"
                                    class="hover:text-orange-400 transition-colors dark:hover:text-orange-300">About</a>
                                <a href="#features"
                                    class="hover:text-orange-400 transition-colors dark:hover:text-orange-300">Features</a>
                                <a href="#contact"
                                    class="hover:text-orange-400 transition-colors dark:hover:text-orange-300">Contact</a>
                            </div>
                            <div class="fixed top-4 right-4 z-50">
                                <button @click="switchTheme"
                                    class="text-orange-400 dark:text-gray-300 transition-colors hover:text-orange-400 dark:hover:text-orange-300 p-2 rounded-full focus:outline-none">
                                    <i class="fa-solid fa-circle-half-stroke text-lg"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                </nav>

                <main class="flex-1 p-6 mt-16 overflow-auto">
                    <div class="mx-auto">
                        <slot></slot>
                    </div>
                </main>
</div>
</div>
            </div>

            <!-- Footer Section -->
            <footer class="py-12 bg-gray-900 text-white dark:bg-gray-800">
                <div class="container mx-auto px-6 text-center">
                    <p class="text-sm mb-4 dark:text-gray-400">
                        &copy; {{ new Date().getFullYear() }} Triple-Taste. All rights reserved.
                    </p>
                    <div class="flex justify-center space-x-6">
                        <a href="#about"
                            class="hover:text-orange-400 transition-colors dark:hover:text-orange-300">About Us</a>
                        <a href="#features"
                            class="hover:text-orange-400 transition-colors dark:hover:text-orange-300">Features</a>
                        <a href="#contact"
                            class="hover:text-orange-400 transition-colors dark:hover:text-orange-300">Contact</a>
                    </div>
                    <div class="mt-4">
                        <a href="https://www.facebook.com" target="_blank"
                            class="text-orange-400 hover:text-orange-300">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://www.twitter.com" target="_blank"
                            class="text-orange-400 hover:text-orange-300 mx-4">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.instagram.com" target="_blank"
                            class="text-orange-400 hover:text-orange-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </footer>

</template>

<script setup>
import { ref, onMounted } from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';
import { switchTheme } from '@/theme';


const features = ref([
    { icon: 'fas fa-utensils', title: 'Quality Ingredients', desc: 'We source the freshest ingredients for every dish.' },
    { icon: 'fas fa-clock', title: 'Fast Delivery', desc: 'Get your favorite meals delivered quickly and safely.' },
    { icon: 'fas fa-headset', title: '24/7 Support', desc: 'Our customer support is here to help you anytime.' },
]);

const steps = ref([
    { number: '1', title: 'Order', desc: 'Choose your favorite meals from our menu.' },
    { number: '2', title: 'Prepare', desc: 'Our chefs prepare your order with care.' },
    { number: '3', title: 'Deliver', desc: 'We deliver your food hot and fresh to your door.' },
]);

const statistics = ref([
    { number: '1000+', label: 'Happy Customers' },
    { number: '500+', label: 'Delivered Meals' },
    { number: '50+', label: 'Restaurants Partnered' },
    { number: '24/7', label: 'Support Hours' },
]);

const contactInfo = ref([
    { icon: 'fas fa-phone-alt', text: 'Call us: (123) 456-7890' },
    { icon: 'fas fa-envelope', text: 'Email: info@tripletaste.com' },
    { icon: 'fas fa-map-marker-alt', text: 'Address: 123 Food St, Gourmet City' },
]);

const form = ref({ name: '', email: '', message: '' });
const showModal = ref(false);

const startOrder = () => {
    showModal.value = true;
};

const submitForm = () => {
    alert(`Message sent: ${form.value.message}`);
    form.value = { name: '', email: '', message: '' }; // Reset form
};

const closeModal = () => {
    showModal.value = false;
};


onMounted(() => {
    AOS.init({ duration: 1000 });
});
</script>

<style>
/* Add any additional styles here */
</style>
