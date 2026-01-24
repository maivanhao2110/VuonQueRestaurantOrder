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
}
?>
