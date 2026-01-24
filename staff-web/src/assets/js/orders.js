/**
 * Orders Management JavaScript
 * Handles order list, detail view, and status updates
 */

let allOrders = [];
let currentFilter = '';
let currentOrderId = null;
let currentSort = 'DESC'; // Default: Newest first
let autoRefreshInterval = null;

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadOrders();
    startAutoRefresh();
    initProfile();
});

// Close dropdown when clicking outside
window.addEventListener('click', (e) => {
    if (!e.target.closest('.profile-dropdown')) {
        const dropdown = document.getElementById('profileDropdown');
        if (dropdown) dropdown.classList.remove('show');
    }
});

function initProfile() {
    const staffUser = JSON.parse(sessionStorage.getItem('staff_user') || '{}');
    const nameEl = document.getElementById('staffName');
    if (staffUser.full_name && nameEl) {
        nameEl.textContent = 'Xin chào, ' + staffUser.full_name;
    } else if (!staffUser.id) {
        // Not logged in or session expired
        window.location.href = 'login.html';
    }
}

function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    if (dropdown) dropdown.classList.toggle('show');
}

function logout(e) {
    if (e) e.preventDefault();
    sessionStorage.removeItem('staff_user');
    window.location.href = 'login.html';
}

// ==================== Password Change ====================

function openChangePasswordModal(e) {
    if (e) e.preventDefault();
    const dropdown = document.getElementById('profileDropdown');
    if (dropdown) dropdown.classList.remove('show');

    document.getElementById('passwordModal').style.display = 'block';
}

function closePasswordModal() {
    document.getElementById('passwordModal').style.display = 'none';
    document.getElementById('changePasswordForm').reset();

    // Reset all password fields to 'password' type
    ['oldPassword', 'newPassword', 'confirmNewPassword'].forEach(id => {
        const input = document.getElementById(id);
        if (input) input.type = 'password';
    });
}

function togglePasswordVisibility(id) {
    const input = document.getElementById(id);
    if (!input) return;

    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

async function handleChangePassword(e) {
    e.preventDefault();
    const oldPass = document.getElementById('oldPassword').value;
    const newPass = document.getElementById('newPassword').value;
    const confirmPass = document.getElementById('confirmNewPassword').value;

    if (newPass === oldPass) {
        alert('Mật khẩu mới không được trùng với mật khẩu cũ');
        return;
    }

    if (newPass !== confirmPass) {
        alert('Mật khẩu mới không khớp');
        return;
    }

    const staffUser = JSON.parse(sessionStorage.getItem('staff_user') || '{}');

    try {
        await staffApi.changePassword(staffUser.id, oldPass, newPass);
        alert('Đổi mật khẩu thành công!');
        closePasswordModal();
    } catch (error) {
        alert('Lỗi: ' + error);
    }
}

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

function toggleSort() {
    currentSort = (currentSort === 'DESC') ? 'ASC' : 'DESC';
    const label = document.getElementById('sortLabel');
    if (label) label.textContent = (currentSort === 'DESC') ? 'Mới nhất' : 'Cũ nhất';

    const btn = document.getElementById('btnSort');
    if (btn) {
        if (currentSort === 'ASC') {
            btn.classList.add('active-sort');
        } else {
            btn.classList.remove('active-sort');
        }
    }

    renderOrders();
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

    // Sort orders locally
    const sortedOrders = [...allOrders].sort((a, b) => {
        const timeA = new Date(a.created_at).getTime();
        const timeB = new Date(b.created_at).getTime();
        return currentSort === 'DESC' ? timeB - timeA : timeA - timeB;
    });

    grid.innerHTML = sortedOrders.map(order => createOrderCard(order)).join('');
}

function createOrderCard(order) {
    const statusInfo = getStatusInfo(order.status);
    const itemCount = order.items ? order.items.length : 0;
    const doneCount = order.items ? order.items.filter(i => i.status === 'DONE' || i.status === 'SERVED').length : 0;
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
        const itemStatus = getItemStatusInfo(item.status, order.status);
        const qty = parseInt(item.quantity); // Ensure number
        const canEdit = (order.status !== 'PAID' && order.status !== 'CANCELLED' && item.status === 'WAITING');

        // Quantity Controls
        let quantityHtml = `x${item.quantity}`;
        if (canEdit) {
            quantityHtml = `
                <div class="quantity-control">
                    <button class="btn-qty" onclick="changeItemQuantity(${item.id}, ${qty - 1})" title="Giảm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                    <span>${qty}</span>
                    <button class="btn-qty" onclick="changeItemQuantity(${item.id}, ${qty + 1})" title="Tăng">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                </div>
            `;
        }

        // Delete button
        let deleteBtn = '';
        if (canEdit) {
            deleteBtn = `
                <button class="btn-delete" onclick="deleteOrderItem(${item.id})" title="Xóa món">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    Xóa
                </button>
            `;
        }

        return `
            <div class="order-item-row">
                <div class="item-info">
                    <span class="item-name">${item.menu_item_name} ${deleteBtn}</span>
                    <span class="item-qty">${quantityHtml}</span>
                    <span class="item-price">${formatCurrency(item.price * item.quantity)}</span>
                </div>
                <div class="item-status-controls">
                    <span class="item-status ${itemStatus.class}">${itemStatus.text}</span>
                    ${renderItemStatusButtons(item, order.status)}
                </div>
            </div>
        `;
    }).join('');

    // Add Item Button
    let addItemHtml = '';
    if (order.status !== 'PAID' && order.status !== 'CANCELLED') {
        addItemHtml = `
            <div class="add-item-container" style="text-align: center; margin-top: 15px;">
                <button class="btn btn-outline-primary" onclick="openAddItemModal(${order.id})">+ Thêm món</button>
            </div>
        `;
    }

    document.getElementById('modalOrderBody').innerHTML = `
        <div class="order-meta">
            <p><strong>Khách hàng:</strong> ${order.customer_name || 'Khách'}</p>
            <p><strong>Thời gian:</strong> ${formatDateTime(order.created_at)}</p>
            ${order.note ? `<p><strong>Ghi chú:</strong> ${order.note}</p>` : ''}
        </div>
        <div class="order-items-list">
            <h4>Danh sách món</h4>
            ${itemsHtml}
            ${addItemHtml}
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
    // Only show buttons if order is confirmed or cooking or done/served
    if (orderStatus === 'CREATED' || orderStatus === 'PAID' || orderStatus === 'CANCELLED') {
        return '';
    }

    if (item.status === 'WAITING') {
        return `<button class="btn btn-sm btn-warning" onclick="updateItemStatus(${item.id}, 'COOKING')">Bắt đầu nấu</button>`;
    } else if (item.status === 'COOKING') {
        return `<button class="btn btn-sm btn-success" onclick="updateItemStatus(${item.id}, 'DONE')">Hoàn thành</button>`;
    } else if (item.status === 'DONE') {
        return `<button class="btn btn-sm btn-serve" onclick="updateItemStatus(${item.id}, 'SERVED')">Phục vụ</button>`;
    } else if (item.status === 'SERVED') {
        return `<span class="done-check">✓</span>`;
    } else {
        return `<span class="done-check">✓</span>`;
    }
}

function renderOrderActions(order) {
    let html = '';

    if (order.status === 'CREATED') {
        html = `
            <button class="btn btn-danger-outline" onclick="cancelOrder(${order.id})" style="margin-right: auto;">❌ Hủy bàn</button>
            <button class="btn btn-secondary" onclick="closeOrderModal()">Đóng</button>
            <button class="btn btn-primary" onclick="confirmOrder(${order.id})">✓ Xác nhận đơn hàng</button>
        `;
    } else if (order.status === 'CONFIRMED' || order.status === 'COOKING' || order.status === 'DONE' || order.status === 'SERVED') {
        const canPay = order.all_items_done;
        html = `
            <button class="btn btn-secondary" onclick="closeOrderModal()">Đóng</button>
            <button class="btn btn-success ${canPay ? '' : 'disabled'}" 
                    onclick="${canPay ? `payOrder(${order.id})` : ''}"
                    ${canPay ? '' : 'disabled'}>
                💰 Thanh toán ${canPay ? '' : '(chưa đủ món)'}
            </button>
        `;

        // Check if can cancel (no items cooking/done/served)
        // We iterate order.items. If any is COOKING or DONE or SERVED, disable cancel
        const hasCookingOrDone = order.items.some(i => i.status === 'COOKING' || i.status === 'DONE' || i.status === 'SERVED');
        if (!hasCookingOrDone) {
            html = `
                <button class="btn btn-danger-outline" onclick="cancelOrder(${order.id})" style="margin-right: auto;">❌ Hủy bàn</button>
                ${html}
            `;
        }
    } else {
        html = `<button class="btn btn-secondary" onclick="closeOrderModal()">Đóng</button>`;
    }

    return html;
}

// ==================== Order Actions ====================

async function confirmOrder(orderId) {
    const currentUser = JSON.parse(sessionStorage.getItem('staff_user') || '{}');
    const staffId = currentUser.id || null;

    try {
        await staffApi.confirmOrder(orderId, staffId);
        alert('Đã xác nhận đơn hàng!');
        await refreshOrderDetail(orderId);
        loadOrders(false);
    } catch (error) {
        console.error('Confirm order error:', error);
        alert('Lỗi: ' + error);
    }
}

let pendingPaymentOrderId = null;

function showPaymentModal(orderId) {
    pendingPaymentOrderId = orderId;
    document.getElementById('paymentModal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
    pendingPaymentOrderId = null;
}

async function confirmPayment(type) {
    if (!pendingPaymentOrderId) return;

    // Disable buttons
    const btns = document.querySelectorAll('#paymentModal button');
    btns.forEach(b => b.disabled = true);

    try {
        await staffApi.payOrder(pendingPaymentOrderId, type);
        alert('Thanh toán thành công!');
        closePaymentModal();
        closeOrderModal();
        loadOrders(false);
    } catch (error) {
        console.error('Pay order error:', error);
        alert('Lỗi: ' + error);
    } finally {
        btns.forEach(b => b.disabled = false);
    }
}

// Keep old function for backward compatibility if needed, but updated to use modal
function payOrder(orderId) {
    showPaymentModal(orderId);
}

async function cancelOrder(orderId) {
    if (!confirm('⚠️ CẢNH BÁO: Bạn có chắc chắn muốn hủy bàn này không?\nHành động này không thể hoàn tác!')) {
        return;
    }

    try {
        await staffApi.cancelOrder(orderId);
        alert('Đã hủy bàn thành công.');
        closeOrderModal();
        loadOrders(false);
    } catch (error) {
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

// ==================== Order Modification ====================

async function changeItemQuantity(itemId, newQuantity) {
    if (newQuantity < 1) return; // Prevent < 1 via this button, user should use delete for 0

    try {
        await staffApi.updateItemQuantity(itemId, newQuantity);
        await refreshOrderDetail(currentOrderId);
        loadOrders(false);
    } catch (error) {
        console.error('Update qty error:', error);
        alert('Lỗi: ' + error);
    }
}

async function deleteOrderItem(itemId) {
    if (!confirm('Bạn có chắc muốn xóa món này không?')) return;

    try {
        await staffApi.deleteItem(itemId);
        await refreshOrderDetail(currentOrderId);
        loadOrders(false);
    } catch (error) {
        console.error('Delete item error:', error);
        alert('Lỗi: ' + error);
    }
}

// Add Item Modal Logic
let addItemOrderId = null;

async function openAddItemModal(orderId) {
    addItemOrderId = orderId;

    // Create modal if not exists
    if (!document.getElementById('addItemModal')) {
        createAddItemModal();
    }

    // Load menu
    try {
        const categories = await staffApi.getCategories();
        renderAddItemModal(categories);
        document.getElementById('addItemModal').style.display = 'flex';
    } catch (error) {
        console.error('Load menu error:', error);
        alert('Không thể tải menu');
    }
}

function closeAddItemModal() {
    document.getElementById('addItemModal').style.display = 'none';
    addItemOrderId = null;
}

function createAddItemModal() {
    const modalHtml = `
    <div id="addItemModal" class="modal-backdrop" style="display: none; z-index: 1200;">
        <div class="modal" style="max-width: 600px; height: 80vh;">
            <div class="modal-header">
                <h3 class="modal-title">Thêm món</h3>
                <button class="modal-close" onclick="closeAddItemModal()">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0; display: flex; flex-direction: column; overflow: hidden;">
                <div id="addItemCategories" class="category-tabs" style="padding: 10px; overflow-x: auto; white-space: nowrap; border-bottom: 1px solid #eee;"></div>
                <div id="addItemList" style="flex: 1; overflow-y: auto; padding: 10px;"></div>
            </div>
        </div>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

async function renderAddItemModal(categories) {
    // Render Categories
    // Add "All" tab first
    let catsHtml = `<button class="btn btn-sm btn-outline-secondary" onclick="loadAddItemMenu(null, this)">Tất cả</button>`;

    catsHtml += categories
        .filter(c => c.name !== 'Tất cả') // Prevent duplicate "All"
        .map(c =>
            `<button class="btn btn-sm btn-outline-secondary" onclick="loadAddItemMenu(${c.id}, this)">${c.name}</button>`
        ).join('');
    document.getElementById('addItemCategories').innerHTML = catsHtml;

    // Load "All" by default
    const firstBtn = document.getElementById('addItemCategories').firstElementChild;
    loadAddItemMenu(null, firstBtn);
}

async function loadAddItemMenu(categoryId, btn) {
    // Active tab style
    document.querySelectorAll('#addItemCategories button').forEach(b => b.classList.remove('active', 'btn-primary'));
    document.querySelectorAll('#addItemCategories button').forEach(b => b.classList.add('btn-outline-secondary'));
    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('active', 'btn-primary');

    document.getElementById('addItemList').innerHTML = '<div class="loading">Đang tải món...</div>';

    try {
        // If categoryId is null, fetch all (no query param)
        // If categoryId is present, append query param
        let url = `${STAFF_API_BASE}/menu`;
        if (categoryId) {
            url += `?category_id=${categoryId}`;
        }

        const response = await fetch(url).then(r => r.json());

        if (!response.success) throw new Error(response.message);

        const items = response.data;
        renderAddItemList(items);

    } catch (error) {
        document.getElementById('addItemList').innerHTML = '<p class="error">Lỗi tải món ăn</p>';
    }
}

function renderAddItemList(items) {
    if (items.length === 0) {
        document.getElementById('addItemList').innerHTML = '<p style="text-align:center; padding: 20px;">Không có món nào</p>';
        return;
    }

    const html = items.map(item => `
        <div class="menu-item-card" onclick="submitAddItem(${item.id})" style="border: 1px solid #eee; padding: 10px; margin-bottom: 8px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: background 0.2s;">
            <div style="display: flex; align-items: center;">
                ${item.image_url ? `<img src="${item.image_url}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 12px;">` : ''}
                <div>
                    <div style="font-weight: 500;">${item.name}</div>
                    <div style="color: #666; font-size: 14px;">${formatCurrency(item.price)}</div>
                </div>
            </div>
            <button class="btn btn-sm btn-primary">+</button>
        </div>
    `).join('');

    document.getElementById('addItemList').innerHTML = html;
}

async function submitAddItem(menuItemId) {
    if (!addItemOrderId) return;

    // Simple add 1 item immediately
    try {
        await staffApi.addOrderItem(addItemOrderId, menuItemId, 1);
        // Show lightweight feedback instead of alert?
        // alert('Đã thêm món');

        // Đóng modal và tải lại toàn bộ trang theo yêu cầu
        closeAddItemModal();
        location.reload();
    } catch (error) {
        alert('Lỗi thêm món: ' + error);
    }
}

// ==================== Helper Functions ====================

function getStatusInfo(status) {
    const map = {
        'CREATED': { text: 'Chờ xác nhận', class: 'status-created' },
        'CONFIRMED': { text: 'Đã xác nhận', class: 'status-confirmed' },
        'COOKING': { text: 'Cooking', class: 'status-cooking' },
        'DONE': { text: 'Hoàn thành', class: 'status-done' },
        'SERVED': { text: 'Đã phục vụ', class: 'status-served' },
        'PAID': { text: 'Đã thanh toán', class: 'status-paid' },
        'CANCELLED': { text: 'Đã hủy', class: 'status-cancelled' }
    };
    return map[status] || { text: status, class: '' };
}

function getItemStatusInfo(status, orderStatus = null) {
    if (orderStatus === 'CREATED' && status === 'WAITING') {
        return { text: 'Chờ xác nhận', class: 'item-waiting' };
    }

    const map = {
        'WAITING': { text: 'Chờ nấu', class: 'item-waiting' },
        'COOKING': { text: 'Cooking', class: 'item-cooking' },
        'DONE': { text: 'Đã xong', class: 'item-done' },
        'SERVED': { text: 'Đã phục vụ', class: 'item-served' }
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
