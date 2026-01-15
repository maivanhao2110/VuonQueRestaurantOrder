/**
 * Cart Page JavaScript
 * Handles cart display and checkout
 */

// Initialize page
document.addEventListener('DOMContentLoaded', () => {
    initCartPage();
    
    // Listen for cart changes
    window.addEventListener('cartChanged', () => {
        renderCart();
    });
});

/**
 * Initialize cart page
 */
function initCartPage() {
    // Preserve table number in navigation
    if (typeof getTableNumber === 'function') {
        const tableNumber = getTableNumber();
        if (tableNumber) {
            updateNavigationLinks(tableNumber);
        }
    }

    // Render cart
    renderCart();
    
    // Event delegation for cart item buttons
    const cartItemsContainer = document.getElementById('cartItems');
    if (cartItemsContainer) {
        // Remove any existing listeners (not strictly necessary with fresh page load but good practice)
        cartItemsContainer.replaceWith(cartItemsContainer.cloneNode(true));
        const newContainer = document.getElementById('cartItems');
        
        newContainer.addEventListener('click', handleCartItemClick);
    }
}

/**
 * Handle cart item clicks (delegation)
 */
function handleCartItemClick(e) {
    // Debug for development
    // console.log('Clicked:', e.target);

    // Find closest button
    const increaseBtn = e.target.closest('.increase-btn');
    const decreaseBtn = e.target.closest('.decrease-btn');
    const removeBtn = e.target.closest('.remove-btn');
    
    let actionItem = null;
    let actionType = null;

    if (increaseBtn) {
        actionItem = increaseBtn;
        actionType = 'increase';
    } else if (decreaseBtn) {
        actionItem = decreaseBtn;
        actionType = 'decrease';
    } else if (removeBtn) {
        actionItem = removeBtn;
        actionType = 'remove';
    }

    if (actionItem) {
        const itemId = actionItem.getAttribute('data-item-id'); // Get as string
        
        // IMPORTANT: Use == for loose equality to handle string/number ID mismatch
        const currentItem = cartManager.cart.find(item => item.id == itemId);

        if (!currentItem) {
            console.warn('Item not found in cart:', itemId);
            return;
        }

        const numericId = currentItem.id; // Use real ID from item

        if (actionType === 'increase') {
            cartManager.updateQuantity(numericId, currentItem.quantity + 1);
            renderCart();
        } else if (actionType === 'decrease') {
            cartManager.updateQuantity(numericId, currentItem.quantity - 1);
            renderCart();
        } else if (actionType === 'remove') {
            removeItem(numericId);
        }
    }
}

/**
 * Update navigation links with table number
 */
function updateNavigationLinks(tableNumber) {
    const navLinks = document.querySelectorAll('.bottom-nav a');
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && !href.includes('?')) {
            link.setAttribute('href', href + '?ban=' + tableNumber);
        }
    });
}

/**
 * Render cart items
 */
function renderCart() {
    const cartItemsContainer = document.getElementById('cartItems');
    const orderSummary = document.getElementById('orderSummary');

    if (!cartItemsContainer || !orderSummary) return;

    if (!cartManager || cartManager.isEmpty()) {
        orderSummary.style.display = 'none'; // Safer than classList
        cartItemsContainer.innerHTML = `
            <div class="empty-cart" style="display: flex; flex-direction: column; align-items: center; padding: 40px 0; text-align: center;">
                <div style="font-size: 48px; margin-bottom: 16px;">🛒</div>
                <p style="color: #6b7280; margin-bottom: 16px;">Giỏ hàng của bạn đang trống</p>
                <a href="menu.html${typeof getTableNumber === 'function' && getTableNumber() ? '?ban='+getTableNumber() : ''}" class="btn btn-primary">Xem Menu</a>
            </div>
        `;
        return;
    }

    orderSummary.style.display = 'block'; // Ensure visible via style
    orderSummary.classList.remove('hidden'); // Ensure visible via class removal

    // Render items
    cartItemsContainer.innerHTML = '';
    cartManager.cart.forEach(item => {
        const itemElement = createCartItemElement(item);
        cartItemsContainer.appendChild(itemElement);
    });

    // Update summary
    updateCartSummary();
}

/**
 * Create cart item element
 */
function createCartItemElement(item) {
    const div = document.createElement('div');
    div.className = 'cart-item';

    const imageUrl = item.image_url || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="80" height="80"%3E%3Crect fill="%23f3f4f6" width="80" height="80"/%3E%3Ctext fill="%239ca3af" font-family="sans-serif" font-size="12" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3E🍽️%3C/text%3E%3C/svg%3E';

    const totalPrice = item.price * item.quantity;
    
    // Helper to format currency safely
    const format = (amount) => typeof formatCurrency === 'function' ? formatCurrency(amount) : amount.toLocaleString('vi-VN') + 'đ';

    div.innerHTML = `
        <img src="${imageUrl}" alt="${item.name}" class="cart-item-image">
        <div class="cart-item-info">
            <h3 class="cart-item-name">${item.name}</h3>
            <p class="cart-item-price">${format(item.price)} x ${item.quantity}</p>
            <div class="cart-item-actions">
                <div class="quantity-control">
                    <button class="quantity-btn decrease-btn" data-item-id="${item.id}">−</button>
                    <span class="quantity-value">${item.quantity}</span>
                    <button class="quantity-btn increase-btn" data-item-id="${item.id}">+</button>
                </div>
                <span class="cart-item-total">${format(totalPrice)}</span>
            </div>
            <button class="remove-btn" data-item-id="${item.id}">🗑️ Xóa</button>
        </div>
    `;

    return div;
}

/**
 * Update cart summary
 */
function updateCartSummary() {
    if (!cartManager) return;
    const totals = cartManager.calculateTotal();
    
    const subtotalEl = document.getElementById('subtotalAmount');
    const totalEl = document.getElementById('totalAmount');
    
    // Helper to format currency safely
    const format = (amount) => typeof formatCurrency === 'function' ? formatCurrency(amount) : amount.toLocaleString('vi-VN') + 'đ';
    
    if (subtotalEl) subtotalEl.textContent = format(totals.subtotal);
    if (totalEl) totalEl.textContent = format(totals.total);
}

/**
 * Remove item from cart with beautiful embedded modal
 */
function removeItem(itemId) {
    const item = cartManager.cart.find(i => i.id == itemId); // Loose equality
    if (!item) return;

    showLocalConfirm(
        `Bạn có chắc muốn xóa món "${item.name}" khỏi giỏ hàng?`,
        () => {
            cartManager.removeFromCart(item.id); // Use real ID
            renderCart();
        }
    );
}

/**
 * Local custom confirmation modal (Self-contained)
 */
function showLocalConfirm(message, onConfirm) {
    // Remove existing modal if any
    const existingModal = document.querySelector('.custom-modal-overlay');
    if (existingModal) existingModal.remove();

    // Create modal
    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'custom-modal-overlay';
    modalOverlay.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 9999;
        display: flex; align-items: center; justify-content: center;
    `;

    const modalBox = document.createElement('div');
    modalBox.className = 'custom-modal-box';
    modalBox.style.cssText = `
        background: white; padding: 24px; border-radius: 12px;
        width: 90%; max-width: 320px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        text-align: center;
    `;

    modalBox.innerHTML = `
        <div style="font-size: 16px; margin-bottom: 20px; color: #374151;">${message}</div>
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <button id="modalCancel" style="
                padding: 8px 16px; border-radius: 6px; border: 1px solid #d1d5db;
                background: white; color: #374151; font-weight: 500; cursor: pointer;
            ">Hủy</button>
            <button id="modalConfirm" style="
                padding: 8px 16px; border-radius: 6px; border: none;
                background: #22c55e; color: white; font-weight: 500; cursor: pointer;
            ">Đồng ý</button>
        </div>
    `;

    modalOverlay.appendChild(modalBox);
    document.body.appendChild(modalOverlay);

    // Events
    const cancelBtn = modalBox.querySelector('#modalCancel');
    const confirmBtn = modalBox.querySelector('#modalConfirm');

    cancelBtn.onclick = () => modalOverlay.remove();
    confirmBtn.onclick = () => {
        onConfirm();
        modalOverlay.remove();
    };
    modalOverlay.onclick = (e) => {
        if (e.target === modalOverlay) modalOverlay.remove();
    };
}

/**
 * Handle checkout global function
 */
window.handleCheckout = async function() {
    try {
        // Get customer info
        const customerNameEl = document.getElementById('customerName');
        const customerPhoneEl = document.getElementById('customerPhone');
        const customerEmailEl = document.getElementById('customerEmail');
        const orderNoteEl = document.getElementById('orderNote');
        
        const customerName = customerNameEl ? customerNameEl.value.trim() : '';
        const customerPhone = customerPhoneEl ? customerPhoneEl.value.trim() : '';
        const customerEmail = customerEmailEl ? customerEmailEl.value.trim() : '';
        const orderNote = orderNoteEl ? orderNoteEl.value.trim() : '';

        // Validate required fields
        if (!customerName) {
            alert('Vui lòng nhập tên của bạn!');
            if (customerNameEl) customerNameEl.focus();
            return;
        }

        if (!cartManager || cartManager.isEmpty()) return;

        const itemCount = cartManager.getItemCount();
        const total = cartManager.calculateTotal().total;
        
        const format = (amount) => typeof formatCurrency === 'function' ? formatCurrency(amount) : amount.toLocaleString('vi-VN') + 'đ';

        showLocalConfirm(
            `Xác nhận đặt ${itemCount} món với tổng tiền ${format(total)}?`,
            async () => {
                if (typeof placeOrder === 'function') {
                    // Pass customer info to placeOrder
                    const order = await placeOrder(customerName, orderNote, customerPhone, customerEmail);
                    if (order) {
                        const tableNumber = typeof getTableNumber === 'function' ? getTableNumber() : null;
                        const redirectUrl = tableNumber ? `order-status.html?ban=${tableNumber}` : 'order-status.html';
                        window.location.href = redirectUrl;
                    }
                } else {
                    alert('Lỗi: Hàm placeOrder không tồn tại');
                }
            }
        );
    } catch (error) {
        console.error('Checkout error:', error);
        alert('Có lỗi xảy ra. Vui lòng thử lại');
    }
};
