<?php
/**
 * Order Service
 */

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';

class OrderService
{
    private $orderModel;
    private $orderItemModel;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->orderModel = new Order($db);
        $this->orderItemModel = new OrderItem($db);
    }

    public function createOrder($customerName, $tableNumber, $items, $note = '')
    {
        try {
            $this->db->beginTransaction();

            // Check for existing active orders for this table
            $activeOrders = $this->orderModel->getByTable($tableNumber);
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
                    $this->orderModel->updateStatus($orderId, 'COOKING');
                }
            } else {
                // Create new order
                $orderId = $this->orderModel->create($customerName, $tableNumber, $note);
                if (!$orderId) {
                    throw new Exception('Không thể tạo đơn hàng');
                }
            }

            // Create order items
            $success = $this->orderItemModel->createBatch($orderId, $items);

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
        $orders = $this->orderModel->getByTable($tableNumber, $status);

        // Attach items to each order
        foreach ($orders as &$order) {
            $order['items'] = $this->orderItemModel->getByOrderId($order['id']);
        }

        return $orders;
    }

    public function getOrderWithItems($orderId)
    {
        $order = $this->orderModel->getById($orderId);

        if ($order) {
            $order['items'] = $this->orderItemModel->getByOrderId($orderId);
        }

        return $order;
    }
}
?>