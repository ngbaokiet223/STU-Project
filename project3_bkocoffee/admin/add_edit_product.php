<?php
require '../config.php'; 

// 1. KIỂM TRA QUYỀN
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php"); 
    exit;
}

$product = ['id' => null, 'name' => '', 'description' => '', 'price' => '', 'image_url' => ''];
$is_editing = false;
$page_title = 'Thêm Sản phẩm Mới';
$errors = [];

// 2. LẤY DỮ LIỆU (NẾU SỬA)
if (isset($_GET['id'])) {
    $is_editing = true;
    $page_title = 'Chỉnh sửa Sản phẩm';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch() ?: $product;
}

// 3. XỬ LÝ FORM
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $img = trim($_POST['image_url']);
    $id = $_POST['product_id'] ?? null;

    if (!$name) $errors[] = "Nhập tên sản phẩm";
    if ($price <= 0) $errors[] = "Giá không hợp lệ";

    if (empty($errors)) {
        if ($id && $is_editing) {
            $sql = "UPDATE products SET name=?, description=?, price=?, image_url=? WHERE id=?";
            $pdo->prepare($sql)->execute([$name, $desc, $price, $img, $id]);
        } else {
            $sql = "INSERT INTO products (name, description, price, image_url) VALUES (?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$name, $desc, $price, $img]);
        }
        redirect('products.php');
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn-submit { background: #d84315; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-submit:hover { background: #bf360c; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>KIET BAN ADMIN</h2>
        <a href="admin_dashboard.php"><i class="fas fa-home"></i> Tổng quan</a>
        <a href="products.php" class="active"><i class="fas fa-coffee"></i> Quản lý Món ăn</a>
        <a href="admin_orders.php"><i class="fas fa-file-invoice-dollar"></i> Quản lý Đơn hàng</a>
        <a href="../index.php"><i class="fas fa-arrow-left"></i> Về Website</a>
    </div>

    <div class="main-content">
        <a href="products.php" style="display:inline-block; margin-bottom:15px; color:#666; text-decoration:none;">← Quay lại danh sách</a>
        
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h2 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;"><?php echo $page_title; ?></h2>

            <?php if (!empty($errors)): ?>
                <div style="background:#f8d7da; color:#721c24; padding:10px; margin-bottom:15px; border-radius:5px;">
                    <?php echo implode('<br>', $errors); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <?php if ($is_editing): ?>
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Tên Sản phẩm</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Giá (VNĐ)</label>
                    <input type="number" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Link Hình ảnh (URL)</label>
                    <input type="text" name="image_url" class="form-control" value="<?php echo htmlspecialchars($product['image_url']); ?>" placeholder="https://...">
                </div>

                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> <?php echo $is_editing ? 'Cập Nhật' : 'Thêm Mới'; ?>
                </button>
            </form>
        </div>
    </div>
</body>
</html>