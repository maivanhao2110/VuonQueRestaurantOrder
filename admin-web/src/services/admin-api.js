const BASE_URL = '/VuonQueRestaurantOrder/backend/src/public/index.php/api/admin';

const adminApi = {
    // Categories
    getCategories: () => fetch(`${BASE_URL}/categories?_t=${Date.now()}`).then(r => r.json()).then(r => r.data),
    createCategory: (data) => fetch(`${BASE_URL}/categories`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(r => r.json()),
    updateCategory: (id, data) => fetch(`${BASE_URL}/categories/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(r => r.json()),
    deleteCategory: (id) => fetch(`${BASE_URL}/categories/${id}`, {
        method: 'DELETE'
    }).then(r => r.json()),
    toggleCategoryStatus: (id, isActive) => fetch(`${BASE_URL}/categories/${id}/status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ is_active: isActive })
    }).then(r => r.json()),

    // Menu Items
    getMenu: (id) => fetch(`${BASE_URL}/menu${id ? `/${id}` : ''}`).then(r => r.json()).then(r => r.data),
    createMenu: (data) => fetch(`${BASE_URL}/menu`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(r => r.json()),
    updateMenu: (id, data) => fetch(`${BASE_URL}/menu/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(r => r.json()),
    deleteMenu: (id) => fetch(`${BASE_URL}/menu/${id}`, {
        method: 'DELETE'
    }).then(r => r.json()),

    // Staff
    getStaff: (id) => fetch(`${BASE_URL}/staff${id ? `/${id}` : ''}`).then(r => r.json()).then(r => r.data),
    createStaff: (data) => fetch(`${BASE_URL}/staff`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(r => r.json()),
    updateStaff: (id, data) => fetch(`${BASE_URL}/staff/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(r => r.json()),
    deleteStaff: (id) => fetch(`${BASE_URL}/staff/${id}`, {
        method: 'DELETE'
    }).then(r => r.json()),
    toggleStaffStatus: (id, isActive) => fetch(`${BASE_URL}/staff/${id}/status`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ is_active: isActive })
    }).then(r => r.json()),

    // Invoices
    getInvoices: () => fetch(`${BASE_URL}/invoices`).then(r => r.json()).then(r => r.data),
    getInvoiceDetail: (id) => fetch(`${BASE_URL}/invoices/${id}`).then(r => r.json()).then(r => r.data),

    // Statistics
    getStatistics: () => fetch(`${BASE_URL}/statistics`).then(r => r.json()).then(r => r.data)
};
