<?php
// get-books.php
header('Content-Type: application/json');
include 'db.php'; // รวมไฟล์เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลหนังสือจากฐานข้อมูล
try {
    $result = $pdo->query("SELECT * FROM books");
    $books = [];

    if ($result) {
        while ($row = $result->fetch()) {
            $books[] = $row;
        }
    }

    // ส่งข้อมูลหนังสือในรูปแบบ JSON
    echo json_encode($books);
} catch (Exception $e) {
    // แสดงข้อความข้อผิดพลาดใน JSON
    echo json_encode(['error' => $e->getMessage()]);
}

$pdo = null; // ปิดการเชื่อมต่อ
?>
