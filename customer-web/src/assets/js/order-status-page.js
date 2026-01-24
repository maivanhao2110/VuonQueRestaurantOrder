/**
 * Order Status Page JavaScript
 */

let refreshIntervalId = null;

// Initialize page
document.addEventListener('DOMContentLoaded', () => {
    initOrderStatusPage();
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (refreshIntervalId) {
        stopOrderStatusRefresh(refreshIntervalId);
    }
});

/**
 * Initialize order status page
 */
function initOrderStatusPage() {
    // Preserve table number
    const tableNumber = getTableNumber();
    if (tableNumber) {
        updateNavigationLinks(tableNumber);
    }

    // Start auto-refresh
    refreshIntervalId = startOrderStatusRefresh(loadOrders);
}

/**
 * Update navigation links
 */
function updateNavigationLinks(tableNumber) {
    const navLinks = document.querySelectorAll('.bottom-nav a');
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && !href.includes('?')) {
            link.setAttribute('href', `${href}?ban=${tableNumber}`);
        }
    });
}

/**
 * Load all orders
 */
async function loadOrders() {
    try {
        const orders = await getTableOrders();

        // Separate active and completed orders
        const activeOrders = orders.filter(order =>
            order.status !== 'DONE' && order.status !== 'CANCELLED'
        );



        // Render
        renderOrders(activeOrders, 'activeOrders');

    } catch (error) {
        console.error('Load orders error:', error);
    }
}

/**
 * Render orders to container
 */
function renderOrders(orders, containerId) {
    const container = document.getElementById(containerId);

    if (orders.length === 0) {
        container.innerHTML = `
            <div class="orders-empty">
                <div class="orders-empty-icon">📋</div>
                <p>Chưa có đơn hàng nào</p>
            </div>
        `;
        return;
    }

    container.innerHTML = '';
    orders.forEach(order => {
        const orderCard = createOrderCard(order);
        container.appendChild(orderCard);
    });
}

/**
 * Create order card element
 */
function createOrderCard(order) {
    const card = document.createElement('div');
    card.className = 'order-card';

    const formattedOrder = formatOrderForDisplay(order);
    const badgeClass = `badge badge-${formattedOrder.status_color}`;

    // Create items HTML
    const itemsHtml = order.items.map(item => `
        <div class="order-item">
            <span class="item-name">${item.menu_item_name}</span>
            <span class="item-quantity">x${item.quantity}</span>
            <span class="item-price">${formatCurrency(item.price * item.quantity)}</span>
        </div>
    `).join('');

    card.innerHTML = `
        <div class="order-header">
            <div class="order-info">
                <h3>Đơn hàng #${order.id}</h3>
                <p class="order-time">${formattedOrder.formatted_date}</p>
            </div>
            <span class="${badgeClass}">${formattedOrder.status_text}</span>
        </div>
        
        <div class="order-items">
            ${itemsHtml}
        </div>

        ${order.note ? `<p class="text-sm text-secondary mb-sm">Ghi chú: ${order.note}</p>` : ''}

        <div class="order-footer">
            <span class="order-total">
                <span class="total-label">Tổng cộng:</span>
                <span class="total-amount">${formatCurrency(formattedOrder.total_amount)}</span>
            </span>
        </div>
    `;

    return card;
}
