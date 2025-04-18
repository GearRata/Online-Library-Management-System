<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: login.php'); // ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
    exit();
}

include('db.php');

// กำหนดตัวแปรเพื่อจัดการข้อความ
$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
    $book_id = intval($_POST['book_id']); // ใช้ intval ป้องกัน SQL Injection

    // ดึงข้อมูลหนังสือ
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();

    if ($book) {
        // ตรวจสอบว่าหนังสือสามารถยืมได้หรือไม่
        if (intval($book['available']) > 0) {
            // ตรวจสอบว่าผู้ใช้ได้ยืมหนังสือเล่มนี้อยู่หรือไม่
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM borrow WHERE user_id = ? AND book_id = ? AND end_date > NOW()");
            $stmt->execute([$_SESSION['user_id'], $book_id]);
            $hasBorrowed = $stmt->fetchColumn();

            if ($hasBorrowed == 0) {
                // เริ่มการยืมหนังสือ
                $start_date = date('Y-m-d');
                $end_date = date('Y-m-d', strtotime('+14 days'));

                // เพิ่มข้อมูลการยืมหนังสือในตาราง borrow
                $stmt = $pdo->prepare("INSERT INTO borrow (user_id, book_id, start_date, end_date) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$_SESSION['user_id'], $book_id, $start_date, $end_date])) {
                    // อัปเดตจำนวนหนังสือที่สามารถยืมได้
                    $stmt = $pdo->prepare("UPDATE books SET available = available - 1 WHERE id = ?");
                    $stmt->execute([$book_id]);

                    $success = "ยืมหนังสือสำเร็จ!";
                } else {
                    $error = "เกิดข้อผิดพลาดในการยืมหนังสือ";
                }
            } else {
                $error = "คุณได้ยืมหนังสือเล่มนี้ไปแล้ว";
            }
        } else {
            $error = "หนังสือนี้ไม่มีให้ยืม";
        }
    } else {
        $error = "ไม่พบข้อมูลหนังสือ";
    }
}

// ดึงข้อมูลหนังสือทั้งหมด
$stmt = $pdo->query("SELECT * FROM books");
$books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืมหนังสือ</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>

<header>
    <h1>ยืมหนังสือ</h1>
</header>

<nav>
    <a href="index.html">หน้าแรก</a>
    <a href="user-account.php">บัญชีผู้ใช้</a>
    <a href="logout.php">ออกจากระบบ</a>
</nav>

<div class="container">
    <?php if ($success): ?>
        <p><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

<footer>
    <p>ห้องสมุดออนไลน์ &copy; 2024</p>
</footer>

</body>
</html>
