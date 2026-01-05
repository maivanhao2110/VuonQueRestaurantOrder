/**
 * Menu Page JavaScript
 * Handles menu display, filtering, and add to cart
 */

// State
let allMenuItems = [];
let allCategories = [];
let currentCategory = 'all';
let currentModalItem = null;
let currentModalQuantity = 1;

// Initialize page
document.addEventListener('DOMContentLoaded', async () => {
    await initMenuPage();
    
    // Event delegation for add-to-cart buttons
    document.getElementById('menuGrid').addEventListener('click', (e) => {
        const btn = e.target.closest('.add-to-cart-btn');
        if (btn) {
            const itemId = btn.getAttribute('data-item-id');
            if (itemId) {
                openAddToCartModal(parseInt(itemId));
            }
        }
    });
});

/**
 * Initialize menu page
 */
async function initMenuPage() {
    // Check table number
    const tableNumber = getTableNumber();
    if (tableNumber) {
        document.getElementById('tableInfo').textContent = `Bàn số ${tableNumber}`;
    } else {
        document.getElementById('tableInfo').textContent = 'Chưa có thông tin bàn';
        showToast('Vui lòng quét mã QR trên bàn để bắt đầu', 'warning', 5000);
    }

    // Preserve table number in navigation links
    if (tableNumber) {
        updateNavigationLinks(tableNumber);
    }

    // Load data
    await loadCategories();
    await loadMenuItems();
}

/**
 * Update navigation links with table number
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
 * Load categories from API
 */
async function loadCategories() {
    try {
        allCategories = await api.getCategories();
        
        if (allCategories.length > 0) {
            renderCategoryTabs();
        }
    } catch (error) {
        console.error('Load categories error:', error);
    }
}

/**
 * Render category tabs
 */
function renderCategoryTabs() {
    const categoryTabs = document.getElementById('categoryTabs');
    
    // Keep "Tất cả" tab and add categories (skip "Tất cả" from database)
    allCategories.forEach(category => {
        // Skip if category name is "Tất cả" to avoid duplicate
        if (category.name === 'Tất cả') {
            return;
        }
        
        const tab = document.createElement('button');
        tab.className = 'category-tab';
        tab.setAttribute('data-category', category.id);
        tab.textContent = category.name;
        tab.onclick = () => filterByCategory(category.id);
        categoryTabs.appendChild(tab);
    });
}

/**
 * Load menu items from API
 */
async function loadMenuItems() {
    try {
        showLoading('Đang tải menu...');
        
        allMenuItems = await api.getMenuItems();
        
        hideLoading();
        
        if (allMenuItems.length === 0) {
            showEmptyState();
        } else {
            renderMenuItems(allMenuItems);
        }
    } catch (error) {
        hideLoading();
        console.error('Load menu error:', error);
        showEmptyState('Không thể tải menu. Vui lòng thử lại sau');
    }
}

/**
 * Render menu items
 */
function renderMenuItems(items) {
    const menuGrid = document.getElementById('menuGrid');
    menuGrid.innerHTML = '';

    if (items.length === 0) {
        showEmptyState('Không tìm thấy món ăn');
        return;
    }

    items.forEach(item => {
        const card = createMenuItemCard(item);
        menuGrid.appendChild(card);
    });
}

/**
 * Create menu item card element
 */
function createMenuItemCard(item) {
    const card = document.createElement('div');
    card.className = 'menu-item-card';
    
    if (!item.is_available) {
        card.classList.add('unavailable');
    }

    // Default image if not provided
    const imageUrl = item.image_url || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23f3f4f6" width="200" height="200"/%3E%3Ctext fill="%239ca3af" font-family="sans-serif" font-size="16" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3ENo image%3C/text%3E%3C/svg%3E';

    card.innerHTML = `
        <img src="${imageUrl}" alt="${item.name}" class="menu-item-image" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22%3E%3Crect fill=%22%23f3f4f6%22 width=%22200%22 height=%22200%22/%3E%3Ctext fill=%22%239ca3af%22 font-family=%22sans-serif%22 font-size=%2216%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ENo image%3C/text%3E%3C/svg%3E'">
        <div class="menu-item-content">
            <h3 class="menu-item-name">${item.name}</h3>
            ${item.description ? `<p class="menu-item-description">${item.description}</p>` : ''}
            <div class="menu-item-footer">
                <span class="menu-item-price">${formatCurrency(item.price)}</span>
                ${item.is_available ? `
                    <button class="add-to-cart-btn" data-item-id="${item.id}">
                        <span>+</span>
                        <span>Thêm</span>
                    </button>
                ` : ''}
            </div>
        </div>
    `;

    return card;
}

/**
 * Filter menu by category
 */
function filterByCategory(categoryId) {
    currentCategory = categoryId;

    // Update active tab
    const tabs = document.querySelectorAll('.category-tab');
    tabs.forEach(tab => {
        const tabCategory = tab.getAttribute('data-category');
        if (String(tabCategory) === String(categoryId)) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });

    // Filter items
    let filteredItems = allMenuItems;
    if (categoryId !== 'all') {
        filteredItems = allMenuItems.filter(item => String(item.category_id) === String(categoryId));
    }

    renderMenuItems(filteredItems);
}

/**
 * Open add to cart modal
 */
function openAddToCartModal(itemId) {
    const item = allMenuItems.find(i => i.id == itemId);
    if (!item || !item.is_available) return;

    currentModalItem = item;
    currentModalQuantity = 1;

    // Update modal content
    document.getElementById('modalItemName').textContent = item.name;
    document.getElementById('modalItemDescription').textContent = item.description || '';
    document.getElementById('modalItemPrice').textContent = formatCurrency(item.price);
    document.getElementById('modalQuantity').textContent = currentModalQuantity;

    const imageUrl = item.image_url || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23f3f4f6" width="200" height="200"/%3E%3Ctext fill="%239ca3af" font-family="sans-serif" font-size="16" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3ENo image%3C/text%3E%3C/svg%3E';
    
    const modalImage = document.getElementById('modalItemImage');
    modalImage.src = imageUrl;
    modalImage.alt = item.name;

    // Show modal
    const modal = document.getElementById('addToCartModal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
}

/**
 * Close add to cart modal
 */
function closeAddToCartModal() {
    const modal = document.getElementById('addToCartModal');
    modal.classList.add('hidden');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
    
    currentModalItem = null;
    currentModalQuantity = 1;
}

/**
 * Increase quantity in modal
 */
function increaseQuantity() {
    currentModalQuantity++;
    document.getElementById('modalQuantity').textContent = currentModalQuantity;
}

/**
 * Decrease quantity in modal
 */
function decreaseQuantity() {
    if (currentModalQuantity > 1) {
        currentModalQuantity--;
        document.getElementById('modalQuantity').textContent = currentModalQuantity;
    }
}

/**
 * Confirm add to cart
 */
function confirmAddToCart() {
    if (currentModalItem) {
        cartManager.addToCart(currentModalItem, currentModalQuantity);
        closeAddToCartModal();
    }
}

/**
 * Show empty state
 */
function showEmptyState(message = 'Chưa có món ăn nào') {
    const menuGrid = document.getElementById('menuGrid');
    menuGrid.innerHTML = `
        <div class="empty-state">
            <div class="empty-state-icon">🍽️</div>
            <h3 class="empty-state-title">${message}</h3>
        </div>
    `;
}

/**
 * Show search modal (placeholder for future enhancement)
 */
function showSearchModal() {
    showToast('Tính năng tìm kiếm đang được phát triển', 'info');
}
