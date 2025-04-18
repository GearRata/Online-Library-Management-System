<?php
session_start();
include('db.php');

// ตรวจสอบสิทธิ์ admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// ดึงข้อมูลหนังสือ
try {
    $stmtBooks = $pdo->query("SELECT * FROM books");
    if ($stmtBooks !== false) {
        $books = $stmtBooks->fetchAll();
    } else {
        $books = [];
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $books = [];
}

// ดึงข้อมูลผู้ใช้
try {
    $stmtUsers = $pdo->query("SELECT id, username, role FROM users");
    $users = $stmtUsers->fetchAll();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $users = [];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        .add-book-form {
            margin-top: 20px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .message {
            color: green;
            margin-bottom: 15px;
        }
        .user-info a {
        color: rgb(0, 0, 0); /* เปลี่ยนเป็นสีที่คุณต้องการ */
        text-decoration: none; /* ลบเส้นใต้ */
        padding: 5px 10px; /* เพิ่มช่องว่างรอบๆ ลิงก์ */
        }

        .user-info a:hover {
        color: rgb(255, 0, 0); /* เปลี่ยนสีเมื่อชี้เมาส์ */
        text-decoration: underline; /* เพิ่มเส้นใต้เมื่อชี้เมาส์ */
        }


    </style>
</head>
<body>

<header>
    <h1>Admin Dashboard</h1>
    <div class="user-info" id="user-info">
        <a href='index.html'>หน้าแรก</a>
    </div>
    <a href="logout.php" class="logout-btn">Logout</a>
    
</header>

<!-- จัดการหนังสือ -->
<h2>จัดการหนังสือ</h2>
<div class="add-book-form">
    <h3>เพิ่มหนังสือใหม่</h3>
    <form method="POST" action="add-book.php">
        <label>ชื่อหนังสือ:</label>
        <input type="text" name="title" required>
        <label>ผู้เขียน:</label>
        <input type="text" name="author" required>
        <label>ISBN:</label>
        <input type="text" name="isbn" required>
        <label>หมวดหมู่:</label>
        <input type="text" name="category" required>
        <label>จำนวน:</label>
        <input type="number" name="quantity" min="1" required>
        <button type="submit" name="add_book">เพิ่มหนังสือ</button>
    </form>
</div>

<h3>คืนหนังสือ</h3>
<form method="POST" action="borrowers.php" style="display:inline;">
                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                <input type="hidden" name="user_id" value="<?php echo $book['borrower_id']; ?>">
                <button type="submit">คืนหนังสือ</button>
            </form>


<h3>รายการหนังสือ</h3>
<table>
    <tr>
        <th>ชื่อหนังสือ</th>
        <th>ผู้เขียน</th>
        <th>ISBN</th>
        <th>หมวดหมู่</th>
        <th>จำนวน</th>
        <th>จำนวนคงเหลือ</th>
        <th>การจัดการ</th>
    </tr>
    <?php foreach ($books as $book): ?>
    <tr>
        <td><?php echo htmlspecialchars($book['title']); ?></td>
        <td><?php echo htmlspecialchars($book['author']); ?></td>
        <td><?php echo htmlspecialchars($book['isbn']); ?></td>
        <td><?php echo htmlspecialchars($book['category']); ?></td>
        <td><?php echo htmlspecialchars($book['quantity']); ?></td>
        <td><?php echo htmlspecialchars($book['available']); ?></td>
        <td>
            <a href="edit-book.php?id=<?php echo $book['id']; ?>">แก้ไข</a>
            <form method="POST" action="delete-book.php" style="display:inline;">
                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                <button type="submit">ลบหนังสือ</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- จัดการสิทธิ์ผู้ใช้ -->
<h2>จัดการสิทธิ์ผู้ใช้</h2>
<table>
    <tr>
        <th>ชื่อผู้ใช้</th>
        <th>สิทธิ์ปัจจุบัน</th>
        <th>อัปเดตสิทธิ์</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?php echo htmlspecialchars($user['username']); ?></td>
        <td><?php echo htmlspecialchars($user['role']); ?></td>
        <td>
            <form method="POST" action="manage-users.php">
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                <select name="new_role">
                    <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                    <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>User</option>
                </select>
                <button type="submit" name="update_role">อัปเดตสิทธิ์</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
