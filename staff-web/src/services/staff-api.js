/**
 * Staff API Service
 * Handles all HTTP communications with backend for Staff Web
 */

const STAFF_API_BASE = '/VuonQueRestaurantOrder/backend/src/public/index.php/api/staff';

const staffApi = {
    // ==================== Orders ====================
    
    /**
     * Get all orders with optional status filter
     */
    getOrders: (status = null) => {
        let url = `${STAFF_API_BASE}/orders`;
        if (status) {
            url += `?status=${status}`;
        }
        return fetch(url)
            .then(r => r.json())
            .then(r => r.success ? r.data : Promise.reject(r.message));
    },

    /**
     * Get order detail by ID
     */
    getOrderDetail: (id) => {
        return fetch(`${STAFF_API_BASE}/orders/${id}`)
            .then(r => r.json())
            .then(r => r.success ? r.data : Promise.reject(r.message));
    },

    /**
     * Confirm order (CREATED -> CONFIRMED)
     */
    confirmOrder: (id) => {
        return fetch(`${STAFF_API_BASE}/orders/${id}/confirm`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(r => r.json())
            .then(r => r.success ? r : Promise.reject(r.message));
    },

    /**
     * Pay order (all items must be DONE)
     */
    payOrder: (id) => {
        return fetch(`${STAFF_API_BASE}/orders/${id}/pay`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(r => r.json())
            .then(r => r.success ? r : Promise.reject(r.message));
    },

    // ==================== Order Items ====================

    /**
     * Update order item status
     */
    updateItemStatus: (itemId, status) => {
        return fetch(`${STAFF_API_BASE}/order-items/${itemId}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status })
        })
            .then(r => r.json())
            .then(r => r.success ? r : Promise.reject(r.message));
    },

    // ==================== Menu ====================

    /**
     * Get all categories
     */
    getCategories: () => {
        return fetch(`${STAFF_API_BASE}/categories`)
            .then(r => r.json())
            .then(r => r.success ? r.data : Promise.reject(r.message));
    },

    /**
     * Get all menu items
     */
    getMenuItems: () => {
        return fetch(`${STAFF_API_BASE}/menu`)
            .then(r => r.json())
            .then(r => r.success ? r.data : Promise.reject(r.message));
    },

    /**
     * Get menu item by ID
     */
    getMenuItem: (id) => {
        return fetch(`${STAFF_API_BASE}/menu/${id}`)
            .then(r => r.json())
            .then(r => r.success ? r.data : Promise.reject(r.message));
    },

    /**
     * Create new menu item
     */
    createMenuItem: (data) => {
        return fetch(`${STAFF_API_BASE}/menu`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(r => r.json());
    },

    /**
     * Update menu item
     */
    updateMenuItem: (id, data) => {
        return fetch(`${STAFF_API_BASE}/menu/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(r => r.json());
    },

    /**
     * Delete menu item (soft delete)
     */
    deleteMenuItem: (id) => {
        return fetch(`${STAFF_API_BASE}/menu/${id}`, {
            method: 'DELETE'
        })
            .then(r => r.json());
    }
};
