<?php
// Tải cấu hình và kết nối CSDL ($pdo)
require 'config.php';

// Khởi tạo các biến
$cart = $_SESSION['cart'] ?? [];
$cart_items = [];
$total = 0;
$error = '';

// --- 1. XỬ LÝ CẬP NHẬT/XÓA GIỎ HÀNG (POST request) ---
// --- 1. XỬ LÝ CẬP NHẬT/XÓA GIỎ HÀNG (POST request) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // TRƯỜNG HỢP 1: Cập nhật số lượng
    if (isset($_POST['update_cart'])) {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'quantity_') === 0) {
                $product_id = (int)str_replace('quantity_', '', $key);
                $quantity = (int)$value;
                
                if (isset($_SESSION['cart'][$product_id])) {
                    if ($quantity > 0) {
                        $_SESSION['cart'][$product_id] = $quantity; 
                    } else {
                        unset($_SESSION['cart'][$product_id]); 
                    }
                }
            }
        }
    } 
    
    // TRƯỜNG HỢP 2: Xóa món (CODE ĐÃ SỬA)
    // Bắt đúng cái nút có name="delete_id"
    elseif (isset($_POST['delete_id'])) {
        $product_id_to_remove = (int)$_POST['delete_id']; // Lấy ID từ giá trị nút bấm
        
        if (isset($_SESSION['cart'][$product_id_to_remove])) {
            unset($_SESSION['cart'][$product_id_to_remove]); // Xóa đúng món đó
        }
    }
    
    // Sau khi xử lý, CHUYỂN HƯỚNG để làm sạch dữ liệu
    redirect('giohang.php'); 
}

// Lấy lại dữ liệu giỏ hàng mới nhất
$cart = $_SESSION['cart'] ?? [];


// --- 2. LẤY THÔNG TIN SẢN PHẨM TỪ CSDL ĐỂ TÍNH TỔNG VÀ HIỂN THỊ ---
if (!empty($cart)) {
    $product_ids = array_keys($cart);
    // Chuẩn bị placeholders cho câu truy vấn IN clause
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    try {
        // SELECT thông tin sản phẩm dựa trên ID trong giỏ hàng
        $stmt = $pdo->prepare("SELECT id, name, price, image_url FROM products WHERE id IN ($placeholders)");
        $stmt->execute($product_ids);
        $products_data = $stmt->fetchAll();

        foreach ($products_data as $product) {
            $quantity = $cart[$product['id']];
            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;
            
            $cart_items[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    } catch (PDOException $e) {
        $error = "Lỗi truy vấn sản phẩm giỏ hàng: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ Hàng Của Bạn</title>
    <link rel="stylesheet" href="assets/css/home.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS bổ sung cho trang giỏ hàng */
        .cart-table-container { 
            overflow-x: auto; 
        }
        .cart-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
            background-color: white;
        }
        .cart-table th, .cart-table td { 
            border: 1px solid #ddd; 
            padding: 12px; 
            text-align: center;
        }
        .cart-table th { 
            background-color: #f0f0f0; 
        }
        .total-summary { 
            text-align: right; 
            font-size: 1.5em; 
            color: #d84315; 
            font-weight: bold;
            margin-top: 20px;
        } 
    </style>
</head>
<body>
    <?php require 'includes/header.php'; ?>
    
    <div class="container">
        <h1>Giỏ Hàng Của Bạn</h1>

        <?php if ($error): ?>
             <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (empty($cart_items)): ?>
            <p style="font-size: 1.2em; text-align: center;">Giỏ hàng trống. <a href="menu.php">Tiếp tục mua sắm</a></p>
        <?php else: ?>
            <form method="POST">
            <div class="cart-table-container">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td style="text-align: left;"><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo number_format($item['price']); ?> VND</td>
                            <td>
                                <input type="number" name="quantity_<?php echo $item['id']; ?>" 
                                       value="<?php echo $item['quantity']; ?>" min="0" style="width: 70px; text-align: center;">
                            </td>
                            <td><?php echo number_format($item['subtotal']); ?> VND</td>
                            <td>
                                <button type="submit" name="delete_id" value="<?php echo $item['id']; ?>" 
                                        style="background-color: #53382c; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px;">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>
                        </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="text-align: left; margin-bottom: 30px;">
                <button type="submit" name="update_cart" style="padding: 10px 15px; background-color: #53382c; color: white; border: none; cursor: pointer; border-radius: 4px;">Cập nhật Giỏ hàng</button>
            </div>
            </form>
            
            <div class="total-summary">
                Tổng Cộng: <?php echo number_format($total); ?> VND
            </div>
            
            <div style="margin-top: 30px;">
                <a href="menu.php">← Tiếp tục mua sắm</a> | 
                <a href="checkout.php" style="background-color: #53382c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-left: 15px;">Tiến hành Thanh toán →</a>
            </div>
        <?php endif; ?>
    </div>

    <?php require 'includes/footer.php'; ?>
</body>
</html>