<?php
require_once __DIR__ . '/../repositories/StaffRepository.php';

class StaffService {
    public function __construct($db) {}
    public function authenticate($u, $p) { return ['success'=>false, 'message'=>'DEBUG MODE']; }
}
?>
