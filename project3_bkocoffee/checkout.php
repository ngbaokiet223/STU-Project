<?php
require 'config.php';

$message = "";

// 1. KIỂM TRA GIỎ HÀNG
$cart = $_SESSION['cart'] ?? [];
// Nếu giỏ hàng trống và không phải là đang submit form thì đẩy về menu
if (empty($cart) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: menu.php");
    exit;
}

// 2. LẤY CHI TIẾT SẢN PHẨM TỪ DATABASE
$cart_items = []; 
$total_price = 0;

if (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            $qty = $cart[$product['id']];
            $product['quantity'] = $qty; 
            $product['line_total'] = $product['price'] * $qty; 
            
            $cart_items[] = $product; 
            $total_price += $product['line_total']; 
        }
    } catch (PDOException $e) {
        $message = "Lỗi kết nối sản phẩm: " . $e->getMessage();
    }
}

// 3. XỬ LÝ ĐẶT HÀNG (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ Form (Tên Input: customer_name, customer_phone, ...)
    $name = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['customer_phone'] ?? '');
    $address = trim($_POST['customer_address'] ?? '');
    $note = trim($_POST['customer_note'] ?? '');
    $user_id = $_SESSION['user_id'] ?? NULL;

    if ($name && $phone && $address) {
        // Bắt đầu Transaction để đảm bảo lưu cả đơn và chi tiết
        $pdo->beginTransaction(); 
        try {
            // A. Lưu vào bảng orders 
            // Cột DB: customer_name, customer_phone, customer_address, customer_note, total_amount
            $sql = "INSERT INTO orders (user_id, customer_name, customer_phone, customer_address, customer_note, total_amount, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $name, $phone, $address, $note, $total_price]);
            
            $order_id = $pdo->lastInsertId(); 

            // B. Lưu chi tiết vào bảng order_items (FIX LỖI 'product_name' SQL)
            $sql_item = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
            $stmt_item = $pdo->prepare($sql_item);
            
            foreach ($cart_items as $item) {
                $stmt_item->execute([
                    $order_id, 
                    $item['id'], 
                    $item['name'], // LƯU TÊN MÓN VÀO CỘT product_name (Đã fix lỗi thiếu cột)
                    $item['quantity'], 
                    $item['price']
                ]);
            }

            $pdo->commit(); // Hoàn tất Transaction
            
            // C. Xóa giỏ hàng và chuyển hướng
            unset($_SESSION['cart']);
            header("Location: thankyou.php?order_id=$order_id");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack(); // Hoàn tác nếu có lỗi
            $message = "ĐẶT HÀNG THẤT BẠI: " . $e->getMessage();
        }
    } else {
        $message = "Vui lòng điền đầy đủ thông tin nhận hàng.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh Toán - Kiet Ban Cafe</title>
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Inline cho trang Checkout */
        .checkout-layout { display: flex; gap: 40px; margin-top: 30px; }
        .checkout-col-left { flex: 3; }
        .checkout-col-right { flex: 2; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #53382c; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        
        .order-summary-box { background: #f9f9f9; padding: 30px; border-radius: 12px; border: 1px solid #eee; }
        .summary-title { margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px; color: #53382c; }
        .summary-item { display:flex; justify-content:space-between; margin-bottom:10px; font-size:0.95em; }
        .item-info { display: flex; gap: 10px; }
        .summary-divider { border-top: 1px dashed #ccc; margin: 15px 0; }
        .summary-total { display: flex; justify-content:space-between; font-size:1.3em; font-weight:bold; color: #53382c;   }
        
        .btn-checkout-confirm { width:100%; padding: 15px; background: #53382c; color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 1.1em; margin-top:20px; cursor: pointer; transition: 0.3s; }
        .btn-checkout-confirm:hover { background: #bf360c; }
        .back-to-cart { display: block; text-align: center; margin-top: 15px; color: #666; font-size: 0.9em; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #f5c6cb; }

        @media (max-width: 800px) { .checkout-layout { flex-direction: column; } }
    </style>
</head>
<body>
    
    <?php require 'includes/header.php'; ?>
    
    <div class="container">
        
        <h1 class="page-title" style="margin-top: 140px;">THANH TOÁN ĐƠN HÀNG</h1>
        
        <?php if ($message): ?>
            <div class="alert-error"><i class="fas fa-exclamation-triangle"></i> <?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" class="checkout-layout">
            
            <div class="checkout-col-left">
                <h3><i class="fas fa-map-marker-alt"></i> Thông tin giao hàng</h3>
                
                <div class="form-group">
                    <label>Họ và Tên (*)</label>
                    <input type="text" name="customer_name" required 
                           value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>"
                           placeholder="Nhập họ tên người nhận">
                </div>
                
                <div class="form-group">
                    <label>Số điện thoại (*)</label>
                    <input type="text" name="customer_phone" required placeholder="Nhập số điện thoại">
                </div>
                
                <div class="form-group">
                    <label>Địa chỉ nhận hàng (*)</label>
                    <input type="text" name="customer_address" required placeholder="Số nhà, tên đường, phường/xã...">
                </div>
                
                <div class="form-group">
                    <label>Ghi chú đơn hàng</label>
                    <textarea name="customer_note" rows="3" placeholder="Ví dụ: Ít đường, giao giờ hành chính..."></textarea>
                </div>
            </div>

            <div class="checkout-col-right">
                <div class="order-summary-box">
                    <h3 class="summary-title">Đơn hàng của bạn (<?php echo count($cart); ?> món)</h3>

                    <div class="summary-list">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="summary-item">
                                <div class="item-info">
                                    <span class="item-name">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </span>
                                    <span class="item-qty">x <?php echo $item['quantity']; ?></span>
                                </div>
                                <span class="item-price">
                                    <?php echo number_format($item['line_total']); ?>₫
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-total">
                        <span>Tổng cộng</span>
                        <span class="total-price"><?php echo number_format($total_price); ?> VNĐ</span>
                    </div>

                    <button type="submit" class="btn-checkout-confirm">ĐẶT HÀNG NGAY</button>
                    
                    <a href="giohang.php" class="back-to-cart">← Quay lại giỏ hàng</a>
                </div>
            </div>
            
        </form>
    </div>
    
    <?php require 'includes/footer.php'; ?>
</body>
</html>