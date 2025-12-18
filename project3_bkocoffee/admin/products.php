<?php
require '../config.php'; 

// 1. KIỂM TRA QUYỀN ADMIN (Chuẩn hóa logic is_admin)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php"); 
    exit;
}

// 2. XỬ LÝ XÓA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['product_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        redirect('products.php?status=deleted');
    } catch (PDOException $e) {
        $error = "Lỗi xóa: " . $e->getMessage();
    }
}

// 3. LẤY DANH SÁCH
try {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY id DESC');
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sản phẩm</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h2>KIET BAN ADMIN</h2>
        <a href="admin_dashboard.php"><i class="fas fa-home"></i> Tổng quan</a>
        <a href="products.php" class="active"><i class="fas fa-coffee"></i> Quản lý Món ăn</a>
        <a href="admin_orders.php"><i class="fas fa-file-invoice-dollar"></i> Quản lý Đơn hàng</a>
        <a href="../index.php"><i class="fas fa-arrow-left"></i> Về Website</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
    </div>

    <div class="main-content">
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
                <h2>DANH SÁCH SẢN PHẨM</h2>
                <a href="add_edit_product.php" class="btn-action" style="background:#28a745; color:white; text-decoration:none; padding:10px 20px; border-radius:5px;">
                    <i class="fas fa-plus"></i> Thêm Món Mới
                </a>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
                    Đã xóa sản phẩm thành công.
                </div>
            <?php endif; ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Tên món</th>
                        <th>Giá</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <?php if($product['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                            <?php else: ?>
                                <span style="color:#999;">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo number_format($product['price']); ?> đ</td>
                        <td>
                            <a href="add_edit_product.php?id=<?php echo $product['id']; ?>" class="btn-action btn-edit">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form method="POST" style="display:inline-block;" onsubmit="return confirm('Xóa món này?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn-action btn-delete" style="border:none; cursor:pointer;">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>