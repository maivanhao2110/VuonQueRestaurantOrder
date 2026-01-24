<?php
/**
 * Staff Controller
 * Handles staff-facing API endpoints (delegates to Services)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../services/OrderService.php';
require_once __DIR__ . '/../services/MenuService.php';
require_once __DIR__ . '/../services/StaffService.php';
require_once __DIR__ . '/../utils/Response.php';

class StaffController
{
    private $staffService;

    public function __construct($orderService, $menuService, $staffService, $db) // keeping db for manual queries if any remain (login legacy) - checking below
    {
        // $this->db = $db; // We are trying to remove direct DB usage.
        // login/password still used direct DB in previous step.
        // I should fix login to use StaffService fully if possible, but I left it raw.
        // Let's inject DB as well for the legacy methods I didn't refactor fully yet.
        $this->db = $db; 
        $this->orderService = $orderService;
        $this->menuService = $menuService;
        $this->staffService = $staffService;
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

    public function login()
    {
        try {
            $data = $this->getJsonBody();
            $username = trim($data['username'] ?? '');
            $password = $data['password'] ?? '';

            if ($username === '' || $password === '') {
                Response::error('Vui lòng nhập tài khoản và mật khẩu');
            }

            $result = $this->staffService->authenticate($username, $password);
            
            if ($result['success']) {
                Response::success('Đăng nhập thành công', $result['data']);
            } else {
                Response::error($result['message']);
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function changePassword()
    {
        try {
            $data = $this->getJsonBody();
            $staffId = $data['staff_id'] ?? null;
            $oldPassword = $data['old_password'] ?? '';
            $newPassword = $data['new_password'] ?? '';

            if (!$staffId || $oldPassword === '' || $newPassword === '') {
                Response::error('Thiếu thông tin thay đổi mật khẩu');
            }

            if ($this->staffService->changePassword($staffId, $oldPassword, $newPassword)) {
                Response::success('Đổi mật khẩu thành công');
            } else {
                Response::error('Không thể cập nhật mật khẩu');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function getMe()
    {
        Response::success('OK', ['message' => 'Auth check endpoint']);
    }

    // ==================== Order Management (Refactored) ====================

    public function getAllOrders()
    {
        try {
            $status = $_GET['status'] ?? null;
            $orders = $this->orderService->getAllOrders($status);
            Response::success('Lấy danh sách đơn hàng thành công', $orders);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function getOrderDetail($id)
    {
        try {
            $order = $this->orderService->getOrderWithItems($id); // method name mismatch? Service has getOrderWithItems, Controller had getOrderDetail calling... getOrderDetail??
            // Old Controller: $order = $this->orderService->getOrderDetail($id);
            // Service: public function getOrderWithItems($orderId)
            // Service also has... checked Service code.
            // Service has `getOrderWithItems($orderId)`.
            // Controller (old) called `getOrderDetail`.
            // So I should use `getOrderWithItems`.
            Response::success('Lấy chi tiết đơn hàng thành công', $order);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function confirmOrder($id)
    {
        try {
            $data = $this->getJsonBody();
            $staffId = $data['staff_id'] ?? null;
            
            if ($this->orderService->confirmOrder($id, $staffId)) {
                Response::success('Xác nhận đơn hàng thành công');
            } else {
                Response::error('Không thể xác nhận đơn hàng');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function payOrder($id)
    {
        try {
            $data = $this->getJsonBody();
            $typePayment = $data['type_payment'] ?? null;

            $invoiceId = $this->orderService->payOrder($id, $typePayment);
            Response::success('Thanh toán thành công. Hóa đơn #' . $invoiceId);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function cancelOrder($id)
    {
        try {
            if ($this->orderService->cancelOrder($id)) {
                Response::success('Đã hủy bàn thành công');
            } else {
                Response::error('Không thể hủy bàn');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function updateItemStatus($itemId)
    {
        try {
            $data = $this->getJsonBody();
            $newStatus = $data['status'] ?? null;

            if ($this->orderService->updateItemStatus($itemId, $newStatus)) {
                Response::success('Cập nhật trạng thái món thành công');
            } else {
                Response::error('Không thể cập nhật trạng thái món');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function addOrderItem($orderId)
    {
        try {
            $data = $this->getJsonBody();
            $menuItemId = $data['menu_item_id'] ?? null;
            $quantity = $data['quantity'] ?? 1;

            if ($this->orderService->addOrderItem($orderId, $menuItemId, $quantity)) {
                Response::success('Thêm món thành công');
            } else {
                Response::error('Không thể thêm món');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function updateOrderItemQuantity($itemId)
    {
        try {
            $data = $this->getJsonBody();
            $quantity = $data['quantity'] ?? null;

            if ($this->orderService->updateOrderItemQuantity($itemId, $quantity)) {
                Response::success('Cập nhật số lượng thành công');
            } else {
                Response::error('Không thể cập nhật');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function deleteOrderItem($itemId)
    {
        try {
            if ($this->orderService->deleteOrderItem($itemId)) {
                Response::success('Xóa món thành công');
            } else {
                Response::error('Không thể xóa món');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    // ==================== Menu CRUD (Refactored to use MenuService) ====================

    public function getCategories()
    {
        try {
            $categories = $this->menuService->getCategories();
            Response::success('Lấy danh mục thành công', $categories);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function getMenuItems()
    {
        try {
            $categoryId = $_GET['category_id'] ?? null;
            $items = $this->menuService->getMenuItemsAdmin($categoryId, true);
            Response::success('Lấy danh sách món ăn thành công', $items);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function getMenuItem($id)
    {
        try {
            $item = $this->menuService->getMenuItem($id);
            if (!$item) Response::error('Không tìm thấy món ăn');
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
            $imageUrl = $data['image_url'] ?? null;
            $description = $data['description'] ?? null;
            $isAvailable = isset($data['is_available']) ? (int) $data['is_available'] : 1;

            if ($categoryId <= 0) Response::error('Thiếu danh mục');
            if ($name === '') Response::error('Tên món không được để trống');
            if (!is_numeric($price) || (float) $price <= 0) Response::error('Giá không hợp lệ');

            $id = $this->menuService->createMenuItem($categoryId, $name, $price, $imageUrl, $description, $isAvailable);

            if ($id) {
                $item = $this->menuService->getMenuItem($id);
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
            $imageUrl = $data['image_url'] ?? null;
            $description = $data['description'] ?? null;
            $isAvailable = isset($data['is_available']) ? (int) $data['is_available'] : 1;

            if ($categoryId <= 0) Response::error('Thiếu danh mục');
            if ($name === '') Response::error('Tên món không được để trống');
            if (!is_numeric($price) || (float) $price <= 0) Response::error('Giá không hợp lệ');

            $success = $this->menuService->updateMenuItem($id, $categoryId, $name, $price, $imageUrl, $description, $isAvailable);

            if ($success) {
                $item = $this->menuService->getMenuItem($id);
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
            if ($this->menuService->deleteMenuItem($id)) {
                Response::success('Xóa món ăn thành công');
            } else {
                Response::error('Không thể xóa món ăn');
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }
}
?>