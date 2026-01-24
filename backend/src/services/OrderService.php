<?php
/**
 * Order Service
 * Business logic for Order management
 */

require_once __DIR__ . '/../repositories/OrderRepository.php';
require_once __DIR__ . '/../repositories/OrderItemRepository.php';
require_once __DIR__ . '/../repositories/MenuItemRepository.php';
require_once __DIR__ . '/../repositories/InvoiceRepository.php';

class OrderService
{
    private $orderRepo;
    private $orderItemRepo;
    private $menuItemRepo;
    private $invoiceRepo;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->orderRepo = new OrderRepository($db);
        $this->orderItemRepo = new OrderItemRepository($db);
        $this->menuItemRepo = new MenuItemRepository($db);
        $this->invoiceRepo = new InvoiceRepository($db);
    }

    // ==================== Customer Logic ====================

    public function createOrder($customerName, $tableNumber, $items, $note = '')
    {
        try {
            $this->db->beginTransaction();

            // Check for existing active orders for this table
            $activeOrders = $this->orderRepo->getByTable($tableNumber);
            $existingOrder = null;

            foreach ($activeOrders as $o) {
                if ($o['status'] !== 'PAID' && $o['status'] !== 'CANCELLED') {
                    $existingOrder = $o;
                    break;
                }
            }

            if ($existingOrder) {
                $orderId = $existingOrder['id'];

                // Append note if present
                if (!empty($note)) {
                    $newNote = (!empty($existingOrder['note']) ? $existingOrder['note'] . " | " : "") . $note;
                    $query = "UPDATE orders SET note = :note WHERE id = :id";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':note', $newNote);
                    $stmt->bindParam(':id', $orderId);
                    $stmt->execute();
                }

                // If order was DONE or CONFIRMED, reset to COOKING because new items arrived
                if ($existingOrder['status'] === 'DONE' || $existingOrder['status'] === 'CONFIRMED') {
                    $this->orderRepo->updateStatus($orderId, 'COOKING');
                }
            } else {
                // Create new order
                $orderId = $this->orderRepo->create($customerName, $tableNumber, $note);
                if (!$orderId) {
                    throw new Exception('Không thể tạo đơn hàng');
                }
            }

            // Create order items
            $success = $this->orderItemRepo->createBatch($orderId, $items);

            if (!$success) {
                throw new Exception('Không thể thêm món vào đơn hàng');
            }

            $this->db->commit();

            return $this->getOrderWithItems($orderId);
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function getOrdersByTable($tableNumber, $status = null)
    {
        $orders = $this->orderRepo->getByTable($tableNumber, $status);
        foreach ($orders as &$order) {
            $order['items'] = $this->orderItemRepo->getByOrderId($order['id']);
        }
        return $orders;
    }

    public function getOrderWithItems($orderId)
    {
        $order = $this->orderRepo->getById($orderId);
        if ($order) {
            $order['items'] = $this->orderItemRepo->getByOrderId($orderId);
            // Calculate extras for Staff view consistency
            $order['total_amount'] = $this->calculateOrderTotal($order['items']);
            $order['all_items_done'] = $this->checkAllItemsDone($order['items']);
        }
        return $order;
    }

    // ==================== Staff Logic ====================

    public function getAllOrders($status = null) {
        $orders = $this->orderRepo->getAll($status);
        foreach ($orders as &$order) {
            $order['items'] = $this->orderItemRepo->getByOrderId($order['id']);
            $order['total_amount'] = $this->calculateOrderTotal($order['items']);
            $order['all_items_done'] = $this->checkAllItemsDone($order['items']);
        }
        return $orders;
    }

    public function confirmOrder($id, $staffId) {
        $order = $this->orderRepo->getById($id);
        if (!$order) {
            throw new Exception('Không tìm thấy đơn hàng');
        }
        if ($order['status'] !== 'CREATED') {
            throw new Exception('Đơn hàng không ở trạng thái chờ xác nhận');
        }
        return $this->orderRepo->updateStatus($id, 'CONFIRMED', $staffId);
    }

    public function payOrder($id, $typePayment) {
        if (!$typePayment || !in_array($typePayment, ['CAST', 'BANK'])) {
            throw new Exception('Vui lòng chọn loại thanh toán (Tiền mặt/Chuyển khoản)');
        }

        $order = $this->orderRepo->getById($id);
        if (!$order) {
            throw new Exception('Không tìm thấy đơn hàng');
        }

        $items = $this->orderItemRepo->getByOrderId($id);
        if (!$this->checkAllItemsDone($items)) {
            throw new Exception('Chưa thể thanh toán - còn món chưa hoàn thành (đang nấu/chờ)');
        }

        $totalAmount = $this->calculateOrderTotal($items);

        try {
            $this->db->beginTransaction();

            if (!$this->orderRepo->payOrder($id)) {
                throw new Exception('Không thể cập nhật trạng thái đơn hàng');
            }

            $invoiceId = $this->invoiceRepo->create($id, $totalAmount, $typePayment);
            if (!$invoiceId) {
                throw new Exception('Không thể tạo hóa đơn');
            }

            $this->db->commit();
            return $invoiceId;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function cancelOrder($id) {
        $order = $this->orderRepo->getById($id);
        if (!$order) throw new Exception('Không tìm thấy đơn hàng');

        if ($order['status'] == 'PAID' || $order['status'] == 'CANCELLED') {
            throw new Exception('Đơn hàng đã kết thúc, không thể hủy');
        }

        $items = $this->orderItemRepo->getByOrderId($id);
        foreach ($items as $item) {
            if ($item['status'] == 'COOKING' || $item['status'] == 'DONE' || $item['status'] == 'SERVED') {
                throw new Exception('Không thể hủy bàn vì có món đang nấu hoặc đã xong');
            }
        }

        return $this->orderRepo->cancel($id);
    }

    public function updateItemStatus($itemId, $newStatus) {
        if (!$newStatus || !in_array($newStatus, ['WAITING', 'COOKING', 'DONE', 'SERVED'])) {
            throw new Exception('Trạng thái không hợp lệ');
        }

        if ($this->orderItemRepo->updateStatus($itemId, $newStatus)) {
            // Auto update Order status if needed
            $this->syncOrderStatus($itemId);
            return true;
        }
        return false;
    }

    public function addOrderItem($orderId, $menuItemId, $quantity) {
        $quantity = (int)$quantity;
        if ($quantity < 1) throw new Exception('Số lượng không hợp lệ');

        $order = $this->orderRepo->getById($orderId);
        if (!$order) throw new Exception('Không tìm thấy đơn hàng');
        if ($order['status'] == 'PAID' || $order['status'] == 'CANCELLED') throw new Exception('Không thể sửa đơn hàng đã chốt');

        $menuItem = $this->menuItemRepo->getById($menuItemId);
        if (!$menuItem) throw new Exception('Món ăn không tồn tại');

        $id = $this->orderItemRepo->create($orderId, $menuItemId, $quantity, $menuItem['price']);
        
        if ($id) {
            if ($order['status'] === 'CONFIRMED') {
                $this->orderRepo->updateStatus($orderId, 'COOKING');
            }
            return $id;
        }
        return false;
    }

    public function updateOrderItemQuantity($itemId, $quantity) {
        $quantity = (int)$quantity;
        if ($quantity < 1) throw new Exception('Số lượng không hợp lệ');

        $item = $this->orderItemRepo->getById($itemId);
        if (!$item) throw new Exception('Món không tồn tại');

        $order = $this->orderRepo->getById($item['order_id']);
        if ($order['status'] == 'PAID' || $order['status'] == 'CANCELLED')
            throw new Exception('Không thể sửa đơn hàng đã chốt');

        return $this->orderItemRepo->updateQuantity($itemId, $quantity);
    }

    public function deleteOrderItem($itemId) {
        $item = $this->orderItemRepo->getById($itemId);
        if (!$item) throw new Exception('Món không tồn tại');

        $order = $this->orderRepo->getById($item['order_id']);
        if ($order['status'] == 'PAID' || $order['status'] == 'CANCELLED')
            throw new Exception('Không thể sửa đơn hàng đã chốt');

        if ($item['status'] != 'WAITING') {
            throw new Exception('Chỉ có thể xóa món đang chờ (WAITING)');
        }

        return $this->orderItemRepo->delete($itemId);
    }

    // ==================== Private Helpers ====================

    private function calculateOrderTotal($items) {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    private function checkAllItemsDone($items) {
        if (empty($items)) return false;
        foreach ($items as $item) {
            if ($item['status'] !== 'DONE' && $item['status'] !== 'SERVED') {
                return false;
            }
        }
        return true;
    }

    private function syncOrderStatus($itemId) {
        $item = $this->orderItemRepo->getById($itemId);
        if ($item) {
            $orderId = $item['order_id'];
            $items = $this->orderItemRepo->getByOrderId($orderId);
            
            $allServed = true;
            $allCookedOrServed = true;

            if (empty($items)) {
                $allServed = false;
                $allCookedOrServed = false;
            }

            foreach ($items as $i) {
                if ($i['status'] !== 'SERVED') $allServed = false;
                if ($i['status'] !== 'DONE' && $i['status'] !== 'SERVED') $allCookedOrServed = false;
            }

            if ($allServed) {
                $this->orderRepo->updateStatus($orderId, 'SERVED', null, false);
            } else if ($allCookedOrServed) {
                $this->orderRepo->updateStatus($orderId, 'DONE', null, false);
            }
        }
    }
}
?>