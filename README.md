# 🍽️ Vườn Quê Restaurant - Order System

Hệ thống đặt món trực tuyến hiện đại dành cho nhà hàng **Vườn Quê**, được thiết kế để tối ưu hóa quy trình phục vụ từ khâu gọi món của khách hàng đến khâu chế biến của đầu bếp và quản lý của chủ nhà hàng.

---

## 🚀 Công Nghệ Sử Dụng

Dự án được xây dựng dựa trên kiến trúc phân tách rõ ràng giữa Backend và Frontend:

- **Backend:** PHP (Custom MVC Pattern), PDO for Database Security.
- **Frontend:** HTML5, CSS3 (Modern UI/UX), Vanilla JavaScript.
- **Real-time:** WebSockets (tích hợp cho thông báo đơn hàng mới).
- **Database:** MySQL (Hệ quản trị cơ sở dữ liệu quan hệ).

---

## 📌 Các Phân Hệ Chính

Hệ thống được chia thành 3 nền tảng chính:

### 👤 1. Khách Hàng (Customer Web)
*Giao diện tối ưu cho thiết bị di động (Mobile-first).*
- **Quét mã QR:** Tự động nhận diện số bàn.
- **Thực đơn số:** Xem danh sách món ăn theo danh mục với hình ảnh trực quan.
- **Giỏ hàng:** Tùy chỉnh số lượng, thêm ghi chú món ăn.
- **Theo dõi đơn hàng:** Xem trạng thái món ăn đang được chế biến hay đã hoàn thành.

### 🧑‍🍳 2. Nhân Viên (Staff Web)
*Công cụ quản lý vận hành tại quầy hoặc bếp.*
- **Quản lý đơn hàng:** Tiếp nhận yêu cầu từ khách hàng theo thời gian thực.
- **Điều phối bếp:** Cập nhật trạng thái món (Chờ xử lý → Đang làm → Hoàn thành).
- **Thanh toán:** Xác nhận hoàn tất đơn hàng và gửi yêu cầu thanh toán.

### 👨‍💼 3. Quản Lý (Admin Web)
*Trung tâm điều hành và thống kê.*
- **Quản lý thực đơn:** Thêm/Sửa/Xóa món ăn và danh mục.
- **Quản lý nhân sự:** Phân quyền và quản lý tài khoản nhân viên.
- **Thống kê doanh thu:** Biểu đồ báo cáo đơn hàng theo ngày/tháng/năm.
- **Quản lý hóa đơn:** Lưu trữ và truy xuất lịch sử giao dịch.

---

## 🏗️ Cấu Trúc Thư Mục

```text
VuonQueRestaurantOrder/
├── customer-web/   # Giao diện dành cho khách hàng
├── staff-web/      # Giao diện dành cho nhân viên phục vụ/bếp
├── admin-web/      # Giao diện quản lý dành cho admin
├── backend/        # API và logic xử lý hệ thống (PHP)
│   ├── src/
│   │   ├── controllers/ # Điều hướng logic
│   │   ├── models/      # Tương tác dữ liệu
│   │   ├── services/    # Logic nghiệp vụ
│   │   └── config/      # Cấu hình hệ thống
├── database/       # Chứa tệp SQL khởi tạo dữ liệu
└── README.md
```

---

## ⚙️ Hướng Dẫn Cài Đặt

### 1. Chuẩn bị môi trường
- Cài đặt **XAMPP** hoặc bất kỳ môi trường hỗ trợ PHP & MySQL.
- PHP version yêu cầu: >= 7.4.

### 2. Thiết lập Database
- Truy cập `phpMyAdmin`.
- Tạo database mới với tên: `db_vuonquerestaurant`.
- Nhập (Import) tệp tin `database/db_vuonquerestaurant.sql`.

### 3. Cấu hình Backend
- Mở tệp: `backend/src/config/database.php`.
- Điều chỉnh thông tin kết nối (host, username, password) phù hợp với môi trường của bạn.

### 4. Chạy ứng dụng
- Di chuyển thư mục dự án vào `htdocs`.
- Truy cập các giao diện qua trình duyệt:
    - Khách hàng: `http://localhost/VuonQueRestaurantOrder/customer-web/src/index.html?ban=01`
    - Nhân viên: `http://localhost/VuonQueRestaurantOrder/staff-web/src/pages/login.html`
    - Quản lý: `http://localhost/VuonQueRestaurantOrder/admin-web/src/pages/login.html`

---

## 👨‍🎓 Thông Tin Đồ Án

- **Môn học:** Xây dựng phần mềm hướng đối tượng.
- **Giảng viên hướng dẫn:** Vũ Đình Long.
- **Nhóm thực hiện:**  Văn Hảo.

---
*Dự án được phát triển với mục tiêu mang lại trải nghiệm ẩm thực hiện đại và chuyên nghiệp cho nhà hàng Vườn Quê.*
