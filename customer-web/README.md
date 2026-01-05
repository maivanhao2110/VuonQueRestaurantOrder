# VuonQueRestaurant - Customer Web

Hệ thống đặt món trực tuyến cho nhà hàng Vườn Quê.

## 🚀 Hướng dẫn cài đặt

### Yêu cầu
- XAMPP (hoặc Apache + PHP + MySQL)
- PHP 7.4+
- MySQL 5.7+

### Cài đặt

1. **Copy project vào htdocs:**
   ```
   C:\xampp\htdocs\VuonQueRestaurantOrder\
   ```

2. **Tạo database:**
   - Mở phpMyAdmin: `http://localhost/phpmyadmin`
   - Tạo database mới: `db_vuonquerestaurant`
   - Import file: `database/db_vuonquerestaurant.sql`

3. **Cấu hình database (nếu cần):**
   - Mở file: `backend/src/config/database.php`
   - Cập nhật thông tin kết nối

4. **Khởi động XAMPP:**
   - Start Apache
   - Start MySQL

5. **Truy cập ứng dụng:**
   ```
   http://localhost/VuonQueRestaurantOrder/customer-web/src/index.html?ban=1
   ```

## 📱 Tính năng

### Customer Web
- ✅ Xem menu theo danh mục
- ✅ Thêm món vào giỏ hàng
- ✅ Đặt món với ghi chú
- ✅ Theo dõi trạng thái đơn hàng
- ✅ QR Code support (thông qua URL parameter `?ban=X`)

## 🔗 API Endpoints

### Menu
- `GET /api/customer/categories` - Danh sách danh mục
- `GET /api/customer/menu` - Danh sách món ăn
- `GET /api/customer/menu/:id` - Chi tiết món ăn

### Orders
- `POST /api/customer/orders` - Tạo đơn hàng
- `GET /api/customer/orders?table_number=X` - Lấy đơn hàng theo bàn
- `GET /api/customer/orders/:id` - Chi tiết đơn hàng

## 📂 Cấu trúc project

```
VuonQueRestaurantOrder/
├── customer-web/
│   └── src/
│       ├── assets/
│       │   ├── css/
│       │   └── js/
│       ├── config/
│       ├── pages/
│       └── services/
├── backend/
│   └── src/
│       ├── config/
│       ├── models/
│       ├── services/
│       ├── controllers/
│       ├── routes/
│       └── public/
└── database/
    └── db_vuonquerestaurant.sql
```

## 🛠️ Công nghệ sử dụng

- **Frontend:** HTML, CSS, Vanilla JavaScript
- **Backend:** PHP 
- **Database:** MySQL
- **Architecture:** MVC pattern

## 📝 Ghi chú

- Table number được truyền qua URL parameter: `?ban=1`, `?ban=2`, etc.
- QR code trên mỗi bàn sẽ chứa URL với table number tương ứng
- Giỏ hàng được lưu trong LocalStorage
- Auto-refresh đơn hàng mỗi 10 giây

## 👥 Phát triển

Developed by: Dai Phat
Project: Restaurant Order Management System
