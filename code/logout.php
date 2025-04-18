<?php
session_start();
session_unset(); // ล้างข้อมูล session
session_destroy(); // ทำลาย session
header('Location: index.html'); // ส่งผู้ใช้กลับไปยังหน้าแรก
exit();
?>
