<?php
session_start();
include('db.php');

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: login.php'); // ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่และมีสิทธิ์เป็น user
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ค้นหาหนังสือ</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>

<header>
    <h1>ค้นหาหนังสือ</h1>
</header>

<nav>
    <a href="index.html">หน้าแรก</a>
    <a href="search.php">ค้นหาหนังสือ</a>
    <a href="logout.php">ออกจากระบบ</a>
</nav>

<div class="container">
    <form method="GET" action="search.php">
        <input type="text" name="query" placeholder="ค้นหาหนังสือ, ผู้แต่ง, หมวดหมู่" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
        <button type="submit">ค้นหา</button>
    </form>

    <div class="results">
        <?php
        if (isset($_GET['query'])) {
            // ใช้ htmlspecialchars ป้องกันการโจมตีแบบ XSS
            $query = htmlspecialchars($_GET['query']);
            
            // เตรียมคำสั่ง SQL สำหรับค้นหาหนังสือ
            $stmt = $pdo->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR category LIKE ?");
            $stmt->execute(["%$query%", "%$query%", "%$query%"]);
            $books = $stmt->fetchAll();
            
            if ($books) {
                echo "<ul>";
                foreach ($books as $book) {
                    // แสดงจำนวนหนังสือที่สามารถยืมได้
                    $available = $book['available'] > 0 ? "พร้อมให้ยืม ({$book['available']} เล่ม)" : "ไม่พร้อมให้ยืม (หมด)";
                    
                    echo "<li>
                        <a href='book-detail.php?id=" . htmlspecialchars($book['id']) . "'>" . htmlspecialchars($book['title']) . " โดย " . htmlspecialchars($book['author']) . "</a>
                        <p><strong>สถานะ:</strong> $available</p>
                    </li>";
                }
                echo "</ul>";
            } else {
                echo "<p style='color: red;'>ไม่พบหนังสือที่ตรงกับการค้นหา ลองค้นหาด้วยคำอื่น ๆ</p>";
            }
        }
        ?>
    </div>
</div>

<footer>
    <p>ห้องสมุดออนไลน์ &copy; 2024</p>
</footer>

</body>
</html>
