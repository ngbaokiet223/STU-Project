<?php
require '../config.php';

// 1. KIỂM TRA QUYỀN ADMIN
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php"); 
    exit;
}

$id = $_GET['id'] ?? 0;

// --- XỬ LÝ CẬP NHẬT TRẠNG THÁI ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    header("Location: admin_order_detail.php?id=$id");
    exit;
}

// 2. LẤY THÔNG TIN ĐƠN HÀNG
// Lấy thêm total_money để dự phòng nếu total_amount thiếu
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) { die("Không tìm thấy đơn hàng."); }

// 3. LẤY CHI TIẾT MÓN ĂN (SỬA LẠI SQL ĐỂ JOIN VỚI BẢNG PRODUCTS)
// Kỹ thuật này giúp lấy được tên món ngay cả khi đơn hàng cũ chưa lưu tên
$sql_items = "
    SELECT oi.*, p.name as product_original_name, p.image_url 
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
";
$stmt_items = $pdo->prepare($sql_items);
$stmt_items->execute([$id]);
$items = $stmt_items->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?php echo $id; ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .order-info-box { display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
        .info-col { flex: 1; background: #f9f9f9; padding: 20px; border-radius: 8px; min-width: 300px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .info-col h3 { margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 10px; color: #555; }
        .info-row { margin-bottom: 10px; border-bottom: 1px dashed #eee; padding-bottom: 5px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>KIET BAN ADMIN</h2>
        <a href="admin_dashboard.php"><i class="fas fa-home"></i> Tổng quan</a>
        <a href="products.php"><i class="fas fa-coffee"></i> Quản lý Món ăn</a>
        <a href="admin_orders.php" class="active"><i class="fas fa-file-invoice-dollar"></i> Quản lý Đơn hàng</a>
        <a href="../index.php"><i class="fas fa-arrow-left"></i> Về Website</a>
    </div>

    <div class="main-content">
        <a href="admin_orders.php" style="display:inline-block; margin-bottom:15px; color:#666; text-decoration:none;">← Quay lại danh sách</a>
        
        <div class="card">
            <h2 style="color: #d84315; margin-top: 0;">CHI TIẾT ĐƠN HÀNG #<?php echo $id; ?></h2>
            
            <div class="order-info-box">
                <div class="info-col">
                    <h3><i class="fas fa-user-circle"></i> Thông tin khách hàng</h3>
                    
                    <div class="info-row">
                        <strong>Họ tên:</strong> <?php echo htmlspecialchars($order['customer_name'] ?? $order['fullname'] ?? 'Khách vãng lai'); ?>
                    </div>
                    <div class="info-row">
                        <strong>SĐT:</strong> <?php echo htmlspecialchars($order['customer_phone'] ?? $order['phone'] ?? '---'); ?>
                    </div>
                    <div class="info-row">
                        <strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['customer_address'] ?? $order['address'] ?? '---'); ?>
                    </div>
                    <div class="info-row">
                        <strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['customer_note'] ?? $order['note'] ?? 'Không có ghi chú'); ?>
                    </div>
                    <div class="info-row">
                        <strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                    </div>
                </div>

                <div class="info-col">
                    <h3><i class="fas fa-tasks"></i> Xử lý đơn hàng</h3>
                    <form method="POST" style="margin-top: 15px;">
                        <label><strong>Trạng thái hiện tại:</strong></label>
                        <select name="status" style="padding: 10px; width: 100%; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc;">
                            <option value="Pending" <?php if(strtolower($order['status'] ?? '') =='pending') echo 'selected'; ?>>Chờ xử lý</option>
                            <option value="Completed" <?php if(strtolower($order['status'] ?? '') =='completed') echo 'selected'; ?>>Đã hoàn thành</option>
                            <option value="Cancelled" <?php if(strtolower($order['status'] ?? '') =='cancelled') echo 'selected'; ?>>Đã hủy</option>
                        </select>
                        <button type="submit" name="update_status" class="btn-action btn-edit" style="width:100%; border:none; cursor:pointer; background: #2196F3; color:white; padding:10px; border-radius:4px;">
                            <i class="fas fa-sync-alt"></i> Cập nhật trạng thái
                        </button>
                    </form>
                </div>
            </div>

            <h3><i class="fas fa-utensils"></i> Danh sách món ăn</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Tên món</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?php 
                                    // LOGIC HIỂN THỊ TÊN MÓN "THÔNG MINH"
                                    // 1. Ưu tiên lấy tên đã lưu trong order_items (nếu có)
                                    // 2. Nếu không có, lấy tên từ bảng products (qua JOIN)
                                    // 3. Nếu vẫn không có, báo lỗi
                                    $displayName = "Món không xác định";
                                    if (!empty($item['product_name'])) {
                                        $displayName = $item['product_name'];
                                    } elseif (!empty($item['product_original_name'])) {
                                        $displayName = $item['product_original_name'];
                                    }
                                    echo htmlspecialchars($displayName); 
                                ?>
                            </td>
                            <td><?php echo number_format($item['price'] ?? 0); ?>đ</td>
                            <td>x<?php echo $item['quantity'] ?? 0; ?></td>
                            <td><strong><?php echo number_format(($item['price']??0) * ($item['quantity']??0)); ?>đ</strong></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center;">Không có dữ liệu món ăn.</td></tr>
                    <?php endif; ?>
                    
                    <tr style="background: #f1f1f1;">
                        <td colspan="3" style="text-align: right; font-weight: bold; padding-right: 20px;">TỔNG THANH TOÁN:</td>
                        <td style="font-weight: bold; color: #d84315; font-size: 1.3em;">
                            <?php echo number_format($order['total_amount'] ?? $order['total_money'] ?? 0); ?> VNĐ
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>