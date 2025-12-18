<?php 
// Lấy số lượng sản phẩm trong giỏ hàng để hiển thị trên menu
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
?>

<header>
    <nav class="main-nav"> 
        
        <div class="nav-links"> 
            <a href="index.php"> Trang Chủ</a>
            <a href="lienhe.php"> Liên Hệ</a>
            <a href="menu.php"> Menu</a>
            <a href="giohang.php"> Giỏ Hàng (<?php echo $cart_count; ?>)</a>
        </div>
        
        <div class="logo-container">
            <a href="index.php">
                <img src="assets/css/images/logoo.jpg" alt="Logo Cửa Hàng" class="site-logo">
            </a>
        </div>
        
        <div class="user-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Xin chào, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></span>
                
                <?php if ($_SESSION['is_admin']): ?>
                    <a href="admin/admin_dashboard">🛠️ Admin</a>
                <?php endif; ?>
                
                <a href="logout.php">Đăng Xuất</a>
            <?php else: ?>
                <a href="login.php">Đăng nhập</a>
                <a href="register.php">Đăng ký</a>
            <?php endif; ?>
        </div>
        
    </nav>
</header>