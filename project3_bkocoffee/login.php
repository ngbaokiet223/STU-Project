<?php
// Bắt đầu session và tải cấu hình
require 'config.php';

$message = '';
$email = ''; 

// --- 1. LOGIC KIỂM TRA ĐĂNG NHẬP (SỬA ĐƯỜNG DẪN) ---
if (isset($_SESSION['user_id'])) {
    // Nếu là Admin -> Vào thư mục admin/
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
        header("Location: admin/admin_dashboard.php"); 
        exit;
    } else {
        header("Location: index.php");
        exit;
    }
}

// --- 2. LOGIC XỬ LÝ POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $message = "Vui lòng điền đầy đủ email và mật khẩu.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, name, password_hash, is_admin FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name']; 
                $_SESSION['is_admin'] = (bool)$user['is_admin']; 
                
                // Chuyển hướng đúng thư mục
                if ($_SESSION['is_admin']) {
                    header("Location: admin/admin_dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $message = "Email hoặc mật khẩu không chính xác.";
            }
        } catch (PDOException $e) {
            $message = "Lỗi: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập - Kiet Ban Cafe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* GIỮ NGUYÊN GIAO DIỆN CỦA BẠN */
        body { background: linear-gradient(135deg, #3e2723 0%, #6d4c41 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; color: white; font-family: sans-serif; }
        .login-card { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); width: 100%; max-width: 400px; text-align: center; border: 1px solid rgba(255, 255, 255, 0.3); }
        h1 { color: white; margin-bottom: 10px; font-size: 2.5em; }
        .input-group { margin-bottom: 20px; text-align: left; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #f5f5f5; }
        input { width: 100%; padding: 12px; border: none; border-radius: 8px; background: rgba(255, 255, 255, 0.2); color: white; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: linear-gradient(90deg, #3e2723, #6d4c41); color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1.1em; transition: 0.3s; margin-top: 10px; }
        .message { margin-bottom: 15px; font-weight: bold; padding: 10px; border-radius: 5px; }
        .signup-link a { color: #ffcc80; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-card"> 
        <h1>ĐĂNG NHẬP</h1>
        <p class="subtitle" style="color: #f5f5f5;">Chào mừng bạn trở lại!</p>
        
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success') echo '<p class="message" style="background: rgba(76, 175, 80, 0.3); color: #d4edda;">Đăng ký thành công!</p>'; ?>
        <?php if ($message): ?>
            <p class="message" style="background: rgba(255, 0, 0, 0.3); color: #ffcccc;">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="Nhập email...">
            </div>
            <div class="input-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required placeholder="Nhập mật khẩu...">
            </div>
            <button type="submit">ĐĂNG NHẬP NGAY</button>
        </form>
        
        <div class="signup-link" style="margin-top:20px">
             Chưa có tài khoản? <a href="register.php">Đăng ký tại đây</a>
        </div>
        <a href="index.php" style="display: block; text-align: center; margin-top: 15px; color: #c4b5b5ff; font-size: 0.9em;">← Về Trang chủ</a>
    </div>
</body>
</html>