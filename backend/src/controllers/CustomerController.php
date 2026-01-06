<?php
/**
 * Customer Controller
 * Handles customer-facing API endpoints
 */

require_once __DIR__ . '/../services/MenuService.php';
require_once __DIR__ . '/../services/OrderService.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';

class CustomerController {
    private $menuService;
    private $orderService;

    public function __construct($db) {
        $this->menuService = new MenuService($db);
        $this->orderService = new OrderService($db);
    }

    // ==================== Menu Endpoints ====================
    
    public function getCategories() {
        try {
            $categories = $this->menuService->getCategories();
            Response::success('Lấy danh mục thành công', $categories);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function getMenu() {
        try {
            $categoryId = $_GET['category_id'] ?? null;
            $menuItems = $this->menuService->getMenuItems($categoryId);
            Response::success('Lấy menu thành công', $menuItems);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function getMenuItem($id) {
        try {
            $item = $this->menuService->getMenuItem($id);
            
            if (!$item) {
                Response::error('Không tìm thấy món ăn');
            }
            
            Response::success('Lấy thông tin món ăn thành công', $item);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    // ==================== Order Endpoints ====================
    
    public function createOrder() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate
            if (!isset($data['table_number']) || !isset($data['items'])) {
                Response::error('Thiếu thông tin bắt buộc');
            }
            
            if (empty($data['items'])) {
                Response::error('Đơn hàng phải có ít nhất 1 món');
            }
            
            $customerName = $data['customer_name'] ?? 'Khách';
            $tableNumber = $data['table_number'];
            $items = $data['items'];
            $note = $data['note'] ?? '';
            
            $order = $this->orderService->createOrder($customerName, $tableNumber, $items, $note);
            
            Response::success('Đặt món thành công', $order);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function getOrders() {
        try {
            $tableNumber = $_GET['table_number'] ?? null;
            $status = $_GET['status'] ?? null;
            
            if (!$tableNumber) {
                Response::error('Thiếu số bàn');
            }
            
            $orders = $this->orderService->getOrdersByTable($tableNumber, $status);
            Response::success('Lấy danh sách đơn hàng thành công', $orders);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function getOrderDetail($id) {
        try {
            $order = $this->orderService->getOrderWithItems($id);
            
            if (!$order) {
                Response::error('Không tìm thấy đơn hàng');
            }
            
            Response::success('Lấy chi tiết đơn hàng thành công', $order);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }
}
?>
