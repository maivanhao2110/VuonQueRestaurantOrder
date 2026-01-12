/**
 * Checkout Logic
 * Handles listing active tables and processing aggregated payments
 */

let groupedTables = [];
let selectedTable = null;

document.addEventListener('DOMContentLoaded', () => {
    initProfile();
    loadActiveTables();
});

function initProfile() {
    const staffUser = JSON.parse(sessionStorage.getItem('staff_user') || '{}');
    const nameEl = document.getElementById('staffName');
    if (staffUser.full_name && nameEl) {
        nameEl.textContent = 'Xin chào, ' + staffUser.full_name;
    }
}

async function loadActiveTables() {
    const grid = document.getElementById('tablesGrid');
    const loading = document.getElementById('tablesLoading');
    const empty = document.getElementById('noTables');
    const billPreview = document.getElementById('billPreview');

    grid.innerHTML = '';
    loading.style.display = 'block';
    empty.style.display = 'none';
    billPreview.style.display = 'none';

    try {
        // Fetch all orders that are not PAID or CANCELLED
        const orders = await staffApi.getOrders();

        // Filter active orders
        const activeOrders = orders.filter(o => o.status !== 'PAID' && o.status !== 'CANCELLED');

        // Group orders by table number
        const tableGroups = activeOrders.reduce((acc, order) => {
            const tableNum = String(order.table_number).trim();
            if (!acc[tableNum]) {
                acc[tableNum] = {
                    table_number: tableNum,
                    orderIds: [],
                    items: [],
                    total_amount: 0,
                    status: 'DONE', // Default to DONE, will demote if any is not DONE
                    customer_name: order.customer_name
                };
            }
            acc[tableNum].orderIds.push(order.id);
            if (order.items) acc[tableNum].items.push(...order.items);
            acc[tableNum].total_amount += parseFloat(order.total_amount || 0);

            // Prioritize lowest status if mixed
            const statusOrder = ['CREATED', 'CONFIRMED', 'COOKING', 'DONE'];
            if (statusOrder.indexOf(order.status) < statusOrder.indexOf(acc[tableNum].status)) {
                acc[tableNum].status = order.status;
            }
            return acc;
        }, {});

        groupedTables = Object.values(tableGroups).sort((a, b) => a.table_number - b.table_number);

        if (groupedTables.length === 0) {
            loading.style.display = 'none';
            empty.style.display = 'block';
            return;
        }

        // Display tables
        loading.style.display = 'none';
        grid.innerHTML = groupedTables.map(table => `
            <div class="table-card" onclick="selectTable('${table.table_number}', this)">
                <span class="table-num">${table.table_number}</span>
                <span class="order-count">${table.items.length} món</span>
                <div style="margin-top: 10px;">
                    <span class="order-status status-${table.status.toLowerCase()}">${getStatusText(table.status)}</span>
                </div>
            </div>
        `).join('');

    } catch (error) {
        console.error('Error loading tables:', error);
        loading.innerHTML = '<p class="error">Không thể tải dữ liệu bàn</p>';
    }
}

async function selectTable(tableNumber, cardElement) {
    // UI selection state
    document.querySelectorAll('.table-card').forEach(c => c.classList.remove('active-selection'));
    cardElement.classList.add('active-selection');

    selectedTable = groupedTables.find(t => t.table_number == tableNumber);
    if (selectedTable) {
        renderBill(selectedTable);
    }
}

function renderBill(table) {
    const billItems = document.getElementById('billItems');
    const billTotal = document.getElementById('billTotalAmount');
    const billTitle = document.getElementById('billTitle');
    const billPreview = document.getElementById('billPreview');

    billTitle.textContent = `Hóa đơn Bàn ${table.table_number}`;

    // Check if combined order items are fully ready
    const allItemsDone = table.items.every(i => i.status === 'DONE');

    billItems.innerHTML = table.items.map(item => `
        <div class="bill-item">
            <div>
                <div style="font-weight:500;">${item.menu_item_name}</div>
                <div style="font-size: 0.8rem; color: #666;">Số lượng: ${item.quantity} x ${formatCurrency(item.price)}</div>
            </div>
            <div style="font-weight:600;">${formatCurrency(item.quantity * item.price)}</div>
        </div>
    `).join('');

    // Check if any order is still CREATED (unconfirmed)
    const hasUnconfirmed = table.orderIds.some(id => {
        // We can check the grouped table status or individual items if we had individual order statuses here
        // But table.status already reflects the "lowest" status (CREATED is lowest)
        return table.status === 'CREATED';
    });

    billTotal.textContent = formatCurrency(table.total_amount);
    billPreview.style.display = 'flex';

    // Update payment button state
    const payBtns = document.querySelectorAll('.payment-actions button');
    if (hasUnconfirmed) {
        payBtns.forEach(btn => {
            btn.classList.add('disabled');
            btn.setAttribute('title', 'Vui lòng xác nhận tất cả đơn hàng của bàn trước khi thanh toán');
            btn.style.opacity = '0.5';
            btn.style.cursor = 'not-allowed';
        });
    } else if (!allItemsDone) {
        payBtns.forEach(btn => {
            btn.classList.add('disabled');
            btn.setAttribute('title', 'Cảnh báo: Còn món chưa hoàn thành');
            btn.style.opacity = '0.7';
        });
    } else {
        payBtns.forEach(btn => {
            btn.classList.remove('disabled');
            btn.removeAttribute('title');
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
        });
    }

    // Scroll to bill
    billPreview.scrollIntoView({ behavior: 'smooth' });
}

function closeBill() {
    document.getElementById('billPreview').style.display = 'none';
    document.querySelectorAll('.table-card').forEach(c => c.classList.remove('active-selection'));
    selectedTable = null;
}

async function processPayment(method) {
    if (!selectedTable) return;

    // Block payment if any order is CREATED
    if (selectedTable.status === 'CREATED') {
        alert('Vui lòng xác nhận các món mới từ đơn hàng này trước khi thanh toán!');
        return;
    }

    // Check if all items are DONE before payment
    const allItemsDone = selectedTable.items.every(i => i.status === 'DONE');
    if (!allItemsDone) {
        if (!confirm('Bàn này vẫn còn món chưa hoàn thành (Chưa phục vụ). Bạn có chắc chắn muốn thanh toán không?')) {
            return;
        }
    }

    try {
        // Pay all orders for this table in parallel or sequence
        // We'll use sequence to avoid potential race conditions on session/auth if any
        for (const orderId of selectedTable.orderIds) {
            await staffApi.payOrder(orderId, method);
        }

        alert('Đã thanh toán thành công cho Bàn ' + selectedTable.table_number);
        loadActiveTables();
    } catch (error) {
        alert('Lỗi thanh toán: ' + error);
    }
}

function getStatusText(status) {
    const map = {
        'CREATED': 'Chờ xác nhận',
        'CONFIRMED': 'Đã xác nhận',
        'COOKING': 'Đang nấu',
        'DONE': 'Hoàn thành',
        'PAID': 'Đã thanh toán',
        'CANCELLED': 'Đã hủy'
    };
    return map[status] || status;
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        minimumFractionDigits: 0
    }).format(amount);
}
