<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // ตรวจสอบชื่อผู้ใช้ในฐานข้อมูล
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // ตรวจสอบรหัสผ่าน
    if ($user && password_verify($password, $user['password'])) {
        // ตั้งค่าข้อมูลการเข้าสู่ระบบใน session
        $_SESSION['username'] = htmlspecialchars($user['username']);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['last_activity'] = time(); // ตั้งค่าเวลาล่าสุดของกิจกรรม

        // เปลี่ยนเส้นทางตาม role
        if ($user['role'] == 'admin') {
            header('Location: admin.php');
        } else {
            header('Location: user-account.php');
        }
        exit();
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}

// ตรวจสอบว่ามีการหมดเวลาเนื่องจาก inactivity หรือไม่
if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $error = "คุณถูกออกจากระบบเนื่องจากไม่มีการใช้งานเกิน 15 นาที";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./styles-login.css">
</head>
<body>

<header>
    <img src="gpn.png" alt="GPN Logo">
    <h1>ระบบห้องสมุดออนไลน์</h1>
    <div class="user-info" id="user-info">
    <nav>
        <a href="index.html">หน้าแรก</a>
    </nav>
    </div>
</header>

<div class="login-container">
    <h2>เข้าสู่ระบบ</h2>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div class="input-group">
            <label for="username">ชื่อผู้ใช้</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="input-group">
            <label for="password">รหัสผ่าน</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">เข้าสู่ระบบ</button>
    </form>
    <div class="footer">
        <p><a href="#">ลืมรหัสผ่าน?</a></p>
    </div>
    <div class="register-link">
        <p>ยังไม่มีบัญชี? <a href="register.php">ลงทะเบียนที่นี่</a></p>
    </div>
    <div class="social-login">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-google"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
    </div>
</div>

</body>
</html>
