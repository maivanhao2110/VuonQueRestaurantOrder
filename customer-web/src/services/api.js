/**
 * API Service Layer
 * Handles all HTTP communications with backend
 */

const api = {
    /**
     * Generic request method
     * @private
     */
    async request(endpoint, options = {}) {
        const url = `${CONFIG.API.BASE_URL}${endpoint}`;
        
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
            },
            ...options
        };

        try {
            const response = await fetch(url, defaultOptions);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'API request failed');
            }

            return data.data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },

    /**
     * GET request
     * @private
     */
    async get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    },

    /**
     * POST request
     * @private
     */
    async post(endpoint, data) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    /**
     * PUT request
     * @private
     */
    async put(endpoint, data) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },

    /**
     * DELETE request
     * @private
     */
    async delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    },

    // ==================== Menu API ====================
    
    /**
     * Get all categories
     * @returns {Promise<Array>} List of categories
     */
    async getCategories() {
        return this.get(CONFIG.API.ENDPOINTS.CATEGORIES);
    },

    /**
     * Get all menu items or by category
     * @param {number|null} categoryId - Optional category ID filter
     * @returns {Promise<Array>} List of menu items
     */
    async getMenuItems(categoryId = null) {
        const endpoint = categoryId 
            ? `${CONFIG.API.ENDPOINTS.MENU}?category_id=${categoryId}`
            : CONFIG.API.ENDPOINTS.MENU;
        return this.get(endpoint);
    },

    /**
     * Get menu item by ID
     * @param {number} itemId - Menu item ID
     * @returns {Promise<Object>} Menu item details
     */
    async getMenuItem(itemId) {
        const endpoint = CONFIG.API.ENDPOINTS.MENU_ITEM.replace(':id', itemId);
        return this.get(endpoint);
    },

    // ==================== Order API ====================

    /**
     * Create new order
     * @param {Object} orderData - Order data
     * @returns {Promise<Object>} Created order
     */
    async createOrder(orderData) {
        return this.post(CONFIG.API.ENDPOINTS.ORDERS, orderData);
    },

    /**
     * Get orders by table number
     * @param {string} tableNumber - Table number
     * @param {string|null} status - Optional status filter
     * @returns {Promise<Array>} List of orders
     */
    async getOrdersByTable(tableNumber, status = null) {
        let endpoint = `${CONFIG.API.ENDPOINTS.ORDERS}?table_number=${tableNumber}`;
        if (status) {
            endpoint += `&status=${status}`;
        }
        return this.get(endpoint);
    },

    /**
     * Get order details
     * @param {number} orderId - Order ID
     * @returns {Promise<Object>} Order details with items
     */
    async getOrderDetails(orderId) {
        const endpoint = CONFIG.API.ENDPOINTS.ORDER_DETAIL.replace(':id', orderId);
        return this.get(endpoint);
    },

    /**
     * Update order status (for future use)
     * @param {number} orderId - Order ID
     * @param {string} status - New status
     * @returns {Promise<Object>} Updated order
     */
    async updateOrderStatus(orderId, status) {
        const endpoint = CONFIG.API.ENDPOINTS.ORDER_DETAIL.replace(':id', orderId);
        return this.put(endpoint, { status });
    }
};
