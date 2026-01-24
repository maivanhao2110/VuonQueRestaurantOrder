<?php
/**
 * Staff Entity
 */

class Staff {
    public $id;
    public $full_name;
    public $username;
    public $password_hash;
    public $position;
    public $is_active;
    public $cccd;
    public $phone;
    public $email;
    public $address;
    public $created_at;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->full_name = $data['full_name'] ?? null;
            $this->username = $data['username'] ?? null;
            $this->password_hash = $data['password_hash'] ?? null;
            $this->position = $data['position'] ?? null;
            $this->is_active = $data['is_active'] ?? null;
            $this->cccd = $data['cccd'] ?? null;
            $this->phone = $data['phone'] ?? null;
            $this->email = $data['email'] ?? null;
            $this->address = $data['address'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
        }
    }
}
?>
