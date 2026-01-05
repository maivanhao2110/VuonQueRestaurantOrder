/**
 * Frontend Configuration
 * Centralized config for API endpoints and app constants
 */

const CONFIG = {
    // API Configuration
    API: {
        BASE_URL: '/VuonQueRestaurantOrder/backend/src/public/index.php',
        ENDPOINTS: {
            // Menu endpoints
            CATEGORIES: '/api/customer/categories',
            MENU: '/api/customer/menu',
            MENU_ITEM: '/api/customer/menu/:id',

            // Order endpoints
            ORDERS: '/api/customer/orders',
            ORDER_DETAIL: '/api/customer/orders/:id',
        }
    },

    // App Constants
    APP: {
        NAME: 'Vườn Quê Restaurant',
        STORAGE_KEYS: {
            CART: 'vuonque_cart',
            CUSTOMER_NAME: 'vuonque_customer_name',
            TABLE_NUMBER: 'vuonque_table_number'
        },
        ORDER_REFRESH_INTERVAL: 5000, // 5 seconds
    },

    // Order Status
    ORDER_STATUS: {
        PENDING: 'PENDING',
        CONFIRMED: 'CONFIRMED',
        PREPARING: 'PREPARING',
        READY: 'READY',
        SERVED: 'SERVED',
        DONE: 'DONE',
        CANCELLED: 'CANCELLED'
    },

    // Currency Format
    CURRENCY: {
        SYMBOL: '₫',
        LOCALE: 'vi-VN'
    }
};
