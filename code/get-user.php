<?php
session_start();
header('Content-Type: application/json');

// ตั้งค่าเวลาหมดอายุเป็น 15 นาที (900 วินาที)
$timeout_duration = 900;

// ตรวจสอบเวลาการทำกิจกรรมล่าสุด
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // ถ้าเกินเวลา ให้ทำการออกจากระบบ
    session_unset();
    session_destroy();
    echo json_encode(['username' => null]); // ส่งกลับ null แทน
    exit();
}

// อัปเดตเวลาการทำกิจกรรมล่าสุด
$_SESSION['last_activity'] = time();

// ตรวจสอบว่าเข้าสู่ระบบแล้วหรือไม่
if (isset($_SESSION['username'])) {
    echo json_encode(['username' => $_SESSION['username']]);
} else {
    echo json_encode(['username' => null]);
}
?>
