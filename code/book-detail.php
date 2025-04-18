<?php
session_start();
include('db.php');

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: login.php'); // ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่และมีสิทธิ์เป็น user
    exit();
}

// ตรวจสอบว่าได้รับ ID ของหนังสือหรือไม่
if (!isset($_GET['id'])) {
    echo "ไม่พบหนังสือ";
    exit();
}

// รับ ID ของหนังสือ
$book_id = intval($_GET['id']);

// ดึงข้อมูลหนังสือจากฐานข้อมูล
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

// ตรวจสอบว่าข้อมูลหนังสือถูกดึงมาอย่างถูกต้อง
if (!$book) {
    echo "ไม่พบข้อมูลหนังสือ";
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?></title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>

<header>
    <h1>รายละเอียดหนังสือ</h1>
</header>

<nav>
    <a href="index.html">หน้าแรก</a>
    <a href="search.php">ค้นหาหนังสือ</a>
    <a href="logout.php">ออกจากระบบ</a>
</nav>

<div class="container">
    <h2><?php echo htmlspecialchars($book['title']); ?></h2>
    <p><strong>ผู้แต่ง:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
    <p><strong>หมวดหมู่:</strong> <?php echo htmlspecialchars($book['category']); ?></p>
    <p><strong>สถานะ:</strong> <?php echo $book['available'] > 0 ? "พร้อมให้ยืม ({$book['available']} เล่ม)" : "ไม่พร้อมให้ยืม (หมด)"; ?></p>

    <?php if ($book['available'] > 0): ?>
        <form method="POST" action="borrow-book.php">
            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
            <button type="submit">ยืมหนังสือ</button>
        </form>
    <?php else: ?>
        <p>หนังสือนี้ไม่สามารถยืมได้ในขณะนี้</p>
    <?php endif; ?>
</div>

<footer>
    <p>ห้องสมุดออนไลน์ &copy; 2024</p>
</footer>

</body>
</html>
