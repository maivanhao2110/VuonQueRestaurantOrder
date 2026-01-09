/**
 * Cart Management
 * Handles shopping cart operations with LocalStorage persistence
 */

class CartManager {
    constructor() {
        this.storageKey = CONFIG.APP.STORAGE_KEYS.CART;
        this.cart = this.loadCart();
    }

    /**
     * Load cart from LocalStorage
     * @returns {Array} Cart items
     */
    loadCart() {
        try {
            const cartData = localStorage.getItem(this.storageKey);
            if (!cartData) return [];
            const parsed = JSON.parse(cartData);
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            console.error('Error loading cart:', error);
            return [];
        }
    }

    /**
     * Save cart to LocalStorage
     */
    saveCart() {
        localStorage.setItem(this.storageKey, JSON.stringify(this.cart));
        this.notifyChange();
    }

    /**
     * Get all cart items
     * @returns {Array} Cart items
     */
    getCart() {
        return this.cart;
    }

    /**
     * Get cart item count
     * @returns {number} Total number of items in cart
     */
    getItemCount() {
        return this.cart.reduce((total, item) => total + item.quantity, 0);
    }

    /**
     * Add item to cart
     * @param {Object} menuItem - Menu item to add
     * @param {number} quantity - Quantity to add
     */
    addToCart(menuItem, quantity = 1) {
        // Ensure cart is always an array
        if (!this.cart || !Array.isArray(this.cart)) {
            this.cart = [];
        }
        
        // Check if item already exists in cart
        const existingItem = this.cart.find(item => item.id === menuItem.id);

        if (existingItem) {
            // Update quantity
            existingItem.quantity += quantity;
        } else {
            // Add new item
            this.cart.push({
                id: menuItem.id,
                name: menuItem.name,
                price: parseFloat(menuItem.price),
                image_url: menuItem.image_url,
                quantity: quantity
            });
        }

        this.saveCart();
        showToast(`Đã thêm ${menuItem.name} vào giỏ hàng`, 'success', 2000);
    }

    /**
     * Update item quantity
     * @param {number} itemId - Menu item ID
     * @param {number} quantity - New quantity
     */
    updateQuantity(itemId, quantity) {
        const item = this.cart.find(item => item.id === itemId);

        if (item) {
            if (quantity <= 0) {
                this.removeFromCart(itemId);
            } else {
                item.quantity = quantity;
                this.saveCart();
            }
        }
    }

    /**
     * Remove item from cart
     * @param {number} itemId - Menu item ID
     */
    removeFromCart(itemId) {
        const index = this.cart.findIndex(item => item.id === itemId);

        if (index !== -1) {
            const itemName = this.cart[index].name;
            this.cart.splice(index, 1);
            this.saveCart();
            showToast(`Đã xóa ${itemName} khỏi giỏ hàng`, 'info', 2000);
        }
    }

    /**
     * Clear entire cart
     */
    clearCart() {
        this.cart = [];
        this.saveCart();
    }

    /**
     * Calculate subtotal
     * @returns {number} Subtotal amount
     */
    calculateSubtotal() {
        return this.cart.reduce((total, item) => {
            return total + (item.price * item.quantity);
        }, 0);
    }

    /**
     * Calculate total (with tax and service charge if applicable)
     * @returns {Object} Total breakdown
     */
    calculateTotal() {
        const subtotal = this.calculateSubtotal();
        const tax = 0; // Vietnam VAT if applicable
        const serviceCharge = 0; // Service charge if applicable
        const total = subtotal + tax + serviceCharge;

        return {
            subtotal,
            tax,
            serviceCharge,
            total
        };
    }

    /**
     * Get cart data for order submission
     * @returns {Array} Order items format
     */
    getOrderItems() {
        return this.cart.map(item => ({
            menu_item_id: item.id,
            quantity: item.quantity,
            price: item.price
        }));
    }

    /**
     * Check if cart is empty
     * @returns {boolean} True if cart is empty
     */
    isEmpty() {
        return this.cart.length === 0;
    }

    /**
     * Notify cart change to update UI
     */
    notifyChange() {
        // Dispatch custom event for UI updates
        window.dispatchEvent(new CustomEvent('cartChanged', {
            detail: {
                cart: this.cart,
                itemCount: this.getItemCount(),
                total: this.calculateTotal()
            }
        }));
    }
}

// Create global cart instance
const cartManager = new CartManager();

// Update cart badge on all pages
function updateCartBadge() {
    const cartBadge = document.querySelector('.nav-badge');
    if (cartBadge) {
        const count = cartManager.getItemCount();
        cartBadge.textContent = count;
        cartBadge.style.display = count > 0 ? 'flex' : 'none';
    }
}

// Listen for cart changes
window.addEventListener('cartChanged', () => {
    updateCartBadge();
});

// Initialize cart badge on page load
document.addEventListener('DOMContentLoaded', () => {
    updateCartBadge();
});
