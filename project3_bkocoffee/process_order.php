<?php
require 'config.php';

// Kiểm tra giỏ hàng và phương thức POST
if (empty($_SESSION['cart']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// 1. Lấy thông tin từ Form
$customer_name = trim($_POST['customer_name'] ?? '');
$customer_email = trim($_POST['customer_email'] ?? '');
$customer_phone = trim($_POST['customer_phone'] ?? '');
$shipping_address = trim($_POST['shipping_address'] ?? '');
$total_amount = floatval($_POST['total_amount'] ?? 0); // Lấy từ form (Đã được tính toán lại trong checkout.php)
$user_id = $_SESSION['user_id'] ?? null;
$payment_status = 'Pending';
$cart = $_SESSION['cart'];

// 2. Tái tính toán và chuẩn bị dữ liệu (Quan trọng để bảo mật)
// Chúng ta cần lấy giá và ID chi tiết từ CSDL để tránh dữ liệu giả mạo từ form
$product_ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$order_items_to_save = [];
$actual_total = 0; // Tính toán lại tổng tiền thực tế từ CSDL

try {
    $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products_data = $stmt->fetchAll();

    foreach ($products_data as $product) {
        $quantity = $cart[$product['id']];
        $price_at_order = $product['price'];
        $actual_total += $price_at_order * $quantity;
        
        $order_items_to_save[] = [
            'product_id' => $product['id'],
            'quantity' => $quantity,
            'price_at_order' => $price_at_order
        ];
    }
} catch (PDOException $e) {
    die("Lỗi truy vấn sản phẩm giỏ hàng: " . $e->getMessage());
}

// Kiểm tra tính hợp lệ của tổng tiền (tùy chọn)
if ($actual_total <= 0) {
    die("Tổng tiền đơn hàng không hợp lệ.");
}

// 3. Bắt đầu TRANSACTION để đảm bảo không có đơn hàng bị thiếu chi tiết
$pdo->beginTransaction();

try {
    // 3a. Chèn vào bảng orders
    $sql_order = "INSERT INTO orders (user_id, customer_name, customer_email, customer_phone, shipping_address, total_amount, payment_status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_order = $pdo->prepare($sql_order);
    $stmt_order->execute([$user_id, $customer_name, $customer_email, $customer_phone, $shipping_address, $actual_total, $payment_status]);
    
    $order_id = $pdo->lastInsertId(); // Lấy ID của đơn hàng vừa chèn

    // 3b. Chèn vào bảng order_items
    $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price_at_order) VALUES (?, ?, ?, ?)";
    $stmt_item = $pdo->prepare($sql_item);

    foreach ($order_items_to_save as $item) {
        $stmt_item->execute([
            $order_id, 
            $item['product_id'], 
            $item['quantity'], 
            $item['price_at_order']
        ]);
    }

    // Nếu mọi thứ thành công, xác nhận transaction
    $pdo->commit();

    // 4. Dọn dẹp và Chuyển hướng
    unset($_SESSION['cart']); // Xóa giỏ hàng sau khi đặt hàng thành công

    // Chuyển hướng đến trang cảm ơn
    redirect('thank_you.php?order_id=' . $order_id);

} catch (Exception $e) {
    // Nếu có lỗi, rollback transaction và hủy bỏ tất cả thay đổi
    $pdo->rollBack();
    die("Lỗi xử lý đơn hàng: " . $e->getMessage());
}
?>