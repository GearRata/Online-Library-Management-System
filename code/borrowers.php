<?php
session_start();
include('db.php');

// ตรวจสอบสิทธิ์ผู้ใช้
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// ดึงข้อมูลผู้ยืมจากฐานข้อมูล
try {
    $stmt = $pdo->query("SELECT users.id AS user_id, users.name AS borrower, users.email, books.title, borrow.start_date, borrow.end_date, borrow.book_id 
                         FROM borrow 
                         JOIN users ON borrow.user_id = users.id
                         JOIN books ON borrow.book_id = books.id");
    $borrowers = $stmt->fetchAll() ?: []; // ดึงข้อมูลและใช้ตัวแปร $borrowers เป็นอาเรย์ว่างถ้าไม่มีข้อมูล
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายชื่อผู้ยืมหนังสือ</title>
    <style>
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
        }
        h1 {
            margin: 0;
        }
        .logout-btn {
            margin-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<header>
    <h1>รายชื่อผู้ยืมหนังสือ</h1>
    <a href="admin.php" class="logout-btn">admin</a>
    <a href="logout.php" class="logout-btn">Logout</a>
    
</header>

<h2>รายการผู้ยืม</h2>
<?php if (isset($_GET['message'])): ?>
    <p><?php echo htmlspecialchars($_GET['message']); ?></p>
<?php endif; ?>

<table>
    <tr>
        <th>ชื่อผู้ยืม</th>
        <th>อีเมล</th>
        <th>ชื่อหนังสือ</th>
        <th>วันที่ยืม</th>
        <th>วันที่ต้องคืน</th>
        <th>การจัดการ</th>
    </tr>
    <?php if (!empty($borrowers)): ?>
        <?php foreach ($borrowers as $borrower): ?>
        <tr>
            <td><?php echo htmlspecialchars($borrower['borrower']); ?></td>
            <td><?php echo htmlspecialchars($borrower['email']); ?></td>
            <td><?php echo htmlspecialchars($borrower['title']); ?></td>
            <td><?php echo htmlspecialchars($borrower['start_date']); ?></td>
            <td><?php echo htmlspecialchars($borrower['end_date'] ?? '-'); ?></td>
            <td>
                <form method="POST" action="delete-borrow.php" style="display:inline;">
                    <input type="hidden" name="book_id" value="<?php echo $borrower['book_id']; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $borrower['user_id']; ?>"> <!-- ใช้ user_id จาก borrower -->
                    <button type="submit">คืนหนังสือ</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">ไม่มีการยืมหนังสือในขณะนี้</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>
