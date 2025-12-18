<?php
require 'config.php';

// Xử lý logic giỏ hàng (Giữ nguyên)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart_quick'])) {
    // Logic này để phòng hờ, nhưng giao diện mới ta sẽ bỏ nút thêm nhanh ở ngoài
}

// Lấy sản phẩm mới nhất
$products = [];
try {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY id DESC LIMIT 8');
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Chủ - Kiet Ban Cafe</title>
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <?php require 'includes/header.php'; ?>
    
    <div class="main-banner">
    <img src="assets/css/images/uu.jpg" alt="Banner Cà Phê"> 
    <div class="banner-content">
        <h2>HƯƠNG VỊ CÀ PHÊ ĐÍCH THỰC</h2>
        <p>Đậm đà bản sắc Việt - Khơi dậy mọi giác quan</p>
        <a href="menu.php" class="btn-banner">MUA NGAY</a>
    </div>
</div>
    <!-- <div id="cartToast" class="toast-notification">
        <i class="fas fa-check-circle" style="margin-right: 8px;"></i> ĐÃ THÊM VÀO GIỎ HÀNG THÀNH CÔNG!
    </div> -->

    <div class="container"> 
        <h1>SẢN PHẨM NỔI BẬT</h1>
        
        <div class="product-list"> 
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    
                    <a href="sanpham.php?id=<?php echo $product['id']; ?>" class="product-link">
                        <div class="product-img-box">
                            <?php if (!empty($product['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-coffee"></i><br>
                                    Món ngon
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    
                    <a href="sanpham.php?id=<?php echo $product['id']; ?>" class="product-link">
                        <h2 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h2>
                    </a>
                    
                    <span class="price"><?php echo number_format($product['price']); ?> VNĐ</span>
                    
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center;">
            <a href="menu.php" class="btn-view-all">XEM TẤT CẢ MENU</a>
        </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('header');
            // Vị trí mà header sẽ chuyển đổi (Thường là chiều cao của banner - header)
            const scrollPoint = 400; // Đổi số này nếu banner của bạn cao/thấp hơn

            function handleScroll() {
                // Lấy vị trí cuộn hiện tại
                const currentScrollPos = window.scrollY;

                if (currentScrollPos > scrollPoint) {
                    // Nếu cuộn quá vị trí quy định, thêm class 'scrolled'
                    header.classList.add('scrolled');
                } else {
                    // Ngược lại, xóa class 'scrolled'
                    header.classList.remove('scrolled');
                }
            }

            // Kích hoạt sự kiện khi cuộn trang
            window.addEventListener('scroll', handleScroll);
            
            // Chạy lần đầu để đảm bảo trạng thái đúng khi tải trang
            handleScroll();
        });
    </script>
    
    <?php require 'includes/footer.php'; ?>

</body>
</html>