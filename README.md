# 🍽️ Restaurant Order System

Hệ thống đặt món cho nhà hàng sử dụng **HTML, JavaScript, PHP và MySQL**.  
Hệ thống được chia thành 3 website độc lập, không cần phân quyền phức tạp.

---

## 📌 Chức năng chính

### 👤 Khách hàng (Customer Web)

- Xem menu
- Nhập tên khách hàng
- Chọn món và số lượng
- Gửi yêu cầu đặt món

---

### 🧑‍🍳 Nhân viên (Staff Web)

- Đăng nhập bằng tài khoản nhân viên
- Xác nhận đơn hàng
- Cập nhật trạng thái món (chờ làm / đang làm / hoàn thành)

---

### 👨‍💼 Quản lý (Admin Web)

- Quản lý menu (thêm / sửa / xóa)
- Quản lý nhân viên
- Xem thống kê đơn hàng
- Xem hóa đơn và thanh toán

---

## 🗂️ Cấu trúc thư mục

restaurant-order-system/
│
├── customer-web/ # Giao diện khách hàng
├── staff-web/ # Giao diện nhân viên
├── admin-web/ # Giao diện quản lý
│
├── backend/ # Xử lý PHP
│ └── config/
│ └── db.php # Kết nối database
│
├── database/
│ └── restaurant_order.sql # File database
│
├── README.md
└── .gitignore

---

## 🗄️ Database

### 🔹 Công nghệ

- MySQL
- Quản lý bằng phpMyAdmin

### 🔹 Các bảng chính

- `category`
- `menu_item`
- `staff`
- `orders`
- `order_item`
- `order_status_log`
- `invoice`
- `payment`

---

## ⚙️ Hướng dẫn cài đặt Database

### 1️⃣ Tạo database

```sql
CREATE DATABASE restaurant_order CHARACTER SET utf8mb4;
2️⃣ Import database
mysql -u root -p restaurant_order < database/restaurant_order.sql

3️⃣ Cấu hình kết nối database

Mở file:

backend/config/db.php


Sửa lại:

$host = "localhost";
$user = "root";
$password = "";
$dbname = "db_vuonquerestaurant";


(Mật khẩu trong database được mã hóa)

🚀 Ghi chú

Dự án sử dụng file SQL thay vì dữ liệu runtime

Không lưu mật khẩu dạng plain text

Dễ mở rộng cho các hệ thống lớn hơn

👨‍🎓 Thông tin đồ án

Môn học: Xây dựng phần mềm hướng đối tượng

Nhóm: Văn Hảo

GVHD: Vũ Đình Long
```
