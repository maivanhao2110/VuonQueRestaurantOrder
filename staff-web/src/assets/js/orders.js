/**
 * Orders Management JavaScript
 * Handles order list, detail view, and status updates
 */

let allOrders = [];
let currentFilter = '';
let currentOrderId = null;
let autoRefreshInterval = null;

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadOrders();
    startAutoRefresh();
});

// ==================== Auto Refresh ====================

function startAutoRefresh() {
    autoRefreshInterval = setInterval(() => {
        loadOrders(false); // Silent refresh
    }, 10000); // Refresh every 10 seconds
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

// ==================== Order Loading ====================

async function loadOrders(showLoading = true) {
    try {
        if (showLoading) {
            document.getElementById('ordersGrid').innerHTML = `
                <div class="loading-container">
                    <div class="loading"></div>
                    <p>Đang tải đơn hàng...</p>
                </div>
            `;
        }

        allOrders = await staffApi.getOrders(currentFilter || null);
        renderOrders();
    } catch (error) {
        console.error('Load orders error:', error);
        document.getElementById('ordersGrid').innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">⚠️</div>
                <p>Không thể tải đơn hàng</p>
                <button class="btn btn-primary" onclick="loadOrders()">Thử lại</button>
            </div>
        `;
    }
}

function refreshOrders() {
    loadOrders();
}

function filterOrders(status) {
    currentFilter = status;

    // Update active tab
    document.querySelectorAll('.filter-tab').forEach(tab => {
        const tabStatus = tab.getAttribute('data-status');
        if (tabStatus === status) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });

    loadOrders();
}

// ==================== Order Rendering ====================

function renderOrders() {
    const grid = document.getElementById('ordersGrid');

    if (allOrders.length === 0) {
        grid.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <p>Chưa có đơn hàng nào</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = allOrders.map(order => createOrderCard(order)).join('');
}

function createOrderCard(order) {
    const statusInfo = getStatusInfo(order.status);
    const itemCount = order.items ? order.items.length : 0;
    const doneCount = order.items ? order.items.filter(i => i.status === 'DONE').length : 0;
    const createdTime = formatTime(order.created_at);

    return `
        <div class="order-card ${statusInfo.class}" onclick="openOrderDetail(${order.id})">
            <div class="order-card-header">
                <div class="order-table">
                    <span class="table-icon">🪑</span>
                    <span class="table-number">Bàn ${order.table_number}</span>
                </div>
                <span class="order-status ${statusInfo.class}">${statusInfo.text}</span>
            </div>
            
            <div class="order-card-body">
                <div class="order-info">
                    <div class="info-row">
                        <span class="info-label">Đơn #${order.id}</span>
                        <span class="info-value">${createdTime}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Khách:</span>
                        <span class="info-value">${order.customer_name || 'Khách'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Món:</span>
                        <span class="info-value">${doneCount}/${itemCount} hoàn thành</span>
                    </div>
                </div>
            </div>
            
            <div class="order-card-footer">
                <span class="order-total">${formatCurrency(order.total_amount)}</span>
                <span class="view-detail">Xem chi tiết →</span>
            </div>
        </div>
    `;
}

// ==================== Order Detail Modal ====================

async function openOrderDetail(orderId) {
    currentOrderId = orderId;

    try {
        const order = await staffApi.getOrderDetail(orderId);
        renderOrderModal(order);
        document.getElementById('orderDetailModal').style.display = 'flex';
    } catch (error) {
        console.error('Load order detail error:', error);
        alert('Không thể tải chi tiết đơn hàng');
    }
}

function closeOrderModal() {
    document.getElementById('orderDetailModal').style.display = 'none';
    currentOrderId = null;
}

function renderOrderModal(order) {
    const statusInfo = getStatusInfo(order.status);

    // Title
    document.getElementById('modalOrderTitle').innerHTML = `
        Đơn hàng #${order.id} - Bàn ${order.table_number}
        <span class="order-status ${statusInfo.class}" style="margin-left: 12px; font-size: 14px;">${statusInfo.text}</span>
    `;

    // Body - Items list
    const itemsHtml = order.items.map(item => {
        const itemStatus = getItemStatusInfo(item.status);
        return `
            <div class="order-item-row">
                <div class="item-info">
                    <span class="item-name">${item.menu_item_name}</span>
                    <span class="item-qty">x${item.quantity}</span>
                    <span class="item-price">${formatCurrency(item.price * item.quantity)}</span>
                </div>
                <div class="item-status-controls">
                    <span class="item-status ${itemStatus.class}">${itemStatus.text}</span>
                    ${renderItemStatusButtons(item, order.status)}
                </div>
            </div>
        `;
    }).join('');

    document.getElementById('modalOrderBody').innerHTML = `
        <div class="order-meta">
            <p><strong>Khách hàng:</strong> ${order.customer_name || 'Khách'}</p>
            <p><strong>Thời gian:</strong> ${formatDateTime(order.created_at)}</p>
            ${order.note ? `<p><strong>Ghi chú:</strong> ${order.note}</p>` : ''}
        </div>
        <div class="order-items-list">
            <h4>Danh sách món</h4>
            ${itemsHtml}
        </div>
        <div class="order-summary">
            <div class="summary-row total">
                <span>Tổng cộng:</span>
                <span>${formatCurrency(order.total_amount)}</span>
            </div>
        </div>
    `;

    // Footer - Action buttons
    document.getElementById('modalOrderFooter').innerHTML = renderOrderActions(order);
}

function renderItemStatusButtons(item, orderStatus) {
    // Only show buttons if order is confirmed or cooking
    if (orderStatus === 'CREATED' || orderStatus === 'PAID' || orderStatus === 'CANCELLED') {
        return '';
    }

    if (item.status === 'WAITING') {
        return `<button class="btn btn-sm btn-warning" onclick="updateItemStatus(${item.id}, 'COOKING')">Bắt đầu nấu</button>`;
    } else if (item.status === 'COOKING') {
        return `<button class="btn btn-sm btn-success" onclick="updateItemStatus(${item.id}, 'DONE')">Hoàn thành</button>`;
    } else {
        return `<span class="done-check">✓</span>`;
    }
}

function renderOrderActions(order) {
    let html = '';

    if (order.status === 'CREATED') {
        html = `
            <button class="btn btn-secondary" onclick="closeOrderModal()">Đóng</button>
            <button class="btn btn-primary" onclick="confirmOrder(${order.id})">✓ Xác nhận đơn hàng</button>
        `;
    } else if (order.status === 'CONFIRMED' || order.status === 'COOKING' || order.status === 'DONE') {
        const canPay = order.all_items_done;
        html = `
            <button class="btn btn-secondary" onclick="closeOrderModal()">Đóng</button>
            <button class="btn btn-success ${canPay ? '' : 'disabled'}" 
                    onclick="${canPay ? `payOrder(${order.id})` : ''}"
                    ${canPay ? '' : 'disabled'}>
                💰 Thanh toán ${canPay ? '' : '(chưa đủ món)'}
            </button>
        `;
    } else {
        html = `<button class="btn btn-secondary" onclick="closeOrderModal()">Đóng</button>`;
    }

    return html;
}

// ==================== Order Actions ====================

async function confirmOrder(orderId) {
    try {
        await staffApi.confirmOrder(orderId);
        alert('Đã xác nhận đơn hàng!');
        await refreshOrderDetail(orderId);
        loadOrders(false);
    } catch (error) {
        console.error('Confirm order error:', error);
        alert('Lỗi: ' + error);
    }
}

async function payOrder(orderId) {
    if (!confirm('Xác nhận thanh toán đơn hàng này?')) return;

    try {
        await staffApi.payOrder(orderId);
        alert('Thanh toán thành công!');
        closeOrderModal();
        loadOrders(false);
    } catch (error) {
        console.error('Pay order error:', error);
        alert('Lỗi: ' + error);
    }
}

async function updateItemStatus(itemId, newStatus) {
    try {
        await staffApi.updateItemStatus(itemId, newStatus);
        await refreshOrderDetail(currentOrderId);
        loadOrders(false);
    } catch (error) {
        console.error('Update item status error:', error);
        alert('Lỗi: ' + error);
    }
}

async function refreshOrderDetail(orderId) {
    const order = await staffApi.getOrderDetail(orderId);
    renderOrderModal(order);
}

// ==================== Helper Functions ====================

function getStatusInfo(status) {
    const map = {
        'CREATED': { text: 'Chờ xác nhận', class: 'status-created' },
        'CONFIRMED': { text: 'Đã xác nhận', class: 'status-confirmed' },
        'COOKING': { text: 'Đang nấu', class: 'status-cooking' },
        'DONE': { text: 'Hoàn thành', class: 'status-done' },
        'PAID': { text: 'Đã thanh toán', class: 'status-paid' },
        'CANCELLED': { text: 'Đã hủy', class: 'status-cancelled' }
    };
    return map[status] || { text: status, class: '' };
}

function getItemStatusInfo(status) {
    const map = {
        'WAITING': { text: 'Chờ nấu', class: 'item-waiting' },
        'COOKING': { text: 'Đang nấu', class: 'item-cooking' },
        'DONE': { text: 'Đã xong', class: 'item-done' }
    };
    return map[status] || { text: status, class: '' };
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        minimumFractionDigits: 0
    }).format(amount);
}

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
