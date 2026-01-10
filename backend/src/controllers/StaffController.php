<?php
/**
 * Staff Controller
 * Handles staff-facing API endpoints (orders, item status, payments, menu)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';
require_once __DIR__ . '/../models/MenuItem.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../utils/Response.php';

class StaffController
{
    private $db;
    private $orderModel;
    private $orderItemModel;
    private $menuItemModel;
    private $categoryModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->orderModel = new Order($db);
        $this->orderItemModel = new OrderItem($db);
        $this->menuItemModel = new MenuItem($db);
        $this->categoryModel = new Category($db);
    }

    private function getJsonBody()
    {
        $raw = file_get_contents('php://input');
        if (!$raw)
            return [];
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    // ==================== Authentication ====================

    /**
     * Staff login
     */
    public function login()
    {
        try {
            $data = $this->getJsonBody();
            $username = trim($data['username'] ?? '');
            $password = $data['password'] ?? '';

            if ($username === '' || $password === '') {
                Response::error('Vui lòng nhập tài khoản và mật khẩu');
            }

            // Find staff by username
            $query = "SELECT id, full_name, username, password_hash, position, is_active 
                      FROM staff WHERE username = :username";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $staff = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$staff) {
                Response::error('Tài khoản không tồn tại');
            }

            if (!$staff['is_active']) {
                Response::error('Tài khoản đã bị khóa');
            }

            // Verify password
            if (!password_verify($password, $staff['password_hash'])) {
                Response::error('Mật khẩu không đúng');
            }

            // Remove sensitive data
            unset($staff['password_hash']);

            Response::success('Đăng nhập thành công', $staff);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    /**
     * Get current staff info
     */
    public function getMe()
    {
        // In a real app, you'd verify a token here
        Response::success('OK', ['message' => 'Auth check endpoint']);
    }

    // ==================== Order Management ====================

    /**
     * Get all orders with optional status filter
     */
    public function getAllOrders()
    {
        try {
            $status = $_GET['status'] ?? null;
            $orders = $this->orderModel->getAll($status);

            // Attach items to each order
            foreach ($orders as &$order) {
                $order['items'] = $this->orderItemModel->getByOrderId($order['id']);
                $order['total_amount'] = $this->calculateOrderTotal($order['items']);
                $order['all_items_done'] = $this->checkAllItemsDone($order['items']);
            }

            Response::success('Lấy danh sách đơn hàng thành công', $orders);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    /**
     * Get order detail by ID
     */
    public function getOrderDetail($id)
    {
        try {
            $order = $this->orderModel->getById($id);

            if (!$order) {
                Response::error('Không tìm thấy đơn hàng');
            }

            $order['items'] = $this->orderItemModel->getByOrderId($id);
            $order['total_amount'] = $this->calculateOrderTotal($order['items']);
            $order['all_items_done'] = $this->checkAllItemsDone($order['items']);

            Response::success('Lấy chi tiết đơn hàng thành công', $order);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    /**
     * Confirm order (CREATED -> CONFIRMED)
     */
    public function confirmOrder($id)
    {
        try {
            $order = $this->orderModel->getById($id);

            if (!$order) {
                Response::error('Không tìm thấy đơn hàng');
            }

            if ($order['status'] !== 'CREATED') {
                Response::error('Đơn hàng không ở trạng thái chờ xác nhận');
            }

            $success = $this->orderModel->updateStatus($id, 'CONFIRMED');

            if ($success) {
                Response::success('Xác nhận đơn hàng thành công');
            } else {
                Response::error('Không thể xác nhận đơn hàng');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    /**
     * Pay order (set status = PAID, end_at = NOW)
     */
    public function payOrder($id)
    {
        try {
            $order = $this->orderModel->getById($id);

            if (!$order) {
                Response::error('Không tìm thấy đơn hàng');
            }

            // Check all items are done
            $items = $this->orderItemModel->getByOrderId($id);
            if (!$this->checkAllItemsDone($items)) {
                Response::error('Chưa thể thanh toán - còn món chưa hoàn thành');
            }

            $success = $this->orderModel->payOrder($id);

            if ($success) {
                Response::success('Thanh toán thành công');
            } else {
                Response::error('Không thể thanh toán đơn hàng');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    // ==================== Order Item Status ====================

    /**
     * Update order item status
     */
    public function updateItemStatus($itemId)
    {
        try {
            $data = $this->getJsonBody();
            $newStatus = $data['status'] ?? null;

            if (!$newStatus || !in_array($newStatus, ['WAITING', 'COOKING', 'DONE'])) {
                Response::error('Trạng thái không hợp lệ');
            }

            $success = $this->orderItemModel->updateStatus($itemId, $newStatus);

            if ($success) {
                Response::success('Cập nhật trạng thái món thành công');
            } else {
                Response::error('Không thể cập nhật trạng thái món');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    // ==================== Menu CRUD ====================

    public function getCategories()
    {
        try {
            $categories = $this->categoryModel->getAll();
            Response::success('Lấy danh mục thành công', $categories);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function getMenuItems()
    {
        try {
            $categoryId = $_GET['category_id'] ?? null;
            $items = $this->menuItemModel->getAllAdmin($categoryId, true);
            Response::success('Lấy danh sách món ăn thành công', $items);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function getMenuItem($id)
    {
        try {
            $item = $this->menuItemModel->getById($id);
            if (!$item) {
                Response::error('Không tìm thấy món ăn');
            }
            Response::success('Lấy thông tin món ăn thành công', $item);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function createMenuItem()
    {
        try {
            $data = $this->getJsonBody();

            $categoryId = (int) ($data['category_id'] ?? 0);
            $name = trim($data['name'] ?? '');
            $price = $data['price'] ?? null;

            if ($categoryId <= 0)
                Response::error('Thiếu danh mục');
            if ($name === '')
                Response::error('Tên món không được để trống');
            if (!is_numeric($price) || (float) $price <= 0)
                Response::error('Giá không hợp lệ');

            $imageUrl = $data['image_url'] ?? null;
            $description = $data['description'] ?? null;
            $isAvailable = isset($data['is_available']) ? (int) $data['is_available'] : 1;

            $id = $this->menuItemModel->create($categoryId, $name, $price, $imageUrl, $description, $isAvailable);

            if ($id) {
                $item = $this->menuItemModel->getById($id);
                Response::success('Thêm món ăn thành công', $item);
            } else {
                Response::error('Không thể thêm món ăn');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function updateMenuItem($id)
    {
        try {
            $data = $this->getJsonBody();

            $categoryId = (int) ($data['category_id'] ?? 0);
            $name = trim($data['name'] ?? '');
            $price = $data['price'] ?? null;

            if ($categoryId <= 0)
                Response::error('Thiếu danh mục');
            if ($name === '')
                Response::error('Tên món không được để trống');
            if (!is_numeric($price) || (float) $price <= 0)
                Response::error('Giá không hợp lệ');

            $imageUrl = $data['image_url'] ?? null;
            $description = $data['description'] ?? null;
            $isAvailable = isset($data['is_available']) ? (int) $data['is_available'] : 1;

            $success = $this->menuItemModel->update($id, $categoryId, $name, $price, $imageUrl, $description, $isAvailable);

            if ($success) {
                $item = $this->menuItemModel->getById($id);
                Response::success('Cập nhật món ăn thành công', $item);
            } else {
                Response::error('Không thể cập nhật món ăn');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function deleteMenuItem($id)
    {
        try {
            // Soft delete
            $success = $this->menuItemModel->setAvailable($id, 0);

            if ($success) {
                Response::success('Xóa món ăn thành công');
            } else {
                Response::error('Không thể xóa món ăn');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    // ==================== Helper Methods ====================

    private function calculateOrderTotal($items)
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    private function checkAllItemsDone($items)
    {
        if (empty($items))
            return false;

        foreach ($items as $item) {
            if ($item['status'] !== 'DONE') {
                return false;
            }
        }
        return true;
    }
}
?>