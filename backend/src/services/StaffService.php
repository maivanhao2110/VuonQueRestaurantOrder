<?php
/**
 * Staff Service
 */

require_once __DIR__ . '/../models/Staff.php';

class StaffService {
    private $staffModel;

    public function __construct($db) {
        $this->staffModel = new Staff($db);
    }

    public function getStaffList($includeInactive = true) {
        return $this->staffModel->getAll($includeInactive);
    }

    public function getStaff($id) {
        return $this->staffModel->getById($id);
    }

    public function createStaff($fullName, $username, $passwordPlain, $cccd = null, $phone = null, $email = null, $address = null, $isActive = 1) {
        $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);
        return $this->staffModel->create($fullName, $username, $passwordHash, $cccd, $phone, $email, $address, $isActive);
    }

    public function updateStaff($id, $fullName, $username, $passwordPlainOrNull, $cccd = null, $phone = null, $email = null, $address = null, $isActive = 1) {
        $passwordHashOrNull = null;
        if ($passwordPlainOrNull !== null && $passwordPlainOrNull !== '') {
            $passwordHashOrNull = password_hash($passwordPlainOrNull, PASSWORD_DEFAULT);
        }

        return $this->staffModel->update($id, $fullName, $username, $passwordHashOrNull, $cccd, $phone, $email, $address, $isActive);
    }

    public function deleteStaff($id) {
        return $this->staffModel->setActive($id, 0);
    }
}

?>
