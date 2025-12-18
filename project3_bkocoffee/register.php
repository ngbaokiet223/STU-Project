<?php
require 'config.php';

$message = '';
$errors = [];
$name = '';
$email = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($name)) $errors[] = "Tên không được để trống.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ.";
    if (strlen($password) < 6) $errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Kiểm tra email tồn tại
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Email này đã được sử dụng.";
            } else {
                // Insert User
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $password_hash]);
                
                redirect('login.php?status=success');
            }
        } catch (PDOException $e) {
            $errors[] = "Lỗi CSDL: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký Tài Khoản</title>
    <link rel="stylesheet" href="assets/css/auth.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-card"> 
        
        <h1>Đăng Ký</h1>
        <p class="subtitle">Tạo tài khoản mới ngay</p>
        
        <?php if (!empty($errors)): ?>
            <p class="message" style="color: #ffcccc; background: rgba(255,0,0,0.2);"><?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label for="name">Họ và Tên:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required placeholder="Nhập tên của bạn...">
            </div>
            
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="Nhập email...">
            </div>
            
            <div class="input-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required placeholder="Tối thiểu 6 ký tự...">
            </div>
            
            <button type="submit">Đăng Ký Tài Khoản</button>
        </form>
        
        <div class="signup-link">
             <p style="text-align: center; color: #f5f5f5; margin-top: 20px;">Đã có tài khoản? 
                <a href="login.php">Đăng nhập ngay</a>
             </p>
        </div>
        
    </div>
</body>
</html>