<?php
require_once 'database.php';
$db = new Database();
if($db->getConnection()) {
    echo "Kết nối thành công!";
}
?>