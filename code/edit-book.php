<?php
session_start();
include('db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// ถ้ามีการส่งฟอร์มแก้ไขหนังสือ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];

    $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, isbn = ?, category = ?, quantity = ?, available = ? WHERE id = ?");
    if ($stmt->execute([$title, $author, $isbn, $category, $quantity, $quantity, $book_id])) {
        header('Location: admin.php');
        exit();
    } else {
        $error = "เกิดข้อผิดพลาดในการแก้ไขหนังสือ";
    }
}

// ดึงข้อมูลหนังสือสำหรับการแก้ไข
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขหนังสือ</title>
</head>
<body>

<h1>แก้ไขหนังสือ</h1>

<?php if (isset($error)): ?>
    <p><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST" action="">
    <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
    <label for="title">ชื่อหนังสือ:</label>
    <input type="text" id="title" name="title" value="<?php echo $book['title']; ?>" required>

    <label for="author">ผู้เขียน:</label>
    <input type="text" id="author" name="author" value="<?php echo $book['author']; ?>" required>

    <label for="isbn">ISBN:</label>
    <input type="text" id="isbn" name="isbn" value="<?php echo $book['isbn']; ?>" required>

    <label for="category">หมวดหมู่:</label>
    <input type="text" id="category" name="category" value="<?php echo $book['category']; ?>" required>

    <label for="quantity">จำนวน:</label>
    <input type="number" id="quantity" name="quantity" value="<?php echo $book['quantity']; ?>" min="0" required>

    <button type="submit">บันทึกการเปลี่ยนแปลง</button>
</form>

</body>
</html>
