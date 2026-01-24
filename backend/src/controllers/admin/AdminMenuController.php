<?php
require_once __DIR__ . '/../../services/MenuService.php';
require_once __DIR__ . '/../../utils/Response.php';

class AdminMenuController {
    private $menuService;

    public function __construct($container) {
        $this->menuService = $container->get('menuService');
    }

    private function getJsonBody() {
        $raw = file_get_contents('php://input');
        if (!$raw) return [];
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    public function listMenuItems() {
        try {
            $categoryId = $_GET['category_id'] ?? null;
            $includeUnavailable = isset($_GET['include_unavailable']) ? (int)$_GET['include_unavailable'] === 1 : true;
            $items = $this->menuService->getMenuItemsAdmin($categoryId, $includeUnavailable);
            Response::success('Lấy danh sách món ăn thành công', $items);
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

    public function createMenuItem() {
        try {
            $data = $this->getJsonBody();
            $categoryId = (int)($data['category_id'] ?? 0);
            $name = trim($data['name'] ?? '');
            $price = $data['price'] ?? null;
            if ($categoryId <= 0) Response::error('Thiếu category_id');
            if ($name === '') Response::error('Tên món không được để trống');
            if (!is_numeric($price) || (float)$price <= 0) Response::error('Giá không hợp lệ');

            $imageUrl = $data['image_url'] ?? null;
            $description = $data['description'] ?? null;
            $isAvailable = isset($data['is_available']) ? (int)$data['is_available'] : 1;

            $id = $this->menuService->createMenuItem($categoryId, $name, $price, $imageUrl, $description, $isAvailable);
            if (!$id) {
                Response::error('Không thể tạo món ăn');
            }

            $created = $this->menuService->getMenuItem($id);
            Response::success('Tạo món ăn thành công', $created);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function updateMenuItem($id) {
        try {
            $data = $this->getJsonBody();
            $categoryId = (int)($data['category_id'] ?? 0);
            $name = trim($data['name'] ?? '');
            $price = $data['price'] ?? null;
            if ($categoryId <= 0) Response::error('Thiếu category_id');
            if ($name === '') Response::error('Tên món không được để trống');
            if (!is_numeric($price) || (float)$price <= 0) Response::error('Giá không hợp lệ');

            $imageUrl = $data['image_url'] ?? null;
            $description = $data['description'] ?? null;
            $isAvailable = isset($data['is_available']) ? (int)$data['is_available'] : 1;

            $ok = $this->menuService->updateMenuItem($id, $categoryId, $name, $price, $imageUrl, $description, $isAvailable);
            if (!$ok) {
                Response::error('Không thể cập nhật món ăn');
            }
            $updated = $this->menuService->getMenuItem($id);
            Response::success('Cập nhật món ăn thành công', $updated);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function deleteMenuItem($id) {
        try {
            $ok = $this->menuService->deleteMenuItem($id);
            if (!$ok) {
                Response::error('Không thể xóa món ăn');
            }
            Response::success('Xóa món ăn thành công');
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }
}
?>
