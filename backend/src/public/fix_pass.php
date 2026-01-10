<?php
// fix_pass.php
require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Mật khẩu muốn đặt lại
    $password = '123456';
    // Tạo hash chuẩn từ server của bạn
    $hash = password_hash($password, PASSWORD_DEFAULT);

    echo "<h1>Đang khôi phục mật khẩu...</h1>";
    echo "<p>Hash mới tạo: " . htmlspecialchars($hash) . "</p>";

    // Cập nhật cho nhanvien1 và quanly1
    $stmt = $db->prepare("UPDATE staff SET password_hash = :hash WHERE username IN ('nhanvien1', 'quanly1')");
    $stmt->bindParam(':hash', $hash);

    if ($stmt->execute()) {
        echo "<h2 style='color:green'>✅ Đã cập nhật thành công!</h2>";
        echo "<p>Tài khoản <b>nhanvien1</b> và <b>quanly1</b> đã được đổi mật khẩu thành: <b>123456</b></p>";
        echo "<a href='/VuonQueRestaurantOrder/staff-web/src/login.html'>👉 Quay lại trang đăng nhập</a>";
    } else {
        echo "<h2 style='color:red'>❌ Cập nhật thất bại.</h2>";
    }

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>