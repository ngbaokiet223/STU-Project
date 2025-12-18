<?php
require 'config.php';

// 1. Lấy ID sản phẩm từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;

// 2. Truy vấn thông tin sản phẩm
if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// 3. Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = (int)($_POST['quantity'] ?? 1);
    if ($quantity < 1) $quantity = 1;

    if ($product) {
        if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
        
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $quantity;
        } else {
            $_SESSION['cart'][$id] = $quantity;
        }
        
        // --- CHỖ SỬA QUAN TRỌNG NHẤT ---
        
        // 1. ĐẶT CỜ BÁO THÀNH CÔNG VÀO SESSION
        $_SESSION['toast_status'] = 'success';
        
        // 2. Chuyển hướng về chính trang đó (không dùng tham số URL nữa)
        // basename($_SERVER['PHP_SELF']) sẽ trả về tên file hiện tại (ví dụ: sanpham.php)
        redirect(basename($_SERVER['PHP_SELF']) . "?id=$id"); 
    }
}
// Lưu ý: Đảm bảo hàm redirect đã được định nghĩa trong config.php
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $product ? htmlspecialchars($product['name']) : 'Sản phẩm không tồn tại'; ?> - Coffee Shop</title>
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <?php require 'includes/header.php'; ?>

    <div id="cartToast" class="toast-notification">
        <i class="fas fa-check-circle" style="margin-right: 8px;"></i> ĐÃ THÊM VÀO GIỎ HÀNG THÀNH CÔNG!
    </div>
    
    <div class="container">
        <?php if ($product): ?>
            <div class="product-detail-wrapper">
                
                <div class="pd-image-col">
                    <?php if (!empty($product['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <div class="action-box" style="height: 400px; font-size: 2em;">NO IMAGE</div>
                    <?php endif; ?>
                </div>
                
                <div class="pd-info-col">
                    <h1 class="pd-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                    
                    <p class="pd-price"><?php echo number_format($product['price']); ?> VNĐ</p>
                    
                    <div class="pd-description">
                        <h3>Mô tả sản phẩm:</h3>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                    
                    <form method="POST" class="pd-form">
                        <div class="quantity-control">
                            <label for="quantity">Số lượng:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="99">
                        </div>
                        
                        <button type="submit" name="add_to_cart" class="btn-add-cart">
                            THÊM VÀO GIỎ HÀNG
                        </button>
                    </form>
                    
                    <div class="success-actions" style="display: flex; justify-content: space-between; gap: 10px;">
    
                        <a href="menu.php" class="btn-modal-action btn-back-menu">
                            <i class="fas fa-arrow-left"></i> Quay lại Menu
                        </a>
    
                        <a href="giohang.php" class="btn-modal-action btn-checkout-modal">
                            Tiến hành Thanh toán <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                
            </div>
        <?php else: ?>
            <p style="text-align: center; padding: 50px;">Sản phẩm không tồn tại hoặc đã bị xóa.</p>
            <div style="text-align: center;"><a href="index.php">Về trang chủ</a></div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.getElementById('cartToast');
            // Kiểm tra nếu có Session báo thành công (Bạn cần gửi Session này từ PHP)
            const urlParams = new URLSearchParams(window.location.search);
            
            // Giả định bạn đã gửi status=added qua URL (Cách đơn giản nhất)
            if (urlParams.get('status') === 'added') { 
                showToast();
            }

            function showToast() {
                toast.classList.add('show');
                
                // Tự động ẩn sau 6 giây
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 6000); 
                
                // Xóa tham số status khỏi URL để tránh F5 lại hiện Toast
                history.replaceState(null, '', window.location.pathname + window.location.search.replace(/&?status=added/, ''));
            }
            
            // *** Nếu bạn dùng POST, hãy kích hoạt Toast sau khi POST thành công mà không redirect ***
            // (Đây là cách nâng cao hơn, yêu cầu xử lý AJAX hoặc đặt biến PHP)

            // VÍ DỤ: Nếu bạn sử dụng biến PHP:
            <?php if (isset($_SESSION['toast_status'])): ?>
                showToast();
                <?php unset($_SESSION['toast_status']); ?> // Xóa session để không hiện lại
            <?php endif; ?>
        });
    </script>
    
    <?php require 'includes/footer.php'; ?>
</body>
</html>