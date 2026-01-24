<?php
/**
 * Admin Controller
 * Handles admin-facing API endpoints (menu/staff/statistics/invoices)
 */

require_once __DIR__ . '/../services/MenuService.php';
require_once __DIR__ . '/../services/StaffService.php';
require_once __DIR__ . '/../services/StatictisService.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../utils/Response.php';

class AdminController {
	private $menuService;
	private $staffService;
	private $statictisService;
	private $invoiceModel;

	public function __construct($db) {
		$this->menuService = new MenuService($db);
		$this->staffService = new StaffService($db);
		$this->statictisService = new StatictisService($db);
		$this->invoiceModel = new Invoice($db);
	}

	private function getJsonBody() {
		$raw = file_get_contents('php://input');
		if (!$raw) return [];
		$data = json_decode($raw, true);
		return is_array($data) ? $data : [];
	}

	// ==================== Category (Admin) ====================
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
			// Debug logging
			file_put_contents(__DIR__ . '/../../debug_admin_cat.log', date('Y-m-d H:i:s') . " - Toggle Cat $id: " . json_encode($data) . "\n", FILE_APPEND);
			
			$isActive = isset($data['is_active']) ? (int)$data['is_active'] : 0;
			
			$ok = $this->menuService->toggleCategoryStatus($id, $isActive);
			if (!$ok) Response::error('Không thể thay đổi trạng thái');
			
			$status = $isActive ? 'Mở khóa' : 'Khóa';
			Response::success("$status danh mục thành công");
		} catch (Exception $e) {
            file_put_contents(__DIR__ . '/../../debug_admin_cat_error.log', $e->getMessage() . "\n", FILE_APPEND);
			Response::error($e->getMessage());
		}
	}

	// ==================== MenuItem (Admin) ====================
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

	// ==================== Staff (Admin) ====================
	public function listStaff() {
			try {
				$includeInactive = isset($_GET['include_inactive']) ? (int)$_GET['include_inactive'] === 1 : true;
				$items = $this->staffService->getStaffList($includeInactive);
				Response::success('Lấy danh sách nhân viên thành công', $items);
			} catch (Exception $e) {
				Response::error($e->getMessage());
			}
		}

		public function getStaff($id) {
			try {
				$item = $this->staffService->getStaff($id);
				if (!$item) {
					Response::error('Không tìm thấy nhân viên');
				}
				Response::success('Lấy thông tin nhân viên thành công', $item);
			} catch (Exception $e) {
				Response::error($e->getMessage());
			}
		}

		public function createStaff() {
			try {
				$data = $this->getJsonBody();
				$fullName = trim($data['full_name'] ?? '');
				$username = trim($data['username'] ?? '');
				$password = $data['password'] ?? '';

				if ($fullName === '') Response::error('Họ tên không được để trống');
				if ($username === '') Response::error('Username không được để trống');
				if ($password === '') Response::error('Password không được để trống');
				
				$position = $data['position'] ?? 'STAFF';
				$cccd = $data['cccd'] ?? null;
				$phone = $data['phone'] ?? null;
				$email = $data['email'] ?? null;
				$address = $data['address'] ?? null;
				$isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

				$id = $this->staffService->createStaff($fullName, $username, $password, $position, $cccd, $phone, $email, $address, $isActive);
				if (!$id) Response::error('Không thể tạo nhân viên');

				$created = $this->staffService->getStaff($id);
				Response::success('Tạo nhân viên thành công', $created);
			} catch (PDOException $e) {
				// Handle common unique constraint issues
				$msg = $e->getMessage();
				if (strpos($msg, 'username') !== false) Response::error('Username đã tồn tại');
				if (strpos($msg, 'cccd') !== false) Response::error('CCCD đã tồn tại');
				if (strpos($msg, 'email') !== false) Response::error('Email đã tồn tại');
				Response::error('Lỗi database khi tạo nhân viên');
			} catch (Exception $e) {
				Response::error($e->getMessage());
			}
		}

		public function updateStaff($id) {
			try {
				$data = $this->getJsonBody();
				$fullName = trim($data['full_name'] ?? '');
				$username = trim($data['username'] ?? '');
				$password = array_key_exists('password', $data) ? $data['password'] : null; // optional

				if ($fullName === '') Response::error('Họ tên không được để trống');
				if ($username === '') Response::error('Username không được để trống');
				
				$position = $data['position'] ?? 'STAFF';
				$cccd = $data['cccd'] ?? null;
				$phone = $data['phone'] ?? null;
				$email = $data['email'] ?? null;
				$address = $data['address'] ?? null;
				$isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

				$ok = $this->staffService->updateStaff($id, $fullName, $username, $position, $password, $cccd, $phone, $email, $address, $isActive);
				if (!$ok) Response::error('Không thể cập nhật nhân viên');

				$updated = $this->staffService->getStaff($id);
				Response::success('Cập nhật nhân viên thành công', $updated);
			} catch (PDOException $e) {
				$msg = $e->getMessage();
				if (strpos($msg, 'username') !== false) Response::error('Username đã tồn tại');
				if (strpos($msg, 'cccd') !== false) Response::error('CCCD đã tồn tại');
				if (strpos($msg, 'email') !== false) Response::error('Email đã tồn tại');
				Response::error('Lỗi database khi cập nhật nhân viên');
			} catch (Exception $e) {
				Response::error($e->getMessage());
			}
		}
		public function deleteStaff($id) {
			try {
				$ok = $this->staffService->deleteStaff($id);
				if (!$ok) Response::error('Không thể xóa nhân viên');
				Response::success('Xóa nhân viên thành công');
			} catch (Exception $e) {
				Response::error($e->getMessage());
			}
		}
		
		public function toggleStaffStatus($id) {
			try {
				$data = $this->getJsonBody();
				$isActive = isset($data['is_active']) ? (int)$data['is_active'] : 0;
				
				$ok = $this->staffService->toggleActive($id, $isActive);
				if (!$ok) Response::error('Không thể thay đổi trạng thái');
				
				$status = $isActive ? 'Mở khóa' : 'Khóa';
				Response::success("$status nhân viên thành công");
			} catch (Exception $e) {
				Response::error($e->getMessage());
			}
		}
	
		// ==================== Statistics (Admin) ====================
		public function getStatistics() {
			try {
				$from = $_GET['from'] ?? null;
				$to = $_GET['to'] ?? null;
				$data = $this->statictisService->getOverview($from, $to);
				Response::success('Lấy thống kê thành công', $data);
			} catch (Exception $e) {
				Response::error($e->getMessage());
			}
		}

		// ==================== Invoices (Admin) ====================
		public function listInvoices() {
			try {
				$data = $this->invoiceModel->getAll();
				Response::success('Lấy danh sách hóa đơn thành công', $data);
			} catch (Exception $e) {
				Response::error($e->getMessage());
			}
		}

		public function getInvoice($id) {
			try {
				$data = $this->invoiceModel->getById($id);
				if (!$data) Response::error('Không tìm thấy hóa đơn');
				Response::success('Lấy chi tiết hóa đơn thành công', $data);
			} catch (Exception $e) {
				Response::error($e->getMessage());
			}
		}
}

?>
