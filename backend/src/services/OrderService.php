<?php
/**
 * Order Service
 */

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';

class OrderService {
    private $orderModel;
    private $orderItemModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->orderModel = new Order($db);
        $this->orderItemModel = new OrderItem($db);
    }

    public function createOrder($customerName, $tableNumber, $items, $note = '') {
        try {
            $this->db->beginTransaction();
            
            // Create order
            $orderId = $this->orderModel->create($customerName, $tableNumber, $note);
            
            if (!$orderId) {
                throw new Exception('Không thể tạo đơn hàng');
            }
            
            // Create order items
            $success = $this->orderItemModel->createBatch($orderId, $items);
            
            if (!$success) {
                throw new Exception('Không thể thêm món vào đơn hàng');
            }
            
            $this->db->commit();
            
            return $this->getOrderWithItems($orderId);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getOrdersByTable($tableNumber, $status = null) {
        $orders = $this->orderModel->getByTable($tableNumber, $status);
        
        // Attach items to each order
        foreach ($orders as &$order) {
            $order['items'] = $this->orderItemModel->getByOrderId($order['id']);
        }
        
        return $orders;
    }

    public function getOrderWithItems($orderId) {
        $order = $this->orderModel->getById($orderId);
        
        if ($order) {
            $order['items'] = $this->orderItemModel->getByOrderId($orderId);
        }
        
        return $order;
    }
}
?>
