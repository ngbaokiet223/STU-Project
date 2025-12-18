<?php
require 'config.php';

// 1. Lấy danh sách danh mục
$categories = [];
try {
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) { echo "Lỗi: " . $e->getMessage(); }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thực Đơn - Kiet Ban Cafe</title>
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require 'includes/header.php'; ?>
    
    <div class="page-header">
        <h1>THỰC ĐƠN</h1>
        <p>Thưởng thức trọn vẹn hương vị</p>
    </div>

    <div class="container" style="margin-top: 0;"> <?php foreach ($categories as $cat): ?>
            <?php
                // Lấy 10 sản phẩm thuộc danh mục này
                $stmt_prod = $pdo->prepare("SELECT * FROM products WHERE category_id = ? LIMIT 10");
                $stmt_prod->execute([$cat['id']]);
                $cat_products = $stmt_prod->fetchAll();
            ?>

            <?php if (!empty($cat_products)): ?>
                <section class="category-section">
                    <div class="cat-header">
                        <h2 class="cat-title"><?php echo htmlspecialchars($cat['name']); ?></h2>
                    </div>

                    <div class="slider-container">
                        <button class="nav-btn prev-btn" onclick="scrollSlider(this, -280)"><i class="fas fa-chevron-left"></i></button>

                        <div class="product-track">
                            <?php foreach ($cat_products as $product): ?>
                                <div class="slide-item">
                                    <a href="sanpham.php?id=<?php echo $product['id']; ?>" class="slide-link">
                                        <div class="product-img-box" style="height: 220px;">
                                            <?php if (!empty($product['image_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Img">
                                            <?php else: ?>
                                                <div class="no-image"><i class="fas fa-coffee"></i></div>
                                            <?php endif; ?>
                                        </div>
                                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                    </a>
                                    <span class="slide-price"><?php echo number_format($product['price']); ?> VNĐ</span>
                                    
                                    <form method="POST" style="margin-top:10px;">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button class="nav-btn next-btn" onclick="scrollSlider(this, 280)"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </section>
                <hr class="section-divider">
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    
    <?php if (isset($_GET['status']) && $_GET['status'] === 'added'): ?>
        <div class="modal-overlay">
            <div class="modal-content">
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="close-modal">&times;</a>
                <div class="modal-icon"><i class="fas fa-heart"></i></div>
                <h2 class="modal-title">CẢM ƠN BẠN<br>ĐÃ CHỌN TÔI</h2>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn-back-menu">OK</a>
            </div>
        </div>
        <script>
            if (history.replaceState) {
                var newUrl = window.location.href.replace(/[\?&]status=added/, '');
                history.replaceState(null, null, newUrl);
            }
        </script>
    <?php endif; ?>

    <?php require 'includes/footer.php'; ?>

    <script>
        function scrollSlider(btn, distance) {
            const container = btn.parentElement.querySelector('.product-track');
            container.scrollBy({ left: distance, behavior: 'smooth' });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.getElementById('cartToast');
            
            // PHẦN QUAN TRỌNG: PHP in ra JS để kích hoạt Toast
            <?php 
            // Kiểm tra nếu biến Session đã được đặt từ logic xử lý POST
            if (isset($_SESSION['toast_status']) && $_SESSION['toast_status'] === 'success'): 
            ?>
                // 1. Thêm class 'show' để hiển thị Toast (CSS đã có)
                toast.classList.add('show');
                
                // 2. Tự động ẩn sau 6 giây
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 6000); 

                // 3. Xóa session để Toast không hiện lại khi người dùng F5
                <?php unset($_SESSION['toast_status']); ?>
                
            <?php endif; ?>
        });
    </script>
</body>
</html>