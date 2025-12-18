<?php
require '../config.php';

// 1. KIỂM TRA QUYỀN ADMIN
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php"); 
    exit;
}

// 2. THỐNG KÊ DOANH THU (Chỉ tính các đơn đã 'Completed')
// SỬA: Dùng cột total_amount thay vì total_money
$sql_revenue = "SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'Completed'";
$stmt_revenue = $pdo->query($sql_revenue);
$revenue_data = $stmt_revenue->fetch();
$total_revenue = $revenue_data['revenue'] ?? 0;

// 3. THỐNG KÊ TỔNG ĐƠN HÀNG
$sql_orders = "SELECT COUNT(*) as total_orders FROM orders";
$total_orders = $pdo->query($sql_orders)->fetchColumn();

// 4. THỐNG KÊ ĐƠN CHỜ XỬ LÝ
$sql_pending = "SELECT COUNT(*) as pending_orders FROM orders WHERE status = 'Pending'";
$pending_orders = $pdo->query($sql_pending)->fetchColumn();

// 5. THỐNG KÊ TỔNG SẢN PHẨM
$sql_products = "SELECT COUNT(*) as total_products FROM products";
$total_products = $pdo->query($sql_products)->fetchColumn();

// 6. LẤY 5 ĐƠN HÀNG MỚI NHẤT
$sql_recent = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5";
$recent_orders = $pdo->query($sql_recent)->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Kiet Ban Cafe</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-widgets { display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
        .widget { flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: space-between; min-width: 200px; }
        .widget-info h3 { margin: 0 0 5px 0; color: #777; font-size: 0.9em; text-transform: uppercase; }
        .widget-info p { margin: 0; font-size: 1.8em; font-weight: bold; color: #333; }
        .widget-icon { font-size: 2.5em; color: #ddd; opacity: 0.5; }
        
        /* Màu sắc icon */
        .widget:nth-child(1) .widget-icon { color: #28a745; opacity: 1; } /* Doanh thu */
        .widget:nth-child(2) .widget-icon { color: #17a2b8; opacity: 1; } /* Đơn hàng */
        .widget:nth-child(3) .widget-icon { color: #ffc107; opacity: 1; } /* Chờ xử lý */
        .widget:nth-child(4) .widget-icon { color: #6c757d; opacity: 1; } /* Sản phẩm */
    </style>
</head>
<body>
    
    <div class="sidebar">
        <h2>KIET BAN ADMIN</h2>
        <a href="admin_dashboard.php" class="active"><i class="fas fa-home"></i> Tổng quan</a>
        <a href="products.php"><i class="fas fa-coffee"></i> Quản lý Món ăn</a>
        <a href="admin_orders.php"><i class="fas fa-file-invoice-dollar"></i> Quản lý Đơn hàng</a>
        <a href="../index.php"><i class="fas fa-arrow-left"></i> Về Website</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
    </div>

    <div class="main-content">
        <h1 style="margin-top: 0;">Dashboard</h1>
        
        <div class="dashboard-widgets">
            <div class="widget">
                <div class="widget-info">
                    <h3>Tổng Doanh Thu</h3>
                    <p><?php echo number_format($total_revenue); ?> VNĐ</p>
                </div>
                <div class="widget-icon"><i class="fas fa-dollar-sign"></i></div>
            </div>

            <div class="widget">
                <div class="widget-info">
                    <h3>Tổng Đơn Hàng</h3>
                    <p><?php echo $total_orders; ?></p>
                </div>
                <div class="widget-icon"><i class="fas fa-shopping-cart"></i></div>
            </div>

            <div class="widget">
                <div class="widget-info">
                    <h3>Chờ Xử Lý</h3>
                    <p><?php echo $pending_orders; ?></p>
                </div>
                <div class="widget-icon"><i class="fas fa-clock"></i></div>
            </div>

            <div class="widget">
                <div class="widget-info">
                    <h3>Tổng Sản Phẩm</h3>
                    <p><?php echo $total_products; ?></p>
                </div>
                <div class="widget-icon"><i class="fas fa-mug-hot"></i></div>
            </div>
        </div>

        <div class="card">
            <h2>Hoạt động gần đây</h2>
            <p>5 đơn hàng mới nhất:</p>
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent_orders) > 0): ?>
                        <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($order['customer_name'] ?? $order['fullname'] ?? 'Khách vãng lai'); ?>
                            </td>
                            <td><?php echo date('d/m H:i', strtotime($order['created_at'])); ?></td>
                            <td style="font-weight:bold;">
                                <?php echo number_format($order['total_amount'] ?? 0); ?>đ
                            </td>
                            <td>
                                <span class="<?php echo ($order['status'] == 'Completed') ? 'status-completed' : 'status-pending'; ?>">
                                    <?php echo $order['status']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5">Chưa có đơn hàng nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>