<?php
require '../config.php';

// 1. KIỂM TRA QUYỀN ADMIN
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php"); 
    exit;
}

// --- 2. XỬ LÝ CHỨC NĂNG XÓA ĐƠN HÀNG (MỚI THÊM) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_order') {
    $order_id = $_POST['order_id'];
    
    try {
        // Bước 1: Xóa chi tiết đơn hàng trước (trong bảng order_items)
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$order_id]);

        // Bước 2: Xóa đơn hàng chính (trong bảng orders)
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);

        // Bước 3: Refresh lại trang và báo thành công
        header("Location: admin_orders.php?status=deleted");
        exit;
    } catch (PDOException $e) {
        $error_msg = "Lỗi khi xóa đơn: " . $e->getMessage();
    }
}

// 3. LẤY DANH SÁCH ĐƠN HÀNG (Sắp xếp mới nhất lên đầu)
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS cho nút Xóa */
        .btn-delete {
            background-color: #dc3545; /* Màu đỏ */
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-delete:hover { background-color: #c82333; }
        
        .alert-success { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>KIET BAN ADMIN</h2>
        <a href="admin_dashboard.php"><i class="fas fa-home"></i> Tổng quan</a>
        <a href="products.php"><i class="fas fa-coffee"></i> Quản lý Món ăn</a>
        <a href="admin_orders.php" class="active"><i class="fas fa-file-invoice-dollar"></i> Quản lý Đơn hàng</a>
        <a href="../index.php"><i class="fas fa-arrow-left"></i> Về Website</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h2>DANH SÁCH ĐƠN HÀNG</h2>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i> Đã xóa đơn hàng thành công!
                </div>
            <?php endif; ?>

            <?php if (isset($error_msg)): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($order['customer_name'] ?? $order['fullname'] ?? 'Khách vãng lai'); ?></strong><br>
                            <small>SĐT: <?php echo htmlspecialchars($order['customer_phone'] ?? $order['phone'] ?? '---'); ?></small>
                        </td>
                        
                        <td><?php echo date('d/m H:i', strtotime($order['created_at'])); ?></td>
                        
                        <td style="font-weight:bold; color:#d84315;">
                            <?php echo number_format($order['total_amount'] ?? 0); ?>đ
                        </td>
                        
                        <td>
                            <?php 
                                $status = $order['status'] ?? 'Pending';
                                $statusClass = 'status-pending';
                                if(strtolower($status) == 'completed') $statusClass = 'status-completed';
                                if(strtolower($status) == 'cancelled') $statusClass = 'status-cancelled';
                            ?>
                            <span class="<?php echo $statusClass; ?>"><?php echo ucfirst($status); ?></span>
                        </td>
                        
                        <td style="display: flex; gap: 5px;">
                            <a href="admin_order_detail.php?id=<?php echo $order['id']; ?>" class="btn-action btn-view">
                                <i class="fas fa-eye"></i> Xem
                            </a>

                            <form method="POST" onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa vĩnh viễn đơn hàng #<?php echo $order['id']; ?> không? Hành động này không thể hoàn tác!');">
                                <input type="hidden" name="action" value="delete_order">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" class="btn-delete" title="Xóa đơn hàng">
                                    <i class="fas fa-trash"></i>
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