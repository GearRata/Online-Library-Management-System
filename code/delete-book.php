<?php
session_start();
include('db.php');

// ตรวจสอบสิทธิ์ผู้ใช้
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// ตรวจสอบว่ามีการส่งค่า book_id มาหรือไม่
if (isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];

    // ลบหนังสือจากฐานข้อมูล
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    if ($stmt->execute([$book_id])) {
        header('Location: admin.php'); // เปลี่ยนเส้นทางกลับไปที่หน้าจัดการหนังสือ
        exit();
    } else {
        echo "ไม่สามารถลบหนังสือได้";
    }
} else {
    echo "ไม่พบข้อมูลหนังสือ";
}
?>
