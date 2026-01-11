/**
 * Utility Functions
 * Common helper functions for the application
 */

/**
 * Get table number from URL parameter
 * @returns {string|null} Table number or null
 */
function getTableNumber() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('ban');
}

/**
 * Format currency in Vietnamese Dong
 * @param {number} amount - Amount to format
 * @returns {string} Formatted currency string
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        minimumFractionDigits: 0
    }).format(amount);
}

/**
 * Format date time
 * @param {string} dateString - ISO date string
 * @returns {string} Formatted date time
 */
function formatDateTime(dateString) {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

/**
 * Show toast notification
 * @param {string} message - Message to display
 * @param {string} type - Toast type (success, error, warning, info)
 * @param {number} duration - Duration in ms (default: 3000)
 */
function showToast(message, type = 'info', duration = 3000) {
    // Create toast container if not exists
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);

    // Auto remove after duration
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

/**
 * Show loading overlay
 * @param {string} message - Loading message
 */
function showLoading(message = 'Đang xử lý...') {
    let overlay = document.querySelector('.loading-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div class="loading"></div>
            <p class="loading-text">${message}</p>
        `;
        document.body.appendChild(overlay);
    } else {
        overlay.classList.remove('hidden');
        overlay.querySelector('.loading-text').textContent = message;
    }
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    const overlay = document.querySelector('.loading-overlay');
    if (overlay) {
        overlay.classList.add('hidden');
    }
}

/**
 * Safe JSON parse with fallback
 * @param {string} str - JSON string
 * @param {any} fallback - Fallback value
 * @returns {any} Parsed JSON or fallback
 */
function safeJSONParse(str, fallback = null) {
    try {
        return JSON.parse(str);
    } catch (e) {
        return fallback;
    }
}

/**
 * Debounce function
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in ms
 * @returns {Function} Debounced function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Get status badge info
 * @param {string} status - Status code
 * @param {string} type - Type (order or item)
 * @returns {object} Badge info {text, color}
 */
function getStatusBadge(status, type = 'order') {
    const statusMap = {
        order: {
            'CREATED': { text: 'Đã tạo', color: 'info' },
            'CONFIRMED': { text: 'Đã xác nhận', color: 'warning' },
            'COOKING': { text: 'Cooking', color: 'info' },
            'DONE': { text: 'Hoàn thành', color: 'success' },
            'CANCELLED': { text: 'Đã hủy', color: 'error' }
        }
    };

    // Handle empty or unknown status
    if (!status || !statusMap[type][status]) {
        return { text: 'Đang xử lý', color: 'warning' };
    }

    return statusMap[type][status];
}

/**
 * Validate Vietnamese phone number
 * @param {string} phone - Phone number
 * @returns {boolean} Is valid
 */
function isValidPhone(phone) {
    const phoneRegex = /^(0|\+84)(3|5|7|8|9)[0-9]{8}$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

/**
 * Validate email
 * @param {string} email - Email address
 * @returns {boolean} Is valid
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}
