<?php
include('db.php'); // เชื่อมต่อกับฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];  // รับชื่อจากฟอร์ม
    $username = $_POST['username'];  // รับชื่อผู้ใช้จากฟอร์ม
    $password = $_POST['password']; // รับรหัสผ่านจากฟอร์ม
    $email = $_POST['email']; // รับอีเมล

    // ตรวจสอบว่าชื่อผู้ใช้หรือรหัสผ่านเป็น 'admin' หรือไม่
    if ($username === 'admin' || $password === 'admin') {
        $error = "ห้ามใช้ชื่อผู้ใช้หรือรหัสผ่านว่า 'admin'"; // แสดงข้อความเตือน
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // เข้ารหัสรหัสผ่าน

        // ตั้งค่าบทบาท (role) เป็น user โดยเริ่มต้น
        $role = 'user';

        // ตรวจสอบว่ามีชื่อผู้ใช้นี้ในระบบแล้วหรือยัง
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $error = "ชื่อผู้ใช้นี้ถูกใช้งานแล้ว";  // ถ้ามีชื่อผู้ใช้ในระบบแล้ว
        } else {
            // เพิ่มข้อมูลผู้ใช้ใหม่ลงในฐานข้อมูล
            $stmt = $pdo->prepare("INSERT INTO users (name, username, password, email, role) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $username, $hashedPassword, $email, $role])) {
                header('Location: login.php'); // ถ้าสำเร็จให้ส่งไปหน้าเข้าสู่ระบบ
                exit(); // ออกจากสคริปต์เพื่อไม่ให้มีการประมวลผลต่อ
            } else {
                $error = "เกิดข้อผิดพลาดในการสมัครสมาชิก"; // แสดงข้อผิดพลาด
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <link rel="stylesheet" href="./styles.css"> <!-- ลิงก์ไปยัง CSS -->
</head>
<body>

<header>
    <h1>สมัครสมาชิก</h1>
</header>

<nav>
    <a href="index.html">หน้าแรก</a>
    <a href="login.php">เข้าสู่ระบบ</a>
</nav>

<div class="container">
    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p> <!-- แสดงข้อผิดพลาดถ้ามี -->
    <?php endif; ?>
    
    <form method="POST" action="register.php">
        <label for="name">ชื่อ:</label>
        <input type="text" id="name" name="name" required> <!-- ฟิลด์ชื่อ -->
        
        <label for="username">ชื่อผู้ใช้:</label>
        <input type="text" id="username" name="username" required> <!-- ฟิลด์ชื่อผู้ใช้ -->
        
        <label for="password">รหัสผ่าน:</label>
        <input type="password" id="password" name="password" required> <!-- ฟิลด์รหัสผ่าน -->
        
        <label for="email">อีเมล:</label>
        <input type="email" id="email" name="email" required> <!-- ฟิลด์อีเมล -->
        
        <button type="submit">สมัครสมาชิก</button> <!-- ปุ่มส่งข้อมูล -->
    </form>
</div>

<footer>
    <p>ห้องสมุดออนไลน์ &copy; 2024</p>
</footer>

</body>
</html>
