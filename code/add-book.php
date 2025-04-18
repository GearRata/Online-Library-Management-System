<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

include('db.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $category = $_POST['category'];
    $quantity = $_POST['available']; // เพิ่มจำนวนหนังสือ

    $stmt = $pdo->prepare("INSERT INTO books (title, author, isbn, category, quantity, available) VALUES (?, ?, ?, ?, ?, ?)"); // เพิ่มฟิลด์จำนวนหนังสือ
    if ($stmt->execute([$title, $author, $isbn, $category, $quantity, $quantity])) { // จำนวนหนังสือที่เพิ่มและจำนวนที่พร้อมให้ยืมเป็นค่าเริ่มต้น
        $success = "เพิ่มหนังสือสำเร็จ!";
    } else {
        $error = "เกิดข้อผิดพลาดในการเพิ่มหนังสือ";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มหนังสือ</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1>เพิ่มหนังสือ</h1>
</header>

<div class="container">
    <?php if (isset($success)): ?>
        <p><?php echo $success; ?></p>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="add-book.php">
        <label for="title">ชื่อหนังสือ:</label>
        <input type="text" id="title" name="title" required>
        
        <label for="author">ผู้เขียน:</label>
        <input type="text" id="author" name="author" required>
        
        <label for="isbn">ISBN:</label>
        <input type="text" id="isbn" name="isbn" required>
        
        <label for="category">หมวดหมู่:</label>
        <input type="text" id="category" name="category" required>
        
        <label for="quantity">จำนวน:</label> <!-- ฟิลด์ใหม่สำหรับจำนวน -->
        <input type="number" id="available" name="available" min="1" required> <!-- จำนวนขั้นต่ำคือ 1 -->
        
        <button type="submit">เพิ่มหนังสือ</button>
    </form>
</div>

<footer>
    <p>ห้องสมุดออนไลน์ &copy; 2024</p>
</footer>

</body>
</html>
