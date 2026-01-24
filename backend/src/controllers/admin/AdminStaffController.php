<?php
require_once __DIR__ . '/../../services/StaffService.php';
require_once __DIR__ . '/../../utils/Response.php';

class AdminStaffController {
    private $staffService;

    public function __construct($container) {
        $this->staffService = $container->get('staffService');
    }

    private function getJsonBody() {
        $raw = file_get_contents('php://input');
        if (!$raw) return [];
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

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
            $msg = $e->getMessage();
            // Basic error matching, could be improved
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
            $password = array_key_exists('password', $data) ? $data['password'] : null;

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
}
?>
