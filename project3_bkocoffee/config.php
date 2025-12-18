<?php
// Bắt đầu Session: Rất quan trọng để quản lý trạng thái đăng nhập và giỏ hàng
session_start();

// ------------------------------------------
// 1. THÔNG TIN KẾT NỐI CƠ SỞ DỮ LIỆU (MYSQL/MARIADB)
// ------------------------------------------
$host = 'localhost';
$db   = 'coffeeshop_db'; // <-- Đảm bảo tên database này khớp với tên bạn đã tạo trong phpMyAdmin
$user = 'root';          // <-- THAY ĐỔI nếu user của bạn không phải là 'root'
$pass = '';              // <-- THAY ĐỔI nếu bạn có đặt mật khẩu cho user MySQL/MariaDB
$charset = 'utf8mb4';    // Bộ mã hóa tốt nhất cho tiếng Việt

// Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Tùy chọn PDO: Rất quan trọng cho bảo mật (chống SQL Injection) và xử lý dữ liệu
$options = [
    // Báo lỗi dưới dạng Exceptions (giúp xử lý lỗi dễ dàng hơn)
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    // Lấy dữ liệu dưới dạng mảng kết hợp (key là tên cột)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
    // Tắt chế độ mô phỏng Prepared Statements (quan trọng cho bảo mật)
    PDO::ATTR_EMULATE_PREPARES   => false,                  
];

// ------------------------------------------
// 2. THIẾT LẬP KẾT NỐI (PDO)
// ------------------------------------------
try {
     // Khởi tạo đối tượng PDO
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Xử lý lỗi kết nối
     // Lỗi 1045: Access denied (Sai user/pass)
     // Lỗi 1049: Unknown database (Sai tên database)
     die("Lỗi kết nối CSDL (MySQL): " . $e->getMessage());
}

// ------------------------------------------
// 3. HÀM CHUNG
// ------------------------------------------
// Hàm chuyển hướng trang web
function redirect($url) {
    header("Location: $url");
    exit;
}
?>