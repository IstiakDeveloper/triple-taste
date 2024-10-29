import { ref, computed } from 'vue'

// Initialize with an empty array
const cartItems = ref([])
const TAX_RATE = 0.1
const BASE_DELIVERY_FEE = 5.00

export function useCart() {
    // Load cart from localStorage on initialization
    const initializeCart = () => {
        try {
            const saved = localStorage.getItem('cart')
            if (saved) {
                cartItems.value = JSON.parse(saved) || []
            }
        } catch (error) {
            console.error('Error loading cart:', error)
            cartItems.value = []
        }
    }

    // Initialize cart when the composable is first used
    initializeCart()

    // Save cart to localStorage
    const saveCart = () => {
        try {
            localStorage.setItem('cart', JSON.stringify(cartItems.value))
        } catch (error) {
            console.error('Error saving cart:', error)
        }
    }

    // Add item to cart
    const addToCart = (food, extras = [], specialInstructions = '') => {
        if (!Array.isArray(cartItems.value)) {
            cartItems.value = []
        }

        const existingItemIndex = cartItems.value.findIndex(item => {
            if (!item || !item.food) return false
            const sameFood = item.food.id === food.id
            const sameExtras = JSON.stringify(item.extras) === JSON.stringify(extras)
            const sameInstructions = item.special_instructions === specialInstructions
            return sameFood && sameExtras && sameInstructions
        })

        if (existingItemIndex > -1) {
            cartItems.value[existingItemIndex].quantity++
        } else {
            cartItems.value.push({
                id: Date.now(),
                food,
                extras,
                quantity: 1,
                unit_price: calculateItemPrice(food, extras),
                special_instructions: specialInstructions
            })
        }

        saveCart()
    }

    // Calculate price for a single item including extras
    const calculateItemPrice = (food, extras) => {
        const basePrice = parseFloat(food.base_price) || 0
        const extrasTotal = extras.reduce((sum, extra) => {
            return sum + (parseFloat(extra.price) || 0)
        }, 0)
        return basePrice + extrasTotal
    }

    // Remove item from cart
    const removeItem = (item) => {
        if (!Array.isArray(cartItems.value)) return
        cartItems.value = cartItems.value.filter(i => i.id !== item.id)
        saveCart()
    }

    // Increase item quantity
    const increaseQuantity = (item) => {
        if (!Array.isArray(cartItems.value)) return
        const index = cartItems.value.findIndex(i => i.id === item.id)
        if (index > -1) {
            cartItems.value[index].quantity++
            saveCart()
        }
    }

    // Decrease item quantity
    const decreaseQuantity = (item) => {
        if (!Array.isArray(cartItems.value)) return
        const index = cartItems.value.findIndex(i => i.id === item.id)
        if (index > -1) {
            if (cartItems.value[index].quantity > 1) {
                cartItems.value[index].quantity--
            } else {
                removeItem(item)
            }
            saveCart()
        }
    }

    // Clear cart
    const clearCart = () => {
        cartItems.value = []
        saveCart()
    }

    // Computed properties with error handling
    const cartItemsCount = computed(() => {
        if (!Array.isArray(cartItems.value)) return 0
        return cartItems.value.reduce((sum, item) => sum + (item?.quantity || 0), 0)
    })

    const subtotal = computed(() => {
        if (!Array.isArray(cartItems.value)) return 0
        return cartItems.value.reduce((sum, item) => {
            if (!item) return sum
            const quantity = item.quantity || 0
            const price = parseFloat(item.unit_price) || 0
            return sum + (quantity * price)
        }, 0)
    })

    const tax = computed(() => {
        return subtotal.value * TAX_RATE
    })

    const deliveryFee = computed(() => {
        return cartItems.value.length > 0 ? BASE_DELIVERY_FEE : 0
    })

    const total = computed(() => {
        return subtotal.value + tax.value + deliveryFee.value
    })

    return {
        cartItems,
        cartItemsCount,
        addToCart,
        removeItem,
        increaseQuantity,
        decreaseQuantity,
        clearCart,
        subtotal,
        tax,
        deliveryFee,
        total
    }
}
