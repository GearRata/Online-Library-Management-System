<?php
session_start();
include('db.php');

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: login.php'); // ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่และมีสิทธิ์เป็น user
    exit();
}



// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// ตรวจสอบว่าข้อมูลผู้ใช้ถูกดึงมาอย่างถูกต้อง
if (!$user) {
    echo "ไม่พบข้อมูลผู้ใช้";
    exit();
}

// ดึงข้อมูลการยืมหนังสือของผู้ใช้
$stmt = $pdo->prepare("SELECT books.title, borrow.start_date, borrow.end_date 
                       FROM borrow 
                       JOIN books ON borrow.book_id = books.id 
                       WHERE borrow.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$borrowed_books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บัญชีผู้ใช้</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>

<header>
    <h1>บัญชีผู้ใช้ของฉัน</h1>
</header>

<nav>
    <a href="index.html">หน้าแรก</a>
    <a href="search.php">ค้นหาหนังสือ</a>
    <a href="logout.php">ออกจากระบบ</a>
</nav>

<div class="container">
    <h2>ข้อมูลส่วนตัว</h2>
    <p><strong>ชื่อผู้ใช้:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>อีเมล:</strong> 
        <?php echo isset($user['email']) ? htmlspecialchars($user['email']) : 'ไม่พบข้อมูล'; ?>
    </p>

    <h2>ประวัติการยืมหนังสือ</h2>
    <?php if ($borrowed_books): ?>
        <table>
            <thead>
                <tr>
                    <th>ชื่อหนังสือ</th>
                    <th>วันที่ยืม</th>
                    <th>วันที่ต้องคืน</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($borrowed_books as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($book['end_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>คุณยังไม่ได้ยืมหนังสือใด ๆ</p>
    <?php endif; ?>
</div>

<footer>
    <p>ห้องสมุดออนไลน์ &copy; 2024</p>
</footer>

</body>
</html>
