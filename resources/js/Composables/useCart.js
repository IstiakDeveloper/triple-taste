// resources/js/Composables/useCart.js

import { ref, computed } from 'vue';

const cartItems = ref([]);
const TAX_RATE = 0.1;
const BASE_DELIVERY_FEE = 5.00;

export function useCart() {
    // Initialize cart
    const initCart = () => {
        try {
            const savedCart = localStorage.getItem('cart');
            cartItems.value = savedCart ? JSON.parse(savedCart) : [];
        } catch (error) {
            console.error('Error loading cart:', error);
            cartItems.value = [];
        }
    };

    // Save cart to localStorage
    const saveCart = () => {
        try {
            localStorage.setItem('cart', JSON.stringify(cartItems.value));
        } catch (error) {
            console.error('Error saving cart:', error);
        }
    };

    // Add item to cart
    const addToCart = (food) => {
        // Create cart item with all necessary data
        const cartItem = {
            id: `${food.id}-${Date.now()}`, // Unique ID for cart item
            food: {
                id: food.id,
                name: food.name,
                base_price: parseFloat(food.base_price),
                image_path: food.image_path,
                description: food.description
            },
            extras: food.selectedExtras?.map(extra => ({
                id: extra.id,
                name: extra.name,
                price: parseFloat(extra.price)
            })) || [],
            quantity: food.quantity || 1,
            special_instructions: food.special_instructions || '',
            unit_price: parseFloat(food.base_price),
            total_price: calculateItemTotal({
                unit_price: parseFloat(food.base_price),
                quantity: food.quantity || 1,
                extras: food.selectedExtras || []
            })
        };

        // Check if similar item exists
        const existingItemIndex = cartItems.value.findIndex(item =>
            item.food.id === food.id &&
            JSON.stringify(item.extras) === JSON.stringify(cartItem.extras) &&
            item.special_instructions === cartItem.special_instructions
        );

        if (existingItemIndex > -1) {
            // Update quantity of existing item
            cartItems.value[existingItemIndex].quantity += cartItem.quantity;
            cartItems.value[existingItemIndex].total_price = calculateItemTotal(cartItems.value[existingItemIndex]);
        } else {
            // Add new item
            cartItems.value.push(cartItem);
        }

        saveCart();
    };

    // Calculate total for a single item including extras
    const calculateItemTotal = (item) => {
        const baseTotal = item.unit_price * item.quantity;
        const extrasTotal = item.extras.reduce((sum, extra) =>
            sum + (parseFloat(extra.price) * item.quantity), 0
        );
        return baseTotal + extrasTotal;
    };

    // Update item quantity
    const updateQuantity = (itemId, newQuantity) => {
        const item = cartItems.value.find(item => item.id === itemId);
        if (item && newQuantity > 0) {
            item.quantity = newQuantity;
            item.total_price = calculateItemTotal(item);
            saveCart();
        }
    };

    // Remove item from cart
    const removeItem = (itemId) => {
        cartItems.value = cartItems.value.filter(item => item.id !== itemId);
        saveCart();
    };

    // Clear entire cart
    const clearCart = () => {
        cartItems.value = [];
        localStorage.removeItem('cart');
    };

    // Computed properties
    const cartItemsCount = computed(() => {
        return cartItems.value.reduce((sum, item) => sum + item.quantity, 0);
    });

    const subtotal = computed(() => {
        return cartItems.value.reduce((sum, item) => sum + item.total_price, 0);
    });

    const tax = computed(() => subtotal.value * TAX_RATE);

    const deliveryFee = computed(() => cartItems.value.length ? BASE_DELIVERY_FEE : 0);

    const total = computed(() => subtotal.value + tax.value + deliveryFee.value);

    // Initialize cart when composable is created
    initCart();

    return {
        cartItems,
        cartItemsCount,
        subtotal,
        tax,
        deliveryFee,
        total,
        addToCart,
        updateQuantity,
        removeItem,
        clearCart,
        calculateItemTotal
    };
}
