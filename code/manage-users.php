<?php
session_start();
include('db.php');

// ตรวจสอบสิทธิ์ admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// ตรวจสอบว่ามีการส่งฟอร์มมา
if (isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];

    // อัปเดตสิทธิ์ผู้ใช้
    try {
        $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
        $stmt->execute(['role' => $new_role, 'id' => $user_id]);

        // ส่งกลับไปที่หน้าแอดมินหลังจากอัปเดต
        header('Location: admin.php');
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // ถ้าไม่มีการส่งฟอร์มมา ส่งกลับไปที่หน้าแอดมิน
    header('Location: admin.php');
    exit();
}
