<?php 
require 'config.php'; 

// Lấy thông tin cơ bản để chào khách (Tên và ID đơn hàng nếu có)
$customer_name = $_SESSION['user_name'] ?? 'bạn';
$order_id = $_GET['order_id'] ?? 'vừa rồi'; 
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt Hàng Thành Công</title>
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require 'includes/header.php'; ?>

    <div class="container thank-you-page">
        
        <div class="success-icon-box">
            <i class="fas fa-check"></i>
        </div>
        
        <h1 class="success-title">ĐẶT HÀNG THÀNH CÔNG!</h1>
        
        <div class="success-message">
            <p>Đơn hàng **#<?php echo htmlspecialchars($order_id); ?>** đã được hệ thống tiếp nhận.</p>
            <p>Cảm ơn <strong><?php echo htmlspecialchars($customer_name); ?></strong> đã lựa chọn Kiet Ban Cafe.</p>
            <p>Chúng tôi sẽ liên hệ sớm nhất để xác nhận và tiến hành giao hàng.</p>
        </div>
        
        <div class="success-actions">
            <a href="menu.php" class="btn-action btn-menu">
                <i class="fas fa-mug-hot"></i> Tiếp tục mua sắm
            </a>
             <a href="index.php" class="btn-action btn-home">
                <i class="fas fa-home"></i> Về Trang Chủ
            </a>
        </div>
    </div>

    <?php require 'includes/footer.php'; ?>
</body>
</html>