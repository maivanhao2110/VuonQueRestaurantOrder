<?php
/**
 * Staff Service
 */

require_once __DIR__ . '/../repositories/StaffRepository.php';

class StaffService {
    private $staffRepo;

    public function __construct($db) {
        $this->staffRepo = new StaffRepository($db);
    }

    public function getStaffList($includeInactive = true) {
        return $this->staffRepo->getAll($includeInactive);
    }

    public function findByUsername($username) {
        return $this->staffRepo->findByUsername($username);
    }

    public function getStaff($id) {
        return $this->staffRepo->getById($id);
    }

    public function createStaff($fullName, $username, $passwordPlain, $position = 'STAFF', $cccd = null, $phone = null, $email = null, $address = null, $isActive = 1) {
        $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);
        return $this->staffRepo->create($fullName, $username, $passwordHash, $position, $cccd, $phone, $email, $address, $isActive);
    }

    public function updateStaff($id, $fullName, $username, $position, $passwordPlainOrNull, $cccd = null, $phone = null, $email = null, $address = null, $isActive = 1) {
        $passwordHashOrNull = null;
        if ($passwordPlainOrNull !== null && $passwordPlainOrNull !== '') {
            $passwordHashOrNull = password_hash($passwordPlainOrNull, PASSWORD_DEFAULT);
        }

        return $this->staffRepo->update($id, $fullName, $username, $position, $passwordHashOrNull, $cccd, $phone, $email, $address, $isActive);
    }

    public function deleteStaff($id) {
        return $this->staffRepo->delete($id);
    }

    public function toggleActive($id, $isActive) {
        return $this->staffRepo->setActive($id, $isActive);
    }

    public function authenticate($username, $password) {
        $staff = $this->staffRepo->findByUsername($username);
        if (!$staff) return ['success' => false, 'message' => 'Tài khoản không tồn tại'];
        if (!$staff['is_active']) return ['success' => false, 'message' => 'Tài khoản đã bị khóa'];
        if (!password_verify($password, $staff['password_hash'])) return ['success' => false, 'message' => 'Mật khẩu không đúng'];
        
        unset($staff['password_hash']);
        return ['success' => true, 'data' => $staff];
    }

    public function changePassword($staffId, $oldPassword, $newPassword) {
        $hash = $this->staffRepo->getPasswordHash($staffId);
        if (!$hash) throw new Exception('Không tìm thấy thông tin nhân viên');
        
        if (!password_verify($oldPassword, $hash)) throw new Exception('Mật khẩu cũ không chính xác');
        if ($oldPassword === $newPassword) throw new Exception('Mật khẩu mới không được trùng với mật khẩu cũ');

        return $this->staffRepo->updatePassword($staffId, password_hash($newPassword, PASSWORD_DEFAULT));
    }
}
