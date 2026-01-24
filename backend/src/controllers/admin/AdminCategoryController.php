<?php
require_once __DIR__ . '/../../services/MenuService.php';
require_once __DIR__ . '/../../utils/Response.php';

class AdminCategoryController {
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

    public function listCategories() {
        try {
            $includeInactive = isset($_GET['include_inactive']) ? (int)$_GET['include_inactive'] === 1 : true;
            $items = $this->menuService->getCategoriesAdmin($includeInactive);
            Response::success('Lấy danh mục thành công', $items);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function createCategory() {
        try {
            $data = $this->getJsonBody();
            $name = trim($data['name'] ?? '');
            if ($name === '') {
                Response::error('Tên danh mục không được để trống');
            }
            $description = $data['description'] ?? null;
            $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

            $id = $this->menuService->createCategory($name, $description, $isActive);
            if (!$id) {
                Response::error('Không thể tạo danh mục');
            }
            $created = $this->menuService->getCategory($id);
            Response::success('Tạo danh mục thành công', $created);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function updateCategory($id) {
        try {
            $data = $this->getJsonBody();
            $name = trim($data['name'] ?? '');
            if ($name === '') {
                Response::error('Tên danh mục không được để trống');
            }
            $description = $data['description'] ?? null;
            $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

            $ok = $this->menuService->updateCategory($id, $name, $description, $isActive);
            if (!$ok) {
                Response::error('Không thể cập nhật danh mục');
            }
            $updated = $this->menuService->getCategory($id);
            Response::success('Cập nhật danh mục thành công', $updated);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function deleteCategory($id) {
        try {
            $ok = $this->menuService->deleteCategory($id);
            if (!$ok) {
                Response::error('Không thể xóa danh mục (có thể đang chứa món ăn?)');
            }
            Response::success('Xóa danh mục thành công');
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function toggleCategoryStatus($id) {
        try {
            $data = $this->getJsonBody();
            $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 0;
            
            $ok = $this->menuService->toggleCategoryStatus($id, $isActive);
            if (!$ok) Response::error('Không thể thay đổi trạng thái');
            
            $status = $isActive ? 'Mở khóa' : 'Khóa';
            Response::success("$status danh mục thành công");
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }
}
?>
