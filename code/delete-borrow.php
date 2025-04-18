<?php
session_start();
include('db.php');

// ตรวจสอบสิทธิ์ผู้ใช้
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// ตรวจสอบว่ามีการส่งข้อมูล book_id และ user_id หรือไม่
if (isset($_POST['book_id']) && isset($_POST['user_id'])) {
    $book_id = $_POST['book_id'];
    $user_id = $_POST['user_id'];

    try {
        // เริ่มธุรกรรม
        $pdo->beginTransaction();

        // ลบข้อมูลการยืมจากตาราง borrow
        $delete_stmt = $pdo->prepare("DELETE FROM borrow WHERE book_id = ? AND user_id = ? AND status = 'active'");
        $delete_stmt->execute([$book_id, $user_id]);

        // ตรวจสอบว่ามีการลบสำเร็จหรือไม่
        if ($delete_stmt->rowCount() > 0) {
            // อัปเดตสถานะของหนังสือในตาราง books
            $update_stmt = $pdo->prepare("UPDATE books SET available = available + 1 WHERE id = ?");
            $update_stmt->execute([$book_id]);

            // ตรวจสอบการอัปเดต
            if ($update_stmt->rowCount() > 0) {
                // คืนหนังสือสำเร็จ
                $pdo->commit(); // ยืนยันการทำธุรกรรม
                header('Location: borrowers.php?message=คืนหนังสือสำเร็จ');
                exit();
            } else {
                // การอัปเดตล้มเหลว
                $pdo->rollBack(); // ยกเลิกการทำธุรกรรม
                echo "เกิดข้อผิดพลาดในการอัปเดตสถานะของหนังสือ.";
            }
        } else {
            echo "ไม่มีข้อมูลการยืมสำหรับผู้ใช้และหนังสือที่ระบุ.";
        }
    } catch (Exception $e) {
        // ยกเลิกการทำธุรกรรมเมื่อเกิดข้อผิดพลาด
        $pdo->rollBack();
        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
} else {
    echo "ข้อมูลไม่ถูกต้อง.";
}
?>
