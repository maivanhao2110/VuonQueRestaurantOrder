/**
 * Order Operations
 * Handles order placement and status tracking
 */

/**
 * Place order from cart
 * @param {string} customerName - Customer name (required)
 * @param {string} note - Order note
 * @param {string} phone - Customer phone (optional)
 * @param {string} email - Customer email (optional)
 * @returns {Promise<Object|null>} Created order or null
 */
async function placeOrder(customerName, note = '', phone = '', email = '') {
    try {
        // Get table number
        const tableNumber = getTableNumber();
        if (!tableNumber) {
            showToast('Không tìm thấy số bàn. Vui lòng quét lại mã QR', 'error');
            return null;
        }

        // Check if cart is empty
        if (cartManager.isEmpty()) {
            showToast('Giỏ hàng trống. Vui lòng chọn món', 'warning');
            return null;
        }

        // Show loading
        showLoading('Đang đặt món...');

        // Use default name if not provided
        const finalCustomerName = customerName && customerName.trim() ? customerName.trim() : 'Khách';

        // Prepare order data
        const orderData = {
            customer_name: finalCustomerName,
            table_number: tableNumber,
            items: cartManager.getOrderItems(),
            note: note.trim(),
            phone: phone.trim(),
            email: email.trim()
        };

        // Submit order to API
        const order = await api.createOrder(orderData);

        if (order) {
            // Clear cart
            cartManager.clearCart();

            // Hide loading
            hideLoading();

            // Show success message
            showToast('Đặt món thành công!', 'success');

            return order;
        } else {
            hideLoading();
            return null;
        }
    } catch (error) {
        hideLoading();
        console.error('Place order error:', error);
        showToast(error.message || 'Không thể đặt món. Vui lòng thử lại', 'error');
        return null;
    }
}

/**
 * Get orders for current table
 * @param {string|null} status - Filter by status
 * @returns {Promise<Array>} List of orders
 */
async function getTableOrders(status = null) {
    try {
        const tableNumber = getTableNumber();
        if (!tableNumber) {
            return [];
        }

        const orders = await api.getOrdersByTable(tableNumber, status);
        return orders;
    } catch (error) {
        console.error('Get table orders error:', error);
        return [];
    }
}

/**
 * Get active (non-completed) orders for table
 * @returns {Promise<Array>} Active orders
 */
async function getActiveOrders() {
    const allOrders = await getTableOrders();
    
    // Filter out cancelled and done orders that are older than 30 minutes
    const thirtyMinutesAgo = new Date(Date.now() - 30 * 60 * 1000);
    
    return allOrders.filter(order => {
        if (order.status === 'CANCELLED') {
            return false;
        }
        
        if (order.status === 'DONE') {
            const orderDate = new Date(order.created_at);
            return orderDate > thirtyMinutesAgo;
        }
        
        return true;
    });
}

/**
 * Get order details
 * @param {number} orderId - Order ID
 * @returns {Promise<Object|null>} Order details
 */
async function getOrderDetails(orderId) {
    try {
        const order = await api.getOrderDetails(orderId);
        return order;
    } catch (error) {
        console.error('Get order details error:', error);
        return null;
    }
}

/**
 * Auto-refresh order status
 * @param {Function} callback - Callback function to call on refresh
 * @returns {number} Interval ID
 */
function startOrderStatusRefresh(callback) {
    // Initial call
    callback();
    
    // Set up interval
    const intervalId = setInterval(callback, CONFIG.APP.ORDER_REFRESH_INTERVAL);
    
    return intervalId;
}

/**
 * Stop order status refresh
 * @param {number} intervalId - Interval ID to stop
 */
function stopOrderStatusRefresh(intervalId) {
    if (intervalId) {
        clearInterval(intervalId);
    }
}

/**
 * Format order for display
 * @param {Object} order - Order object
 * @returns {Object} Formatted order
 */
function formatOrderForDisplay(order) {
    const statusBadge = getStatusBadge(order.status, 'order');
    
    return {
        ...order,
        formatted_date: formatDateTime(order.created_at),
        status_text: statusBadge.text,
        status_color: statusBadge.color,
        total_amount: order.items ? order.items.reduce((sum, item) => 
            sum + (item.price * item.quantity), 0
        ) : 0
    };
}
