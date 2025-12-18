<?php
require 'config.php';

$success_msg = "";

// Xử lý khi người dùng bấm "Gửi Tin Nhắn"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ở đây bạn có thể thêm code gửi email thật hoặc lưu vào CSDL
    // Hiện tại mình sẽ hiển thị thông báo thành công giả lập
    $success_msg = "Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Liên Hệ - Kiet Ban Cafe</title>
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <?php require 'includes/header.php'; ?>
    
    <div class="contact-page-bg">
        <div class="contact-card">
            <h1 class="contact-title">Liên Hệ Với Chúng Tôi</h1>
            
            <?php if ($success_msg): ?>
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="fullname">Họ và Tên</label>
                    <input type="text" id="fullname" name="fullname" placeholder="Nhập tên của bạn" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="email@example.com" required>
                </div>

                <div class="form-group">
                    <label for="message">Tin Nhắn</label>
                    <textarea id="message" name="message" rows="5" placeholder="Nội dung tin nhắn..." required></textarea>
                </div>

                <button type="submit" class="btn-send-contact">Gửi Tin Nhắn</button>
            </form>
        </div>
    </div>
    
    <?php require 'includes/footer.php'; ?>
</body>
</html>